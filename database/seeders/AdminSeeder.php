<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminEmail = config('app.admin.email');
        $adminPassword = config('app.admin.password');

        if (! is_string($adminEmail) || ! is_string($adminPassword) || ($adminEmail === '' || $adminEmail === '0') || ($adminPassword === '' || $adminPassword === '0')) {
            $this->command->warn(
                'ADMIN_EMAIL and ADMIN_PASSWORD environment variables are required to seed admin user.',
            );

            return;
        }

        User::query()
            ->firstOrCreate([
                'name' => 'Super User',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
            ])
            ->roles()
            ->attach(1);
    }
}
