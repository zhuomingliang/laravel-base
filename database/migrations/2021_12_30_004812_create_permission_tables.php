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
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '用户管理', 'name' => 'user/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '创建用户', 'name' => 'user/postIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '修改用户', 'name' => 'user/putIndex']);

        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '角色管理', 'name' => 'role/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '权限管理', 'name' => 'permission/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 1, 'cname' => '系统日志', 'name' => 'SystemLog/getIndex']);

        // 嘉宾信息
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '嘉宾信息', 'name' => 'GuestInformation/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '新增嘉宾', 'name' => 'GuestInformation/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '数据导入', 'name' => 'GuestInformation/PostImport']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '修改嘉宾', 'name' => 'GuestInformation/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 2, 'cname' => '删除嘉宾', 'name' => 'GuestInformation/DeleteIndex']);

        // 首页信息菜单，不需要控制器和方法代码，用来给前端判断是否有这个权限
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '首页信息', 'name' => 'IndexInformation/getIndex']);

        // 用餐安排
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '用餐安排', 'name' => 'DiningArrangements/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增用餐', 'name' => 'DiningArrangements/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '数据导入', 'name' => 'DiningArrangements/PostImport']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改用餐', 'name' => 'DiningArrangements/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除用餐', 'name' => 'DiningArrangements/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'DiningArrangements/PutStatus']);

        // 住宿安排
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '住宿安排', 'name' => 'AccommodationArrangements/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增住宿', 'name' => 'AccommodationArrangements/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '数据导入', 'name' => 'AccommodationArrangements/PostImport']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改住宿', 'name' => 'AccommodationArrangements/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除嘉宾', 'name' => 'AccommodationArrangements/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'AccommodationArrangements/PutStatus']);

        // 乘车安排
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '乘车安排', 'name' => 'RideArrangements/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增车次', 'name' => 'RideArrangements/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '数据导入', 'name' => 'RideArrangements/PostImport']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改车次', 'name' => 'RideArrangements/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除车次', 'name' => 'RideArrangements/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'RideArrangements/PutStatus']);

        // 行程安排
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '行程安排', 'name' => 'TravelArrangements/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增行程', 'name' => 'TravelArrangements/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改行程', 'name' => 'TravelArrangements/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除行程', 'name' => 'TravelArrangements/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'TravelArrangements/PutStatus']);

        // 演讲活动
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '演讲活动', 'name' => 'SpeechActivities/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增演讲', 'name' => 'SpeechActivities/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改演讲', 'name' => 'SpeechActivities/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除演讲', 'name' => 'SpeechActivities/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'SpeechActivities/PutStatus']);

        // 本地信息
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '本地信息', 'name' => 'LocalInformation/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增信息', 'name' => 'LocalInformation/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改信息', 'name' => 'LocalInformation/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除信息', 'name' => 'LocalInformation/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'LocalInformation/PutStatus']);

        // 展会信息
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '展会信息', 'name' => 'HomeDecorationExpo/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增展会', 'name' => 'HomeDecorationExpo/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改展会', 'name' => 'HomeDecorationExpo/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除展会', 'name' => 'HomeDecorationExpo/DeleteIndex']);

        // 宣传片
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '宣传片列表', 'name' => 'AdvertisingVideo/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增宣传片', 'name' => 'AdvertisingVideo/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改宣传片', 'name' => 'AdvertisingVideo/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除宣传片', 'name' => 'AdvertisingVideo/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态',   'name' => 'HomeDecorationExpo/PutStatus']);

        // 文件信息
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '文件列表', 'name' => 'FileInformation/getIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '新增文件', 'name' => 'FileInformation/PostIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改文件', 'name' => 'FileInformation/PutIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '删除文件', 'name' => 'FileInformation/DeleteIndex']);
        Permission::create(['guard_name' => 'admin', 'pg_id' => 3, 'cname' => '修改状态', 'name' => 'FileInformation/PutStatus']);
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
