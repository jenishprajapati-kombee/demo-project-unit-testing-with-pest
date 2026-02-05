<?php

namespace Tests\Browser\User;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Laravel Dusk Test Case for User Module
 */
class UserTestCase extends DuskTestCase
{
    /**
     * Test the complete User module.
     */
    public function test_user_module_complete_flow()
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

            // 3. Navigate to User
            $browser->waitFor('[data-label="' . __('messages.side_menu.user') . '"]', 10)
                ->click('[data-testid="side_menu_user"]')
                ->waitForLocation('/user', 20)
                ->waitFor('[data-testid="add_new"]', 10)
                ->pause(1000);

            // ==========================================
            // 4. Test Negative Cases (Validation)
            // ==========================================

            // 4.1 Check Required Fields
            $browser->click('[data-testid="add_new"]')
                ->waitForLocation('/user/create', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000)
                ->script([
                    "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
                ]);

            $browser->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.name.required'), 10)
                ->assertSee(__('messages.user.validation.messsage.email.required'))
                ->assertSee(__('messages.user.validation.messsage.password.required'))
                ->assertSee(__('messages.user.validation.messsage.role_id.required'))
                ->assertSee(__('messages.user.validation.messsage.dob.required'))
                ->assertSee(__('messages.user.validation.messsage.profile.required'))
                ->assertSee(__('messages.user.validation.messsage.country_id.required'))
                ->assertSee(__('messages.user.validation.messsage.state_id.required'))
                ->assertSee(__('messages.user.validation.messsage.city_id.required'))
                ->assertSee(__('messages.user.validation.messsage.gender.required'))
                ->assertSee(__('messages.user.validation.messsage.status.required'))
                ->pause(2000);

            // 4.2 Check Max Length & Other Validations

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"name\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"name\"]').removeAttribute('required');");
            $browser->clear('[data-testid="name"]')
                ->typeSlowly('[data-testid="name"]', \Illuminate\Support\Str::random(100 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.name.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"email\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"email\"]').removeAttribute('required');");
            $browser->clear('[data-testid="email"]')
                ->typeSlowly('[data-testid="email"]', \Illuminate\Support\Str::random(200 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.email.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->clear('[data-testid="email"]')
                ->typeSlowly('[data-testid="email"]', 'invalid-email')
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.email.email'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"password\"]').removeAttribute('minlength');");
            $browser->script("document.querySelector('[data-testid=\"password\"]').removeAttribute('required');");
            $browser->clear('[data-testid="password"]')
                ->typeSlowly('[data-testid="password"]', \Illuminate\Support\Str::random(6 - 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.password.min'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"password\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"password\"]').removeAttribute('required');");
            $browser->clear('[data-testid="password"]')
                ->typeSlowly('[data-testid="password"]', \Illuminate\Support\Str::random(191 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.password.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->clear('[data-testid="dob"]')
                ->typeSlowly('[data-testid="dob"]', 'invalid-date')
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.user.validation.messsage.dob.date_format'), 10)
                ->pause(1000);

            $role = \App\Models\Role::inRandomOrder()->first();
            $roleId = $role?->id;
            $country = \App\Models\Country::inRandomOrder()->first();
            $countryId = $country?->id;
            $state = \App\Models\State::where('country_id', $country->id)->inRandomOrder()->first();
            $stateId = $state?->id;
            $city = \App\Models\City::where('state_id', $state->id)->inRandomOrder()->first();
            $cityId = $city?->id;
            $name = strtoupper(fake()->unique()->bothify('???????????????'));
            $email = fake()->unique()->safeEmail();
            $password = '123456';
            $roleId = $role?->id;
            $dob = fake()->date();
            $profile = base_path('tests/fixtures/dummy.png');
            $countryId = $country?->id;
            $stateId = $state?->id;
            $cityId = $city?->id;
            $gender = fake()->randomElement(['F', 'M']);
            $status = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->typeSlowly('[data-testid="name"]', $name)
                ->pause(500)
                ->typeSlowly('[data-testid="email"]', $email)
                ->pause(500)
                ->typeSlowly('[data-testid="password"]', $password)
                ->pause(500)
                ->typeSlowly('[data-testid="role_id_search"]', $roleId)
                ->pause(2000)
                ->keys('[data-testid="role_id_search"]', '{arrow_down}')
                ->pause(500)
                ->keys('[data-testid="role_id_search"]', '{enter}')
                ->pause(1000)
                ->typeSlowly('[data-testid="dob"]', $dob)
                ->pause(500)
                ->attach('[data-testid="profile"]', $profile)
                ->pause(1000)
                ->select('[data-testid="country_id"]', $countryId)
                ->pause(1000)
                ->select('[data-testid="state_id"]', $stateId)
                ->pause(1000)
                ->select('[data-testid="city_id"]', $cityId)
                ->pause(1000)
                ->click('input[data-testid="gender"][value="' . $gender . '"]')
                ->pause(500)
                ->click('input[data-testid="status"][value="' . $status . '"]')
                ->pause(500);

            // Submit
            $browser->click('[data-testid="submit_button"]')
                ->waitForLocation('/user', 20)
                ->waitForText(__('messages.user.messages.success'), 10)
                ->pause(2000);

            // 5.2 View User
            // Verify record exists in table
            // deferLoading is enabled, so we must wait for data to appear
            $browser->click('[data-testid="view_button"]')
                ->waitForText($name, 20)
                ->waitForText('User Details', 20)
                ->pause(1000)
                ->click('[data-testid="close_modal"]');

            $browser->pause(1000); // Wait for modal close

            // 5.3 Edit User
            $browser->click('[data-testid="edit_button"]')
                ->waitForLocation('/user/' . User::latest()->first()->id . '/edit', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000);

            $updatedrole = \App\Models\Role::inRandomOrder()->first();
            $updatedroleId = $updatedrole?->id;
            $updatedcountry = \App\Models\Country::inRandomOrder()->first();
            $updatedcountryId = $updatedcountry?->id;
            $updatedstate = \App\Models\State::where('country_id', $updatedcountry->id)->inRandomOrder()->first();
            $updatedstateId = $updatedstate?->id;
            $updatedcity = \App\Models\City::where('state_id', $updatedstate->id)->inRandomOrder()->first();
            $updatedcityId = $updatedcity?->id;
            $updatedname = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedemail = fake()->unique()->safeEmail();
            $updatedpassword = '123456';
            $updatedroleId = $updatedrole?->id;
            $updateddob = fake()->date();
            $updatedprofile = base_path('tests/fixtures/dummy.png');
            $updatedcountryId = $updatedcountry?->id;
            $updatedstateId = $updatedstate?->id;
            $updatedcityId = $updatedcity?->id;
            $updatedgender = fake()->randomElement(['F', 'M']);
            $updatedstatus = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->clear('[data-testid="name"]')
                ->pause(500)->typeSlowly('[data-testid="name"]', $updatedname)
                ->pause(500)
                ->clear('[data-testid="email"]')
                ->pause(500)->typeSlowly('[data-testid="email"]', $updatedemail)
                ->pause(500)
                ->clear('[data-testid="password"]')
                ->pause(500)->typeSlowly('[data-testid="password"]', $updatedpassword)
                ->pause(500)
                ->clear('[data-testid="role_id_search"]')
                ->typeSlowly('[data-testid="role_id_search"]', $updatedroleId)
                ->pause(2000)
                ->keys('[data-testid="role_id_search"]', '{arrow_down}')
                ->pause(500)
                ->keys('[data-testid="role_id_search"]', '{enter}')
                ->pause(1000)
                ->clear('[data-testid="dob"]')
                ->pause(500)->typeSlowly('[data-testid="dob"]', $updateddob)
                ->pause(500)
                ->attach('[data-testid="profile"]', $updatedprofile)
                ->pause(1000)
                ->select('[data-testid="country_id"]', $updatedcountryId)
                ->pause(1000)
                ->select('[data-testid="state_id"]', $updatedstateId)
                ->pause(1000)
                ->select('[data-testid="city_id"]', $updatedcityId)
                ->pause(1000)
                ->click('input[data-testid="gender"][value="' . $updatedgender . '"]')
                ->pause(500)
                ->click('input[data-testid="status"][value="' . $updatedstatus . '"]')
                ->pause(500)
                ->click('[data-testid="submit_button"]')
                ->waitForLocation('/user', 20)
                ->waitForText(__('messages.user.messages.update'), 10)
                ->pause(2000);

            // 5.4 Delete User
            $browser->waitForText($updatedname)
                ->pause(2000)
                ->click('[data-testid="delete_button"]')
                ->waitForText('Delete Record', 10)
                ->pause(1000)
                ->click('[data-testid="delete-button"]')
                ->waitForText(__('messages.user.messages.delete'), 10)
                ->assertDontSee($updatedname)
                ->pause(3000);
        });
    }
}
