<?php

namespace App\Http\Controllers\Setting;

use App\Models\Club;
use App\Models\Developer\SystemLog;
use App\Models\Membership;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClubMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        view()->share('backend_title', 'Členovia klubu'); //title

        $club = Club::findOrFail($id);

        $membership_class = new Membership();

        $management = null; //managment team

        $club_class = new Club();
        $users_raw = $club_class->getUsersFromClub($id, 1);

        $users = null;
        if ($users_raw){
            foreach ($users_raw as $k => $u){
                $users[$k] = $u;
                $users[$k]['membership'] = $membership_class->getMembershipForUserAndClub($u->id, $id, 1);
            }
        }

        //typy funkcii managementu
        $user_function = DB::table('user_function')->where('management', 1)->get();

        if($user_function){
            foreach ($user_function as $uf){
                $management_users = DB::table('club_users')
                    ->where('club_users.club_id', $id )
                    ->where('club_users.user_function_id', $uf->id)
                    ->leftJoin('user_function as uf', 'uf.id', '=', 'club_users.user_function_id')
                    ->orderBy('club_users.user_function_id')
                    ->latest('club_users.id')
                    ->take(1)
                    ->first();
                if($management_users){
                    $management[] = collect($management_users)->put('user', $users_raw->find($management_users->user_id));
                }
            }
        }

        return view('setting.club-member.detail')
            ->with('user_function', $user_function)
            ->with('users', $users)
            ->with('management', collect($management))
            ->with('club', $club);

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

        $data = [];

        $last_user_function = DB::table('club_users')
            ->where('user_function_id', $request->position )
            ->where('club_id', $id )
            ->latest('id')
            ->first();

        $count_days=0;
        if($last_user_function){
            //spocitame ako dlho vykonaval funkciu
            $count_days = Carbon::createFromFormat('Y-m-d H:i:s', $last_user_function->valid_from)->diffInDays(Carbon::createFromFormat('d.m.Y', $request->valid_from ));
        }


        if($last_user_function) {
            //zaktualizujem zaznam funkcia do
            DB::table('club_users')
                ->where('id', $last_user_function->id)
                ->update(       [
                                    'valid_to' => Carbon::createFromFormat('d.m.Y', $request->valid_from ),
                                    'count_days' => $count_days ]);

            // zapiseme do systemoveho logu
            $system_log_class = new SystemLog();
            $log_transaction = "Changed user function: " . $request->position . " User: " . $request->user_id;
            $system_log_class->addRecordToSystemLog('user_function', $last_user_function->id ,$log_transaction, $log_transaction, Auth::user()->id);
        }

        // a zapiseme novy zaznam
        $data['user_function_id'] = $request->position;
        $data['club_id'] = $id;
        $data['user_id'] = $request->user_id;
        $data['valid_from'] = Carbon::createFromFormat('d.m.Y', $request->valid_from );
        $data['valid_to'] = null;
        $data['count_days'] = 0;

        DB::table('club_users')->insert($data);

        return redirect()->route('setting.club-member.show', ['id' => $id] )->with('message', 'Záznam úspešne upravený');


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
