<?php

declare(strict_types=1);

use App\Actions\DeleteEmployment;
use App\Models\Employment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->action = app(DeleteEmployment::class);
});

it('deletes employment successfully', function (): void {
    $user = User::factory()->create();
    $employment = Employment::factory()->create([
        'user_id' => $user->id,
    ]);

    $result = $this->action->handle($employment);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('employments', [
        'id' => $employment->id,
    ]);
});

it('handles transaction properly', function (): void {
    $user = User::factory()->create();
    $employment = Employment::factory()->create([
        'user_id' => $user->id,
    ]);

    // Test that the transaction completes successfully
    $result = $this->action->handle($employment);

    expect($result)->toBeTrue();
    $this->assertDatabaseMissing('employments', [
        'id' => $employment->id,
    ]);
});
