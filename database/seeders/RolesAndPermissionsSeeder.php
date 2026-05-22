<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();

        /*
        |--------------------------------------------------------------------------
        | Modules & Permissions
        |--------------------------------------------------------------------------
        */

        $modules = [

            'dashboard' => [
                'view',
            ],

            'collections' => [
                'view',
                'add',
                'modify',
                'delete',
                'collect',
                'process',
            ],

            'processed_collections' => [
                'view',
                'modify',
                'delete',
            ],

            'clients' => [
                'view',
                'add',
                'modify',
                'delete',
            ],

            'categories' => [
                'view',
                'add',
                'modify',
                'delete',
            ],

            'users' => [
                'view',
                'add',
                'modify',
                'delete',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($modules as $module => $actions) {

            foreach ($actions as $action) {

                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Create Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'Driver',
            'guard_name' => 'web',
        ]);

        Role::firstOrCreate([
            'name' => 'Warehouse Staff',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Give Admin All Permissions
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions(Permission::where('guard_name', 'web')->get());

        app(\Spatie\Permission\PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }
}
//php artisan db:seed --class=RolesAndPermissionsSeeder