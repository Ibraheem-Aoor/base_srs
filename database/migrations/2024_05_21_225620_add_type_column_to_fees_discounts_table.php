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
        Schema::table('fees_discounts', function (Blueprint $table) {
            $table->string('discount_type')->default('discount')->after('title')->comment('Discounted amount might be flagged as grant or discount for the users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_discounts', function (Blueprint $table) {
            $table->dropColumn('discount_type');
        });
    }
};
