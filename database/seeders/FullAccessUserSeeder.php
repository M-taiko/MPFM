<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * A user who can do everything an admin can, except see, edit or delete users, roles and permissions.
 *
 *   php artisan db:seed --class=FullAccessUserSeeder
 *
 * Login: test@Smartoilfield.com / 123456789
 */
class FullAccessUserSeeder extends Seeder
{
    public function run(): void
    {
        // user, role and permission management stay with real admins
        $blocked = fn (string $name) => (bool) preg_match('/_(users|roles|permissions)$/', $name);

        $role = Role::firstOrCreate(['name' => 'full_access']);
        $role->syncPermissions(Permission::all()->reject(fn ($p) => $blocked($p->name)));

        $user = User::updateOrCreate(['email' => 'test@Smartoilfield.com'], [
            'name' => 'Test User',
            'password' => Hash::make('123456789'),
            'email_verified_at' => now(),
        ]);
        $user->syncRoles([$role]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $this->command?->info('Full-access user ready: test@Smartoilfield.com / 123456789');
    }
}
