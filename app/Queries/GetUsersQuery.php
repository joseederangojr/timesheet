<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final readonly class GetUsersQuery
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(Request $request): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->when($request->get('search'), function (
                Builder $query,
                mixed $search,
            ): void {
                $searchTerm = is_string($search) ? $search : '';
                $query
                    ->where('name', 'like', sprintf('%%%s%%', $searchTerm))
                    ->orWhere('email', 'like', sprintf('%%%s%%', $searchTerm));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
