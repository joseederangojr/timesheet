<?php

declare(strict_types=1);

use App\Models\Client;
use App\Models\Employment;
use App\Models\User;
use App\Queries\FindRoleByNameQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Admin Employments Management', function (): void {
    beforeEach(function (): void {
        $this->seed();

        $adminRole = resolve(FindRoleByNameQuery::class)->handle('admin');
        $employeeRole = resolve(FindRoleByNameQuery::class)->handle('employee');

        $this->admin = User::factory()->hasAttached($adminRole)->create();
        $this->employee = User::factory()->hasAttached($employeeRole)->create(['name' => 'John Employee']);
        $this->client = Client::factory()->create(['name' => 'Tech Corp']);
    });

    it('displays employments in sidebar navigation', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/dashboard');

        $page
            ->assertSee('Employments')
            ->click('Employments')
            ->assertPathIs('/admin/employments')
            ->assertSee('Employment Records');
    });

    it('displays employments index page with data table', function (): void {
        Employment::factory()->count(3)->create([
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments');

        $page
            ->assertSee('Employment Records')
            ->assertSee('John Employee')
            ->assertSee('Tech Corp')
            ->assertSee('Add Employment');
    });

    it('can access create employment page', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/employments/create')
            ->assertSee('Create Employment Record')
            ->assertSee('Employee')
            ->assertSee('Client (Optional)')
            ->assertSee('Position')
            ->assertSee('Hire Date')
            ->assertSee('Status')
            ->assertSee('Create Employment');
    });

    it('can access users list page', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/users')
            ->assertSee('John Employee')
            ->assertSee('Users'); // Page title should be present
    });

    it('can access user show page', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
            'position' => 'Software Engineer',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/users/'.$this->employee->id);

        $page
            ->assertSee('John Employee')
            ->assertSee('Employment Information');
    });

    it('can access employment show page', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
            'position' => 'Full Stack Developer',
            'status' => 'active',
            'salary' => 75000.00,
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id);

        $page
            ->assertSee('Employment Details')
            ->assertSee('Full Stack Developer')
            ->assertSee('John Employee');
    });

    it('can access employment show page with end option', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'status' => 'active',
            'position' => 'Project Manager',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id);

        $page
            ->assertSee('Employment Details')
            ->assertSee('End Employment'); // Button should be visible for active employment
    });

    it('prevents editing active employment details', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'status' => 'active',
            'position' => 'Team Lead',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id);

        $page->assertDontSee('Edit Employment'); // Should not show edit button for active employment
    });

    it('can access edit page for terminated employment', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'status' => 'terminated',
            'position' => 'Consultant',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id.'/edit');

        $page->assertSee('Update Employment');
    });

    it('displays employment filters', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'status' => 'active',
            'position' => 'Active Position',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'status' => 'terminated',
            'position' => 'Terminated Position',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments');

        $page
            ->assertSee('Active Position')
            ->assertSee('Terminated Position')
            ->assertSee('Status'); // Filter should be present
    });

    it('displays employment data', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Frontend Developer',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Backend Developer',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments');

        $page
            ->assertSee('Frontend Developer')
            ->assertSee('Backend Developer');
    });

    it('requires admin authentication for employment pages', function (): void {
        $this->visit('/admin/employments')->assertPathIs('/login');
    });

    it('can create new employment record', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/employments/create')
            // Select employee using combobox
            ->click('text=Select employee...')
            ->waitForText('John Employee')
            ->click('text=John Employee')
            // Select client using combobox
            ->click('text=Select client...')
            ->waitForText('Tech Corp')
            ->click('text=Tech Corp')
            // Fill form fields
            ->type('#position', 'Senior Developer')
            ->type('#hire_date', '2024-01-15')
            ->type('#effective_date', '2024-01-15')
            ->type('#salary', '85000.00')
            ->type('#work_location', 'Remote')
            ->press('Create Employment');

        $page
            ->assertPathIs('/admin/employments')
            ->assertSee('Employment record created successfully');

        // Verify the employment was created in database
        $this->assertDatabaseHas('employments', [
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
            'position' => 'Senior Developer',
            'status' => 'active',
            'salary' => 85000.00,
        ]);
    });

    it('can delete employment record', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Position to Delete',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id)
            ->press('Delete Employment');

        $page
            ->assertPathIs('/admin/employments')
            ->assertSee('Employment record deleted successfully');

        // Verify employment was deleted
        $this->assertDatabaseMissing('employments', [
            'id' => $employment->id,
        ]);
    });

    it('can search employments by position', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Frontend Developer',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Backend Developer',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments')
            ->type('search', 'Frontend')
            ->press('Search');

        $page
            ->assertSee('Frontend Developer')
            ->assertDontSee('Backend Developer');
    });

    it('can filter employments by status', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Active Job',
            'status' => 'active',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Terminated Job',
            'status' => 'terminated',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments?status=active');

        $page
            ->assertSee('Active Job')
            ->assertDontSee('Terminated Job');
    });

    it('can filter employments by client', function (): void {
        $client2 = Client::factory()->create(['name' => 'Another Corp']);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
            'position' => 'Tech Corp Job',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'client_id' => $client2->id,
            'position' => 'Another Corp Job',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments?client='.$this->client->id);

        $page
            ->assertSee('Tech Corp Job')
            ->assertDontSee('Another Corp Job');
    });

    it('displays employment history on user profile', function (): void {
        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'First Job',
            'status' => 'terminated',
        ]);

        Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Current Job',
            'status' => 'active',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/users/'.$this->employee->id);

        $page
            ->assertSee('Employment Information')
            ->assertSee('First Job')
            ->assertSee('Current Job')
            ->assertSee('Terminated')
            ->assertSee('Active');
    });

    it('can navigate to employment from user profile', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Clickable Job',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/users/'.$this->employee->id)
            ->click('Clickable Job');

        $page->assertPathIs('/admin/employments/'.$employment->id);
    });

    it('can navigate to user from employment details', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'position' => 'Job with User Link',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id)
            ->click('John Employee');

        $page->assertPathIs('/admin/users/'.$this->employee->id);
    });

    it('can navigate to client from employment details', function (): void {
        $employment = Employment::factory()->create([
            'user_id' => $this->employee->id,
            'client_id' => $this->client->id,
            'position' => 'Job with Client Link',
        ]);

        $this->actingAs($this->admin);

        $page = visit('/admin/employments/'.$employment->id)
            ->click('Tech Corp');

        $page->assertPathIs('/admin/clients/'.$this->client->id);
    });

    it('shows add employment button on user profile', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/users/'.$this->employee->id);

        $page
            ->assertSee('Add Employment')
            ->click('Add Employment')
            ->assertPathIs('/admin/employments/create')
            ->assertSee('John Employee'); // Should pre-select the user
    });

    it('shows add employment button on employments index', function (): void {
        $this->actingAs($this->admin);

        $page = visit('/admin/employments')
            ->click('Add Employment')
            ->assertPathIs('/admin/employments/create');
    });
});
