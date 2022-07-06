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
        Schema::create('main_menu', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('名称');
            $table->smallInteger('order')->default(1)->comment('顺序');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('main_menu');
    }
};
