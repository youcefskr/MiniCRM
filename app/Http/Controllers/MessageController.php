<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Contact;
use App\Models\ConversationParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    /**
     * Liste des conversations de l'utilisateur
     */
    public function index()
    {
        $userId = Auth::id();
        
        $conversations = Conversation::whereHas('participants', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->with([
            'participants',
            'lastMessage.user',
            'contact'
        ])
        ->withCount('participants')
        ->orderBy('updated_at', 'desc')
        ->get();

        // Transformer pour avoir les infos de l'autre participant et compter les non lus
        $conversations = $conversations->map(function($conversation) use ($userId) {
            if ($conversation->type === 'private') {
                $otherParticipant = $conversation->getOtherParticipant($userId);
                $conversation->other_participant = $otherParticipant;
            }
            
            // Compter les messages non lus
            $participant = \App\Models\ConversationParticipant::where('conversation_id', $conversation->id)
                ->where('user_id', $userId)
                ->first();
            
            $unreadCount = 0;
            if ($participant) {
                $unreadCount = $conversation->messages()
                    ->where('user_id', '!=', $userId)
                    ->where('created_at', '>', $participant->last_read_at ?? '1970-01-01')
                    ->count();
            }
            
            $conversation->unread_count = $unreadCount;
            return $conversation;
        });

        $allUsers = User::where('id', '!=', Auth::id())->get();

        return view('messages.index', compact('conversations'))->with('users', $allUsers);
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show(Conversation $conversation)
    {
        // Vérifier que l'utilisateur est participant
        if (!$conversation->hasParticipant(Auth::id())) {
            abort(403, 'Vous n\'avez pas accès à cette conversation.');
        }

        // Marquer comme lue
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($participant) {
            $participant->markAsRead();
        }

        $messages = $conversation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $otherParticipant = null;
        if ($conversation->type === 'private') {
            $otherParticipant = $conversation->getOtherParticipant(Auth::id());
        }

        return view('messages.show', compact('conversation', 'messages', 'otherParticipant'));
    }

    /**
     * Créer ou obtenir une conversation privée
     */
    public function createPrivate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
        ]);

        $otherUserId = $request->user_id;

        if ($otherUserId == Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas créer une conversation avec vous-même.');
        }

        // Chercher si une conversation privée existe déjà
        $existingConversations = Conversation::where('type', 'private')
            ->whereHas('participants', function($query) use ($otherUserId) {
                $query->where('user_id', $otherUserId);
            })
            ->whereHas('participants', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->withCount('participants')
            ->get();
        
        $conversation = $existingConversations->firstWhere('participants_count', 2);

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'private',
                'created_by' => Auth::id(),
                'contact_id' => $request->contact_id,
            ]);

            $conversation->participants()->attach([Auth::id(), $otherUserId]);
        }

        return redirect()->route('messages.show', $conversation);
    }

    /**
     * Créer un groupe
     */
    public function createGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'description' => 'nullable|string',
        ]);

        $conversation = Conversation::create([
            'type' => 'group',
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'contact_id' => $request->contact_id,
        ]);

        // Ajouter le créateur et les autres participants
        $participants = array_merge([Auth::id()], $request->user_ids);
        $conversation->participants()->attach(array_unique($participants));

        return redirect()->route('messages.show', $conversation)
            ->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Envoyer un message
     */
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required_without:file|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        if (!$conversation->hasParticipant(Auth::id())) {
            abort(403, 'Vous n\'êtes pas participant de cette conversation.');
        }

        $filePath = null;
        $fileName = null;
        $fileType = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $fileType = $file->getMimeType();
            $filePath = $file->store('messages', 'public');
        }

        $message = $conversation->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content ?? '',
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
        ]);

        // Mettre à jour la date de la conversation
        $conversation->touch();

        // Broadcast event pour temps réel
        broadcast(new \App\Events\MessageSent($message))->toOthers();

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    /**
     * Marquer les messages comme lus
     */
    public function markAsRead(Conversation $conversation)
    {
        if (!$conversation->hasParticipant(Auth::id())) {
            abort(403);
        }

        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($participant) {
            $participant->markAsRead();
        }

        return response()->json(['success' => true]);
    }

    /**
     * Obtenir les nouveaux messages (pour polling AJAX)
     */
    public function getMessages(Conversation $conversation, Request $request)
    {
        if (!$conversation->hasParticipant(Auth::id())) {
            abort(403);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer comme lus
        $participant = ConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', Auth::id())
            ->first();
        if ($participant) {
            $participant->markAsRead();
        }

        return response()->json($messages);
    }

    /**
     * Rechercher dans les messages
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $conversationIds = Conversation::whereHas('participants', function($q) {
            $q->where('user_id', Auth::id());
        })->pluck('id');

        $messages = Message::whereIn('conversation_id', $conversationIds)
            ->where('content', 'like', "%{$query}%")
            ->with(['conversation', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('messages.search', compact('messages', 'query'));
    }

    /**
     * Obtenir les utilisateurs en ligne (pour affichage)
     */
    public function getOnlineUsers()
    {
        // Simple check - on peut améliorer avec des sessions actives
        $users = User::where('id', '!=', Auth::id())->get();
        
        return response()->json($users);
    }

    /**
     * Créer une nouvelle conversation (vue)
     */
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('messages.create', compact('users'));
    }
}
