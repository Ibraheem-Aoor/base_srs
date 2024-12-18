<?php

namespace Database\Seeders;

use DB;
use App\Models\SMSSetting;
use Illuminate\Database\Seeder;

class SMSSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('s_m_s_settings')->delete();

        $s_m_s_settings = SMSSetting::create([

            'nexmo_key'=>env('NEXMO_KEY'),
            'nexmo_secret'=>env('NEXMO_SECRET'),
            'nexmo_sender_name'=>'ABC',
            'twilio_sid'=>env('TWILIO_SID'),
            'twilio_auth_token'=>env('TWILIO_AUTH_TOKEN'),
            'twilio_number'=> env('TWILIO_NUMBER'),
            'status'=>'1',

        ]);
    }
}
