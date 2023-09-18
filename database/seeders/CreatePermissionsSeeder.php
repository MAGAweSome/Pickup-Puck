<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreatePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Iterate over each of the roles and permissions tables
        // dd(
        //     config('permission.table_names')
        // );
        
        Schema::disableForeignKeyConstraints();
        foreach(config('permission.table_names') as $key => $table_name) {
            // Clear the roles and permissions tables
            DB::table($table_name)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        
        // Add two roles, admin and player and assign permissions
        $admin_role = Role::create(['name' => 'admin']);

        // Create permissions for the users
        Permission::create(['name' => 'edit games'])->assignRole([$admin_role]);
        Permission::create(['name' => 'create games'])->assignRole([$admin_role]);
        Permission::create(['name' => 'input game score'])->assignRole([$admin_role]);
    }
}
