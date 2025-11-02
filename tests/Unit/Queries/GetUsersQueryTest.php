<?php

declare(strict_types=1);

use App\Data\UserFilters;
use App\Models\Role;
use App\Models\User;
use App\Queries\GetUsersQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('GetUsersQuery', function (): void {
    beforeEach(function (): void {
        $this->query = new GetUsersQuery();
    });

    describe('handle', function (): void {
        it('returns paginated users with roles', function (): void {
            $role = Role::factory()->create(['name' => 'admin']);
            $users = User::factory(3)->create();

            foreach ($users as $user) {
                $user->roles()->attach($role);
            }

            $filters = new UserFilters();
            $result = $this->query->handle($filters);

            expect($result)
                ->toBeInstanceOf(
                    Illuminate\Contracts\Pagination\LengthAwarePaginator::class,
                )
                ->and($result->count())
                ->toBe(3)
                ->and($result->first()->relationLoaded('roles'))
                ->toBeTrue()
                ->and($result->first()->roles->first()->name)
                ->toBe('admin');
        });

        it('filters users by name search', function (): void {
            $john = User::factory()->create(['name' => 'John Doe']);
            $jane = User::factory()->create(['name' => 'Jane Smith']);
            $bob = User::factory()->create(['name' => 'Bob Johnson']);

            $filters = new UserFilters(search: 'John');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(2); // John Doe + Bob Johnson (contains "John")

            $names = $result->pluck('name')->toArray();
            expect($names)
                ->toContain('John Doe')
                ->and($names)
                ->toContain('Bob Johnson')
                ->and($names)
                ->not->toContain('Jane Smith');
        });

        it('filters users by email search', function (): void {
            $user1 = User::factory()->create(['email' => 'test@example.com']);
            $user2 = User::factory()->create(['email' => 'demo@example.com']);
            $user3 = User::factory()->create(['email' => 'user@other.com']);

            $filters = new UserFilters(search: 'example');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(2); // test@example.com + demo@example.com

            $emails = $result->pluck('email')->toArray();
            expect($emails)
                ->toContain('test@example.com')
                ->and($emails)
                ->toContain('demo@example.com')
                ->and($emails)
                ->not->toContain('user@other.com');
        });

        it('returns users ordered by latest created first', function (): void {
            $firstUser = User::factory()->create([
                'created_at' => now()->subDays(2),
            ]);
            $secondUser = User::factory()->create([
                'created_at' => now()->subDays(1),
            ]);
            $thirdUser = User::factory()->create(['created_at' => now()]);

            $filters = new UserFilters();
            $result = $this->query->handle($filters);

            expect($result->first()->id)
                ->toBe($thirdUser->id)
                ->and($result->last()->id)
                ->toBe($firstUser->id);
        });

        it(
            'returns empty result when search finds no matches',
            function (): void {
                User::factory(3)->create();

                $filters = new UserFilters(search: 'nonexistent');
                $result = $this->query->handle($filters);

                expect($result->count())
                    ->toBe(0)
                    ->and($result->total())
                    ->toBe(0);
            },
        );

        it('handles pagination with multiple pages', function (): void {
            User::factory(20)->create();

            $filters = new UserFilters();
            $result = $this->query->handle($filters);

            expect($result->hasPages())
                ->toBeTrue()
                ->and($result->total())
                ->toBe(20)
                ->and($result->currentPage())
                ->toBe(1);
        });

        it('paginates results with 15 items per page', function (): void {
            User::factory(20)->create();

            $filters = new UserFilters();
            $result = $this->query->handle($filters);

            expect($result->perPage())
                ->toBe(15)
                ->and($result->count())
                ->toBe(15)
                ->and($result->total())
                ->toBe(20);
        });

        it('handles empty string search parameter', function (): void {
            User::factory()->create(['name' => 'John Doe']);
            User::factory()->create(['name' => 'Jane Smith']);

            $filters = new UserFilters(search: '');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(2);
        });

        it('uses withQueryString for pagination', function (): void {
            User::factory(20)->create(['name' => 'Test User']);

            $filters = new UserFilters(search: 'Test');
            $result = $this->query->handle($filters);

            expect($result->hasPages())
                ->toBeTrue()
                ->and($result->total())
                ->toBe(20);
        });

        it('searches both name and email with OR logic', function (): void {
            $user1 = User::factory()->create([
                'name' => 'John Doe',
                'email' => 'unrelated@example.com',
            ]);
            $user2 = User::factory()->create([
                'name' => 'Jane Smith',
                'email' => 'john@other.com',
            ]);
            $user3 = User::factory()->create([
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
            ]);

            $filters = new UserFilters(search: 'john');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(2);

            $resultIds = $result->pluck('id')->toArray();
            expect($resultIds)
                ->toContain($user1->id)
                ->and($resultIds)
                ->toContain($user2->id)
                ->and($resultIds)
                ->not->toContain($user3->id);
        });

        it('filters users by role', function (): void {
            $adminRole = Role::factory()->create(['name' => 'admin']);
            $employeeRole = Role::factory()->create(['name' => 'employee']);

            $adminUser = User::factory()->create();
            $adminUser->roles()->attach($adminRole);

            $employeeUser = User::factory()->create();
            $employeeUser->roles()->attach($employeeRole);

            $noRoleUser = User::factory()->create();

            $filters = new UserFilters(role: 'admin');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(1);
            expect($result->first()->id)->toBe($adminUser->id);
        });

        it('filters users by verification status', function (): void {
            $verifiedUser = User::factory()->create([
                'email_verified_at' => now(),
            ]);
            $unverifiedUser = User::factory()->create([
                'email_verified_at' => null,
            ]);

            $filters = new UserFilters(verified: 'verified');
            $result = $this->query->handle($filters);

            expect($result->count())->toBe(1);
            expect($result->first()->id)->toBe($verifiedUser->id);
        });

        it(
            'handles invalid sort field by falling back to first allowed field',
            function (): void {
                User::factory(3)->create();

                $filters = new UserFilters(sortBy: 'invalid_field');
                $result = $this->query->handle($filters);

                expect($result->count())->toBe(3);
                // Should sort by 'name' (first allowed)
            },
        );
    });
});
