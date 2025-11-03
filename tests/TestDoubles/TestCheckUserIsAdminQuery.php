<?php

declare(strict_types=1);

namespace Tests\TestDoubles;

use App\Models\User;
use App\Queries\CheckUserIsAdminQueryInterface;

final class TestCheckUserIsAdminQuery implements CheckUserIsAdminQueryInterface
{
    public function __construct(private bool $returnValue) {}

    public function handle(User $user): bool
    {
        return $this->returnValue;
    }
}
