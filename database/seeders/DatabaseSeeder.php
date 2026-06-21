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
                'name' => 'Choc Drizzle',
                'category' => 'Brownies',
                'description' => 'Classic fudgy brownies topped with a generous drizzle of premium chocolate for extra sweetness and indulgence.',
                'price' => 35.00,
            ],
            [
                'name' => 'Plain',
                'category' => 'Brownies',
                'description' => 'Rich, moist, and fudgy chocolate brownies with a soft texture and deep chocolate flavor.',
                'price' => 30.00,
            ],
            [
                'name' => 'Double Chocolate',
                'category' => 'Banana Bread',
                'description' => 'Moist banana cake loaded with rich chocolate and chocolate chips for double the chocolate goodness.',
                'price' => 35.00,
            ],
            [
                'name' => 'Klasik / Ori',
                'category' => 'Banana Bread',
                'description' => 'Soft and fluffy banana cake made with ripe bananas for a naturally sweet and comforting taste.',
                'price' => 32.00,
            ],
             [
                'name' => 'Hawaiian Coconut Pandan',
                'category' => 'Banana Bread',
                'description' => 'A tropical twist featuring fragrant pandan and coconut flavors, creating a unique and aromatic cake experience.',
                'price' => 40.00,
            ],
            [
                'name' => 'Cheese',
                'category' => 'Banana Bread',
                'description' => 'Soft banana cake topped with creamy cheese for a delicious balance of sweet and savory flavors.',
                'price' => 45.00,
            ], 
            [
                'name' => 'Ori',
                'category' => 'Mini Pack',
                'description' => 'A convenient mini-sized classic banana cake, perfect for individual enjoyment or gifting.',
                'price' => 10.00,
            ],
            [
                'name' => 'Double Choc',
                'category' => 'Mini Pack',
                'description' => 'Mini banana cake packed with rich chocolate flavor in every bite.',
                'price' => 12.00,
            ],
            [
                'name' => 'Brownies',
                'category' => 'Mini Pack',
                'description' => 'Bite-sized fudgy brownies that are rich, chocolatey, and perfect for snacking.',
                'price' => 12.00,
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
