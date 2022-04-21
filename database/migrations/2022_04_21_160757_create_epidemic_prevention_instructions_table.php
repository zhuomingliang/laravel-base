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
        Schema::create('epidemic_prevention_instructions', function (Blueprint $table) {
            $table->id();
            $table->text('content')->comment('内容');
        });

        DB::statement("COMMENT ON TABLE epidemic_prevention_instructions IS '防疫信息'");
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