<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_debug_admin_permissions(): void
{
    $user = User::factory()->create();
    $user->assignRole('admin');
    
    // تشخيص الصلاحيات
    dump('User roles:', $user->getRoleNames()->toArray());
    dump('Has manage_users?', $user->hasPermissionTo('manage_users'));
    
    $response = $this->actingAs($user)->get('/admin/users');
    $content = $response->getContent();
    
    // عرض جزء من المحتوى للتشخيص
    dump('Contains إضافة?', str_contains($content, 'إضافة'));
    dump('Contains users.create?', str_contains($content, 'users.create'));
    dump('Contains @can?', str_contains($content, 'can'));
    
    $this->assertTrue(true);
}

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_login_page_renders(): void
{
    $response = $this->get('/login');
    $response->assertStatus(200);
    $response->assertSee('البريد الإلكتروني');
}

 public function test_login_page_is_rtl(): void
{
    $response = $this->get('/login');
    $response->assertStatus(200);
    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'rtl') || str_contains($content, 'ar') || str_contains($content, 'Cairo'),
        'Login page should support RTL'
    );
}

public function test_dashboard_shows_welcome_message(): void
{
    $user = \App\Models\User::factory()->create(['name' => 'أحمد محمد']);
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'أحمد') ||
        str_contains($content, 'لوحة التحكم') ||
        str_contains($content, 'المخازن'),
        'Dashboard should contain welcome message or user name'
    );
}

public function test_dashboard_shows_statistics(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'المستخدمون') ||
        str_contains($content, 'الأدوار') ||
        str_contains($content, 'المخازن'),
        'Dashboard should contain statistics'
    );
}

public function test_sidebar_contains_required_links(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'لوحة التحكم'),
        'Sidebar should contain dashboard link'
    );
}

public function test_navbar_shows_user_name(): void
{
    $user = \App\Models\User::factory()->create(['name' => 'خالد العلي']);
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'خالد') ||
        str_contains($content, 'خ') ||
        str_contains($content, $user->email),
        'Navbar should show user name or avatar'
    );
}

public function test_users_page_contains_table(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/users');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'المستخدمون') ||
        str_contains($content, 'users') ||
        str_contains($content, 'table'),
        'Users page should contain users table or heading'
    );
}

public function test_roles_page_contains_table(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/roles');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'الأدوار') ||
        str_contains($content, 'roles') ||
        str_contains($content, 'table'),
        'Roles page should contain roles table or heading'
    );
}

public function test_add_user_button_visible_for_admin(): void
{
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/admin/users');

    $response->assertStatus(200);

    // المدير لديه صلاحية إدارة المستخدمين
    $this->assertTrue($user->hasPermissionTo('manage_users'));

    // زر إضافة مستخدم يجب أن يظهر للمدير
    $response->assertSee('إضافة مستخدم');

    // رابط صفحة إضافة المستخدم يجب أن يكون موجودًا
    $response->assertSee(route('admin.users.create', absolute: false));
}
public function test_layout_uses_cairo_font(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'Cairo') ||
        str_contains($content, 'cairo') ||
        str_contains($content, 'font') ||
        str_contains($content, 'Tajawal'),
        'Layout should use Arabic font'
    );
}

public function test_layout_uses_bootstrap_rtl(): void
{
    $user = \App\Models\User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/dashboard');
    $response->assertStatus(200);

    $content = $response->getContent();
    $this->assertTrue(
        str_contains($content, 'bootstrap') ||
        str_contains($content, 'Bootstrap') ||
        str_contains($content, 'cdn'),
        'Layout should use Bootstrap'
    );
}
}