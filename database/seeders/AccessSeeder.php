<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // role perms
        Permission::create(['name' => 'roles_view', 'table_name' => 'roles']);
        Permission::create(['name' => 'roles_create', 'table_name' => 'roles']);
        Permission::create(['name' => 'roles_update', 'table_name' => 'roles']);
        Permission::create(['name' => 'roles_delete', 'table_name' => 'roles']);

        // permission perms
        Permission::create(['name' => 'permissions_view', 'table_name' => 'permissions']);
        Permission::create(['name' => 'permissions_create', 'table_name' => 'permissions']);
        Permission::create(['name' => 'permissions_update', 'table_name' => 'permissions']);
        Permission::create(['name' => 'permissions_delete', 'table_name' => 'permissions']);

        // user perms
        Permission::create(['name' => 'users_view', 'table_name' => 'users']);
        Permission::create(['name' => 'users_create', 'table_name' => 'users']);
        Permission::create(['name' => 'users_update', 'table_name' => 'users']);
        Permission::create(['name' => 'users_delete', 'table_name' => 'users']);

        // access perms
        Permission::create(['name' => 'access_view', 'table_name' => 'access']);
        Permission::create(['name' => 'access_update', 'table_name' => 'access']);

        // post_categories perms
        Permission::create(['name' => 'post_categories_view', 'table_name' => 'post_categories']);
        Permission::create(['name' => 'post_categories_create', 'table_name' => 'post_categories']);
        Permission::create(['name' => 'post_categories_update', 'table_name' => 'post_categories']);
        Permission::create(['name' => 'post_categories_delete', 'table_name' => 'post_categories']);

        // posts perms
        Permission::create(['name' => 'posts_view', 'table_name' => 'posts']);
        Permission::create(['name' => 'posts_create', 'table_name' => 'posts']);
        Permission::create(['name' => 'posts_update', 'table_name' => 'posts']);
        Permission::create(['name' => 'posts_delete', 'table_name' => 'posts']);
    }
}
