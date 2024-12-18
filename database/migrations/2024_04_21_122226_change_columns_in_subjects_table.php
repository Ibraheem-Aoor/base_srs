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
        Schema::table('subjects', function (Blueprint $table) {
            $table->integer('subject_type')->nullable()->change();
            $table->integer('class_type')->nullable()->change();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->tinyInteger('subject_type')->default('1')->comment('0 Optional & 1 Compulsory');
            $table->tinyInteger('class_type')->default('1')->comment('1 Theory, 2 Practical & 3 Both');
        });
    }
};
