<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\EpidemicPreventionInstructions;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('epidemic_prevention_instructions', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('内容');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('创建时间');
            $table->timestamp('updated_at')->default(DB::raw('LOCALTIMESTAMP(0)'))->comment('更新时间');
        });

        DB::statement("COMMENT ON TABLE epidemic_prevention_instructions IS '防疫信息'");

        EpidemicPreventionInstructions::create(['content' => '内容']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('epidemic_prevention_instructions');
    }
};
