<?php

namespace App\Services\Moodle;

use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class UserService extends BaseService
{
    protected $model = User::class;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create Subject On Moodle
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


    /**
     * Update Subject On Moodle
     */
    public function edit(User $user, $suspended = false)
    {
        // Create the data array for the POST request
        $users = [
            [
                'id' => $user->id_on_moodle,
                'username' => generate_moodle_username($user->first_name, $user->last_name), //  username
                'firstname' => $user->first_name, //  first name
                'lastname' => $user->last_name, //  last name
                'email' => $user->email, //  email
                'country' => $user->country ?? "", // Optional home country code
                'middlename' => $user->father_name ?? "", // Optional
                // 'institution' => 'Example Institution', // Optional
                'department' => $user->program->title, // Optional
                'phone1' => $user->phone, // Optional
                'address' => $user->present_address, // Optional
                'suspended' => $suspended,
                'lang' => 'en', // Default language
            ]
        ];

        $query_params['users'] = $users;
        $query_params['wsfunction'] = 'core_user_update_users';

        return parent::update($query_params);
    }


    /**
     * Change Student Moodle Password
     */
    public function changePassword(User $user, $password)
    {
        // Create the data array for the POST request
        $users = [
            [
                'id' => $user->id_on_moodle,
                'username' => generate_moodle_username($user->first_name, $user->last_name), //  username
                'firstname' => $user->first_name, //  first name
                'lastname' => $user->last_name, //  last name
                'password' => $password,
            ]
        ];

        $query_params['users'] = $users;
        $query_params['wsfunction'] = 'core_user_update_users';
        return parent::update($query_params);
    }
    /**
     * Delete Student On Moodle.
     */
    public function destroy(User $user)
    {
        // Create the data array for the POST request
        $user_ids = [
            (int) $user->id_on_moodle,
        ];
        $query_params['userids'] = $user_ids;
        $query_params['wsfunction'] = 'core_user_delete_users';
        return parent::delete($query_params);
    }
}
