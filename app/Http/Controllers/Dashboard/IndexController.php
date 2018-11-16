<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Club;
use App\Models\Event\Event;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust\Laratrust;

class IndexController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }



    public function index()
    {

        $data = null;
        $club_class = new Club();
        $event_class = new Event();
        $user_class = new User();

        $users_count = DB::table('memberships')->where('active', 1)->count();
        $clubs_cout = DB::table('clubs')->where('active', 1)->count() - 1;
        $events_count = DB::table('events')->where('active', 1)->count();

        // zbrazenie nadchádzajúcich eventov
        $data['earliest_events'] = null;
        $active_user_clubs = $club_class->getClubsFromUsers(Auth::user()->id,1)->pluck('id');
        if ($active_user_clubs->count() > 0){
            foreach ($active_user_clubs as $auc){
                $earliest_events = $event_class->getEarliestEvent($auc, 1);
                if ($earliest_events){
                    $data['earliest_events'][] = $earliest_events;
                }
            }
        }

        $user_loged_function = $user_class->getActualUserFunction(Auth::user()->id);

        $data['function_statt'] = null;

        if(!Auth::user()->hasRole(['superadministrator', 'administrator'])){

            if ($user_loged_function){

                foreach ($user_loged_function as $k => $ulf){
                    // ak ma uzivatel funkciu pre urcity klub
                    $function_statt[$k]['club'] =  $ulf->club_title;
                    $function_statt[$k]['club_members_count'] = $club_class->getUsersFromClub($ulf->club_id)->count();
                    $function_statt[$k]['new_members'] =  $club_class->countNewUsersFromClub($ulf->club_id, 1, $ulf->valid_from);
                    $function_statt[$k]['function'] =  $ulf;
                }

                $data['function_statt'] = $function_statt;
            }

        }


        $user['count'] = $users_count;
        $club['count'] = $clubs_cout;
        $club['events'] = $events_count;

        $finance['invoice'] = DB::table('invoices')
            ->where('status', 5)
            ->where('proforma', 1)
            ->get()->sum('price');


        return view('dashboard.index')
            ->with('data', $data)
            ->with('finance', $finance)
            ->with('club', $club)
            ->with('user', $user);

    }


}
