<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PermissionGroup;

class CreatePermissionGroupTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('名称');
            $table->timestamps();
        });

        $this->insertPermissionGroups();
    }

    public function insertPermissionGroups() {
        PermissionGroup::create(['pg_id' => 1, 'name' => '系统管理']);
        PermissionGroup::create(['pg_id' => 2, 'name' => '数据管理']);
        PermissionGroup::create(['pg_id' => 3, 'name' => '信息管理']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('permission_groups');
    }
}
