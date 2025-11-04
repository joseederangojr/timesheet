<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Employment;
use Illuminate\Support\Facades\DB;

final readonly class DeleteEmployment
{
    public function handle(Employment $employment): bool
    {
        return DB::transaction(fn () => $employment->delete()) ?? false;
    }
}
