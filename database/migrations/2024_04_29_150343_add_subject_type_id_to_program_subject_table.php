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
        Schema::table('program_subject', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_type_id')->nullable();
            $table->foreign('subject_type_id')->references('id')->on('subject_types')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('program_subject', function (Blueprint $table) {
            $table->dropForeign('subject_type_id');
        });
    }
};
