<?php
namespace App\Services\Moodle;

use App\Models\Program;
use App\Models\Session;
use App\Models\StudentEnroll;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class RoleService extends BaseService
{
    /**
     * Used To Get Roles From moodle.
     *
     * get array of categories "Programs"
     */
    public function getAllRoles()
    {
        #enrol_manual_enrol_users
        $query_params['wsfunction'] = 'local_wsgetroles_get_roles';
        return parent::get($query_params);
    }
    /**
     * Get Student Role Id From Moodle
     */
    public function getStudentRoleId()
    {
        $roles = $this->getAllRoles();
        if(is_array($roles))
        {
            foreach ($roles as $role) {
                if (@$role['shortname'] == 'student' || @$role['archetype'] == 'student') {
                    return $role['id'];
                }
            }
        }
        throw new \Exception('Student Role On Not Found');
    }
}
