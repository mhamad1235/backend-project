<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;
class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Account::create([
            'name' => 'Hotel Sunshine',
            'phone' => '+96477596565',
            'password' => Hash::make('password123'),
            'role_type' => 'hotel',
        ]);

        Account::create([
            'name' => 'Motel GreenStay',
            'phone' => '+9642656548',
            'password' => Hash::make('password123'),
            'role_type' => 'motel',
        ]);

        Account::create([
            'name' => 'Tourism Agency Alpha',
            'phone' => '+96455689842',
            'password' => Hash::make('password123'),
            'role_type' => 'agency',
        ]);
    }
}
