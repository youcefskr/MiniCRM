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
     * Display a listing of users (with search, role filter and pagination).
     */
    public function index(Request $request)
    {
        $query = User::with('roles');

        // Filtrage par recherche (nom ou email)
        if ($search = $request->input('q')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrage par rôle
        if ($role = $request->input('role')) {
            $query->whereHas('roles', function($q) use ($role) {
                $q->where('name', $role);
            });
        }

        // Récupération des utilisateurs filtrés et paginés
        $users = $query->orderBy('name')
                       ->paginate(15)
                       ->withQueryString();

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
     * Export filtered users to CSV.
     */
    public function export(Request $request)
    {
        $q = $request->input('q');
        $role = $request->input('role');

        $users = User::with('roles')
            ->when($q, fn($query) => $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            }))
            ->when($role, fn($query) => $query->whereHas('roles', fn($q2) => $q2->where('name', $role)))
            ->orderBy('name')
            ->get();

        $filename = 'users_export_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($users) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel compatibility (optional)
            echo "\xEF\xBB\xBF";
            fputcsv($handle, ['ID', 'Name', 'Email', 'Roles', 'Created At']);
            foreach ($users as $u) {
                fputcsv($handle, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->roles->pluck('name')->implode(';'),
                    optional($u->created_at)->toDateTimeString(),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk delete users (ids[] array). Protect self and super-admin.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = $request->input('ids', []);
        $deleted = 0;
        foreach ($ids as $id) {
            if ($id === auth()->id()) {
                continue;
            }
            $user = User::find($id);
            if (!$user) {
                continue;
            }
            if ($user->hasRole('super-admin')) {
                continue;
            }
            $user->delete();
            $deleted++;
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$deleted} utilisateur(s) supprimé(s).");
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

