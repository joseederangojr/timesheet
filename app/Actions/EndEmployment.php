<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Employment;
use Illuminate\Support\Facades\DB;

final readonly class EndEmployment
{
    public function handle(Employment $employment, ?string $endDate = null): Employment
    {
        return DB::transaction(function () use ($employment, $endDate) {
            $endDate ??= now()->toDateString();

            $employment->update([
                'status' => 'terminated',
                'end_date' => $endDate,
            ]);

            return $employment->fresh() ?? $employment;
        });
    }
}
