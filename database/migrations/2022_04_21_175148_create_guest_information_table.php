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
        Schema::create('guest_information', function (Blueprint $table) {
            $table->id();
            $table->integer('home_decoration_expo_id')->comment('家博会 ID');
            $table->string('full_name')->comment('姓名');
            $table->string('phone')->comment('手机号');
            $table->string('from')->comment('添加渠道');
            $table->string('uniq_no')->nullable()->comment('唯一标识');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');

            $table->unique(['full_name', 'phone']);
        });

        DB::statement("COMMENT ON TABLE guest_information IS '嘉宾信息'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('guest_information');
    }
};
