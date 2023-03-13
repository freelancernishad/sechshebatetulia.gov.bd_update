<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTikaLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tika_logs', function (Blueprint $table) {
            $table->id();
            $table->string('applicantId')->nullable();
            $table->string('tikaname')->nullable();
            $table->string('tikadose')->nullable();
            $table->string('kendro_name')->nullable();
            $table->string('kormir_name')->nullable();
            $table->string('tikaDate')->nullable();
            $table->string('nextTikaDate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tika_logs');
    }
}
