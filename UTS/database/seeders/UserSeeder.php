<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567890',
            ],
            [
                'name' => 'Ani Wijaya',
                'email' => 'ani@example.com',
                'phone' => '082123456789',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@example.com',
                'phone' => '083123456788',
            ],
            [
                'name' => 'Dedi Kurniawan',
                'email' => 'dedi@example.com',
                'phone' => '084123456787',
            ],
            [
                'name' => 'Eka Pratama',
                'email' => 'eka@example.com',
                'phone' => '085123456786',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
