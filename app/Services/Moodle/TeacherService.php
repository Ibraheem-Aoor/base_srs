<?php

namespace App\Services\Moodle;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class TeacherService extends BaseService
{
    protected $model = User::class;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create teacher On Moodle
     */
    public function store(User $user, $password)
    {
        // Create the data array for the POST request
        $users = [
            [
                'username' => generate_moodle_username($user->first_name, $user->last_name), //  username
                'firstname' => $user->first_name, //  first name
                'password' => $password,
                'lastname' => $user->last_name, //  last name
                'email' => $user->email, //  email
                'country' => $user->country ?? "", // Optional home country code
                'middlename' => $user->father_name ?? "", // Optional
                // 'institution' => 'Example Institution', // Optional
                'department' => $user->department_id, // Optional
                'phone1' => $user->phone, // Optional
                'address' => $user->present_address, // Optional
                'lang' => 'en', // Default language
                'idnumber' => $user->id
            ]
        ];

        $query_params['users'] = $users;
        $query_params['wsfunction'] = 'core_user_create_users';

        return parent::create($query_params);
    }
}
