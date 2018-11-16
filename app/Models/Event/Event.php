<?php

namespace App\Models\Event;

use App\Models\Club;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    //

    public function type()
    {
        return $this->belongsTo('App\Models\Event\EventTypes','event_type','id');
    }


    public function club()
    {
        return $this->belongsTo('App\Models\Club','club_id','id');
    }


    public function getImageThumbAttribute() {

        if(strlen($this->image) > 0){
            $path = asset('images/event'). '/' . $this->id .'/sq/'.$this->image;
        } else {
            $path = asset('images/default'). '/sq/default_alpha.png';
        }

        return $path;
    }


//
//    $user_activity = DB::table('events_activities AS ea')
//    ->select('ea.id','ea.user_id', 'ea.event_id', 'ea.activity_id', 'ea.club_id',
//    'eal.name AS activity_name', 'u.name AS activity_user_name', 'u.surname AS activity_user_surname')
//    ->join('users AS u', 'u.id', '=', 'ea.user_id')
//    ->join('events_activities_list AS eal', 'eal.id', '=', 'ea.activity_id')
//    ->get();
//
//    dd($user_activity);

    // vrati nablizsi termin eventu klubu
    public function getEarliestEvent($club_id, $take=1)
    {
        $events = Event::where('event_from', '>', Carbon::now())
                ->where('club_id', $club_id )
                ->orderBy('event_from')
                ->first();

        return $events;

    }



    public function getEventActivity($event_id)
    {

        $event_id = intval($event_id);

        $activity = DB::table('events_activities AS ea')
            ->select('eal.id AS id', 'eal.name AS activity', 'u.name', 'u.surname')
            ->join('events_activities_list AS eal', 'ea.activity_id', 'eal.id')
            ->join('users AS u', 'u.id', '=', 'ea.user_id', 'LEFT' )
            ->where('event_id', $event_id )->get();

        return $activity;

    }


    //vrati udalosti na ktorych bol user
    public function getEventFromUsers($user_id=null, $event_type=null)
    {

        $user_id = intval($user_id);

        $query = DB::table('attendance_lists as ac')
                    ->select('ac.*', 'e.title', 'e.id AS event_id', 'e.event_from AS event_from',
                        'ac.user_status_id AS user_status_id', 'user_attend AS user_attend', 'event_from AS event_from',
                        'c.title AS club_title', 'us.status as user_status_attend');

        $query->join('events as e', 'e.id', '=', 'ac.event_id')
                ->join('clubs AS c', 'c.id', '=', 'e.club_id')
                ->join('event_types AS et', 'et.id', '=', 'e.event_type')
                ->join('user_status AS us', 'us.id', '=', 'ac.user_status_id');

        $query->where('ac.user_id', $user_id)
                ->where('e.active', 2)

                        ->orderByDesc('event_from');

        // typ eventu
        if($event_type > 0){
            $query->where('e.event_type', $event_type);
        }


        return $query->get();

    }



    // vrati uzivatelov ktory boli fyzicky pritomny na evente
    public function getEventUserAttendBalance($event_id, $user_attend=0)
    {

        $event_id = intval($event_id);

        $query = DB::table('attendance_lists as ac')
            ->select(['ac.*', 'u.id as user_id', 'u.*', 'e.event_from', 'egs.status as status_name',
                'ac.status as status_id', 'us.status AS user_status_name', 'ac.description AS attend_description',
                'il.name AS industry','ac.id AS attend_id', 'e.price AS price',
                DB::raw('CONCAT(u2.name," ", u2.surname ) AS invited_user')
            ]);

        if ($user_attend > 0){
            $query->where('ac.user_attend', intval($user_attend));
        }

        $query->where('ac.event_id', $event_id)
            ->join('users  as u', 'ac.user_id', '=', 'u.id', 'LEFT')
            ->join('events  as e', 'ac.event_id', '=', 'e.id', 'LEFT')
            ->join('events_guest_status  as egs', 'ac.status', '=', 'egs.id', 'LEFT')
            ->join('user_status  as us', 'u.status', '=', 'us.id', 'LEFT')
            ->join('industries_list AS il', 'il.id', '=', 'u.industry_id', 'LEFT OUTER')
            ->join('users AS u2', 'u.created_user', '=', 'u2.id', 'LEFT OUTER')
            ->orderBy('egs.id')
            ->orderBy('u.surname');

        $attendance = $query->get();

        return $attendance;

    }

    public function getEventAttendanceList($event_id, $user_attend=0)
    {

        $event_id = intval($event_id);

        $query = DB::table('attendance_lists as ac')
            ->select(['ac.*', 'u.id as user_id', 'u.*', 'e.event_from', 'egs.status as status_name',
                'ac.status as status_id', 'us.status AS user_status_name', 'ac.description AS attend_description',
                'il.name AS industry','ac.id AS attend_id', 'e.price AS price',
                DB::raw('CONCAT(u2.name," ", u2.surname ) AS invited_user'),

                // vrati ci clen ma clenstvo v klube ktory organizuje meeting
                DB::raw('( SELECT count(mc.id) FROM  membership_club AS mc
             
                                    LEFT JOIN memberships AS m ON m.id = mc.membership_id
                                    LEFT JOIN membership_user AS mu  ON mu.membership_id = m.id 
                                    WHERE mu.user_id = ac.user_id   
                                    AND mc.club_id = e.club_id     
                                    AND m.active = 1 ) AS attend_user_event'),

                'e.title AS event_title'
            ]);


        if ($user_attend > 0){
            $query->where('ac.user_attend', intval($user_attend));
        }

        $query->where('ac.event_id', $event_id)
            ->join('users  as u', 'ac.user_id', '=', 'u.id', 'LEFT')
            ->join('events  as e', 'ac.event_id', '=', 'e.id', 'LEFT')
            ->join('events_guest_status  as egs', 'ac.status', '=', 'egs.id', 'LEFT')
            ->join('user_status  as us', 'u.status', '=', 'us.id', 'LEFT')
            ->join('industries_list AS il', 'il.id', '=', 'u.industry_id', 'LEFT OUTER')
            ->join('users AS u2', 'u.created_user', '=', 'u2.id', 'LEFT OUTER')
            ->orderBy('egs.id')
            ->orderBy('u.surname');

        $attendance = $query->get();

        return $attendance;

    }


    public function getMembershipsFromUser($club_id, $user_id,  $active=0)
    {

        $club_id = intval($club_id);
        $user_id = intval($user_id);

        $membership =  DB::table('membership_user AS mu')
            ->select('mu.user_id')
            ->join('memberships','membership_user.membership_id','=', 'memberships.id' )
            ->rightJoin('membership_club', 'membership_club.membership_id','=', 'memberships.id' )
            ->where('membership_club.club_id', '=', $club_id)
            ->where('memberships.active', $active )

            ->distinct()
            ->pluck('mu.user_id');

        return $membership;

    }


    public function getAttendanceGroupUserType( $event_id)
    {

        $event_class = new Event();
        $club_class = new Club();

        $attendance_raw = $event_class->getEventAttendanceList($event_id);

        $event = Event::find($event_id);

        $attendance['confirmed_member'] = null;
        $attendance['apologized_member'] = null;
        $attendance['confirmed_guests'] = null;
        $attendance['apologized_guests'] = null;
        $attendance['other_guests'] = null;

        foreach ($attendance_raw  as $a ){
            $a->is_club_member = $club_class->hasUserMembershipInClub($a->user_id , $event->club_id);

            if($a->is_club_member && $a->status_id == 2){
                $attendance['confirmed_member'][] = $a;

            }elseif ($a->is_club_member && $a->status_id == 3){
                $attendance['apologized_member'][] = $a;

            }elseif ($a->is_club_member == null && $a->status_id == 2){
                $attendance['confirmed_guests'][] = $a;

            }elseif ($a->is_club_member  == null && $a->status_id == 3){
                $attendance['apologized_guests'][] = $a;

            }elseif ($a->is_club_member  == null && !in_array($a->status_id ,  [2,3])){
                $attendance['other_guests'][] = $a;
            }
        }

        return $attendance;

    }




    public function getEventsInTerm( $event_from=null, $event_to=null, $club_id=0, $user_id=null){

        $club_id = intval($club_id);
        $user_id = intval($user_id);


        $query = DB::table('events AS e')
                ->select('e.id', 'e.title', 'e.event_from', 'e.event_to', 'c.short_title AS club_title',
                                    'e.host_name AS host_name')
                ->join('clubs as c', 'c.id', '=', 'e.club_id')
                ->where('e.event_from', '>', $event_from)
                ->where('e.event_to', '<', $event_to);

            // klub
            if($club_id > 0){
                $query->where('e.club_id', $club_id);
            }

            // ranny a ppobednajsi kklub
            $query->whereIn('e.event_type', [1,4]);

            // uzavrete eventy
            $query->where('e.active', 2);

        // uzavrete eventy
        $query->orderByDesc('e.event_from');

        $events = $query->get();

        return $events;

    }


}
