<?php

namespace App\Http\Controllers\Events;

use App\Models\Event\Event;
use App\Models\UploadImages;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use File;

class EventUploadImagesController extends Controller
{
    public function uploadImagesBlueImp(Request $request)
    {

        $image_class = new UploadImages();

        $files = Input::file('files');
        $data = [];

        $event_id = intval(Input::get('event_id'));

        $event = Event::find($event_id);
        $event_name = $event->title . '-' . Carbon::createFromFormat('Y-m-d H:i:s',$event->event_from)->format('d.m.Y');

        $data['event_id'] = $event_id;

        $res = $image_class->procesImageBlueImp($files, 'event_images', $data['event_id'], $event_name, $data);

        Log::info('upload_blue_imp', ['object' => $res] );

        // errors, no uploaded file
        return response()->json(['files' => $res ]);


    }
}
