<?php

namespace App\Http\Controllers\Admin\AcademySite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\GetRecordingsParameters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use stdClass;
use Throwable;
use Yoeunes\Toastr\Facades\Toastr;

class ContactController extends Controller
{
    protected $db;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = __('module_academy_site') . ' - ' . __('contacts');
        $this->route = 'admin.academy_site.contacts';
        $this->view = 'admin.academy_site.';
        $this->path = 'Academy Site - Contacts';
        $this->access = 'academy_site';
        $this->db  = DB::connection('academy_site');
        // $this->middleware('permission:' . $this->access . '-view', ['only' => ['index']]);
    }


    /**
     * Display Contacts Sned To Academy Site.
     */
    public function index()
    {
        try {
            $data['title'] = $this->title;
            $data['route'] = $this->route;
            $data['path'] = $this->path;
            $data['rows'] = $this->db->table('contacts')->get();
            return view($this->view . 'contacts.index', $data);
        } catch (Throwable $e) {
            info('Error in ' . __METHOD__ . ' : ' . $e->getMessage());
            Toastr::error(__('general_error'));
            return back();
        }
    }

}
