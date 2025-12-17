<x-layouts.app :title="$conversation->type === 'private' ? ($otherParticipant->name ?? 'Conversation') : $conversation->name">
    <div class="h-[calc(100vh-4rem)] flex" x-data="{
        messages: {{ json_encode($messages) }},
        newMessage: '',
        fileInput: null,
        selectedFile: null,
        isLoading: false,
        showEmojiPicker: false,
        showAttachMenu: false,
        isTyping: false,
        typingUser: null,
        conversationId: {{ $conversation->id }},
        lastMessageId: {{ $messages->last()?->id ?? 0 }},
        
        emojis: ['😀', '😂', '😍', '🥰', '😎', '🤔', '👍', '👎', '❤️', '🔥', '🎉', '👏', '😊', '🙏', '💪', '✨', '🚀', '💯', '👀', '🤝'],
        
        init() {
            this.scrollToBottom();
            
            // WebSocket ou Polling
            if (window.Echo) {
                try {
                    window.Echo.private(`conversation.${this.conversationId}`)
                        .listen('.message.sent', (data) => {
                            if (!this.messages.some(m => m.id === data.message.id)) {
                                this.messages.push(data.message);
                                this.lastMessageId = data.message.id;
                            }
                        })
                        .listenForWhisper('typing', (e) => {
                            this.typingUser = e.name;
                            this.isTyping = true;
                            setTimeout(() => { this.isTyping = false; }, 3000);
                        });
                } catch (error) {
                    this.enablePolling();
                }
            } else {
                this.enablePolling();
            }
            
            this.$watch('messages', () => {
                this.$nextTick(() => this.scrollToBottom());
            });
        },
        
        pollingInterval: null,
        
        enablePolling() {
            if (this.pollingInterval) clearInterval(this.pollingInterval);
            this.pollingInterval = setInterval(() => this.checkNewMessages(), 2000);
        },
        
        async checkNewMessages() {
            try {
                const response = await fetch(`/messages/${this.conversationId}/messages?last_message_id=${this.lastMessageId}`);
                if (!response.ok) return;
                const newMessages = await response.json();
                if (newMessages?.length > 0) {
                    newMessages.forEach(msg => {
                        if (!this.messages.some(m => m.id === msg.id)) {
                            this.messages.push(msg);
                            this.lastMessageId = msg.id;
                        }
                    });
                }
            } catch (error) {}
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() && !this.selectedFile) return;
            
            this.isLoading = true;
            const formData = new FormData();
            formData.append('content', this.newMessage);
            if (this.selectedFile) {
                formData.append('file', this.selectedFile);
            }
            
            try {
                const response = await fetch(`/messages/${this.conversationId}/send`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.lastMessageId = data.message.id;
                    this.newMessage = '';
                    this.selectedFile = null;
                    if (this.fileInput) this.fileInput.value = '';
                }
            } catch (error) {
                alert('Erreur lors de l\\'envoi');
            } finally {
                this.isLoading = false;
            }
        },
        
        addEmoji(emoji) {
            this.newMessage += emoji;
            this.showEmojiPicker = false;
            this.$refs.messageInput.focus();
        },
        
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                this.selectedFile = file;
                this.fileInput = event.target;
            }
        },
        
        removeSelectedFile() {
            this.selectedFile = null;
            if (this.fileInput) this.fileInput.value = '';
        },
        
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        },
        
        formatTime(date) {
            return new Date(date).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        },
        
        isImage(fileType) {
            return fileType && fileType.startsWith('image/');
        },
        
        getFileIcon(fileType) {
            if (!fileType) return '📄';
            if (fileType.includes('pdf')) return '📕';
            if (fileType.includes('word')) return '📘';
            if (fileType.includes('excel') || fileType.includes('spreadsheet')) return '📗';
            return '📄';
        }
    }">
        <!-- Sidebar avec liste condensée -->
        <div class="hidden lg:block w-80 bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <a href="{{ route('messages.index') }}" class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400 hover:underline">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Toutes les conversations
                </a>
            </div>
        </div>

        <!-- Zone de conversation principale -->
        <div class="flex-1 flex flex-col bg-gradient-to-b from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-900">
            <!-- Header de la conversation -->
            <div class="flex-shrink-0 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('messages.index') }}" class="lg:hidden p-2 -ml-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        
                        <div class="relative">
                            @if($conversation->type === 'private')
                                <div class="size-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold shadow-lg">
                                    {{ $otherParticipant->name[0] ?? 'U' }}
                                </div>
                            @else
                                <div class="size-12 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white shadow-lg">
                                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute -bottom-0.5 -right-0.5 size-3.5 bg-green-500 border-2 border-white dark:border-zinc-900 rounded-full"></div>
                        </div>
                        
                        <div>
                            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                {{ $conversation->type === 'private' ? ($otherParticipant->name ?? 'Utilisateur') : $conversation->name }}
                            </h2>
                            <div class="flex items-center gap-2">
                                <template x-if="isTyping">
                                    <span class="text-sm text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                                        <span class="flex gap-0.5">
                                            <span class="size-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                            <span class="size-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                            <span class="size-1.5 bg-indigo-600 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                        </span>
                                        <span x-text="typingUser + ' écrit...'"></span>
                                    </span>
                                </template>
                                <template x-if="!isTyping">
                                    <span class="text-sm text-green-600 dark:text-green-400 flex items-center gap-1">
                                        <span class="size-2 bg-green-500 rounded-full"></span>
                                        En ligne
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if($conversation->contact)
                            <a href="{{ route('contacts.show', $conversation->contact) }}" 
                               class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg"
                               title="Voir le contact">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </a>
                        @endif
                        <button class="p-2 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div x-ref="messagesContainer" class="flex-1 overflow-y-auto px-4 py-6 space-y-4">
                <template x-for="(message, index) in messages" :key="message.id">
                    <div>
                        <!-- Séparateur de date (simplifié) -->
                        <template x-if="index === 0 || new Date(messages[index-1]?.created_at).toDateString() !== new Date(message.created_at).toDateString()">
                            <div class="flex justify-center my-4">
                                <span class="px-3 py-1 text-xs text-zinc-500 bg-white dark:bg-zinc-800 rounded-full shadow-sm">
                                    <span x-text="new Date(message.created_at).toLocaleDateString('fr-FR', {day: 'numeric', month: 'long'})"></span>
                                </span>
                            </div>
                        </template>
                        
                        <!-- Message -->
                        <div class="flex" :class="message.user_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                            <div class="flex items-end gap-2 max-w-[75%]" :class="message.user_id === {{ Auth::id() }} ? 'flex-row-reverse' : ''">
                                <!-- Avatar (seulement pour les autres) -->
                                <template x-if="message.user_id !== {{ Auth::id() }}">
                                    <div class="flex-shrink-0 size-8 rounded-full bg-gradient-to-br from-zinc-400 to-zinc-500 flex items-center justify-center text-white text-xs font-bold">
                                        <span x-text="message.user?.name?.charAt(0)?.toUpperCase() || 'U'"></span>
                                    </div>
                                </template>
                                
                                <div :class="message.user_id === {{ Auth::id() }} ? 'items-end' : 'items-start'" class="flex flex-col">
                                    <!-- Nom (groupe seulement) -->
                                    @if($conversation->type === 'group')
                                        <template x-if="message.user_id !== {{ Auth::id() }}">
                                            <span class="text-xs font-medium text-zinc-500 mb-1 ml-1" x-text="message.user?.name"></span>
                                        </template>
                                    @endif
                                    
                                    <!-- Bulle de message -->
                                    <div class="relative group">
                                        <div class="px-4 py-2.5 rounded-2xl shadow-sm"
                                             :class="message.user_id === {{ Auth::id() }} 
                                                 ? 'bg-gradient-to-br from-indigo-500 to-purple-600 text-white rounded-br-md' 
                                                 : 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-bl-md border border-zinc-200 dark:border-zinc-700'">
                                            
                                            <!-- Image preview -->
                                            <template x-if="message.file_path && isImage(message.file_type)">
                                                <div class="mb-2">
                                                    <img :src="'/storage/' + message.file_path" 
                                                         :alt="message.file_name"
                                                         class="max-w-[240px] rounded-lg cursor-pointer hover:opacity-90"
                                                         @click="window.open('/storage/' + message.file_path, '_blank')">
                                                </div>
                                            </template>
                                            
                                            <!-- File attachment -->
                                            <template x-if="message.file_path && !isImage(message.file_type)">
                                                <a :href="'/storage/' + message.file_path" 
                                                   target="_blank"
                                                   class="flex items-center gap-2 p-2 mb-2 rounded-lg"
                                                   :class="message.user_id === {{ Auth::id() }} ? 'bg-white/10 hover:bg-white/20' : 'bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600'">
                                                    <span class="text-2xl" x-text="getFileIcon(message.file_type)"></span>
                                                    <div class="min-w-0">
                                                        <span class="text-sm font-medium block truncate" x-text="message.file_name"></span>
                                                        <span class="text-xs opacity-70">Cliquer pour télécharger</span>
                                                    </div>
                                                </a>
                                            </template>
                                            
                                            <!-- Text content -->
                                            <p class="text-sm whitespace-pre-wrap break-words" x-text="message.content" x-show="message.content"></p>
                                        </div>
                                        
                                        <!-- Time & Read status -->
                                        <div class="flex items-center gap-1 mt-1 px-1"
                                             :class="message.user_id === {{ Auth::id() }} ? 'justify-end' : 'justify-start'">
                                            <span class="text-[11px] text-zinc-400" x-text="formatTime(message.created_at)"></span>
                                            <template x-if="message.user_id === {{ Auth::id() }}">
                                                <svg class="size-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- État vide -->
                <template x-if="messages.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-center">
                        <div class="size-24 mb-4 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center">
                            <svg class="size-12 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Commencez la conversation</h3>
                        <p class="text-sm text-zinc-500">Envoyez votre premier message !</p>
                    </div>
                </template>
            </div>

            <!-- Zone de saisie -->
            <div class="flex-shrink-0 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700 px-4 py-4">
                <!-- Preview fichier sélectionné -->
                <template x-if="selectedFile">
                    <div class="mb-3 flex items-center gap-3 p-3 bg-zinc-100 dark:bg-zinc-800 rounded-xl">
                        <template x-if="selectedFile.type.startsWith('image/')">
                            <img :src="URL.createObjectURL(selectedFile)" class="size-14 rounded-lg object-cover">
                        </template>
                        <template x-if="!selectedFile.type.startsWith('image/')">
                            <div class="size-14 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-2xl">
                                📄
                            </div>
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate" x-text="selectedFile.name"></p>
                            <p class="text-xs text-zinc-500" x-text="(selectedFile.size / 1024).toFixed(1) + ' KB'"></p>
                        </div>
                        <button @click="removeSelectedFile()" class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </template>
                
                <div class="flex items-end gap-3">
                    <!-- Bouton emoji -->
                    <div class="relative">
                        <button @click="showEmojiPicker = !showEmojiPicker" 
                                class="p-2.5 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                        
                        <!-- Emoji picker -->
                        <div x-show="showEmojiPicker" 
                             @click.away="showEmojiPicker = false"
                             x-transition
                             class="absolute bottom-14 left-0 bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-700 p-3 w-64">
                            <div class="grid grid-cols-5 gap-2">
                                <template x-for="emoji in emojis" :key="emoji">
                                    <button @click="addEmoji(emoji)" 
                                            class="text-2xl p-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition"
                                            x-text="emoji">
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton fichier -->
                    <label class="p-2.5 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-xl transition cursor-pointer">
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <input type="file" 
                               class="hidden" 
                               @change="handleFileSelect($event)"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif,.webp">
                    </label>
                    
                    <!-- Input message -->
                    <div class="flex-1 relative">
                        <textarea x-ref="messageInput"
                                  x-model="newMessage"
                                  @keydown.enter.exact.prevent="sendMessage()"
                                  @keydown.enter.shift.exact="newMessage += '\n'"
                                  rows="1"
                                  placeholder="Tapez votre message..."
                                  class="w-full px-4 py-3 rounded-2xl border-zinc-200 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 focus:ring-2 focus:ring-indigo-500 focus:border-transparent resize-none text-zinc-900 dark:text-zinc-100"
                                  style="min-height: 48px; max-height: 120px;"></textarea>
                    </div>
                    
                    <!-- Bouton envoyer -->
                    <button @click="sendMessage()"
                            :disabled="isLoading || (!newMessage.trim() && !selectedFile)"
                            class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transition-all">
                        <template x-if="!isLoading">
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </template>
                        <template x-if="isLoading">
                            <svg class="size-6 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
