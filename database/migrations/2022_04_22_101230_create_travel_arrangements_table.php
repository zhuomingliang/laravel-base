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
        Schema::create('travel_arrangements', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('日期');
            $table->jsonb('scheduling')->comment('行程安排');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE travel_arrangements IS '行程安排'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('travel_arrangements');
    }
};
