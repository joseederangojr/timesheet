<?php

declare(strict_types=1);

use App\Models\User;
use App\Queries\FindRoleByNameQuery;

it('can toggle admin sidebar', function (): void {
    $role = resolve(FindRoleByNameQuery::class)->handle('admin');
    $admin = User::factory()->hasAttached($role)->create();

    $this->actingAs($admin);

    $page = visit('/admin/dashboard');

    // Check that sidebar is expanded by default
    $page->waitForText('Dashboard')->assertSee('Users');

    // Click the collapse toggle button
    $page->click('button[aria-label="Collapse sidebar"]');

    // Wait a moment for CSS transition
    $page->wait(1);
    // After collapse, the navigation text should not be visible
    // but the page should still show the main content
    $page->assertSee('Admin Panel'); // Header should still be visible

    // Click toggle again to expand
    $page->click('button[aria-label="Expand sidebar"]');

    // Wait a moment for CSS transition
    $page->wait(1);

    // Verify the sidebar is expanded again
    $page->assertSee('Dashboard')->assertSee('Users');
});
