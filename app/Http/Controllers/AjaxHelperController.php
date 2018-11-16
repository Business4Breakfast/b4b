<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Invitation\Invitation;
use App\Models\UploadFiles;
use App\Models\UploadImages;
use Carbon\Carbon;
use Doctrine\DBAL\Events;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function MongoDB\BSON\toJSON;

class AjaxHelperController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @param  Illuminate\Http\Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        if($request->ajax()){
            return "AJAX";
        }
        return "HTTP";
    }



    // funkcia odosiela kopie pozvanko z prezencnej listiny
    public function getFunction(Request $request)
    {

        // zmazanie event obrazku
        if(isset($request->action) && strcmp($request->action, "delete_image_event") == 0 ){

            if(isset($request->image_id)  && intval($request->image_id) > 0  ){

                $event_id = intval($request->image_id);
                $id = intval($request->image_id);

                $image_class = new UploadImages();
                $res = $image_class->deleteImage('event-images', $id);

                if ($res) DB::table('events_images')->delete($id);

            }

            return  response()->json(['status' => 'OK']);
        }


        // zobrazenie fotografii eventu
        if(isset($request->action) && strcmp($request->action, "get_event_images") == 0 ){

            if(isset($request->event)  && intval($request->event) > 0  ){

                $images = [];
                $images_raw = DB::table('events_images')->where('event_id', intval($request->event))->orderByDesc('id')->get();

                if ($images_raw){
                    foreach ($images_raw as $k => $img){
                        $images[$k] = $img;
                        $images[$k]->image_src = asset('/images/event-images/') . '/'. intval($request->event) .'/sq/'.  $img->image;
                    }
                }

                return  response()->json([ 'data' => $images]);
            }

        }


        // zmazanie suboru podla id (files)
        if(isset($request->action) && strcmp($request->action, "delete_uploaded_file") == 0 ){

            if(isset($request->file_id)  && intval($request->file_id) > 0  ){

                $id = intval($request->file_id);

                $file_class = new UploadFiles();

                $file_class->deleteFile($id);

            }

            return  response()->json(['status' => 'OK']);

        }



        // data vracia  uzivatelov kokretnej akcie alebo vsetkych clenov
        if(isset($request->action) && strcmp($request->action, "get_members_attend") == 0 ){

            //nastavenie datumu od do
            if(isset($request->reference_type)  && $request->reference_type > 0  ){

                $users = null;
                $event_class =  new Event();

                if(intval($request->reference_type) == 4 ) {

                    $users = DB::table('users')
                        ->select('id AS user_id',
                            DB::raw('CONCAT(name," ", surname ) AS user_name'))
                        ->where('admin', 1)
                        ->get();


                    $users_all = $users->map(function ($item) {
                        return ['user_id' => $item->user_id, 'user_name' => $item->user_name  ];
                    });

                    // zobrazime zoznam clenov danej udalosti
                    $users = $event_class->getEventUserAttendBalance($request->event_id, 1);

                    $users = $users->map(function ($item) {
                        return ['user_id' => $item->user_id, 'user_name' => $item->name . ' ' . $item->surname ];
                    });

                    $users = $users->merge($users_all);
                    $users = $users->unique();

                    //dump($users);s

                } else {

                    $users = null;

                    // zobrazime zoznam clenov danej udalosti
                    $users = $event_class->getEventUserAttendBalance($request->event_id, 1);

                    $users = $users->map(function ($item) {
                        return ['user_id' => $item->user_id, 'user_name' => $item->name . ' ' . $item->surname ];
                    });
                    $users = collect($users->toArray());
                }

                return response()->json($users);

            }

        }

        // odosleme kopie pozvanok hosti k udalosti
        if(isset($request->action) && strcmp($request->action, "send_invitation_multiplay") == 0 ){

            $invitation_class = new Invitation();

            $attendance = null;

            if(isset($request->attendance) && is_array($request->attendance)){
                foreach ($request->attendance as $a){

                    $attendance = DB::table('attendance_lists')->find($a);
                    if($attendance){
                        // odosleme email na zaklade prezencky
                        $invitation_class->addNotificationInvitationToCue( $attendance->user_id, $attendance->user_invite_id,
                            $attendance->event_id, $attendance->id, $attendance->invitation_type );
                    }
                }
            }

            return  response()->json(['status' => 'OK']);

        }



        $user_activity = null;

        // data kalendar udalaosti
        if(isset($request->action) && strcmp($request->action, "set_event_activity") == 0 ){

            $user_id = intval($request->user_id);
            $event_id = intval($request->event_id);
            $activity_type = intval($request->activity_type);
            $club_id = intval($request->club_id);

            $res = null;

            if($event_id > 0 && $activity_type > 0 && $club_id > 0){

                $user_activity = DB::table('events_activities')
                    ->select('id','user_id')
                    ->where('event_id', $event_id)
                    ->where('activity_id', $activity_type)
                    ->first();

                //zmazeme zaznam
                if ($user_id == 0) {

                    DB::table('events_activities')
                        ->where('event_id', $event_id)
                        ->where('activity_id', $activity_type)
                        ->delete();

                    $res = 'delete';

                }else{
                    //existuje zaznam
                    if($user_activity && $user_activity->user_id > 0){
                        if ($user_activity->user_id != $user_id){
                            //update zaznam
                            DB::table('events_activities')
                                ->where('id', $user_activity->id)
                                ->where('activity_id', $activity_type)
                                ->update( ['user_id' => $user_id]);
                            $res = 'update';
                        }

                    } else {
                        //insert
                       $new_id  = DB::table('events_activities')
                            ->insertGetId( ['user_id' => $user_id,
                                            'event_id' => $event_id,
                                            'activity_id' => $activity_type,
                                            'user_id_create' => Auth::user()->id,
                                            'club_id' => $club_id
                                            ]);

                                    $res = 'insert';

                    }
                }
            }

//          Log::info(print_r($request->all(), true));

            return response()->json($res);

        }


        // data kalendar udalaosti
        if(isset($request->action) && strcmp($request->action, "get_calendar_data") == 0 ){

            //nastavenie datumu od do
            if(isset($request->start) && isset($request->end)){

                $res = null;

                $events = Event::where('event_from', '>', Carbon::createFromFormat('Y-m-d', $request->start)->format('Y-m-d H:i:S'))
                                ->where('event_to', '<', Carbon::createFromFormat('Y-m-d', $request->end)->format('Y-m-d H:i:S'))
                                ->get();

                if($events){
                    foreach ($events as $k => $v){

                        $res[] = [
                            'title' => $v->club->short_title,
                            'id' => $v->id,
                            'start' => Carbon::createFromFormat('Y-m-d H:i:s', $v->event_from)->format('Y-m-d H:i:s'),
                            'end' => Carbon::createFromFormat('Y-m-d H:i:s', $v->event_to)->format('Y-m-d H:i:s'),
                            'description' => $v->title,
                            'backgroundColor' => $v->type->color,
                            'textColor' => 'white',
                            'borderColor' => $v->type->color,
                            'editable' => false
                        ];

                    }
                }
//
//              Log::info(print_r($request->all(), true));

                return response()->json($res);

            }

        }


        // data statistika graf
        if(isset($request->action) && strcmp($request->action, "get_event_stat_data") == 0 ){


            $date_from = Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d');
            $date_to = Carbon::createFromFormat('d.m.Y', $request->date_to)->format('Y-m-d');
            $club = $request->club_id;
            $event_type = $request->event_type;

            $club_class = new Club();

            $coupons = $club_class->referenceCouponsStat($date_from, $date_to, $club, $event_type);

            if($coupons){
                foreach ($coupons as $k => $v){

                    $res[] = [
                        'records' => $v->records,
                        'price' => $v->price,
                        'reference' => $v->reference,
                        'face_to_face' => $v->face_to_face,
                        'evidence' => $v->evidence,
                        'thank_you' => $v->thank_you,
                        'month' => $v->month,
                        'year' => $v->year
                    ];

                }
            }

            return response()->json($res);

        }


    }



}
