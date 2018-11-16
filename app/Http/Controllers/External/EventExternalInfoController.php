<?php

namespace App\Http\Controllers\External;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Setting\Industry;
use App\Models\Setting\Interest;
use App\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EventExternalInfoController extends Controller
{

    /**
     * EventExternalInfoController constructor.
     */
    public function __construct()
    {
        $this->hashid = new Hashids(env('HASH_SALT'), 10);
    }


    //zobrazenie formulara pre kupu listku k eventu
    public function showDetailInfo($event_hash)
    {

        $event_hash_decode = $this->hashid->decode($event_hash);

        if($event_hash_decode) {

            $event_detail = Event::find($event_hash_decode[0]);  // [0] id eventu

            if ($event_detail && $event_detail->event_to > Carbon::now()){

                $recipient = User::find($event_hash_decode[1]);  // [1] prijemca pozvanky
                $invite_person = User::find($event_hash_decode[2]);  // [2] pozyvajuci clen

                $event_class = new Event();

                $activities = DB::table('events_activities AS ea')
                    ->select('ea.id AS id', 'ea.description AS description', 'u.id AS user_id',
                        DB::raw( 'CONCAT(u.name, " ", u.surname) AS full_name'),
                        'eal.name as activity_name', 'eal.id AS activity_id')
                    ->join('users AS u', 'u.id', '=', 'ea.user_id')
                    ->join( 'events_activities_list AS eal', 'eal.id', 'ea.activity_id' )
                    ->where('ea.event_id', $event_detail->id )
                    ->orderByDesc('id')
                    ->get();

                //zoznam pozvanych hosti
                $attendance = $event_class->getEventAttendanceList( $event_detail->id);

                return view('external.event.event_info')
                    ->with('attendance', $attendance->where('status_id', 2)->sortBy('surname'))
                    ->with('activities', $activities)
                    ->with('recipient', $recipient)
                    ->with('event', $event_detail);

            }else{

                return view('external.error')->with('message', 'Udalosť neexistuje alebo sa uz konala v minulosti.');

            }

        }else{

            return view('external.error');

        }
    }

    // prihlaska za clena po podakovani
    public function applicationFormMember($guest)
    {

        $data = null;
        $user = null;

        $industry = Industry::orderBy('name')->where('active', 1)->get();
        $countries = DB::table('countries')->get();
        $clubs = Club::orderByDesc('active')->orderBy('short_title')->where('active', 1)->get();
        $interest = Interest::orderBy('name')->where('active', 1)->get();

        $event_hash_decode = $this->hashid->decode($guest);

        if($event_hash_decode) {

            $user = User::find($event_hash_decode[0]);

            dump($user);
            dump($event_hash_decode);
        }

        return view('external.application-form.member_add')
                    ->with('data', $data)
                    ->with('user', $user)
                    ->with('industry', $industry)
                    ->with('interest', $interest)
                    ->with('clubs', $clubs)
                    ->with('countries', $countries);

    }


    // prihlaska za clena po podakovani
    public function applicationFormMemberStore(Request $request)
    {

        $data['data'] = json_encode($request->except('_token'));
        $data['user_id'] = intval($request->user_id);


        DB::table('application_form')->insert($data);

        dd( json_encode($request->except('_token')) );

    }

}
