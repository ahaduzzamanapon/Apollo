<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Fix Patient Route
        DB::table('menus')->where('route', 'patients.index')->update(['route' => 'admin.patients.index']);
        DB::table('menus')->where('route', 'patients.create')->update(['route' => 'admin.patients.create']);
        
        // Ensure other routes are prefixed correctly if needed
        // Assuming other routes were fine or fixed previously (e.g. admin.users.index)
    }

    public function down()
    {
        DB::table('menus')->where('route', 'admin.patients.index')->update(['route' => 'patients.index']);
        DB::table('menus')->where('route', 'admin.patients.create')->update(['route' => 'patients.create']);
    }
};
