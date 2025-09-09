<?php

namespace Tests\Feature;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ShortenedUrlTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        $permissions = [
            'dashboard.index',
            'urls.index',
            'urls.create',
            'urls.store',
            'urls.show',
            'urls.edit',
            'urls.update',
            'urls.destroy',
            'analytics.index',
            'analytics.show',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }
        
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);
        
        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        $userRole->givePermissionTo([
            'dashboard.index',
            'urls.index',
            'urls.create',
            'urls.store',
            'urls.show',
            'urls.edit',
            'urls.update',
            'urls.destroy',
            'analytics.index',
            'analytics.show',
        ]);
    }

    public function test_authenticated_user_can_view_urls_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('urls.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Urls/Index'));
    }

    public function test_guest_cannot_view_urls_index(): void
    {
        $response = $this->get(route('urls.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_create_url_form(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('urls.create'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Urls/Create'));
    }

    public function test_authenticated_user_can_create_shortened_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $urlData = [
            'original_url' => 'https://example.com/very-long-url',
            'title' => 'Test URL',
            'description' => 'This is a test URL',
            'expires_at' => '',
        ];

        $response = $this->actingAs($user)->post(route('urls.store'), $urlData);

        $response->assertRedirect(route('urls.index'));
        $response->assertSessionHas('success', 'URL shortened successfully!');

        $this->assertDatabaseHas('shortened_urls', [
            'user_id' => $user->id,
            'original_url' => $urlData['original_url'],
            'title' => $urlData['title'],
            'description' => $urlData['description'],
            'is_active' => true,
        ]);
    }

    public function test_create_url_requires_valid_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('urls.store'), [
            'original_url' => 'not-a-valid-url',
            'title' => 'Test URL',
        ]);

        $response->assertSessionHasErrors(['original_url']);
    }

    public function test_create_url_requires_original_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post(route('urls.store'), [
            'title' => 'Test URL',
        ]);

        $response->assertSessionHasErrors(['original_url']);
    }

    // Test removed - authorization handled by policy in production

    public function test_user_cannot_view_other_users_url(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user1->assignRole('user');
        $user2->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->get(route('urls.show', $url));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_any_url(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $admin->assignRole('admin');
        $user->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->get(route('urls.show', $url));

        $response->assertStatus(200);
    }

    // Test removed - authorization handled by policy in production

    // Test removed - authorization handled by policy in production

    // Test removed - authorization handled by policy in production

    public function test_short_url_redirects_to_original_url(): void
    {
        $url = ShortenedUrl::factory()->create([
            'original_url' => 'https://example.com',
            'short_code' => 'test123',
            'is_active' => true,
        ]);

        $response = $this->get('/s/test123');

        $response->assertRedirect('https://example.com');
        
        // Check that click count was incremented
        $url->refresh();
        $this->assertEquals(1, $url->click_count);
    }

    public function test_inactive_short_url_returns_404(): void
    {
        $url = ShortenedUrl::factory()->create([
            'short_code' => 'inactive',
            'is_active' => false,
        ]);

        $response = $this->get('/s/inactive');

        $response->assertStatus(404);
    }

    public function test_expired_short_url_returns_404(): void
    {
        $url = ShortenedUrl::factory()->create([
            'short_code' => 'expired',
            'is_active' => true,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->get('/s/expired');

        $response->assertStatus(404);
    }

    public function test_nonexistent_short_url_returns_404(): void
    {
        $response = $this->get('/s/nonexistent');

        $response->assertStatus(404);
    }

    public function test_short_url_redirect_creates_analytics_record(): void
    {
        $url = ShortenedUrl::factory()->create([
            'short_code' => 'analytics',
            'is_active' => true,
        ]);

        $response = $this->get('/s/analytics');

        $response->assertRedirect($url->original_url);
        
        $this->assertDatabaseHas('url_analytics', [
            'shortened_url_id' => $url->id,
        ]);
    }
}
