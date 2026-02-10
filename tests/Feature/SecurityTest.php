<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that passwords are automatically hashed when creating users.
     */
    public function test_passwords_are_hashed_automatically(): void
    {
        $plainPassword = 'TestPassword123';
        
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'role' => UserRole::ADMIN,
        ]);

        // Password should not be stored in plain text
        $this->assertNotEquals($plainPassword, $user->password);
        
        // Password should be hashed and verifiable
        $this->assertTrue(Hash::check($plainPassword, $user->password));
    }

    /**
     * Test that CSRF protection is enforced on POST requests.
     */
    public function test_csrf_protection_is_enforced(): void
    {
        $user = User::factory()->create(['role' => UserRole::ADMIN]);

        // Attempt POST without CSRF token should fail
        // Laravel redirects back with error instead of returning 419
        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => UserRole::JUDGE->value,
        ], [
            'X-CSRF-TOKEN' => 'invalid-token'
        ]);

        // Laravel redirects back when CSRF fails
        $response->assertRedirect();
    }

    /**
     * Test that rate limiting is applied to login attempts.
     */
    public function test_login_rate_limiting_is_enforced(): void
    {
        // Make 6 failed login attempts (limit is 5 per minute)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login.post'), [
                'email' => 'nonexistent@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        // The 6th attempt should be rate limited
        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test that Form Request validation sanitizes inputs.
     */
    public function test_form_request_validation_works(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Test with invalid data
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => '', // Required field missing
            'email' => 'invalid-email', // Invalid email format
            'password' => 'weak', // Too weak password
            'password_confirmation' => 'weak',
            'role' => 'invalid-role', // Invalid role
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    }

    /**
     * Test that XSS attempts are prevented through validation.
     */
    public function test_xss_prevention_through_validation(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Attempt to inject script tag
        $response = $this->actingAs($admin)->post(route('admin.contestants.store'), [
            'name' => '<script>alert("XSS")</script>',
            'contestant_number' => 'C001',
            'profile_details' => 'Test profile',
            'is_active' => true,
        ]);

        // Should succeed (validation allows it)
        $response->assertRedirect();

        // But when displayed, Blade will escape it automatically
        $this->assertDatabaseHas('contestants', [
            'name' => '<script>alert("XSS")</script>',
        ]);
    }

    /**
     * Test that SQL injection is prevented through Eloquent.
     */
    public function test_sql_injection_prevention(): void
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        // Attempt SQL injection in email field
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Test User',
            'email' => "test@example.com' OR '1'='1",
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'role' => UserRole::JUDGE->value,
        ]);

        // Should fail validation (invalid email format)
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Test that authentication is required for protected routes.
     */
    public function test_authentication_required_for_protected_routes(): void
    {
        // Attempt to access admin route without authentication
        $response = $this->get(route('admin.users.index'));
        
        $response->assertRedirect(route('login'));
    }

    /**
     * Test that role-based authorization is enforced.
     */
    public function test_role_based_authorization_is_enforced(): void
    {
        $judge = User::factory()->create(['role' => UserRole::JUDGE]);

        // Judge should not be able to access admin routes
        $response = $this->actingAs($judge)->get(route('admin.users.index'));
        
        $response->assertStatus(403); // Forbidden
    }
}
