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
        Schema::create('hotel_information', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('酒店名');
            $table->string('address')->comment('地址');
            $table->string('telephone')->comment('前台电话');
            $table->string('wifi_password')->nullable()->comment('WIFI密码');
            $table->string('breakfast_information')->nullable()->comment('早餐时间地点信息');
            $table->string('video')->nullable()->comment('宣传视频');
            $table->string('liaison')->nullable()->comment('总联络人姓名');
            $table->string('liaison_phone')->nullable()->comment('总联络人电话');
            $table->string('director')->nullable()->comment('酒店负责人');
            $table->string('director_phone')->nullable()->comment('酒店负责人电话');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE hotel_information IS '酒店信息'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('hotel_information');
    }
};
