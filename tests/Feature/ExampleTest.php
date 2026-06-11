<?php

use App\Models\AvailableDate;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

test('the public landing page returns a successful response', function () {
    MenuItem::create([
        'name' => 'Classic Chocolate Fudge',
        'category' => 'Best Seller',
        'description' => 'Rich chocolate sponge layered with silky fudge cream.',
        'price' => 35,
        'serves' => '6-8 pax',
        'is_active' => true,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Browse Menu');
});

test('guests can browse the catalog', function () {
    MenuItem::create([
        'name' => 'Strawberry Shortcake',
        'category' => 'Fresh Fruit',
        'description' => 'Light vanilla sponge with whipped cream.',
        'price' => 40,
        'serves' => '8-10 pax',
        'is_active' => true,
    ]);

    $response = $this->get('/catalog');

    $response->assertStatus(200);
    $response->assertSee('Choose your perfect cake');
    $response->assertSee('Login to Order');
});

test('guests must login before ordering or viewing history', function () {
    $this->get('/order')->assertRedirect('/login');
    $this->get('/history')->assertRedirect('/login');
});

test('admin login uses username and only accepts admin accounts', function () {
    $admin = User::factory()->create([
        'name' => 'Admin User',
        'username' => 'admin',
        'role' => 'admin',
        'phone' => '011-11111111',
        'password' => 'password123',
    ]);

    User::factory()->create([
        'name' => 'Customer User',
        'username' => 'customer-name',
        'role' => 'customer',
        'phone' => '012-3333333',
        'password' => 'password123',
    ]);

    $this->post('/admin/login', [
        'username' => 'customer-name',
        'password' => 'password123',
    ])->assertSessionHasErrors('username');

    $this->post('/admin/login', [
        'username' => $admin->username,
        'password' => 'password123',
    ])->assertRedirect('/admin/orders');
});

test('customers order from the catalog modal and can view history after login', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'phone' => '012-2222222',
    ]);

    MenuItem::create([
        'name' => 'Vanilla Bean Dream',
        'category' => 'Classic',
        'description' => 'Soft vanilla bean cake finished with buttercream.',
        'price' => 30,
        'serves' => '6-8 pax',
        'is_active' => true,
    ]);

    AvailableDate::create([
        'date' => today()->addDay()->toDateString(),
        'is_available' => true,
    ]);

    $this->actingAs($customer)->get('/catalog')
        ->assertStatus(200)
        ->assertSee('Place Order')
        ->assertSee('Submit Order');

    $this->actingAs($customer)->get('/order')
        ->assertRedirect('/catalog');

    $this->actingAs($customer)->get('/history')
        ->assertStatus(200)
        ->assertSee('Purchase History');
});

test('customers can submit orders with sqlite timestamp-style available dates', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'phone' => '012-4444444',
    ]);

    $menuItem = MenuItem::create([
        'name' => 'Chocolate Celebration',
        'category' => 'Best Seller',
        'description' => 'Chocolate cake with fudge cream.',
        'price' => 45,
        'serves' => '8-10 pax',
        'is_active' => true,
    ]);

    $date = today()->addDays(3)->toDateString();

    DB::table('available_dates')->insert([
        'date' => $date.' 00:00:00',
        'is_available' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($customer)->post('/order', [
        'menu_item_id' => $menuItem->id,
        'delivery_date' => $date,
        'frosting' => 'Vanilla Buttercream',
        'address' => '123 Cake Street',
    ])->assertRedirect('/history');

    expect(Order::where('user_id', $customer->id)->count())->toBe(1);
});

test('customers can edit and cancel pending orders from history', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'phone' => '012-5555555',
    ]);

    $menuItem = MenuItem::create([
        'name' => 'Editable Chocolate Cake',
        'category' => 'Best Seller',
        'description' => 'Chocolate cake with ganache.',
        'price' => 38,
        'serves' => '6-8 pax',
        'is_active' => true,
    ]);

    $firstDate = today()->addDay()->toDateString();
    $secondDate = today()->addDays(2)->toDateString();

    AvailableDate::create(['date' => $firstDate, 'is_available' => true]);
    AvailableDate::create(['date' => $secondDate, 'is_available' => true]);

    $order = Order::create([
        'order_number' => 'SD-TEST-EDIT',
        'user_id' => $customer->id,
        'menu_item_id' => $menuItem->id,
        'cake_name' => $menuItem->name,
        'cake_price' => $menuItem->price,
        'delivery_date' => $firstDate,
        'frosting' => 'Vanilla Buttercream',
        'toppings' => [],
        'phone' => $customer->phone,
        'address' => 'Old Address',
        'status' => Order::STATUS_PENDING,
    ]);

    $this->actingAs($customer)->get('/history')
        ->assertStatus(200)
        ->assertSee('Edit Details')
        ->assertSee('Cancel Order');

    $this->actingAs($customer)->put("/orders/{$order->id}", [
        'delivery_date' => $secondDate,
        'frosting' => 'Chocolate Ganache',
        'toppings' => ['Mango'],
        'address' => 'New Address',
    ])->assertRedirect('/history');

    $order->refresh();
    expect($order->delivery_date->toDateString())->toBe($secondDate);
    expect($order->frosting)->toBe('Chocolate Ganache');
    expect($order->toppings)->toBe(['Mango']);
    expect($order->address)->toBe('New Address');

    $this->actingAs($customer)->patch("/orders/{$order->id}/cancel")
        ->assertRedirect('/history');

    expect($order->refresh()->status)->toBe(Order::STATUS_CANCELLED);
});

test('customers cannot edit or cancel confirmed orders', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
        'phone' => '012-6666666',
    ]);

    $menuItem = MenuItem::create([
        'name' => 'Locked Vanilla Cake',
        'category' => 'Classic',
        'description' => 'Vanilla cake with buttercream.',
        'price' => 32,
        'serves' => '6-8 pax',
        'is_active' => true,
    ]);

    $date = today()->addDay()->toDateString();
    AvailableDate::create(['date' => $date, 'is_available' => true]);

    $order = Order::create([
        'order_number' => 'SD-TEST-LOCK',
        'user_id' => $customer->id,
        'menu_item_id' => $menuItem->id,
        'cake_name' => $menuItem->name,
        'cake_price' => $menuItem->price,
        'delivery_date' => $date,
        'frosting' => 'Vanilla Buttercream',
        'toppings' => [],
        'phone' => $customer->phone,
        'address' => 'Locked Address',
        'status' => Order::STATUS_CONFIRMED,
    ]);

    $this->actingAs($customer)->put("/orders/{$order->id}", [
        'delivery_date' => $date,
        'frosting' => 'Chocolate Ganache',
        'address' => 'Changed Address',
    ])->assertForbidden();

    $this->actingAs($customer)->patch("/orders/{$order->id}/cancel")
        ->assertForbidden();

    expect($order->refresh()->status)->toBe(Order::STATUS_CONFIRMED);
    expect($order->address)->toBe('Locked Address');
});

test('admins can toggle available dates from the calendar', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'phone' => '011-5555555',
    ]);

    $date = today()->addDays(4)->toDateString();

    $this->actingAs($admin)->post('/admin/dates/toggle', [
        'date' => $date,
    ])->assertRedirect();

    expect(AvailableDate::whereDate('date', $date)->where('is_available', true)->exists())->toBeTrue();

    $this->actingAs($admin)->post('/admin/dates/toggle', [
        'date' => $date,
    ])->assertRedirect();

    expect(AvailableDate::whereDate('date', $date)->where('is_available', false)->exists())->toBeTrue();
});

test('admins can navigate the date calendar forward', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'phone' => '011-6666666',
    ]);

    $this->actingAs($admin)->get('/admin/dates?month=3')
        ->assertStatus(200)
        ->assertSee(today()->startOfMonth()->addMonths(3)->format('F Y'));
});

test('admins can view order, date, and menu management pages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'phone' => '011-11111111',
    ]);

    MenuItem::create([
        'name' => 'Red Velvet Bliss',
        'category' => 'Signature',
        'description' => 'Velvety cocoa cake paired with cream cheese frosting.',
        'price' => 42,
        'serves' => '8-10 pax',
        'is_active' => true,
    ]);

    AvailableDate::create([
        'date' => today()->addDays(2)->toDateString(),
        'is_available' => true,
    ]);

    $this->actingAs($admin)->get('/admin/orders')
        ->assertStatus(200)
        ->assertSee('Manage Orders');

    $this->actingAs($admin)->get('/admin/dates')
        ->assertStatus(200)
        ->assertSee('Select Available Dates');

    $this->actingAs($admin)->get('/admin/menu')
        ->assertStatus(200)
        ->assertSee('Manage Menu')
        ->assertSee('Add Menu Item')
        ->assertSee('Edit Menu Item')
        ->assertSee('Red Velvet Bliss');
});
