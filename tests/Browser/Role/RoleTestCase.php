<?php

namespace Tests\Browser\Role;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Laravel Dusk Test Case for Role Module
 */
class RoleTestCase extends DuskTestCase
{
    /**
     * Test the complete Role module.
     */
    public function test_role_module_complete_flow()
    {
        // 1. Setup Test Data
        $user = User::factory()->create([
            'role_id' => 1, // Admin Role
            'status' => config('constants.user.status.key.active'),
            'password' => bcrypt('123456'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            // 2. Login
            $browser->visit('/')
                ->waitFor('#email', 20)
                ->typeSlowly('#email', $user->email)
                ->pause(500)
                ->typeSlowly('[data-testid="password"]', '123456')
                ->pause(500)
                ->press('#login-button')
                ->waitForLocation('/dashboard', 20)
                ->pause(1000);

            // 3. Navigate to Role
            $browser->waitFor('[data-label="' . __('messages.side_menu.role') . '"]', 10)
                ->click('[data-testid="side_menu_role"]')
                ->waitForLocation('/role', 20)
                ->waitFor('[data-testid="add_new"]', 10)
                ->pause(1000);

            // ==========================================
            // 4. Test Negative Cases (Validation)
            // ==========================================

            // 4.1 Check Required Fields
            $browser->click('[data-testid="add_new"]')
                ->waitForLocation('/role/create', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000)
                ->script([
                    "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
                ]);

            $browser->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.role.validation.messsage.name.required'), 10)
                ->assertSee(__('messages.role.validation.messsage.status.required'))
                ->pause(2000);

            // 4.2 Check Max Length & Other Validations

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"name\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"name\"]').removeAttribute('required');");
            $browser->clear('[data-testid="name"]')
                ->typeSlowly('[data-testid="name"]', \Illuminate\Support\Str::random(191 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.role.validation.messsage.name.max'), 10)
                ->pause(1000);

            $name = strtoupper(fake()->unique()->bothify('???????????????'));
            $status = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->typeSlowly('[data-testid="name"]', $name)
                ->pause(500)
                ->click('[data-testid="status"]')->pause(500);

            // Submit
            $browser->click('[data-testid="submit_button"]')
                ->waitForLocation('/role', 20)
                ->waitForText(__('messages.role.messages.success'), 10)
                ->pause(2000);

            // 5.2 View Role
            // Verify record exists in table
            // deferLoading is enabled, so we must wait for data to appear
            $browser->click('[data-testid="view_button"]')
                ->waitForText($name, 20)
                ->waitForText('Role Details', 20)
                ->pause(1000)
                ->click('[data-testid="close_modal"]');

            $browser->pause(1000); // Wait for modal close

            // 5.3 Edit Role
            $browser->click('[data-testid="edit_button"]')
                ->waitForLocation('/role/' . \App\Models\Role::latest()->first()->id . '/edit', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000);

            $updatedname = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedstatus = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->clear('[data-testid="name"]')
                ->pause(500)->typeSlowly('[data-testid="name"]', $updatedname)
                ->pause(500)
                ->click('[data-testid="status"]')->pause(500)
                ->click('[data-testid="submit_button"]')
                ->waitForLocation('/role', 20)
                ->waitForText(__('messages.role.messages.update'), 10)
                ->pause(2000);

            // 5.4 Delete Role
            $browser->waitForText($updatedname)
                ->pause(2000)
                ->click('[data-testid="delete_button"]')
                ->waitForText('Delete Record', 10)
                ->pause(1000)
                ->click('[data-testid="delete-button"]')
                ->waitForText(__('messages.role.messages.delete'), 10)
                ->assertDontSee($updatedname)
                ->pause(3000);
        });
    }
}
