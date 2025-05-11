<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Timelog;
use App\Models\User;
use App\Models\Usertotal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{

    public function run(): void
    {
        Company::truncate();
        User::truncate();
        Timelog::truncate();
        Usertotal::truncate();

         Company::create([
            'company_name' => 'Heaven',
            'company_logo' => 'images/TaxusLogo.png',
            'company_code' => 1618033988,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $now = now('Europe/Brussels');
        $godUser = User::create([
            'name' => 'God',
            'email' => 'taxus.work@gmail.com',
            'password' => Hash::make('password123'),
            'admin' => true,
            'company_code' => 1618033988, // Heaven
            'email_verified_at' => now(),
            'god' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Timelog::create([
            'UserId' => $godUser->id,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Usertotal::create([
            'UserId' => $godUser->id,
            'Month' => $now->startOfMonth(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}