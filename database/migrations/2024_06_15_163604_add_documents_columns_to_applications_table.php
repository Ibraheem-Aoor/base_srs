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
    public function up()
    {
        Schema::table('applications', function (Blueprint $table) {
            // For DM
            $table->text('id_or_passport')->nullable();
            $table->text('letter_of_intrest')->nullable();
            $table->text('proof_master_in_en')->nullable();
            $table->text('certificate_of_recommendation')->nullable();
            //for dm-master-bachelor
            $table->text('grade_transcripts');
            $table->text('previous_certificates');
            // for bachelor only
            $table->text('english_certificate')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'id_or_passport',
                'letter_of_intrest',
                'proof_master_in_en',
                'grade_transcripts',
                'previous_certificates',
                'english_certificate'
            ]);
        });
    }
};
