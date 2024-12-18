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
        Schema::table('enroll_subjects', function (Blueprint $table) {
            $table->integer('semester_id')->unsigned()->nullable()->change();
            $table->integer('session_id')->unsigned()->nullable()->after('program_id');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enroll_subjects', function (Blueprint $table) {
            $table->dropForeign('session_id');
        });
    }
};
