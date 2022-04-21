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
        Schema::create('ride_arrangement', function (Blueprint $table) {
            $table->id();
            $table->integer('home_decoration_expo_id')->comment('家博会 ID');
            $table->string('auto_no')->comment('车次');
            $table->string('license_plate_number')->nullable()->comment('车牌号');
            $table->string('driver')->nullable()->comment('司机');
            $table->string('driver_phone')->nullable()->comment('司机电话号码');
            $table->string('commentator')->nullable()->comment('讲解员');
            $table->string('commentator_phone')->nullable()->comment('讲解员电话号码');
            $table->string('attendants')->nullable()->comment('服务员');
            $table->string('attendants_phone')->nullable()->comment('服务员电话号码');
            $table->boolean('status')->default(true)->comment('状态');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE dining_arrangements IS '乘车安排'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('ride_arrangement');
    }
};
