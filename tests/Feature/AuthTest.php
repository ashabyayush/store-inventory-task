<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_login_redirects_to_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

        public function test_logout_ends_session_and_dashboard_needs_login_again(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');

        $this->assertGuest();
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_pages_are_sent_with_no_store_header(): void
    {
        $response = $this->get('/login');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
    }
}