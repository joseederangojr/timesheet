<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\CreateEmploymentData;
use App\Models\Employment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreateEmployment
{
    public function handle(CreateEmploymentData $data): Employment
    {
        return DB::transaction(function () use ($data) {
            // Check if user already has an active employment
            if ($data->status->value === 'active') {
                $existingActive = Employment::query()
                    ->where('user_id', $data->user->id)
                    ->where('status', 'active')
                    ->exists();
                throw_if($existingActive, InvalidArgumentException::class, 'User already has an active employment record.');
            }

            return Employment::query()->create([
                'user_id' => $data->user->id,
                'client_id' => $data->client?->id,
                'position' => $data->position,
                'hire_date' => $data->hireDate,
                'status' => $data->status,
                'salary' => $data->salary,
                'work_location' => $data->workLocation,
                'effective_date' => $data->effectiveDate,
                'end_date' => $data->endDate,
            ]);
        });
    }
}
