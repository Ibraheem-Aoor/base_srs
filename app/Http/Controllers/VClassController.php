<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\BBBhelper;
use App\Models\ClassRoutine;
use Illuminate\Http\Request;
use App\Models\District;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Enum\Role;
use BigBlueButton\Parameters\CreateMeetingParameters;
use BigBlueButton\Parameters\DeleteRecordingsParameters;
use BigBlueButton\Parameters\GetMeetingInfoParameters;
use BigBlueButton\Parameters\GetRecordingsParameters;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use stdClass;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VClassController extends Controller
{

    public function createMeetingParamsFromRoutine($routineId)
    {
        $result = new stdClass();

        $result->routine = ClassRoutine::find($routineId);

        if (!$result->routine)
            return false;

        $result->meetingName = $result->routine->room->title . ' - ' . $result->routine->teacher->first_name . ' ' . $result->routine->teacher->last_name . ' presenting ' . $result->routine->subject->code;
        // $meetingID = $classRoutine->program->id.'-'.$classRoutine->session->id.'-'.$classRoutine->semester->id.'-'.$classRoutine->section->id.'-'.$classRoutine->teacher->id.'-'.$classRoutine->subject->code.'-'.$classRoutine->room->id;
        // $result->meetingID = $result->routine->session->id.'-'.$result->routine->semester->id.'-'.$result->routine->teacher->id.'-'.$result->routine->subject->code.'-'.$result->routine->room->id.'-'.$result->routine->day.'-'.$result->routine->start_time.'-'.$result->routine->end_time;
        $result->meetingID = $result->routine->session->id . '-' . $result->routine->semester->id . '-' . $result->routine->teacher->id . '-' . $result->routine->subject->code . '-' . $result->routine->room->id;

        return $result;
    }

    public function joinMeeting($routineId)
    {
        //  return $this->viewRecordings($routineId);

        $meetingParams = $this->createMeetingParamsFromRoutine($routineId);
        if (!$meetingParams)
            return "Invalid routine";

        $bbb = new BigBlueButton();

        $getMeetingInfoParams = new GetMeetingInfoParameters($meetingParams->meetingID, null);
        $response = $bbb->getMeetingInfo($getMeetingInfoParams);
        switch ($response->getReturnCode()) {
            case 'SUCCESS':
                // $meetingPassword = $response->getMeeting()->getAttendeePassword();
                break;
            case 'FAILED':
                $createMeetingParams = new CreateMeetingParameters($meetingParams->meetingID, $meetingParams->meetingName);
                // $createMeetingParams->setAttendeePassword($attendee_password);
                // $createMeetingParams->setModeratorPassword($moderator_password);
                // $createMeetingParams->setDuration($duration);
                // $createMeetingParams->setLogoutUrl($urlLogout);
                // $createMeetingParams->setMeetingExpireIfNoUserJoinedInMinutes(5);
                // $createMeetingParams->setMeetingExpireWhenLastUserLeftInMinutes(5);
                $createMeetingParams->setRecord(true);
                $createMeetingParams->setAllowStartStopRecording(true);
                $createMeetingParams->setAutoStartRecording(false);

                $response = $bbb->createMeeting($createMeetingParams);
                if ($response->getReturnCode() == 'FAILED') {
                    return $response->getMessage();
                }
                // $meetingPassword = $response->getAttendeePassword();
                break;
        }

        // if (empty($meetingPassword)){
        //     return "Invalid meeting password";
        // }

        $user = null;
        $role = null;
        if (Auth::guard('student')->check()) {
            $user = Auth::guard('student')->user();
            $role = Role::VIEWER;
        } elseif (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $role = Role::MODERATOR;
        }

        $joinMeetingParams = new JoinMeetingParameters(
            $meetingParams->meetingID,
            $user->first_name . ' ' . $user->last_name,
            $role
        );

        $joinMeetingParams->setRedirect(true);
        $meetingUrl = $bbb->getJoinMeetingURL($joinMeetingParams);

        return redirect($meetingUrl);
    }

    public function getClassRecordings($routineId)
    {
        $meetingParams = $this->createMeetingParamsFromRoutine($routineId);
        if (!$meetingParams)
            abort(404, "Invalid routine");

        return $this->getMeetingRecordings($meetingParams->meetingID);
    }

    public function getMeetingRecordings($meetingId)
    {

        $bbb = new BigBlueButton();

        $recordingParams = new GetRecordingsParameters();

        $subjectCode = null;
        if ($meetingId) {
            // Split the string by hyphens
            $parts = explode('-', $meetingId);

            // Get the second last element which is the subject code
            $subjectCode = $parts[count($parts) - 2];
            // $recordingParams->setMeetingId($meetingId);
        }

        $response = $bbb->getRecordings($recordingParams);


        if ($response->getReturnCode() != 'SUCCESS')
            abort(500);

        // $recording = $response->getRawXml()->recordings->recording;

        // $recordingUrl = $recording->playback->format->url;
        // return redirect($recordingUrl);

        $recordings = [];
        foreach ($response->getRawXml()->recordings->recording as $recording) {
            $rec = [
                'id' => $recording->recordID . '',
                'meetingId' => $recording->meetingID . '',
                'startTime' => date("Y-m-d H:i", intval($recording->startTime . '') / 1000),
                'endTime' => date("Y-m-d H:i", intval($recording->endTime . '') / 1000),
                'size' => AppHelper::humanFileSize(intval($recording->playback->format->size . '')),
                'url' => $recording->playback->format->url . '',
            ];
            // If we are searching for a recording. we will get all the subject recordings and show them to the student. regardless the session and sesmester.
            if (isset($subjectCode)) {
                if (strpos($rec['meetingId'], $subjectCode) !== false) {
                    $recordings[] = $rec;
                }
            }else{
                $recordings[] = $rec;
            }
        }
        if (is_array($recordings)) {
            usort($recordings, function ($a, $b) {
                return $b['startTime'] <=> $a['startTime'];
            });
        }
        return response()->json($recordings);
    }

    public function deleteRecording($recordingId)
    {

        if (Auth::guard('student')->check()) {
            return response()->json(false);
        }

        if (Auth::guard('web')->check() && !Auth::guard('web')->user()->is_admin) {
            return response()->json(false);
        }

        $bbb = new BigBlueButton();

        $deleteRecordingsParams = new DeleteRecordingsParameters($recordingId);
        $response = $bbb->deleteRecordings($deleteRecordingsParams);

        // if ($response->getReturnCode() == 'SUCCESS') {
        //     // recording deleted
        // } else {
        //     // something wrong
        // }

        return response()->json($response->getReturnCode() == 'SUCCESS');
    }

    public function downloadRecording($recordingId)
    {
        $downloadUrl = "https://bbb.academy-uk.net/presentation/$recordingId/video/webcams.mp4";

        $headers = array(
            'Content-Type' => 'video/mp4',
            'Content-Disposition: attachment; filename="presentation.mp4"'
        );

        $callback = function () use ($downloadUrl) {
            $file = fopen('php://output', 'w');
            fputs($file, file_get_contents($downloadUrl));
            fclose($file);
        };

        return response()->streamDownload($callback, "presentation.mp4", $headers)->send();
    }



    public function index()
    {
        $recordingParams = new GetRecordingsParameters();
        $bbb = new BigBlueButton();
        $response = $bbb->getRecordings($recordingParams);

        if ($response->getReturnCode() == 'SUCCESS') {
            dd($response->getRawXml()->recordings->recording, $response->getRawXml()->recordings);
            foreach ($response->getRawXml()->recordings->recording as $recording) {
                // process all recording
            }
        }
    }
}
