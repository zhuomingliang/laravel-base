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
        Schema::create('tail_navigation', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->integer('views')->default(0)->comment('访问次数');
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
        Schema::dropIfExists('tail_navigation');
    }
};