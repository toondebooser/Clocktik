<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'toondebooser@gmail.com',
            'password' => Hash::make('password123'),
            'admin' => true,
            'companyCode' => 1234567890,
            'email_verified_at' => now(),
            'god' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        User::create([
            'name' => 'God',
            'email' => 'taxus.work@gmail.com',
            'password' => Hash::make('password123'),
            'admin' => true,
            'companyCode' => 1234567890,
            'email_verified_at' => now(),
            'god' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Create regular users
        $users = [
            [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password123'),
                'admin' => false,
                'companyCode' => 1234567890,
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
                'companyCode' => 1234567890,
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
                'companyCode' => 1234567890,
                'email_verified_at' => now(),
                'god' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert regular users
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
