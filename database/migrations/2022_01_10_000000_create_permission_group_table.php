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
        PermissionGroup::create(['id' => 1, 'name' => '系统管理']);
        PermissionGroup::create(['id' => 2, 'name' => '内容管理']);
        PermissionGroup::create(['id' => 3, 'name' => '首页管理']);
        PermissionGroup::create(['id' => 4, 'name' => '导航栏管理']);
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
