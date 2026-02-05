<?php

namespace Tests\Browser\Brand;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Laravel Dusk Test Case for Brand Module
 */
class BrandTestCase extends DuskTestCase
{
    /**
     * Test the complete Brand module.
     */
    public function test_brand_module_complete_flow()
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

            // 3. Navigate to Brand
            $browser->waitFor('[data-label="' . __('messages.side_menu.brand') . '"]', 10)
                ->click('[data-testid="side_menu_brand"]')
                ->waitForLocation('/brand', 20)
                ->waitFor('[data-testid="add_new"]', 10)
                ->pause(1000);

            // ==========================================
            // 4. Test Negative Cases (Validation)
            // ==========================================

            // 4.1 Check Required Fields
            $browser->click('[data-testid="add_new"]')
                ->waitForLocation('/brand/create', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000)
                ->script([
                    "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
                ]);

            $browser->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.brand.validation.messsage.name.required'), 10)
                ->assertSee(__('messages.brand.validation.messsage.bob.required'))
                ->assertSee(__('messages.brand.validation.messsage.description.required'))
                ->assertSee(__('messages.brand.validation.messsage.country_id.required'))
                ->assertSee(__('messages.brand.validation.messsage.state_id.required'))
                ->assertSee(__('messages.brand.validation.messsage.city_id.required'))
                ->assertSee(__('messages.brand.validation.messsage.status.required'))
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
                ->waitForText(__('messages.brand.validation.messsage.name.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->clear('[data-testid="bob"]')
                ->typeSlowly('[data-testid="bob"]', 'invalid-date')
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.brand.validation.messsage.bob.date_format'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"description\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"description\"]').removeAttribute('required');");
            $browser->clear('[data-testid="description"]')
                ->typeSlowly('[data-testid="description"]', \Illuminate\Support\Str::random(500 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.brand.validation.messsage.description.max'), 10)
                ->pause(1000);

            $country = \App\Models\Country::inRandomOrder()->first();
            $countryId = $country?->id;
            $state = \App\Models\State::inRandomOrder()->first();
            $stateId = $state?->id;
            $city = \App\Models\City::inRandomOrder()->first();
            $cityId = $city?->id;
            $name = strtoupper(fake()->unique()->bothify('???????????????'));
            $remark = ucwords(fake()->unique()->words(3, true));
            $bob = fake()->word();
            $description = strtoupper(fake()->unique()->bothify('???????????????'));
            $countryId = $country?->id;
            $stateId = $state?->id;
            $cityId = $city?->id;
            $status = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->typeSlowly('[data-testid="name"]', $name)
                ->pause(500)
                ->typeSlowly('[data-testid="remark"]', $remark)
                ->pause(500)
                ->typeSlowly('[data-testid="bob"]', $bob)
                ->pause(500)
                ->typeSlowly('[data-testid="description"]', $description)
                ->pause(500)
                ->select('[data-testid="country_id"]', $countryId)
                ->pause(1000)
                ->select('[data-testid="state_id"]', $stateId)
                ->pause(1000)
                ->select('[data-testid="city_id"]', $cityId)
                ->pause(1000)
                ->click('input[data-testid="status"][value="' . $status . '"]')
                ->pause(500);

            // Submit
            $browser->click('[data-testid="submit_button"]')
                ->waitForLocation('/brand', 20)
                ->waitForText(__('messages.brand.messages.success'), 10)
                ->pause(2000);

            // 5.2 View Brand
            // Verify record exists in table
            // deferLoading is enabled, so we must wait for data to appear
            $browser->click('[data-testid="view_button"]')
                ->waitForText($name, 20)
                ->waitForText('Brand Details', 20)
                ->pause(1000)
                ->click('[data-testid="close_modal"]');

            $browser->pause(1000); // Wait for modal close

            // 5.3 Edit Brand
            $browser->click('[data-testid="edit_button"]')
                ->waitForLocation('/brand/' . \App\Models\Brand::latest()->first()->id . '/edit', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000);

            $updatedcountry = \App\Models\Country::inRandomOrder()->first();
            $updatedcountryId = $updatedcountry?->id;
            $updatedstate = \App\Models\State::inRandomOrder()->first();
            $updatedstateId = $updatedstate?->id;
            $updatedcity = \App\Models\City::inRandomOrder()->first();
            $updatedcityId = $updatedcity?->id;
            $updatedname = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedremark = ucwords(fake()->unique()->words(3, true));
            $updatedbob = fake()->word();
            $updateddescription = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedcountryId = $updatedcountry?->id;
            $updatedstateId = $updatedstate?->id;
            $updatedcityId = $updatedcity?->id;
            $updatedstatus = fake()->randomElement(['Y', 'N']);

            $browser->pause(500)
                ->clear('[data-testid="name"]')
                ->pause(500)->typeSlowly('[data-testid="name"]', $updatedname)
                ->pause(500)
                ->clear('[data-testid="remark"]')
                ->pause(500)->typeSlowly('[data-testid="remark"]', $updatedremark)
                ->pause(500)
                ->clear('[data-testid="bob"]')
                ->pause(500)->typeSlowly('[data-testid="bob"]', $updatedbob)
                ->pause(500)
                ->clear('[data-testid="description"]')
                ->pause(500)->typeSlowly('[data-testid="description"]', $updateddescription)
                ->pause(500)
                ->select('[data-testid="country_id"]', $updatedcountryId)
                ->pause(1000)
                ->select('[data-testid="state_id"]', $updatedstateId)
                ->pause(1000)
                ->select('[data-testid="city_id"]', $updatedcityId)
                ->pause(1000)
                ->click('input[data-testid="status"][value="' . $updatedstatus . '"]')
                ->pause(500)
                ->click('[data-testid="submit_button"]')
                ->waitForLocation('/brand', 20)
                ->waitForText(__('messages.brand.messages.update'), 10)
                ->pause(2000);

            // 5.4 Delete Brand
            $browser->waitForText($updatedname)
                ->pause(2000)
                ->click('[data-testid="delete_button"]')
                ->waitForText('Delete Record', 10)
                ->pause(1000)
                ->click('[data-testid="delete-button"]')
                ->waitForText(__('messages.brand.messages.delete'), 10)
                ->assertDontSee($updatedname)
                ->pause(3000);
        });
    }
}
