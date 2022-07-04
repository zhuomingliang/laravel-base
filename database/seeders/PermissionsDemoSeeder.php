<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsDemoSeeder extends Seeder {
    /**
     * Create the initial roles and permissions.
     *
     * @return void
     */
    public function run() {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name' => 'admin', 'cname' => '用户管理', 'name' => 'user/getIndex']);
        Permission::create(['guard_name' => 'admin', 'cname' => '创建用户', 'name' => 'user/postIndex']);
        Permission::create(['guard_name' => 'admin', 'cname' => '修改用户', 'name' => 'user/putIndex']);

        Permission::create(['guard_name' => 'admin', 'cname' => '角色管理', 'name' => 'role/getIndex']);
        Permission::create(['guard_name' => 'admin', 'cname' => '权限管理', 'name' => 'permission/getIndex']);
        Permission::create(['guard_name' => 'admin', 'cname' => '系统日志', 'name' => 'SystemLog/getIndex']);

        // create roles and assign existing permissions
        $role1 = Role::create(['guard_name' => 'admin', 'name' => '系统管理员']);
        $role1->givePermissionTo('user');
        $role1->givePermissionTo('user/postIndex');

        $role2 = Role::create(['guard_name' => 'admin', 'name' => '维护管理员']);
        $role2->givePermissionTo('SystemLog');

        $role3 = Role::create(['guard_name' => 'admin', 'name' => '超级管理员']);
        // gets all permissions via Gate::before rule; see AuthServiceProvider

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Example',
            'email' => 'test@example.com',
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'ExampleAdmin',
            'email' => 'admin@example.com',
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'ExampleSuperAdmin',
            'email' => 'superadmin@example.com',
        ]);
        $user->assignRole($role3);
    }
}
