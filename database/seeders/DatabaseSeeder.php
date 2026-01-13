<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Ahmed Sender',
            'phone' => '0918862872',
            'password' => Hash::make('password123'),
            'balance' => 5000,
        ]);


        User::create([
            'name' => 'Mohamed Receiver',
            'phone' => '0922399275',
            'password' => Hash::make('password123'),
            'balance' => 100,
        ]);
         User::create([
        'name' => 'Sara Ali',
        'phone' => '0928146482',
        'password' => Hash::make('password123'),
        'balance' => 800,
    ]);


    }
}
