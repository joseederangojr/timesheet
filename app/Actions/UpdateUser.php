<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateUserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUser
{
    public function handle(UpdateUserData $data): User
    {
        return DB::transaction(function () use ($data) {
            $data->user->update([
                'name' => $data->name,
                'email' => $data->email,
            ]);

            $data->user->roles()->sync($data->roles->pluck('id'));

            return $data->user->fresh(['roles']);
        });
    }
}
