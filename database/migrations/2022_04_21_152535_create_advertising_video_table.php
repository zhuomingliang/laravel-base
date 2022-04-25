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
        Schema::create('advertising_video', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->text('video')->comment('视频');
            $table->integer('sort')->comment('排序');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE advertising_video IS '宣传片'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('advertising_video');
    }
};
