<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Company;
use App\Models\Timelog;
use App\Models\User;
use App\Models\Usertotal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $companies = [
            [
                'company_name' => 'Beterams',
                'image' => 'images/95090418_1090903184610004_7235939885578715136_n.png',
                'color' => "#019541",
                'company_code' => 1234567890,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'company_name' => 'De Bever',
                'image' => 'images/bever.png',
                'color' => "black",
                'company_code' => 1234567891,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];
       

      

        // Create regular users
        $users = [
            [
                'name' => 'God',
                'email' => 'taxus.work@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 0000000000,
                'email_verified_at' => now(),
                'god' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Wim',
                'email' => 'toondebooser@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 1234567890,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sam',
                'email' => 'sam@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 1234567891,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Sanne',
                'email' => 'sanne@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567891,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'kamiel',
                'email' => 'kamiel@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567891,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert regular users
        foreach ($users as $user) {
            $createdUser = User::create($user);
            Timelog::create([
                'UserId' => $createdUser->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            Usertotal::create([
                'UserId' => $createdUser->id,
                'Month' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        };

        foreach ($companies as $company) {
            Company::create($company);
        };
    }
}
