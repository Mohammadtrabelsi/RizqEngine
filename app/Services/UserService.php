<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

/**
 * Owns querying and persistence for application users, including role
 * assignment and the avatar media promoted from a staged upload.
 */
class UserService
{
    public function __construct(private readonly UploadService $uploads) {}

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

    /**
     * Create a user, assign the given role and (optionally) attach the avatar
     * staged under the provided temp folder token.
     *
     * @param  array{name: string, email: string, password: string, is_active?: mixed}  $data
     */
    public function create(array $data, string $role, ?string $imageFolder = null): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? null,
        ]);

        $user->assignRole($role);

        $this->uploads->promoteToMedia($user, $imageFolder, 'avatars');

        return $user;
    }

    /**
     * Update a user's profile fields, sync its role and (optionally) replace
     * the avatar with the upload staged under the temp folder token.
     *
     * @param  array{name: string, email: string, is_active?: mixed}  $data
     */
    public function update(User $user, array $data, string $role, ?string $imageFolder = null): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? null,
        ]);

        $user->syncRoles($role);

        $this->uploads->promoteToMedia($user, $imageFolder, 'avatars');

        return $user;
    }

    /**
     * Update the given user's own profile (name, email) and optionally its
     * avatar. Profile uploads are staged under the "temp/" path (no "public/"
     * prefix) unlike the user-management flow.
     *
     * @param  array{name: string, email: string}  $data
     */
    public function updateProfile(User $user, array $data, ?string $imageFolder = null): User
    {
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $this->uploads->promoteToMedia($user, $imageFolder, 'avatars', 'temp/');

        return $user;
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Register a new self-service user and grant the default Admin role.
     *
     * @param  array{name: string, email: string, password: string}  $data
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => 1,
        ]);

        $user->assignRole('Admin');

        return $user;
    }

    public function delete(int $id): void
    {
        User::findOrFail($id)->delete();
    }

    public function deleteModel(User $user): void
    {
        $user->delete();
    }
}
