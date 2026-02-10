<?php

use App\Models\Brand;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    // Mock the permission check
    Gate::define('add-brand', fn($user) => true);
});

it('renders the create brand component', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->assertStatus(Response::HTTP_OK)
        ->assertSee(__('messages.submit_button_text'));
});

it('authorizes the component', function () {
    Gate::define('add-brand', fn($user) => false);

    Livewire::test(App\Livewire\Brand\Create::class)
        ->assertStatus(Response::HTTP_FORBIDDEN);
});

it('dispatches breadcrumb event on mount', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->assertDispatched('breadcrumbList');
});

it('loads initial data on mount', function () {
    Country::factory()->count(3)->create();
    State::factory()->count(3)->create();
    City::factory()->count(3)->create();

    Livewire::test(App\Livewire\Brand\Create::class)
        ->assertSet('countries', function ($countries) {
            return count($countries) >= 3;
        })
        ->assertSet('states', function ($states) {
            return count($states) >= 3;
        })
        ->assertSet('cities', function ($cities) {
            return count($cities) >= 3;
        });
});

it('validates required fields', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->call('store')
        ->assertHasErrors([
            'name' => 'required',
            'bob' => 'required',
            'description' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'status' => 'required',
        ]);
});

it('validates name max length', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('name', str_repeat('a', 192))
        ->call('store')
        ->assertHasErrors(['name' => 'max']);
});

it('validates description max length', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('description', str_repeat('a', 501))
        ->call('store')
        ->assertHasErrors(['description' => 'max']);
});

it('validates bob date format', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('bob', '2023-10-10') // Missing H:i:s
        ->call('store')
        ->assertHasErrors(['bob' => 'date_format']);
});

it('validates status in list', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('status', 'X')
        ->call('store')
        ->assertHasErrors(['status' => 'in']);
});

it('validates existence of country state and city', function () {
    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('country_id', 999)
        ->set('state_id', 999)
        ->set('city_id', 999)
        ->call('store')
        ->assertHasErrors([
            'country_id' => 'exists',
            'state_id' => 'exists',
            'city_id' => 'exists',
        ]);
});

it('creates a new brand successfully', function () {
    $country = Country::factory()->create();
    $state = State::factory()->create();
    $city = City::factory()->create();

    $data = [
        'name' => 'Test Brand',
        'remark' => 'Test Remark',
        'bob' => '2024-02-10 10:00:00',
        'description' => 'Test Description',
        'country_id' => $country->id,
        'state_id' => $state->id,
        'city_id' => $city->id,
        'status' => 'Y',
    ];

    Livewire::test(App\Livewire\Brand\Create::class)
        ->set('name', $data['name'])
        ->set('remark', $data['remark'])
        ->set('bob', $data['bob'])
        ->set('description', $data['description'])
        ->set('country_id', $data['country_id'])
        ->set('state_id', $data['state_id'])
        ->set('city_id', $data['city_id'])
        ->set('status', $data['status'])
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect('/brand')
        ->assertSessionHas('success', __('messages.brand.messages.success'));

    $this->assertDatabaseHas('brands', $data);
});
