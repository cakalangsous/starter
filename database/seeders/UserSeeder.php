<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dev = User::create([
            'name' => 'Developer',
            'email' => 'developer@dev.com',
            'avatar' => null,
            'password' => bcrypt('asdfasdf')
        ]);

        $super_admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'avatar' => null,
            'password' => bcrypt('asdfasdf')
        ]);

        Role::create(['name' => 'Developer']);
        $dev->assignRole('Developer');

        $sa_role = Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Admin']);
        
        $super_admin->assignRole($sa_role);
    }
}
