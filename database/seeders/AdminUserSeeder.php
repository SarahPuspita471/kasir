<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // pastikan unik berdasarkan email
        User::updateOrCreate(
            ['email' => 'admin@kasirku.local'],
            [
                'name' => 'admin',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'remember_token' => Str::random(10),
                'role' => 'admin',
            ]
        );
    }
}
