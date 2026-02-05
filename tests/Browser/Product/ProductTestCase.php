<?php

namespace Tests\Browser\Product;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * Laravel Dusk Test Case for Product Module
 */
class ProductTestCase extends DuskTestCase
{
    /**
     * Test the complete Product module.
     */
    public function test_product_module_complete_flow()
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

            // 3. Navigate to Product
            $browser->waitFor('[data-label="' . __('messages.side_menu.product') . '"]', 10)
                ->click('[data-testid="side_menu_product"]')
                ->waitForLocation('/product', 20)
                ->waitFor('[data-testid="add_new"]', 10)
                ->pause(1000);

            // ==========================================
            // 4. Test Negative Cases (Validation)
            // ==========================================

            // 4.1 Check Required Fields
            $browser->click('[data-testid="add_new"]')
                ->waitForLocation('/product/create', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000)
                ->script([
                    "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
                ]);

            $browser->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.product.validation.messsage.name.required'), 10)
                ->assertSee(__('messages.product.validation.messsage.description.required'))
                ->assertSee(__('messages.product.validation.messsage.code.required'))
                ->assertSee(__('messages.product.validation.messsage.price.required'))
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
                ->waitForText(__('messages.product.validation.messsage.name.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"description\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"description\"]').removeAttribute('required');");
            $browser->clear('[data-testid="description"]')
                ->typeSlowly('[data-testid="description"]', \Illuminate\Support\Str::random(500 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.product.validation.messsage.description.max'), 10)
                ->pause(1000);

            $browser->script([
                "document.querySelectorAll('[required]').forEach(el => el.removeAttribute('required'));",
            ]);
            $browser->script("document.querySelector('[data-testid=\"code\"]').removeAttribute('maxlength');");
            $browser->script("document.querySelector('[data-testid=\"code\"]').removeAttribute('required');");
            $browser->clear('[data-testid="code"]')
                ->typeSlowly('[data-testid="code"]', \Illuminate\Support\Str::random(20 + 1))
                ->click('[data-testid="submit_button"]')
                ->waitForText(__('messages.product.validation.messsage.code.max'), 10)
                ->pause(1000);

            $name = strtoupper(fake()->unique()->bothify('???????????????'));
            $description = strtoupper(fake()->unique()->bothify('???????????????'));
            $code = strtoupper(fake()->unique()->bothify('???????????????'));
            $price = fake()->numberBetween(1, 100);

            $browser->pause(500)
                ->typeSlowly('[data-testid="name"]', $name)
                ->pause(500)
                ->typeSlowly('[data-testid="description"]', $description)
                ->pause(500)
                ->typeSlowly('[data-testid="code"]', $code)
                ->pause(500)
                ->typeSlowly('[data-testid="price"]', $price)
                ->pause(500);

            // Submit
            $browser->click('[data-testid="submit_button"]')
                ->waitForLocation('/product', 20)
                ->waitForText(__('messages.product.messages.success'), 10)
                ->pause(2000);

            // 5.2 View Product
            // Verify record exists in table
            // deferLoading is enabled, so we must wait for data to appear
            $browser->click('[data-testid="view_button"]')
                ->waitForText($name, 20)
                ->waitForText('Product Details', 20)
                ->pause(1000)
                ->click('[data-testid="close_modal"]');

            $browser->pause(1000); // Wait for modal close

            // 5.3 Edit Product
            $browser->click('[data-testid="edit_button"]')
                ->waitForLocation('/product/' . \App\Models\Product::latest()->first()->id . '/edit', 20)
                ->waitFor('[data-testid="submit_button"]', 10)
                ->pause(1000);

            $updatedname = strtoupper(fake()->unique()->bothify('???????????????'));
            $updateddescription = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedcode = strtoupper(fake()->unique()->bothify('???????????????'));
            $updatedprice = fake()->numberBetween(1, 100);

            $browser->pause(500)
                ->clear('[data-testid="name"]')
                ->pause(500)->typeSlowly('[data-testid="name"]', $updatedname)
                ->pause(500)
                ->clear('[data-testid="description"]')
                ->pause(500)->typeSlowly('[data-testid="description"]', $updateddescription)
                ->pause(500)
                ->clear('[data-testid="code"]')
                ->pause(500)->typeSlowly('[data-testid="code"]', $updatedcode)
                ->pause(500)
                ->clear('[data-testid="price"]')
                ->pause(500)->typeSlowly('[data-testid="price"]', $updatedprice)
                ->pause(500)
                ->click('[data-testid="submit_button"]')
                ->waitForLocation('/product', 20)
                ->waitForText(__('messages.product.messages.update'), 10)
                ->pause(2000);

            // 5.4 Delete Product
            $browser->waitForText($updatedname)
                ->pause(2000)
                ->click('[data-testid="delete_button"]')
                ->waitForText('Delete Record', 10)
                ->pause(1000)
                ->click('[data-testid="delete-button"]')
                ->waitForText(__('messages.product.messages.delete'), 10)
                ->assertDontSee($updatedname)
                ->pause(3000);
        });
    }
}
