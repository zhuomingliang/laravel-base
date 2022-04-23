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
        Schema::create('dining_arrangements', function (Blueprint $table) {
            $table->id();
            $table->integer('home_decoration_expo_id')->comment('家博会 ID');
            $table->date('date')->unique()->comment('日期');
            $table->string('breakfast_place')->nullable()->comment('早餐地点');
            $table->string('breakfast_picture')->nullable()->comment('早餐桌次安排图');
            $table->string('lunch_place')->nullable()->comment('午餐地点');
            $table->string('lunch_picture')->nullable()->comment('午餐桌次安排图');
            $table->string('dinner_place')->nullable()->comment('晚餐地点');
            $table->string('dinner_picture')->nullable()->comment('晚餐桌次安排图');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE dining_arrangements IS '用餐安排'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('dining_arrangements');
    }
};
