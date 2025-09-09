<?php

namespace Tests\Feature;

use App\Models\ShortenedUrl;
use App\Models\UrlAnalytic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AnalyticsTest extends TestCase
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

    public function test_authenticated_user_can_view_analytics_index(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Analytics/Index'));
    }

    public function test_guest_cannot_view_analytics(): void
    {
        $response = $this->get(route('analytics.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_analytics_for_their_own_url(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('analytics.show', $url));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Analytics/Show'));
    }

    public function test_user_cannot_view_analytics_for_other_users_url(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user1->assignRole('user');
        $user2->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->get(route('analytics.show', $url));

        $response->assertStatus(403);
    }

    public function test_admin_can_view_analytics_for_any_url(): void
    {
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $admin->assignRole('admin');
        $user->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($admin)->get(route('analytics.show', $url));

        $response->assertStatus(200);
    }

    public function test_analytics_index_shows_user_statistics(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        // Create URLs for the user
        $urls = ShortenedUrl::factory()->count(3)->create([
            'user_id' => $user->id,
            'click_count' => 10,
        ]);

        $response = $this->actingAs($user)->get(route('analytics.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Analytics/Index')
                ->has('stats')
                ->where('stats.total_urls', 3)
                ->where('stats.total_clicks', 30)
        );
    }

    public function test_url_redirect_creates_analytics_record(): void
    {
        $url = ShortenedUrl::factory()->active()->create([
            'short_code' => 'test123',
            'original_url' => 'https://example.com',
        ]);

        $this->assertEquals(0, $url->analytics()->count());

        $response = $this->get('/s/test123');

        $response->assertRedirect('https://example.com');
        
        // Check analytics record was created
        $this->assertEquals(1, $url->analytics()->count());
        
        $analytic = $url->analytics()->first();
        $this->assertNotNull($analytic->ip_address);
        $this->assertNotNull($analytic->user_agent);
        $this->assertNotNull($analytic->clicked_at);
    }

    public function test_analytics_record_captures_user_agent(): void
    {
        $url = ShortenedUrl::factory()->active()->create([
            'short_code' => 'useragent',
        ]);

        $userAgent = 'Mozilla/5.0 (Test Browser)';
        
        $response = $this->withHeaders([
            'User-Agent' => $userAgent,
        ])->get('/s/useragent');

        $response->assertRedirect($url->original_url);
        
        $this->assertDatabaseHas('url_analytics', [
            'shortened_url_id' => $url->id,
            'user_agent' => $userAgent,
        ]);
    }

    public function test_analytics_record_captures_referrer(): void
    {
        $url = ShortenedUrl::factory()->active()->create([
            'short_code' => 'referrer',
        ]);

        $referrer = 'https://google.com';
        
        $response = $this->withHeaders([
            'Referer' => $referrer,
        ])->get('/s/referrer');

        $response->assertRedirect($url->original_url);
        
        $this->assertDatabaseHas('url_analytics', [
            'shortened_url_id' => $url->id,
            'referer' => $referrer,
        ]);
    }

    public function test_click_count_increments_on_redirect(): void
    {
        $url = ShortenedUrl::factory()->active()->create([
            'short_code' => 'counter',
            'click_count' => 5,
        ]);

        $response = $this->get('/s/counter');

        $response->assertRedirect($url->original_url);
        
        $url->refresh();
        $this->assertEquals(6, $url->click_count);
    }

    public function test_multiple_clicks_create_separate_analytics_records(): void
    {
        $url = ShortenedUrl::factory()->active()->create([
            'short_code' => 'multiple',
        ]);

        // First click
        $this->get('/s/multiple');
        
        // Second click
        $this->get('/s/multiple');
        
        // Third click
        $this->get('/s/multiple');

        $this->assertEquals(3, $url->analytics()->count());
        
        $url->refresh();
        $this->assertEquals(3, $url->click_count);
    }

    public function test_analytics_show_displays_url_statistics(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        
        $url = ShortenedUrl::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create some analytics data
        UrlAnalytic::factory()->count(5)->create([
            'shortened_url_id' => $url->id,
            'clicked_at' => now()->subDays(1),
        ]);

        $response = $this->actingAs($user)->get(route('analytics.show', $url));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Analytics/Show')
                ->has('url')
                ->has('analytics')
                ->where('total_clicks', 5)
        );
    }
}
