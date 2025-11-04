<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateEmploymentData;
use App\Models\Employment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class UpdateEmployment
{
    public function handle(UpdateEmploymentData $data): Employment
    {
        return DB::transaction(function () use ($data) {
            // Check if trying to update an active employment with non-status changes
            if ($data->employment->status === 'active') {
                $originalData = $data->employment->only([
                    'user_id',
                    'client_id',
                    'position',
                    'hire_date',
                    'salary',
                    'work_location',
                    'effective_date',
                    'end_date',
                ]);
                $newData = [
                    'user_id' => $data->user->id,
                    'client_id' => $data->client?->id,
                    'position' => $data->position,
                    'hire_date' => $data->hireDate,
                    'salary' => $data->salary,
                    'work_location' => $data->workLocation,
                    'effective_date' => $data->effectiveDate,
                    'end_date' => $data->endDate,
                ];
                $hasNonStatusChanges =
                    array_diff_assoc($newData, $originalData) !== [];
                throw_if(
                    $hasNonStatusChanges,
                    InvalidArgumentException::class,
                    'Cannot update active employment records except for status changes.',
                );
            }

            // If changing status to active, ensure no other active employment exists for this user
            if (
                $data->status->value === 'active' &&
                $data->employment->status !== 'active'
            ) {
                $existingActive = Employment::query()
                    ->where('user_id', $data->user->id)
                    ->where('status', 'active')
                    ->where('id', '!=', $data->employment->id)
                    ->exists();
                throw_if(
                    $existingActive,
                    InvalidArgumentException::class,
                    'User already has an active employment record.',
                );
            }

            $data->employment->update([
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

            return $data->employment->fresh() ?? $data->employment;
        });
    }
}
