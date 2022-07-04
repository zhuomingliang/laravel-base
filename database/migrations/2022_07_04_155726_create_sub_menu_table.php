<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sub_menu', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('main_menu_id')->comment('主目录 ID');
            $table->string('name')->unique()->comment('名称');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sub_menu');
    }
};
