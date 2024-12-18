<?php

namespace App\Services\Moodle;

use App\Models\Program;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class StudentService extends BaseService
{
    protected $model = Student::class;


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create Subject On Moodle
     */
    public function store(Student $student, $password)
    {
        // Create the data array for the POST request
        $users = [
            [
                'username' => generate_moodle_username($student->first_name, $student->last_name), //  username
                'firstname' => $student->first_name, //  first name
                'password' => $password,
                'lastname' => $student->last_name, //  last name
                'email' => $student->email, //  email
                'country' => $student->country ?? "", // Optional home country code
                'middlename' => $student->father_name ?? "", // Optional
                // 'institution' => 'Example Institution', // Optional
                'department' => $student->program->title, // Optional
                'phone1' => $student->phone, // Optional
                'address' => $student->present_address, // Optional
                'lang' => 'en', // Default language
                'idnumber' => $student->id
            ]
        ];

        $query_params['users'] = $users;
        $query_params['wsfunction'] = 'core_user_create_users';

        return parent::create($query_params);
    }


    /**
     * Update Subject On Moodle
     */
    public function edit(Student $student, $suspended = false)
    {
        // Create the data array for the POST request
        $users = [
            [
                'id' => $student->id_on_moodle,
                'username' => generate_moodle_username($student->first_name, $student->last_name), //  username
                'firstname' => $student->first_name, //  first name
                'lastname' => $student->last_name, //  last name
                'email' => $student->email, //  email
                'country' => $student->country ?? "", // Optional home country code
                'middlename' => $student->father_name ?? "", // Optional
                // 'institution' => 'Example Institution', // Optional
                'department' => $student->program->title, // Optional
                'phone1' => $student->phone, // Optional
                'address' => $student->present_address, // Optional
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
    public function changePassword(Student $student, $password)
    {
        // Create the data array for the POST request
        $users = [
            [
                'id' => $student->id_on_moodle,
                'username' => generate_moodle_username($student->first_name, $student->last_name), //  username
                'firstname' => $student->first_name, //  first name
                'lastname' => $student->last_name, //  last name
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
    public function destroy(Student $student)
    {
        // Create the data array for the POST request
        $user_ids = [
            (int) $student->id_on_moodle,
        ];
        $query_params['userids'] = $user_ids;
        $query_params['wsfunction'] = 'core_user_delete_users';
        return parent::delete($query_params);
    }
}
