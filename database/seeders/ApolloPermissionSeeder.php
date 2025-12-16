<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Menu;

class ApolloPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Permissions
        $permissions = [
            'doctors.browse', 'doctors.read', 'doctors.edit', 'doctors.add', 'doctors.delete',
            'reports.browse', 'reports.read', 'reports.edit', 'reports.add', 'reports.delete',
            'ledgers.browse', 'ledgers.read', 'ledgers.edit', 'ledgers.add', 'ledgers.delete',
            'expenses.browse', 'expenses.read', 'expenses.edit', 'expenses.add', 'expenses.delete',
            'patients.browse', 'patients.read', 'patients.edit', 'patients.add', 'patients.delete',
            'commission.browse', // Add generic commission permission
        ];

        foreach ($permissions as $perm) {
            \App\Models\Permission::firstOrCreate([
                'name' => $perm
            ]);
        }

        // Assign to Admin Role
        $role = \App\Models\Role::where('name', 'Super Admin')->first();
        if (!$role) {
            $role = \App\Models\Role::first(); 
        }

        if ($role) {
            // Give all permissions to super admin
            $allPermissions = \App\Models\Permission::pluck('id')->toArray();
            $role->permissions()->syncWithoutDetaching($allPermissions);
        }

        // 2. Menus
        // Check if Apollo Center exists to avoid duplicates if run multiple times
        $hospitalMenu = Menu::where('title', 'Apollo Center')->first();
        
        if (!$hospitalMenu) {
            $parentId = Menu::insertGetId([
                'title' => 'Apollo Center',
                'route' => null,
                'parent_id' => null,
                'order' => 10,
                'icon' => 'bi-hospital',
            ]);

            Menu::insert([
                ['title' => 'Doctors', 'route' => 'doctors.index', 'parent_id' => $parentId, 'order' => 1],
                ['title' => 'Test Reports', 'route' => 'reports.index', 'parent_id' => $parentId, 'order' => 2],
                ['title' => 'Patient Billing', 'route' => 'patients.index', 'parent_id' => $parentId, 'order' => 3],
                ['title' => 'Commission', 'route' => 'commission.index', 'parent_id' => $parentId, 'order' => 4],
            ]);
        }

        $accountsMenu = Menu::where('title', 'Accounts')->whereNull('parent_id')->first();
        if (!$accountsMenu) {
            $accountsId = Menu::insertGetId([
                'title' => 'Accounts',
                'route' => null,
                'parent_id' => null,
                'order' => 11,
                'icon' => 'bi-wallet',
            ]);

            Menu::insert([
                ['title' => 'Ledgers', 'route' => 'ledgers.index', 'parent_id' => $accountsId, 'order' => 1],
                ['title' => 'Expenses', 'route' => 'expenses.index', 'parent_id' => $accountsId, 'order' => 2],
            ]);
        }
    }
}
