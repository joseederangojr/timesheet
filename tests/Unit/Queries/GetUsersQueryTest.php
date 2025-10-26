<?php

declare(strict_types=1);

use App\Models\Role;
use App\Models\User;
use App\Queries\GetUsersQuery;
use Illuminate\Http\Request;

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

            $request = new Request();
            $result = $this->query->handle($request);

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

            $request = new Request(['search' => 'John']);
            $result = $this->query->handle($request);

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

            $request = new Request(['search' => 'example']);
            $result = $this->query->handle($request);

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

            $request = new Request();
            $result = $this->query->handle($request);

            expect($result->first()->id)
                ->toBe($thirdUser->id)
                ->and($result->last()->id)
                ->toBe($firstUser->id);
        });

        it(
            'returns empty result when search finds no matches',
            function (): void {
                User::factory(3)->create();

                $request = new Request(['search' => 'nonexistent']);
                $result = $this->query->handle($request);

                expect($result->count())
                    ->toBe(0)
                    ->and($result->total())
                    ->toBe(0);
            },
        );

        it('handles pagination with multiple pages', function (): void {
            User::factory(20)->create();

            $request = new Request();
            $result = $this->query->handle($request);

            expect($result->hasPages())
                ->toBeTrue()
                ->and($result->total())
                ->toBe(20)
                ->and($result->currentPage())
                ->toBe(1);
        });

        it('paginates results with 15 items per page', function (): void {
            User::factory(20)->create();

            $request = new Request();
            $result = $this->query->handle($request);

            expect($result->perPage())
                ->toBe(15)
                ->and($result->count())
                ->toBe(15)
                ->and($result->total())
                ->toBe(20);
        });

        it('handles non-string search parameter', function (): void {
            User::factory()->create(['name' => 'John Doe']);
            User::factory()->create(['name' => 'Jane Smith']);

            $request = new Request(['search' => 123]);
            $result = $this->query->handle($request);

            expect($result->count())->toBe(2);
        });

        it('handles empty string search parameter', function (): void {
            User::factory()->create(['name' => 'John Doe']);
            User::factory()->create(['name' => 'Jane Smith']);

            $request = new Request(['search' => '']);
            $result = $this->query->handle($request);

            expect($result->count())->toBe(2);
        });

        it('uses withQueryString for pagination', function (): void {
            User::factory(20)->create(['name' => 'Test User']);

            $request = new Request(['search' => 'Test', 'page' => 1]);
            $result = $this->query->handle($request);

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

            $request = new Request(['search' => 'john']);
            $result = $this->query->handle($request);

            expect($result->count())->toBe(2);

            $resultIds = $result->pluck('id')->toArray();
            expect($resultIds)
                ->toContain($user1->id)
                ->and($resultIds)
                ->toContain($user2->id)
                ->and($resultIds)
                ->not->toContain($user3->id);
        });
    });
});
