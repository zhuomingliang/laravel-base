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
        Schema::create('content', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sub_menu_id');
            $table->string('title')->comment('标题');
            $table->text('content')->comment('内容');
            $table->integer('views')->default(0)->comment('访问次数');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');

            $table->index(['sub_menu_id', 'status', 'deleted_at', 'created_at']);
            $table->index(['status', 'deleted_at', 'created_at']);
            $table->index('created_at');
        });

        \DB::select('CREATE EXTENSION IF NOT EXISTS pgroonga;');
        \DB::select('CREATE INDEX ON content USING pgroonga ((ARRAY[title, content]));');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('content');
    }
};
