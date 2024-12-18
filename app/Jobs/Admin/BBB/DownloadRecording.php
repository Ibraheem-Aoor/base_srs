<?php

namespace App\Jobs\Admin\BBB;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DownloadRecording implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $meeting_id, $recording_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($meeting_id, $recording_id)
    {
        $this->meeting_id = $meeting_id;
        $this->recording_id = $recording_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {

            info('START DOWNLOAD RECORDING. MEETING ID: ' . $this->meeting_id);
            $recording_id = $this->recording_id;
            $meeting_id = $this->meeting_id;
         
            // URLs for the deskshare and webcam videos
            $deskshareUrl = "https://bbb.academy-uk.net/presentation/{$recording_id}/deskshare/deskshare.mp4";
            $webcamUrl = "https://bbb.academy-uk.net/presentation/{$recording_id}/video/webcams.mp4";


            // Download both videos asynchronously
            $deskshareResponse = Http::withoutVerifying()->timeout(100000)->get($deskshareUrl);
            $webcamResponse = Http::withoutVerifying()->timeout(100000)->get($webcamUrl);

            // Save both files to the storage directory
            $deskshareFileName = "deskshare.mp4";
            $webcamFileName = "webcam.mp4";

            Storage::disk('public')->put("bbb/{$meeting_id}/{$recording_id}/{$deskshareFileName}", $deskshareResponse->body());
            Storage::disk('public')->put("bbb/{$meeting_id}/{$recording_id}/{$webcamFileName}", $webcamResponse->body());

            // Return the paths to the downloaded videos
            $desksharePath = storage_path("app/merged_videos/{$deskshareFileName}");
            $webcamPath = storage_path("app/merged_videos/{$webcamFileName}");
            info('FINISH DOWNLOAD RECORDING. MEETING ID: ' . $this->meeting_id);
            info('PATHS: 1-deskshare: ' . $desksharePath . '---- 2-Webcam: ' . $webcamPath);
        } catch (Throwable $e) {
            info('ERROR IN ' . __METHOD__ . 'IN: ' . get_class($this));
            info($e);
        }
    }
}
