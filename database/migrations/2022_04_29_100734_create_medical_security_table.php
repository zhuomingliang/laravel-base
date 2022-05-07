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
        Schema::create('medical_security', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hotel_information_id')->comment('酒店信息 ID');
            $table->date('date')->comment('日期');
            $table->string('doctor')->nullable()->comment('保障医师');
            $table->string('doctor_phone')->nullable()->comment('保障医师电话');
            $table->string('doctor_address')->nullable()->comment('保障医师住宿酒店');
            $table->string('nurse')->nullable()->comment('保障护士');
            $table->string('nurse_phone')->nullable()->comment('保障护士电话');
            $table->string('nurse_address')->nullable()->comment('保障护士住宿酒店');
            $table->string('nucleic_acid_testing_address')->nullable()->comment('核酸检测地址');
            $table->string('isolation_address')->nullable()->comment('留观隔离地址');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');

            $table->unique(['hotel_information_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('medical_security');
    }
};
