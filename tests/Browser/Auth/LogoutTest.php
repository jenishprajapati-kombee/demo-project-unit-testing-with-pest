<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Test successful logout from the application.
 */
class LogoutTest extends DuskTestCase
{
    /**
     * Test the complete user logout flow:login with valid credentials, open the user profile dropdown, click 'Sign Out', and verify redirection to the login page.
     */
    public function test_user_can_logout_successfully(): void
    {
        // Create a dedicated admin user for the test
        $user = User::factory()->create([
            'role_id' => 1, // Admin Role
            'status' => config('constants.user.status.key.active'),
            'password' => bcrypt('123456'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // 1. Login
            $browser->visit('/')
                ->pause(5000)
                ->waitFor('#email', 20)
                ->typeSlowly('#email', $user->email)
                ->typeSlowly('[data-testid="password"]', '123456')
                ->press('#login-button')
                ->waitForLocation('/dashboard', 20);

            // 2. Open User Profile dropdown
            $browser->waitFor('[data-testid="user_profile_dropdown"]', 20)
                ->click('[data-testid="user_profile_dropdown"]')
                ->pause(5000); // Wait for dropdown animation

            // 3. Click Sign Out
            $browser->waitFor('[data-testid="side_menu_logout"]', 15)
                ->assertSee(__('messages.side_menus.label_logout'))
                ->click('[data-testid="side_menu_logout"]')
                ->pause(5000); // Wait for logout processing and redirection

            // 4. Verify redirected to login page
            $browser->waitForLocation('/', 20)
                ->assertPathIs('/')
                ->assertSee('Enter your email and password below to log in');
        });
    }
}
