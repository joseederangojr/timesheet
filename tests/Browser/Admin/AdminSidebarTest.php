<?php

declare(strict_types=1);

use App\Models\User;
use App\Queries\FindRoleByNameQuery;

it('can toggle admin sidebar', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();
    $this->actingAs($admin)
        ->withCookie('sidebar', '1')
        ->visit('/admin/dashboard')
        ->wait(0.1)
        ->assertSee('Users')
        ->click('button[aria-label="Collapse sidebar"]')
        ->wait(0.1)
        ->assertSee('Admin Panel')
        ->click('button[aria-label="Expand sidebar"]')
        ->wait(0.1)
        ->assertSee('Users');
});
