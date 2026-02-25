<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Admin Role (has all permissions by default via Gate in AuthServiceProvider, but let's create the role)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Client Role (for MoonShine limited access)
        $clientRole = Role::firstOrCreate(['name' => 'cliente', 'guard_name' => 'web']);

        // Assign Admin role to first user (Super Admin) if exists
        $adminUser = User::find(1);
        if ($adminUser && ! $adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
        }
    }
}
