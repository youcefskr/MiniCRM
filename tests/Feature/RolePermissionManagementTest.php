<?php


namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un rôle admin avec toutes les permissions
        $adminRole = Role::create(['name' => 'admin']);
        $permission = Permission::create(['name' => 'manage role and permissions']);
        
        // Créer un utilisateur admin
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $adminRole->givePermissionTo($permission);
    }

    /** @test */
    public function admin_can_view_roles_and_permissions_page()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.roles.index'))
            ->assertStatus(200)
            ->assertViewIs('roles.index')
            ->assertViewHas(['roles', 'permissions']);
    }

    /** @test */
    public function admin_can_create_new_role()
    {
        $roleData = [
            'name' => 'test-role',
            'permissions' => ['manage role and permissions']
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), $roleData)
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'test-role']);
    }

    /** @test */
    public function admin_cannot_create_duplicate_role()
    {
        Role::create(['name' => 'existing-role']);

        $roleData = [
            'name' => 'existing-role',
            'permissions' => ['manage role and permissions']
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.roles.store'), $roleData)
            ->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_update_role()
    {
        $role = Role::create(['name' => 'old-role']);

        $updateData = [
            'name' => 'updated-role',
            'permissions' => ['manage role and permissions']
        ];

        $this->actingAs($this->admin)
            ->put(route('admin.roles.update', $role), $updateData)
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('roles', ['name' => 'updated-role']);
    }

    /** @test */
    public function admin_cannot_delete_protected_roles()
    {
        $protectedRole = Role::create(['name' => 'super-admin']);

        $this->actingAs($this->admin)
            ->delete(route('admin.roles.destroy', $protectedRole))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('roles', ['name' => 'super-admin']);
    }

    /** @test */
    public function admin_can_create_new_permission()
    {
        $permissionData = [
            'name' => 'test-permission'
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.permissions.store'), $permissionData)
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('permissions', ['name' => 'test-permission']);
    }

    /** @test */
    public function admin_can_delete_permission()
    {
        $permission = Permission::create(['name' => 'test-permission']);

        $this->actingAs($this->admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertRedirect(route('admin.roles.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('permissions', ['name' => 'test-permission']);
    }

    
}