<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Menu::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $menus = [
            // 1. Dashboard (Order 1)
            ['id' => 1, 'title' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'bi bi-speedometer2', 'parent_id' => NULL, 'order' => 1, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
            
            // 2. New Patient (Order 2) - [NEW]
            ['id' => 19, 'title' => 'New Patient', 'route' => 'patients.create', 'icon' => 'bi bi-person-plus', 'parent_id' => NULL, 'order' => 2, 'created_at' => '2025-12-16 10:40:38', 'updated_at' => '2025-12-16 10:40:38'],

            // 3. Patient Billing (Order 3) - [Shifted]
            ['id' => 14, 'title' => 'Patient Billing', 'route' => 'patients.index', 'icon' => 'fa-solid fa-house-flag', 'parent_id' => NULL, 'order' => 3, 'created_at' => NULL, 'updated_at' => '2025-12-16 10:40:38'],

            // 4. Apollo Center (Order 4) - [Shifted]
            ['id' => 11, 'title' => 'Apollo Center', 'route' => NULL, 'icon' => 'bi-hospital', 'parent_id' => NULL, 'order' => 4, 'created_at' => NULL, 'updated_at' => '2025-12-16 10:40:21'],
            ['id' => 12, 'title' => 'Doctors', 'route' => 'doctors.index', 'icon' => NULL, 'parent_id' => 11, 'order' => 1, 'created_at' => NULL, 'updated_at' => NULL],
            ['id' => 13, 'title' => 'Test Reports', 'route' => 'reports.index', 'icon' => NULL, 'parent_id' => 11, 'order' => 2, 'created_at' => NULL, 'updated_at' => NULL],
            ['id' => 15, 'title' => 'Commission', 'route' => 'commission.index', 'icon' => NULL, 'parent_id' => 11, 'order' => 3, 'created_at' => NULL, 'updated_at' => '2025-12-16 10:40:21'],

            // 5. Accounts (Order 5) - [Shifted]
            ['id' => 16, 'title' => 'Accounts', 'route' => NULL, 'icon' => 'bi-wallet', 'parent_id' => NULL, 'order' => 5, 'created_at' => NULL, 'updated_at' => '2025-12-16 10:40:21'],
            ['id' => 17, 'title' => 'Ledgers', 'route' => 'ledgers.index', 'icon' => NULL, 'parent_id' => 16, 'order' => 1, 'created_at' => NULL, 'updated_at' => NULL],
            ['id' => 18, 'title' => 'Expenses', 'route' => 'expenses.index', 'icon' => NULL, 'parent_id' => 16, 'order' => 2, 'created_at' => NULL, 'updated_at' => NULL],

            // 6. User Management (Order 6) - [Shifted]
            ['id' => 2, 'title' => 'User Management', 'route' => 'admin.users.index', 'icon' => 'bi bi-people', 'parent_id' => NULL, 'order' => 6, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:40:21'],

            // 7. Roles & Permissions (Order 7) - [Shifted]
            ['id' => 3, 'title' => 'Roles & Permissions', 'route' => NULL, 'icon' => 'bi bi-shield-lock', 'parent_id' => NULL, 'order' => 7, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:40:21'],
            ['id' => 4, 'title' => 'Roles', 'route' => 'admin.roles.index', 'icon' => NULL, 'parent_id' => 3, 'order' => 1, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
            ['id' => 5, 'title' => 'Permissions', 'route' => 'admin.permissions.index', 'icon' => NULL, 'parent_id' => 3, 'order' => 2, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],

            // 8. Configurations (Order 8) - [Shifted]
            ['id' => 6, 'title' => 'Configurations', 'route' => NULL, 'icon' => 'fa-solid fa-house-flag', 'parent_id' => NULL, 'order' => 8, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:40:21'],
            ['id' => 7, 'title' => 'Menu Builder', 'route' => 'admin.menus.index', 'icon' => NULL, 'parent_id' => 6, 'order' => 1, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
            ['id' => 8, 'title' => 'CRUD Builder', 'route' => 'crud-builder.index', 'icon' => NULL, 'parent_id' => 6, 'order' => 2, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
            ['id' => 9, 'title' => 'Settings', 'route' => 'admin.settings.index', 'icon' => NULL, 'parent_id' => 6, 'order' => 3, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
            ['id' => 10, 'title' => 'Theme Settings', 'route' => 'admin.theme.index', 'icon' => NULL, 'parent_id' => 6, 'order' => 4, 'created_at' => '2025-12-16 10:27:18', 'updated_at' => '2025-12-16 10:27:18'],
        ];

        Menu::insert($menus);
    }
}
