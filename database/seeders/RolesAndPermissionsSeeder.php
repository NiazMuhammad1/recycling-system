<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = [
            'collections' => ['view','add','modify','collect','process'],
            'clients'     => ['view','add','modify'],
            'dashboard'   => ['view'],
            'processed_collections' => ['modify'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $allPermissions[] = Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        $admin  = Role::firstOrCreate(['name' => 'Admin']);
        $driver = Role::firstOrCreate(['name' => 'Driver']);
        $ware_house_staff = Role::firstOrCreate(['name' => 'Warehouse Staff']);

        // Admin gets everything
        $admin->syncPermissions(Permission::all());

        // Example: Driver limited permissions
        $driver->syncPermissions([
            'collections.view',
            'collections.collect',
            'clients.view',
        ]);

        // Example: ITAD UK role
        $ware_house_staff->syncPermissions([
            'collections.view','collections.add','collections.modify','collections.process',
            'clients.view','clients.add','clients.modify',
            'dashboard.view',
        ]);
    }
}