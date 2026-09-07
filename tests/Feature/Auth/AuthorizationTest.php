<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $keeper;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->manager = User::factory()->create();
        $this->manager->assignRole('warehouse_manager');

        $this->keeper = User::factory()->create();
        $this->keeper->assignRole('store_keeper');

        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole('user');
    }

    public function test_admin_has_all_permissions(): void
    {
        $this->assertTrue($this->admin->hasPermissionTo('view_dashboard'));
        $this->assertTrue($this->admin->hasPermissionTo('manage_users'));
        $this->assertTrue($this->admin->hasPermissionTo('view_users'));
        $this->assertTrue($this->admin->hasPermissionTo('manage_roles'));
        $this->assertTrue($this->admin->hasPermissionTo('view_roles'));
    }

    public function test_regular_user_cannot_manage_users(): void
    {
        $this->assertFalse($this->regularUser->hasPermissionTo('manage_users'));
    }

    public function test_regular_user_cannot_manage_roles(): void
    {
        $this->assertFalse($this->regularUser->hasPermissionTo('manage_roles'));
    }

    public function test_regular_user_can_view_dashboard(): void
    {
        $this->assertTrue($this->regularUser->hasPermissionTo('view_dashboard'));
    }

    public function test_all_roles_exist(): void
    {
        $this->assertTrue(Role::where('name', 'admin')->exists());
        $this->assertTrue(Role::where('name', 'warehouse_manager')->exists());
        $this->assertTrue(Role::where('name', 'store_keeper')->exists());
        $this->assertTrue(Role::where('name', 'user')->exists());
    }

    public function test_admin_can_access_all_admin_pages(): void
    {
        $pages = ['/admin/users', '/admin/roles'];

        foreach ($pages as $page) {
            $response = $this->actingAs($this->admin)->get($page);
            $response->assertStatus(200, "Failed asserting that admin can access {$page}");
        }
    }

    public function test_regular_user_is_forbidden_from_admin_pages(): void
    {
        $pages = ['/admin/users', '/admin/roles'];

        foreach ($pages as $page) {
            $response = $this->actingAs($this->regularUser)->get($page);
            $response->assertStatus(403, "Failed asserting that user is forbidden from {$page}");
        }
    }

    public function test_guest_cannot_access_admin_pages(): void
    {
        $pages = ['/admin/users', '/admin/roles', '/dashboard'];

        foreach ($pages as $page) {
            $response = $this->get($page);
            $response->assertRedirect('/login');
        }
    }

    public function test_permissions_exist_in_database(): void
    {
        $requiredPermissions = [
            'view_dashboard',
            'manage_users', 'view_users',
            'manage_roles', 'view_roles',
        ];

        foreach ($requiredPermissions as $permission) {
            $this->assertTrue(
                Permission::where('name', $permission)->exists(),
                "Permission '{$permission}' should exist in database"
            );
        }
    }
}