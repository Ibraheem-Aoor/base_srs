<?php

namespace Tests\Feature\Moodle;

use App\Services\Moodle\RoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class RoleModuleTest extends TestCase
{
    use RefreshDatabase;
    protected $role_moodle_service;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up the Moodle configuration for the test
        Config::set('moodle.host', env('MOODLE_HOST'));
        Config::set('moodle.token', env('MOODLE_TOKEN'));
        Config::set('moodle.webservice_endpoint', env('MOODLE_WEBSERVICE_ENDPOINT'));
        Config::set('moodle.timeout', env('MOODLE_TIMEOUT', 180));
        Config::set('moodle.uri', 'webservice/rest/server.php');

        $this->role_moodle_service = new RoleService();
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_roles_exists_on_moodle()
    {
        $roles = $this->role_moodle_service->getAllRoles();
        $this->assertIsArray($roles);
    }
}
