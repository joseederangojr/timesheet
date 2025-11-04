<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\EmploymentStatus;
use App\Models\Client;
use App\Models\Employment;
use App\Models\User;

final readonly class UpdateEmploymentData
{
    public function __construct(
        public Employment $employment,
        public User $user,
        public ?Client $client,
        public string $position,
        public string $hireDate,
        public EmploymentStatus $status,
        public ?string $salary,
        public ?string $workLocation,
        public string $effectiveDate,
        public ?string $endDate,
    ) {
        //
    }
}
