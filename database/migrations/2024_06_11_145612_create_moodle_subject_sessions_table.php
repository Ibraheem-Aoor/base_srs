<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moodle_subject_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('id_on_moodle');
            $table->unsignedInteger('subject_id')->nullable();
            $table->unsignedInteger('session_id')->nullable();
            $table->unique(['subject_id', 'session_id']);
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
        Schema::dropIfExists('moodle_subject_sessions');
    }
};
