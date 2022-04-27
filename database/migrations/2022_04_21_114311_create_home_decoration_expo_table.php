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
        Schema::create('home_decoration_expo', function (Blueprint $table) {
            $table->id();
            $table->string('daterange')->comment('时间范围');
            $table->string('title')->unique()->comment('家博会主题');
            $table->text('description')->nullable()->comment('家博会简介内容');
            $table->boolean('status')->default(true)->comment('状态');
            $table->softDeletes('deleted_at', 0)->comment('软删除');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });


        DB::statement('
            ALTER TABLE home_decoration_expo
            ALTER COLUMN "daterange" TYPE daterange USING daterange::daterange;
        ');

        // 两个家博会之间的时间范围不可冲突
        DB::statement('
            ALTER TABLE home_decoration_expo
            ADD EXCLUDE USING GIST ("daterange" WITH &&);
        ');

        DB::statement("COMMENT ON TABLE home_decoration_expo IS '家博会'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('home_decoration_expo');
    }
};
