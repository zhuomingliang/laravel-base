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
        Schema::create('carsoul', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('position')->comment('位置');
            $table->string('image');
            $table->string('link');
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
        Schema::dropIfExists('carsoul');
    }
};
