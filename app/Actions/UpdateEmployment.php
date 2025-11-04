<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\UpdateEmploymentData;
use App\Models\Employment;
use Illuminate\Support\Facades\DB;

final readonly class UpdateEmployment
{
    public function handle(UpdateEmploymentData $data): Employment
    {
        return DB::transaction(function () use ($data) {
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
