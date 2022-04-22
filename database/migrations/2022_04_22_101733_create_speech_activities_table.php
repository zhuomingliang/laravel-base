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
        Schema::create('speech_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('home_decoration_expo_id')->comment('家博会 ID');
            $table->text('title')->comment('主题');
            $table->date('date')->comment('日期');
            $table->time('time')->comment('时间');
            $table->text('place')->comment('地点');
            $table->text('host')->comment('主持人');
            $table->text('guest')->comment('嘉宾');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE speech_activities IS '演讲活动'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('speech_activities');
    }
};
