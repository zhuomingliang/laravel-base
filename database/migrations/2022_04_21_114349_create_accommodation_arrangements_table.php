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
        Schema::create('accommodation_arrangements', function (Blueprint $table) {
            $table->id();
            $table->integer('home_decoration_expo_id')->comment('家博会 ID');
            $table->string('hotel')->unique()->comment('酒店名');
            $table->jsonb('storey_info')->default('{}')->comment('楼层信息');
            $table->string('contacts')->nullable()->comment('联系人');
            $table->string('contact_telephone')->nullable()->comment('午餐桌次安排图');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE dining_arrangements IS '住宿安排'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('accommodation_arrangements');
    }
};
