<?php

namespace App\Http\Controllers\Setting;

use App\Models\Club;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ClubBreakfastController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.club.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad klubov', 'link' => route('setting.club.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Prehľad klubov'); //title
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        dd('breakfast');
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

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový klub', 'link' => route('setting.club.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad klubov', 'link' => route('setting.club.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Udalosti (raňajky) klubu'); //title

        $club_class = new Club();
        $club_users =  $club_class->getUsersFromClub($id);


        $user_activity = DB::table('events_activities')
            ->select('id','user_id', 'event_id', 'activity_id', 'club_id')
            ->get();


        $items = DB::table('events')
            ->where('club_id', $id)
            ->orderByDesc('event_from')
            ->paginate(20);;


        $club = Club::findOrFail($id);


        return view('setting.club-breakfast.detail')
            ->with('items', $items)
            ->with('club_users', $club_users)
            ->with('user_activity', $user_activity)
            ->with('club', $club);

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function ticket($id)
    {

        view()->share('backend_title', 'Referenčné ústrižky raňajok'); //title

        $items = DB::table('reference_coupons as rc')
            ->where('rc.reference_id', $id)
            ->select('uf.name as from_name', 'uf.surname as from_surname', 'ut.name as to_name', 'ut.surname as to_surname',
                            'rc.id', 'rc.reference_type', 'rc.date as date', 'rc.value_1', 'rl.name as ref_name', 'rc.description', 'rc.*')
            ->join('references_list AS rl','rc.reference_type','=','rl.id')
            ->join('users  as uf', 'rc.user_from', '=', 'uf.id', 'LEFT')
            ->join('users  as ut', 'rc.user_to', '=', 'ut.id', 'LEFT')
            ->orderByDesc('rc.id')
            ->paginate(20);;

        return view('setting.club-breakfast.ticket')
            ->with('items', $items);

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function attendance($id)
    {
        view()->share('backend_title', 'Prezenčná listina raňajok'); //title

        $items = DB::table('attendance_lists as ac')
            ->select(['ac.*', 'u.id as user_id', 'u.*', 'e.event_from', 'egs.status as status_name',
                        'ac.status as status_id', 'us.status AS user_status_name'
                    ])
            ->where('ac.event_id', $id)
            ->join('users  as u', 'ac.user_id', '=', 'u.id', 'LEFT')
            ->join('events  as e', 'ac.event_id', '=', 'e.id', 'LEFT')
            ->join('events_guest_status  as egs', 'ac.status', '=', 'egs.id', 'LEFT')
            ->join('user_status  as us', 'ac.user_status_id', '=', 'us.id', 'LEFT')

            ->orderBy('egs.id')
            ->orderBy('ac.id')
            ->paginate(20);

        return view('setting.club-breakfast.attendance')
            ->with('items', $items);

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
