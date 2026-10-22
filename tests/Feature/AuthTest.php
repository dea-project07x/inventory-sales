<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com', 'password' => 'secret-pass-1']);

        $this->from(route('login'))
            ->post(route('login.attempt'), ['email' => $user->email, 'password' => 'salah-total'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'b@example.com', 'password' => 'secret-pass-1']);

        $this->post(route('login.attempt'), ['email' => $user->email, 'password' => 'secret-pass-1'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_is_stored_hashed(): void
    {
        $user = User::factory()->create(['password' => 'secret-pass-1']);

        $this->assertNotSame('secret-pass-1', $user->fresh()->password);
    }
}
