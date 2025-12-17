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

        // --- Roles & Permissions Mapping ---
        
        // 1. Super Admin (Already has everything)
        $superRole = \App\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        // Assign all permissions
        $allPerms = \App\Models\Permission::pluck('id')->toArray();
        $superRole->permissions()->syncWithoutDetaching($allPerms);

        // 2. Receptionist
        // Needs Access to: Patients (All), Doctors (Read), Reports (Read), Commission (No), Accounts (No)
        $receptionistRole = \App\Models\Role::firstOrCreate(['name' => 'Receptionist']);
        $receptionistPerms = \App\Models\Permission::whereIn('name', [
            'patients.browse', 'patients.read', 'patients.edit', 'patients.add', 'patients.delete', // Full Patient Access
            'doctors.browse', 'doctors.read', // View Doctors
            'reports.browse', 'reports.read', // View Test List
        ])->pluck('id')->toArray();
        $receptionistRole->permissions()->sync($receptionistPerms);

        // 3. Accountant
        // Needs Access to: Accounts (All), Commission (Read), Dashboard (Read)
        $accountantRole = \App\Models\Role::firstOrCreate(['name' => 'Accountant']);
        $accountantPerms = \App\Models\Permission::whereIn('name', [
            'ledgers.browse', 'ledgers.read', 'ledgers.edit', 'ledgers.add', 'ledgers.delete',
            'expenses.browse', 'expenses.read', 'expenses.edit', 'expenses.add', 'expenses.delete',
            'commission.browse',
            'reports.browse', 'reports.read', // View Reports to check prices
        ])->pluck('id')->toArray();
        $accountantRole->permissions()->sync($accountantPerms);

        // 4. Lab Technologist
        // Needs Access to: Reports (Manage Tests), Patients (Read)
        $labRole = \App\Models\Role::firstOrCreate(['name' => 'Lab Technologist']);
        $labPerms = \App\Models\Permission::whereIn('name', [
            'reports.browse', 'reports.read', 'reports.edit', 'reports.add', // Manage Test Settings
            'patients.browse', 'patients.read', // View Patients to do tests
        ])->pluck('id')->toArray();
        $labRole->permissions()->sync($labPerms);


        // --- Sample Users ---

        // Receptionist User
        $receptionist = \App\Models\Admin::firstOrCreate(
            ['email' => 'reception@apollo.com'],
            ['name' => 'Reception Desk', 'password' => bcrypt('password')]
        );
        $receptionist->roles()->syncWithoutDetaching([$receptionistRole->id]);

        // Accountant User
        $accountant = \App\Models\Admin::firstOrCreate(
            ['email' => 'accounts@apollo.com'],
            ['name' => 'Accountant', 'password' => bcrypt('password')]
        );
        $accountant->roles()->syncWithoutDetaching([$accountantRole->id]);

        // Lab User
        $lab = \App\Models\Admin::firstOrCreate(
            ['email' => 'lab@apollo.com'],
            ['name' => 'Lab Room 1', 'password' => bcrypt('password')]
        );
        $lab->roles()->syncWithoutDetaching([$labRole->id]);

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
