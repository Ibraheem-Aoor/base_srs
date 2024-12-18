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
            $table->enum('subject_type' , ['Compulsory' , 'Elective']);
            $table->unsignedBigInteger('exam_type_category_id')->nullable();
            $table->foreign('exam_type_category_id')->references('id')->on('exam_type_categories')->onDelete('cascade');
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
            $table->dropColumn(['subject_type']);
            $table->dropForeign('exam_type_category_id');
        });
    }
};
