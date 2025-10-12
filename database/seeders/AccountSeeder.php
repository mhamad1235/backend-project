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
            'phone' => '7754812814',
            'password' => Hash::make('password12345'),
            'role_type' => 'hotel',
        ]);
           Account::create([
            'name' => 'Torist Sunshine',
            'phone' => '7754812815',
            'password' => Hash::make('password12345'),
            'role_type' => 'tourist',
        ]);
           Account::create([
            'name' => 'restaurant Sunshine',
            'phone' => '7754812816',
            'password' => Hash::make('password12345'),
            'role_type' => 'restaurant',
        ]);

     
    }
}
