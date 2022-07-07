<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class CreatePermissionTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $teams = config('permission.teams');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
        if ($teams && empty($columnNames['team_foreign_key'] ?? null)) {
            throw new \Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        Schema::create($tableNames['permissions'], function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('pg_id')->default(0);
            $table->string('icon', 32)->default('')->comment('图标');
            $table->string('cname', 32)->comment('权限中文名称');
            $table->string('name')->comment('权限名称，用于权限验证');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->string('description')->default('')->comment('描述');
            $table->smallInteger('sequence')->default(0)->comment('顺序');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) { // permission.testing is a fix for sqlite testing
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name')->comment('角色名称');       // For MySQL 8.0 use string('name', 125);
            $table->string('guard_name'); // For MySQL 8.0 use string('guard_name', 125);
            $table->string('description')->default('')->comment('描述');
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [PermissionRegistrar::$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $teams) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');
            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [PermissionRegistrar::$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->unsignedBigInteger(PermissionRegistrar::$pivotPermission);
            $table->unsignedBigInteger(PermissionRegistrar::$pivotRole);

            $table->foreign(PermissionRegistrar::$pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign(PermissionRegistrar::$pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([PermissionRegistrar::$pivotPermission, PermissionRegistrar::$pivotRole], 'role_has_permissions_permission_id_role_id_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));

        $this->insertMenu();
    }

    private function insertMenu() {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '用户管理', 'name' => 'user']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '创建用户', 'name' => 'user/postIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '修改用户', 'name' => 'user/putIndex']);

        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '角色管理', 'name' => 'role']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '权限管理', 'name' => 'permission']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '系统日志', 'name' => 'SystemLog']);

        // 导航栏
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '导航栏', 'name' => 'Navigation']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '新增导航栏', 'name' => 'Navigation/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改导航栏', 'name' => 'Navigation/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '删除导航栏', 'name' => 'Navigation/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '获取一级导航栏', 'name' => 'Navigation/getMainMenu']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '新增一级导航栏', 'name' => 'Navigation/postMainMenu']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改一级导航栏', 'name' => 'Navigation/putMainMenu']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改一级导航栏2', 'name' => 'Navigation/putMainMenu2']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '获取二级导航栏', 'name' => 'Navigation/getSubMenuByMainMenuId']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '新增二级导航栏', 'name' => 'Navigation/postSubMenu']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改二级导航栏', 'name' => 'Navigation/putSubMenu']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改一级导航栏顺序', 'name' => 'Navigation/putMainOrder']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改二级导航栏顺序', 'name' => 'Navigation/putSubOrder']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改一级导航栏状态', 'name' => 'Navigation/PutMainMenuStatus']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改二级导航栏状态', 'name' => 'Navigation/PutSubMenuStatus']);

        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '尾部导航栏', 'name' => 'TailNavigation']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '新增尾部导航栏', 'name' => 'TailNavigation/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '修改尾部导航栏', 'name' => 'TailNavigation/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 4, 'cname' => '删除尾部导航栏', 'name' => 'TailNavigation/DeleteIndex']);

        // 网站内容
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '网站内容', 'name' => 'Content']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '新增内容', 'name' => 'Content/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '修改内容', 'name' => 'Content/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '删除内容', 'name' => 'Content/DeleteIndex']);

        // 首页模块管理
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '首页模块管理', 'name' => 'Homepage']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增首页模块', 'name' => 'Homepage/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改首页模块', 'name' => 'Homepage/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除首页模块', 'name' => 'Homepage/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改顺序', 'name' => 'Homepage/putOrder']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'Homepage/putStatus']);

        // 轮播图
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '轮播图', 'name' => 'Carsoul']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增轮播图', 'name' => 'Carsoul/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改轮播图', 'name' => 'Carsoul/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除轮播图', 'name' => 'Carsoul/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改顺序', 'name' => 'Carsoul/putOrder']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'Carsoul/putStatus']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
