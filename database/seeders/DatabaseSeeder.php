<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Timelog;
use App\Models\User;
use App\Models\Usertotal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Predefined companies
        $predefinedCompanies = [
            [
                'company_name' => 'Beterams',
                'company_logo' => 'images/95090418_1090903184610004_7235939885578715136_n.png',
                'company_color' => "#019541",
                'company_code' => 1234567890,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'De Bever',
                'company_logo' => 'images/bever.png',
                'company_color' => "black",
                'company_code' => 1234567891,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'company_name' => 'Heaven',
                'company_logo' => 'images/TaxusLogo.png',
                'company_code' => 1618033988,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Generate 47 additional companies (to reach 50 total)
        $generatedCompanies = [];
        for ($i = 1; $i <= 47; $i++) {
            $generatedCompanies[] = [
                'company_name' => $faker->company,
                'company_color' => $faker->hexColor,
                'company_code' => 1000000000 + $i, // Unique codes starting at 1000000001
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Combine predefined and generated companies
        $companies = array_merge($predefinedCompanies, $generatedCompanies);

        // Insert all companies
        foreach ($companies as $company) {
            Company::create($company);
        }

        // Predefined users
        $predefinedUsers = [
            [
                'name' => 'God',
                'email' => 'taxus.work@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 1618033988, // Heaven
                'email_verified_at' => now(),
                'god' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wim',
                'email' => 'toondebooser@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 1234567890, // Beterams
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sam',
                'email' => 'sam@gmail.com',
                'password' => Hash::make('password123'),
                'admin' => true,
                'company_code' => 1234567891, // De Bever
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890, // Beterams
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890, // Beterams
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567890, // Beterams
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sanne',
                'email' => 'sanne@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567891, // De Bever
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kamiel',
                'email' => 'kamiel@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'company_code' => 1234567891, // De Bever
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        $now= now('Europe/Brussels');
        // Count predefined users per company
        $companyUserCounts = [
            1234567890 => 0, // Beterams
            1234567891 => 0, // De Bever
            1618033988 => 0, // Heaven
        ];
        foreach ($predefinedUsers as $user) {
            $companyUserCounts[$user['company_code']]++;
        }

        // Insert predefined users and their Timelog/Usertotal records
        foreach ($predefinedUsers as $user) {
            $createdUser = User::create($user);
            Timelog::create([
                'UserId' => $createdUser->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            Usertotal::create([
                'UserId' => $createdUser->id,
                'Month' => $now->startOfMonth(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Generate additional users to ensure 10 workers per company
        $additionalUsers = [];
        foreach ($companies as $company) {
            $companyCode = $company['company_code'];
            // Determine how many additional users are needed
            $existingUsers = $companyUserCounts[$companyCode] ?? 0;
            $usersNeeded = 10 - $existingUsers;

            // Generate additional users for this company
            for ($j = 1; $j <= $usersNeeded; $j++) {
                $additionalUsers[] = [
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password123'),
                    'admin' => false,
                    'company_code' => $companyCode,
                    'email_verified_at' => now(),
                    'god' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert additional users and their Timelog/Usertotal records
        foreach ($additionalUsers as $user) {
            $createdUser = User::create($user);
            Timelog::create([
                'UserId' => $createdUser->id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            Usertotal::create([
                'UserId' => $createdUser->id,
                'Month' => $now->startOfMonth(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}