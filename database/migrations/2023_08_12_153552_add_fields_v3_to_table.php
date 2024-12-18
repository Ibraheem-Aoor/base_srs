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
        Schema::table('grades', function (Blueprint $table) {
            $table->text('remark')->nullable()->change();
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->date('date')->nullable()->change();
            $table->time('time')->nullable()->change();
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->string('reference')->nullable()->change();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->string('reference')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('remark');
        });

        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('time');
        });

        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn('reference');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};
