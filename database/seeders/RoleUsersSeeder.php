<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RoleUsersSeeder extends Seeder
{
    /**
     * Create one user for every role defined in the system.
     *
     * Each user is named after its role and gets a predictable email
     * (e.g. the "Super Admin" role => super.admin@test.com) so the login
     * for any role is easy to find. Existing users are left untouched.
     *
     * @return void
     */
    public function run()
    {
        foreach (Role::all() as $role) {
            $slug = Str::of($role->name)->lower()->replace(' ', '.')->value();

            $user = User::firstOrCreate(
                ['email' => $slug.'@test.com'],
                [
                    'name' => $role->name,
                    'password' => Hash::make('12345678'),
                    'is_active' => 1,
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}
