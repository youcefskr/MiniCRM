<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    /**
     * Affiche la liste des rôles et permissions
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Crée un nouveau rôle
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'Le nom du rôle est obligatoire',
            'name.unique' => 'Ce rôle existe déjà',
            'permissions.*.exists' => 'Une ou plusieurs permissions sont invalides'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        return redirect()
            ->route('admin.roles.index') // Changé de 'roles.index' à 'admin.roles.index'
            ->with('success', "Le rôle \"{$role->name}\" a été créé avec succès.");
    }

    /**
     * Met à jour un rôle existant
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'Le nom du rôle est obligatoire',
            'name.unique' => 'Ce rôle existe déjà',
            'permissions.*.exists' => 'Une ou plusieurs permissions sont invalides'
        ]);

        $role->update(['name' => $validated['name']]);

        // Synchronise les permissions
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()
            ->route('admin.roles.index')  // Changé de 'roles.index'
            ->with('success', "Le rôle \"{$role->name}\" a été mis à jour.");
    }

    /**
     * Supprime un rôle
     */
    public function destroyRole(Role $role)
    {
        // Empêche la suppression de rôles système critiques
        $protectedRoles = ['super-admin', 'admin'];
        
        if (in_array($role->name, $protectedRoles)) {
            return redirect()
                ->route('admin.roles.index')  // Changé de 'roles.index'
                ->with('error', "Le rôle \"{$role->name}\" est protégé et ne peut pas être supprimé.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()
            ->route('admin.roles.index')  // Changé de 'roles.index'
            ->with('success', "Le rôle \"{$roleName}\" a été supprimé.");
    }

    /**
     * Crée une nouvelle permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ], [
            'name.required' => 'Le nom de la permission est obligatoire',
            'name.unique' => 'Cette permission existe déjà'
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        return redirect()
            ->route('admin.roles.index')  // Changé de 'roles.index'
            ->with('success', "La permission \"{$permission->name}\" a été créée avec succès.");
    }

    /**
     * Supprime une permission
     */
    public function destroyPermission(Permission $permission)
    {
        $permissionName = $permission->name;
        
        // Détache la permission de tous les rôles avant suppression
        $permission->roles()->detach();
        $permission->delete();

        return redirect()
            ->route('admin.roles.index')  // Changé de 'roles.index'
            ->with('success', "La permission \"{$permissionName}\" a été supprimée.");
    }

    /**
     * Met à jour une permission existante
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update($validated);

        return redirect()
            ->route('admin.roles.index')
            ->with('success', "La permission \"{$permission->name}\" a été mise à jour.");
    }
}