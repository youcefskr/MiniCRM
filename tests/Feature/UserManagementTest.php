<?php


namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected $admin;
    protected $role;

    protected function setUp(): void
    {
        parent::setUp();
        
        
        $this->role = Role::create(['name' => 'admin']);
        
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    /** @test */
    public function admin_can_view_users_list()
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.index'))
            ->assertStatus(200)
            ->assertViewIs('users.index')
            ->assertViewHas('users')
            ->assertViewHas('roles');
    }

    /** @test */
    public function admin_can_create_new_user()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['admin']
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);
    }

    /** @test */
    public function admin_cannot_create_user_with_duplicate_email()
    {
        $existingUser = User::factory()->create();

        $userData = [
            'name' => $this->faker->name,
            'email' => $existingUser->email, // Email déjà utilisé
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['admin']
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.users.store'), $userData);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function admin_can_update_user()
    {
        $user = User::factory()->create();
        $newData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'roles' => ['admin']
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.users.update', $user), $newData);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com'
        ]);
    }

    /** @test */
    public function admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $user));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    /** @test */
    public function admin_cannot_delete_super_admin()
    {
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $superAdmin));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id' => $superAdmin->id
        ]);
    }
}