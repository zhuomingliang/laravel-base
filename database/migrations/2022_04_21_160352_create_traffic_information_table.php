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
        Schema::create('traffic_information', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('类型');
            $table->string('title')->comment('标题');
            $table->text('pictures')->comment('图片');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement('ALTER TABLE traffic_information ALTER COLUMN pictures TYPE text[] USING ARRAY[pictures]');
        DB::statement('ALTER TABLE traffic_information ALTER COLUMN pictures SET DEFAULT \'{}\'');

        DB::statement("COMMENT ON TABLE vehicle_safeguard IS '交通信息'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('traffic_information');
    }
};
