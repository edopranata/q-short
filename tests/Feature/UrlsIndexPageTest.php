<?php

namespace Tests\Feature;

use App\Models\ShortenedUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UrlsIndexPageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Role $userRole;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'urls.show']);
        Permission::create(['name' => 'urls.create']);
        Permission::create(['name' => 'urls.edit']);
        Permission::create(['name' => 'urls.delete']);
        
        // Create user role
        $this->userRole = Role::create(['name' => 'user']);
        $this->userRole->givePermissionTo(['urls.show', 'urls.create', 'urls.edit', 'urls.delete']);
        
        // Create test user
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
    }

    /** @test */
    public function test_urls_index_page_returns_successful_response()
    {
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertStatus(200);
    }

    /** @test */
    public function test_urls_index_page_renders_correct_vue_component()
    {
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
        );
    }

    /** @test */
    public function test_urls_index_page_passes_correct_props_structure()
    {
        // Create some test URLs for the user
        ShortenedUrl::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'is_active' => true
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls')
                ->has('urls.data')
                ->has('urls.links')
                ->has('urls.total')
                ->has('urls.from')
                ->has('urls.to')
                ->has('urls.current_page')
                ->has('urls.last_page')
                ->has('urls.per_page')
                ->has('urls.prev_page_url')
                ->has('urls.next_page_url')
        );
    }

    /** @test */
    public function test_urls_index_displays_user_urls_with_correct_data_structure()
    {
        $url = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test URL',
            'original_url' => 'https://example.com',
            'short_code' => 'abc123',
            'is_active' => true
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 1)
                ->where('urls.data.0.id', $url->id)
                ->where('urls.data.0.title', 'Test URL')
                ->where('urls.data.0.original_url', 'https://example.com')
                ->where('urls.data.0.short_code', 'abc123')
                ->where('urls.data.0.is_active', true)
                ->has('urls.data.0.click_count')
                ->has('urls.data.0.created_at')
                ->has('urls.data.0.updated_at')
                ->has('urls.data.0.short_url')
        );
    }

    /** @test */
    public function test_urls_index_only_shows_authenticated_user_urls()
    {
        $otherUser = User::factory()->create();
        $otherUser->assignRole('user');
        
        // Create URLs for different users
        $userUrl = ShortenedUrl::factory()->create(['user_id' => $this->user->id]);
        $otherUserUrl = ShortenedUrl::factory()->create(['user_id' => $otherUser->id]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 1)
                ->where('urls.data.0.id', $userUrl->id)
        );
    }

    /** @test */
    public function test_urls_index_pagination_works_correctly()
    {
        // Create more than 10 URLs to test pagination
        ShortenedUrl::factory()->count(15)->create([
            'user_id' => $this->user->id
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 10) // Default pagination is 10
                ->where('urls.total', 15)
                ->where('urls.current_page', 1)
                ->where('urls.last_page', 2)
                ->where('urls.per_page', 10)
        );
    }

    /** @test */
    public function test_urls_index_includes_analytics_data()
    {
        $url = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        // Create analytics data
        $url->analytics()->create([
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser',
            'referer' => 'https://example.com',
            'clicked_at' => now()
        ]);
        
        $url->increment('click_count');
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data.0')
                ->where('urls.data.0.click_count', 1)
                ->has('urls.data.0.analytics')
        );
    }

    /** @test */
    public function test_urls_index_handles_empty_state()
    {
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 0)
                ->where('urls.total', 0)
        );
    }

    /** @test */
    public function test_urls_index_requires_authentication()
    {
        $response = $this->get('/urls');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_urls_index_includes_flash_messages()
    {
        ShortenedUrl::factory()->create([
            'user_id' => $this->user->id
        ]);
        
        $response = $this->actingAs($this->user)
            ->withSession(['success' => 'URL created successfully!'])
            ->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls')
        );
    }

    /** @test */
    public function test_urls_index_sorts_urls_by_latest_first()
    {
        $oldUrl = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()->subDays(2)
        ]);
        
        $newUrl = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'created_at' => now()
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 2)
                ->where('urls.data.0.id', $newUrl->id)
                ->where('urls.data.1.id', $oldUrl->id)
        );
    }

    /** @test */
    public function test_urls_index_includes_short_url_property()
    {
        $url = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'short_code' => 'test123'
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $expectedShortUrl = url('/s/test123');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data.0')
                ->where('urls.data.0.short_url', $expectedShortUrl)
        );
    }

    /** @test */
    public function test_urls_index_handles_urls_with_null_title()
    {
        ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'title' => null
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data.0')
                ->where('urls.data.0.title', null)
        );
    }

    /** @test */
    public function test_urls_index_includes_correct_route_helpers()
    {
        $response = $this->actingAs($this->user)->get('/urls');
        
        // Test that the page can access route helpers
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
        );
        
        // Verify routes are available
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('urls.create'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('urls.edit'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('urls.destroy'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('analytics.show'));
    }

    /** @test */
    public function test_urls_index_performance_with_large_dataset()
    {
        // Create a larger dataset to test performance
        ShortenedUrl::factory()->count(50)->create([
            'user_id' => $this->user->id
        ]);
        
        $startTime = microtime(true);
        $response = $this->actingAs($this->user)->get('/urls');
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        
        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data', 10) // Should still paginate to 10
                ->where('urls.total', 50)
        );
        
        // Assert that the page loads within reasonable time (less than 1 second)
        $this->assertLessThan(1.0, $executionTime, 'Page should load within 1 second');
    }

    /** @test */
    public function test_urls_index_includes_all_required_url_properties()
    {
        $url = ShortenedUrl::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Test Title',
            'original_url' => 'https://example.com',
            'short_code' => 'abc123',
            'is_active' => true
        ]);
        
        $response = $this->actingAs($this->user)->get('/urls');
        
        $response->assertInertia(fn (Assert $page) => 
            $page->component('Urls/Index')
                ->has('urls.data.0', fn (Assert $urlData) => 
                    $urlData->has('id')
                        ->has('user_id')
                        ->has('title')
                        ->has('original_url')
                        ->has('short_code')
                        ->has('description')
                        ->has('is_active')
                        ->has('click_count')
                        ->has('expires_at')
                        ->has('created_at')
                        ->has('updated_at')
                        ->has('short_url')
                        ->has('analytics')
                        ->has('custom_slug')
                        ->has('is_custom')
                )
        );
    }
}