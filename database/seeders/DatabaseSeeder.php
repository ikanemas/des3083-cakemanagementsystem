<?php

namespace Database\Seeders;

use App\Models\AvailableDate;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '011-11111111'],
            [
                'name' => 'Aifii Qaseh Admin',
                'username' => 'admin',
                'role' => 'admin',
                'password' => 'password123',
            ]
        );

        $users = [
            [
                'name' => 'test1',
                'phone' => '010-123456789',
                'password' => 'test1abc123',
            ],
            [
                'name' => 'test2',
                'phone' => '011-23456789',
                'password' => 'test2abc123',
            ],
            [
                'name' => 'test3',
                'phone' => '012-34567890',
                'password' => 'test3abc123',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['phone' => $user['phone']],
                $user + ['role' => 'customer']
            );
        }

        $menuItems = [
            [
                'name' => 'Classic Chocolate Fudge',
                'category' => 'Best Seller',
                'description' => 'Rich chocolate sponge layered with silky fudge cream and dark chocolate drizzle.',
                'price' => 35.00,
                'serves' => '6-8 pax',
            ],
            [
                'name' => 'Strawberry Shortcake',
                'category' => 'Fresh Fruit',
                'description' => 'Light vanilla sponge with whipped cream and fresh strawberries between every layer.',
                'price' => 40.00,
                'serves' => '8-10 pax',
            ],
            [
                'name' => 'Vanilla Bean Dream',
                'category' => 'Classic',
                'description' => 'Soft vanilla bean cake finished with smooth buttercream and delicate sugar pearls.',
                'price' => 30.00,
                'serves' => '6-8 pax',
            ],
            [
                'name' => 'Red Velvet Bliss',
                'category' => 'Signature',
                'description' => 'Velvety cocoa cake paired with cream cheese frosting and a soft crumb coating.',
                'price' => 42.00,
                'serves' => '8-10 pax',
            ],
        ];

        foreach ($menuItems as $menuItem) {
            MenuItem::updateOrCreate(
                ['name' => $menuItem['name']],
                $menuItem + ['is_active' => true]
            );
        }

        foreach ([1, 2, 3, 5, 7] as $daysFromNow) {
            $date = today()->addDays($daysFromNow);
            $availableDate = AvailableDate::whereDate('date', $date)->first() ?? new AvailableDate([
                'date' => $date,
            ]);

            $availableDate->is_available = true;
            $availableDate->save();
        }
    }
}
