<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Owns querying and persistence for application users.
 */
class UserService
{
    public function paginate(?string $search = null, int $perPage = 12): LengthAwarePaginator
    {
        return User::query()
            ->when($search, function ($query) use ($search) {
                $term = '%'.$search.'%';
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            })
            ->latest()
            ->paginate($perPage);
    }

    public function delete(int $id): void
    {
        User::findOrFail($id)->delete();
    }
}
