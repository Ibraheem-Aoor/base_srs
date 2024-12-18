<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\AppHelper;
use App\Helpers\BBBhelper;
use App\Models\ClassRoutine;
use App\Models\District;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Enum\Role;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use Yoeunes\Toastr\Facades\Toastr;

class BigBlueButtonController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Module Data
        $this->title = __('module_bbb');
        $this->route = 'admin.bbb.';
        $this->view = 'admin.bbb.';
        $this->path = 'BigBlueButton';
        $this->access = 'bbb';


        $this->middleware('permission:' . $this->access . '-view', ['only' => ['index']]);
    }


    public function index()
    {
        try {
            $recordingParams = new GetRecordingsParameters();
            $bbb = new BigBlueButton();
            $bbb->setTimeOut(180);
            $response = $bbb->getRecordings($recordingParams);
            $data['title'] = $this->title;
            $data['route'] = $this->route;
            $data['path'] = $this->path;
            $data['rows'] = [];
            if ($response->getReturnCode() == 'SUCCESS') {
                $data['rows'] = $this->getRecordings($response);
            }
            return view($this->view . 'recordings.index', $data);
        } catch (Throwable $e) {
            info('Error in ' . __METHOD__ . ' : ' . $e->getMessage());
            Toastr::error(__('general_error'));
            return back();
        }
    }

    protected function getRecordings($response)
    {
        $recordings = [];
        if (Cache::has('bbb_recordings')) {
            $recordings = Cache::get('bbb_recordings');
        } else {
            foreach ($response->getRawXml()->recordings->recording as $recording) {
                $obj = new stdClass;
                $obj->name = (string) $recording->name;
                $obj->meetingId = isset($recording->metadata->meetingId) ? (string) $recording->metadata->meetingId : null;
                $obj->start_time = date('Y-m-d H:i:s', (int) ($recording->startTime / 1000));
                $obj->preview_img = isset($recording->playback->format->preview->images->image[0]) ? (string) $recording->playback->format->preview->images->image[0] : public_path('dashboard/images/placeholder.jpg');
                $obj->state = (string) $recording->state;
                $obj->playback_url = (string)$recording->playback?->format?->url ?? "";
                $recordings[] = $obj;
            }
            $recordings = cacheAndGet('bbb_recordings' , now()->addDay() , $recordings);
        }
        return $recordings;
    }
}
