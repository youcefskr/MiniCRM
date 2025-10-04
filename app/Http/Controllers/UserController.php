<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => [
                    'required', 
                    'string', 
                    'email', 
                    'max:255',
                    'unique:users,email'
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed'
                ],
                'roles' => ['required', 'array'],
                'roles.*' => ['exists:roles,name']
            ], [
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
                'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.'
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);

            $user->assignRole($validated['roles']);

            return redirect()
                ->route('admin.users.index')
                ->with('success', "L'utilisateur a été créé avec succès.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Erreur de validation. Veuillez vérifier les informations saisies.');
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', Password::defaults()],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name']
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);
        $user->syncRoles($validated['roles']);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$user->name} a été mis à jour avec succès.");
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "Vous ne pouvez pas supprimer votre propre compte.");
        }

        // Prevent deletion of super-admin
        if ($user->hasRole('super-admin')) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', "Vous ne pouvez pas supprimer un super administrateur.");
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "L'utilisateur {$userName} a été supprimé avec succès.");
    }
}