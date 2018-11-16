<?php

namespace App\Http\Controllers\Events;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Event\EventTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust;

class EventStatsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        view()->share('backend_title', 'Prehľad štatistík klubu'); //title

        $this->request = $request;

        $club_class = new Club();

        $loged_user = Auth::user();

        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();


        $event_types = EventTypes::all();
        $date_min = Carbon::createFromDate(null, 1, 1);
        $date_max = Event::max('event_to');
        $req = [];

        $req['search_club'] = null;
        $req['search_type'] = 1;

        $query = DB::table('events as e')
            ->select(
                ['e.*', 'c.id as club_id', 'c.title as club_title', 'e.event_type', 'e.title AS event_title', 'et.name as event_type_name','c.address_city as city',
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id ) as attend_count'),
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id AND al.status = 2) as attend_count_confirm'),
                    DB::raw('( SELECT count(id) FROM  reference_coupons AS rc WHERE rc.reference_id = e.id ) as reference_coupons_count'),

                    //hodnota biznisu
                    DB::raw('( SELECT sum(price) FROM  reference_coupons AS rc WHERE rc.reference_id = e.id ) as reference_coupons_price'),

                    // vsetci ktory prisli
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id AND al.user_attend = 1 
                                                            ) as count_attended'),

                    // vsetci clenovia aj clenovia inych klubov prisli
                    DB::raw('( SELECT count(id) FROM  attendance_lists AS al WHERE al.event_id = e.id AND al.user_attend = 1 
                                     AND al.user_status_id = 5 ) as member_all_attended'),

                    // vrati clenovia konkretneho kubu k eventu
                    DB::raw('( SELECT count(al.id) FROM  attendance_lists AS al
                                    LEFT JOIN membership_user AS mu  ON mu.user_id = al.user_id                                
                                    LEFT JOIN memberships AS m ON m.id = mu.membership_id
                                    LEFT JOIN membership_club AS mc ON mc.membership_id = m.id

                                    WHERE mu.user_id = al.user_id                                       
                                    AND al.event_id = e.id 
                                    AND al.user_status_id = 5 
                                    AND mc.club_id = e.club_id     
                                    AND m.active = 1 ) AS member_count'),

                    // vrati ospravedlneny clenovia konkretneho kubu k eventu
                    DB::raw('( SELECT count(al.id) FROM  attendance_lists AS al
                                    LEFT JOIN membership_user AS mu  ON mu.user_id = al.user_id                                
                                    LEFT JOIN memberships AS m ON m.id = mu.membership_id
                                    LEFT JOIN membership_club AS mc ON mc.membership_id = m.id

                                    WHERE mu.user_id = al.user_id                                       
                                    AND al.event_id = e.id 
                                    AND al.user_status_id = 5 
                                    AND al.status = 3 
                                    AND mc.club_id = e.club_id     
                                    AND m.active = 1 ) AS member_count_apologize'),

                    // vrati clenovia konkretneho kubu k eventu ktory prissli
                    DB::raw('( SELECT count(al.id) FROM  attendance_lists AS al
                                    LEFT JOIN membership_user AS mu  ON mu.user_id = al.user_id                                
                                    LEFT JOIN memberships AS m ON m.id = mu.membership_id
                                    LEFT JOIN membership_club AS mc ON mc.membership_id = m.id
                                    WHERE mu.user_id = al.user_id                                       
                                    AND al.event_id = e.id 
                                    AND al.user_status_id = 5 
                                    AND al.user_attend = 1 
                                    AND mc.club_id = e.club_id     
                                    AND m.active = 1 ) AS member_count_attend')


                ])
            ->join('clubs as c', 'c.id','=', 'e.club_id' )
            ->join('event_types as et', 'et.id','=', 'e.event_type', 'left' );

//        // ak ma opravnenie vidiet vseky eventy
//        if (!Laratrust::can('events-listing-all')){
//
//            //ak je uzivatel clenom vykonneho tímu vidi len svoje  udalosti
//            $user_clubs = $club_class->getClubsFromUsers($loged_user->id);
//            if( Auth::user()->hasRole(['franchisee', 'manager', 'executive-member', '']) ){
//                $query->whereIn('e.club_id', $user_clubs->pluck('id') );
//            }
//        }


        // uzavrete eventy
        $query->where('e.active', 2);

        if(isset($request->date_from) ) {
            if($request->date_from != Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y') ) {
                $query->where('e.event_from', '>', Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d H:i:s'));
            }
            $req['date_from'] = $request->date_from;
            $query->where('e.event_from', '>', Carbon::createFromFormat('d.m.Y', $request->date_from)->format('Y-m-d H:i:s'));

        } else {
            $req['date_from'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y');
        }

        if(isset($request->date_to) ) {
            if($request->date_to != Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y') ) {
                $query->where('e.event_to', '<', Carbon::createFromFormat('d.m.Y', $request->date_to)->format('Y-m-d H:i:s'));
            }
            $req['date_to'] = $request->date_to;
        } else {
//            $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y');
            $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', now())->addMonths(3)->format('d.m.Y');
            $query->where('e.event_to', '<', Carbon::now()->addWeeks(2)->format('Y-m-d H:i:s'));
        }

        if (isset($request->search_club) &&  strlen($request->search_club) > 0) {
            $query->where( 'e.club_id', $request->search_club);
            $req['search_club'] = $request->search_club;
        }


        if (isset($request->search_type) &&  strlen($request->search_type) > 0) {
            $query->where( 'e.event_type', $request->search_type);
            $req['search_type'] = $request->search_type;
        }


        // len kluby do ktorych ma user pristup
        $users_clubs = $club_class->getClubsFromUsers(Auth::user()->id)->pluck('id')->toArray();
        // len kluby do ktorych ma user pristup
        $query->whereIn( 'e.club_id', $users_clubs);


        $query->orderByDesc('e.event_from', 'e.id');

        $items = $query->paginate(4)
            ->appends(request()
                ->query());

        return view('events.stats.index')
            ->with('req',$req)
            ->with('clubs', $clubs)
            ->with('event_types', $event_types)
            ->with('items', $items);    }

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
        //
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
