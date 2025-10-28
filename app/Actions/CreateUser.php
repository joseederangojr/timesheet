<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateUserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class CreateUser
{
    public function handle(CreateUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $user->roles()->attach($data->roles);

            return $user;
        });
    }
}
