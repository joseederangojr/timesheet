<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateUserData;
use App\Models\User;
use App\Notifications\Auth\WelcomeNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateUser
{
    public function handle(CreateUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $password = $data->password ?? Str::password(12);

            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $password,
            ]);

            $user->roles()->attach($data->roles);

            // Send welcome email with generated password
            $user->notify(new WelcomeNotification($password));

            return $user;
        });
    }
}
