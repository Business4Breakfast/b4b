<?php

namespace App\Http\Controllers\Setting;

use App\Models\Event\Event;
use App\Models\Membership;
use App\Models\Setting\Industry;
use App\Models\Setting\Interest;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MemberStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd('stat');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $user = User::find($id);

        $industry = Industry::orderBy('name')->get();
        $interest = Interest::orderBy('name')->get();

        $membership_class = new Membership();
        $event_class = new Event();

        $stats_total = null;

        $guests_member = DB::table('users AS u')
            ->select('u.name', 'u.surname', 'u.phone', 'u.email', 'u.created_at',
                                'u.company', 'u.id', 'il.name AS industry')
            ->whereNotIn('u.id',[0,1])
            ->leftJoin('industries_list AS il', 'il.id' ,'=', 'u.industry_id', 'outer')
            ->orderBy('u.surname')
            ->where('u.created_user', $id)
            ->get();
        //
        $user_memberships = $membership_class->getMembershipsForUsers($id, 0);


        // vracia referncne listky usera dal aj dostal total
        $count_reference_total = DB::table('reference_coupons AS rc')
            ->join('references_list AS rl', 'rl.id','=', 'rc.reference_type')
            ->whereRaw(" (rc.user_from = $id OR rc.user_to = $id) ")
            ->get();

        // vratis novo zapisanych hosti v obdobi clenstva
        $invited_guests_total = DB::table('users AS u')
            ->where('u.created_user', $id)
            ->get();

        // vracia pocet pozvaných hosti daneho usera na eventy
        $count_attend_user_total = DB::table('attendance_lists AS al')
            ->where('al.user_invite_id', $id)
            ->whereIn('al.user_status_id', [1,2,3])
            ->get();


        // vsetky eveny uzuvatela
        $event_class = new Event();
        $events_from_user = $event_class->getEventFromUsers($id, null);


        $stats_total['reference_from_evidence_price'] = $count_reference_total->where('user_from', $id)
                                                        ->where('reference_type', 4)
                                                        ->sum('price');

        $stats_total['reference_to_evidence_price'] = $count_reference_total->where('user_to', $id)
                                                        ->where('reference_type', 4)
                                                        ->sum('price');

        $stats_total['reference_evidence_price'] = $count_reference_total->where('reference_type', 4)
                                                        ->sum('price');

        $stats_total['reference_evidence_from'] = $count_reference_total->where('user_from', $id)->count();
        $stats_total['reference_evidence_to'] = $count_reference_total->where('user_to', $id)->count();
        $stats_total['invited_guest_total'] = $invited_guests_total->count();
        $stats_total['invited_guest_attend_total'] = $count_attend_user_total->count();


        $event_stat = [];
        // prejdeme jednotlive clenstva a vypocitame statistiky
        if ($user_memberships){
            foreach ($user_memberships as $v){

                // eventy pocas trvania clenstva
                $events = $event_class->getEventsInTerm($v->valid_from, $v->valid_to, $v->club_id, $id);

                    // idcka eventov v damo obdobi
                    $events_ids = $events->pluck('id')->toArray();

                    // vracia pocet navstev na kluboch kde je clenom a ma priznak ze prisiel
                    $count_attend_event = DB::table('attendance_lists AS al')
                        ->whereIn( 'event_id', $events_ids )
                        ->where('al.user_id', $id)
                        ->where('al.user_attend', 1)
                        ->get();

                    // vratis novo zapisanych hosti v obdobi clenstva
                    $invited_guests = DB::table('users AS u')
                        ->where('u.created_user', $id)
                        ->where('u.created_at', '>', $v->valid_from)
                        ->where('u.created_at', '<', $v->valid_to)
                        ->get();

                    // vracia pocet pozvaných hosti daneho usera na eventy
                    $count_attend_user = DB::table('attendance_lists AS al')
                        ->whereIn( 'event_id', $events_ids )
                        ->where('al.user_invite_id', $id)
                        ->whereIn('al.user_status_id', [1,2,3])
                        ->get();

                    // vracia referncne listky usera dal aj dostal
                    $count_reference = DB::table('reference_coupons AS rc')
                        ->join('references_list AS rl', 'rl.id','=', 'rc.reference_type')
                        ->whereIn( 'reference_id', $events_ids )
                        ->whereRaw(" (rc.user_from = $id OR rc.user_to = $id) ")
                        ->get();


//                    $events_attend = null;
//                    if ($events){
//                        foreach ($events as $k => $event){
//
//                            $events_attend = $event;
//                            $attend_event_user = DB::table('attendance_lists AS al')
//                                ->where( 'event_id', $event->id )
//                                ->where('al.user_id', $id)
//                                ->where('al.user_attend', 1)
//                                ->first();
//                            $events_attend->attend = ($attend_event_user) ? $attend_event_user->user_attend : null;
//                            $events_attend->status = ($attend_event_user) ? $attend_event_user->status : null;
//
//                        }
//                    } else {
//                        $events_attend = null;
//                    }


                $event_stat[] =

                        [
                            'membership' => $v,
                            'events_from_user'  => $events_from_user,
                            'events' => $events,
//                            'events_attend' => $events_attend,
                            'event_count' => $events->count(),
                            'event_attend' => $count_attend_event->count(),
                            'user_created' => $invited_guests->count(),
                            'guest_attend_count' => $count_attend_user->count(),
                            'guest_attend_count_confirmed' => $count_attend_user->where('status', 2)->count(), // confirmed
                            'guest_attend_count_attend' => $count_attend_user->where('user_attend', 1)->count(), // attend

                            'reference_from' => $count_reference->where('user_from', $id)
                                                        ->count(), // odovzdal
                            'reference_from_evidence' => $count_reference->where('user_from', $id)
                                                        ->where('reference_type', 4)
                                                        ->count(), // odovzdal svedectvo


                            'reference_from_evidence_price' => $count_reference->where('user_from', $id)
                                ->where('reference_type', 4)
                                ->sum('price'),

                            // odovzdal svedectvo    cena
                            'reference_from_ref' => $count_reference->where('user_from', $id)
                                                ->where('reference_type', 1)
                                                ->count(), // odovzdal referencia

                            'reference_from_1x1' => $count_reference->where('user_from', $id)
                                ->where('reference_type', 3)
                                ->count(), // odovzdal referencia

                            'reference_to' => $count_reference->where('user_to', $id)
                                                        ->count(), // dostal

                            'reference_to_evidence' => $count_reference->where('user_to', $id)
                                ->where('reference_type', 4)
                                ->count(), // odovzdal svedectvo

                            'reference_to_evidence_price' => $count_reference->where('user_to', $id)
                                ->where('reference_type', 4)
                                ->sum('price'), // odovzdal svedectvo    cena

                            'reference_to_ref' => $count_reference->where('user_to', $id)
                                ->where('reference_type', 1)
                                ->count(), // dostal referencia

                            'reference_to_1x1' => $count_reference->where('user_to', $id)
                                ->where('reference_type', 3)
                                ->count(), // dostal referenciaC
                        ];


            }
        }


        // navsteva jednotlivych akcii

        $udalosti = null;
        $navstivene_udalosti=null;

        // prejdeme jednotlive clenstva a vypocitame statistiky
        if ($user_memberships) {
            foreach ($user_memberships as $k =>$v) {

               // dump($v);

                $navstivene_udalosti[$k] = $v;

                // eventy pocas trvania clenstva
                $udalosti = $event_class->getEventsInTerm($v->valid_from, $v->valid_to, $v->club_id, $id);

                $events_attend = null;
                if ($udalosti){
                    foreach ($udalosti as $key => $event){

                        $events_attend[$key] = $event;
                        $attend_event_user = DB::table('attendance_lists AS al')
                            ->where( 'event_id', $event->id )
                            ->where('al.user_id', $id)
                            ->where('al.user_attend', 1)
                            ->first();

                        $events_attend[$key]->attend = ($attend_event_user) ? $attend_event_user->user_attend : null;
                        $events_attend[$key]->status = ($attend_event_user) ? $attend_event_user->status : null;

                    }

                    $navstivene_udalosti[$k]->udalosti = $events_attend;


                } else {
                    $events_attend = null;
                }

            }

        }


        return view('setting.member.detail')
                    ->with('event_stat', $event_stat)
                    ->with('event_attend', $navstivene_udalosti)
                    ->with('guests', $guests_member)
                    ->with('industry', $industry)
                    ->with('interest', $interest)
                    ->with('stats_total', $stats_total)
                    ->with('user', $user);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
