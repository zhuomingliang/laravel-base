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
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamps();
        });
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
