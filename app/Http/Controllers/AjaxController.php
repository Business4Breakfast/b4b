<?php

namespace App\Http\Controllers;

use App\Helpers\VATCheck\VatCheck;
use App\Models\Club;
use App\Models\Company;
use App\User;
use Carbon\Carbon;
use DB;
use Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laratrust;
use Log;

class AjaxController extends Controller
{

    private  $parameters = null;

    public function saveAttendanceStatus(Request $request){


        Log::info('ajax', [intval($request->attendance)]);
        Log::info('ajax', [intval($request->event)]);


        $res = DB::table('attendance_lists')
              ->where('id', intval($request->attendance))
              ->where('event_id', $request->event)
              ->update(['status' => $request->status]);

            if ($res == 1) {
                $response = $request->all();
            } else $response = null;

        return  response()->json($response);

    }


    public function getUserDataSearch(Request $request){

        $query = User::query();

        $user = $query->take(30)->get();

        $response = json_decode($user, true);

        return  response()->json($response);

    }

    public function getUserDetail(Request $request)
    {

        $user_id = trim($request->user_id);
        $user = User::find($user_id);
        $response = json_decode($user, true);

        $status = 'false';
        if($user) $status = 'true';

        return  response()->json(['data' => $response, 'status' => $status, 'message' => 'Takýto užívateľ neexistuje']);
    }


    public function getCountiesInDistrict(Request $request)
    {

        $district_id = trim($request->district_id);
        $counties = DB::table('counties')->where('id_district', $district_id)->get();

        $response = json_decode($counties, true);
        $status = ($counties) ? 'true' : 'false';

        return  response()->json(['data' => $response, 'status' => $status, 'message' => 'This district non exist']);
    }


    public function getGuestData(Request $request)
    {

        $columns = [
            0 =>'id',
            1 =>'name',
            2=> 'email',
            3=> 'phone',
        ];

        $club_class = new Club();

        $total_record = DB::table('users')
            ->whereNotIn('id',[0,1])
            ->count();

        $total_filtered = $total_record;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $direction = $request->input('order.0.dir');

        $user_status = intval($request->user_status);
        $user_crm_status = intval($request->user_crm_status);
        $created_user = intval($request->user_created);
        $industry_id = intval($request->industry_id);
        $club_id = intval($request->club_id);

//        $search_string = $request->input('search.value');

        $clubs = null;
        if(!Laratrust::can('contact-listing-all-club')){
            $clubs = $club_class->getIdClubsFromUsers(Auth::user()->id);
        }

        if ($club_id>0){
            $clubs = [$club_id];
        }


        Log::info('ajax conntroler user status', [ Auth::user()->full_name ] );
        Log::info('ajax conntroler search string'  , [  $request->toArray() ] );

        $search = $request->input('search.value');

        $query = null;
        $query = DB::table('users AS u');

        $query->select('u.*', 'u1.name AS created_name', 'u1.surname AS created_surname', 'us.status AS user_status',
                                'u1.id AS created_user_id');
        $query->join('users AS u1','u.created_user', '=', 'u1.id', 'LEFT OUTER' )
                ->join('user_status AS us', 'u.status', '=', 'us.id', 'LEFT');

        //status filter
        $filter_user_status = null;
        if ($user_status  && $user_status > 0){
            //aktivne
            if ($user_status == 90){
                $query->whereIn('u.status', [1,2,3]);
                //neaktivne
            }elseif ($user_status == 80){
                $query->whereIn('u.status', [4,5,6]);
                // ostne podla stavu
            }else{
                $query->where('u.status', intval($user_status));
            }
        }

        //filter industry
        if ($industry_id > 0)  $query->where('u.industry_id', $industry_id);
        //filter user created
        if ($created_user > 0)  $query->where('u.created_user', $created_user);
        //filter crm
        if ($user_crm_status > 0)  $query->where('u.crm_status', $user_crm_status);


        if ($clubs){

            $this->parameters['clubs'] = $clubs;
            $query->whereIn('u.id',function ($query) {
                $query->select('uc.user_id')->from('user_club AS uc')
                    ->Where('uc.club_id', $this->parameters['clubs']);
            });
        }

        // nie pre systemoveho usera
        $query->whereNotIn('u.id',[0,1]);

        // len kontakty hosti nie uzivatelov
        //$query->where('u.admin', 0);

        $query->where(function ($q) use ($search){
            $q->where('u.email', 'LIKE',"%{$search}%" )
                ->orWhere('u.name', 'LIKE', "%{$search}%")
                ->orWhere('u.surname', 'LIKE', "%{$search}%")
                ->orWhere('u.title_before', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('CONCAT(u.name," ",u.surname)'), 'LIKE', "%{$search}%")
                ->orwhere('u.id', $search );
            });

            $query->offset($start)
                      ->orderBy($order, $direction)
                      ->limit($limit);

            $guests = $query->get();

            // END vypis guest


        $query = null;

        $query = DB::table('users AS u');

        //status filter
        $filter_user_status = null;
        if ($user_status  && $user_status > 0){
            //aktivne
            if ($user_status == 90){
                $query->whereIn('u.status', [1,2,3]);
                //neaktivne
            }elseif ($user_status == 80){
                $query->whereIn('u.status', [4,5,6]);
                // ostne podla stavu
            }else{
                $query->where('u.status', intval($user_status));
            }
        }

        //filter industry
        if ($industry_id > 0)  $query->where('u.industry_id', $industry_id);
        //filter user created
        if ($created_user > 0)  $query->where('u.created_user', $created_user);
        //filter crm
        if ($user_crm_status > 0)  $query->where('u.crm_status', $user_crm_status);



        if ($clubs){

            $this->parameters['clubs'] = $clubs;

            $query->whereIn('u.id',function ($query) {
                $query->select('uc.user_id')->from('user_club AS uc')
                    ->Where('uc.club_id', $this->parameters['clubs']);
            });
        }

        // nie pre systemoveho usera
        $query->whereNotIn('u.id',[0,1]);

        // len kontakty hosti nie uzivatelov
        //$query->where('u.admin', 0);

        $query->where(function ($q) use ($search){
            $q->where('u.email', 'LIKE',"%{$search}%" )
                ->orWhere('u.name', 'LIKE', "%{$search}%")
                ->orWhere('u.surname', 'LIKE', "%{$search}%")
                ->orWhere('u.title_before', 'LIKE', "%{$search}%")
                ->orWhere(DB::raw('CONCAT(u.name," ",u.surname)'), 'LIKE', "%{$search}%")
                ->orwhere('u.id', $search );
        });

        $total_filtered = $query->count();


        $data = [];

        if ($guests){
            foreach ($guests as $g){

                $res['id'] = $g->id;


                $res_name = $g->title_before . ' ' . $g->name . ' ' . $g->surname;
                if($g->created_user_id > 0){
                    $res_name .=  ' <small>[' . $g->created_name . ' ' . $g->created_surname .'] ' . Carbon::createFromFormat('Y-m-d H:i:s', $g->created_at)->format('d.m.y') .'</small>';
                }

                $res['name'] = $res_name;

                $res_name= null;

                $res['status'] =  $g->user_status;
                $res['email'] = $g->email;
                $res['phone'] = $g->phone . '<br>';

                $action['edit_user'] = '<a href="' . route('setting.user.edit', ["guest" => $g->id ]) . '" type="button" class="pull-right btn btn-success btn-xs m-l-sm"><i class="fa fa-user-o"></i> </a>';

                $action['edit_guest'] = '<a href="' . route('guests.guest-listings.edit', ["guest" => $g->id ]) . '" type="button" class="pull-right btn btn-default btn-xs m-l-sm"><i class="fa fa-edit"></i> </a>';

                $action['delete'] = '
                    <a type="button" data-item-id="'. $g->id .'" class="m-l-sm pull-right btn btn-danger btn-xs delete-alert"> <i class="fa fa-trash-o"></i></a>
                    <form method="POST" action="' . route('guests.guest-listings.destroy', ["guest" => $g->id ])  . '" accept-charset="UTF-8" class="class=&quot;m-l-sm pull-right btn btn-danger btn-xs delete-alert hide" id="item-del-'.$g->id.'">
                    <input name="_method" type="hidden" value="DELETE">
                    <input name="_token" type="hidden" value="' . csrf_token() . '">
                    <input name="user_id" type="hidden" value="' . $g->id . '">
                    </form>
                ';

                $res['action'] = "";

                if(Laratrust::can('contact-delete-guest')) $res['action'] .= $action['delete'];

                if(Laratrust::can('users-update')) $res['action'] .= $action['edit_user'];

                if(Laratrust::can('contact-edit-guest')) $res['action'] .= $action['edit_guest'];

                $data[] = $res;
            }

        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($total_record),
            "recordsFiltered" => intval($total_filtered),
            "data"            => $data
        );

        //echo json_encode($json_data);

        return  response()->json($json_data);
    }

    public function checkVatRegistration(Request $request)
    {

        $vat = trim($request->ic_dph);
        $vatCheck = new VatCheck();
        $response = $vatCheck->checkVAT($vat);
        $response = json_decode($response, true);

        return  response()->json(['status' => $response, 'message' => 'Číslo DPH registrácie neexistuje']);
    }

    public function getCompanyData(Request $request)
    {

        $company_id = trim($request->company_id);
        $company = Company::find($company_id);
        $response = json_decode($company, true);

        $status = 'false';
        if($company) $status = 'true';

        return  response()->json(['data' => $response, 'status' => $status, 'message' => 'Takáto spoločnosť neexistuje']);
    }

    public function getClubData(Request $request)
    {

        $club_id = trim($request->club_id);
        $club = Club::find($club_id);

        $last_event = DB::table('events')->where('club_id', $club_id )->pluck('event_from')->last();

        if($last_event){
            $club['last_event_date'] = Carbon::createFromFormat( 'Y-m-d H:i:s', $last_event)->format('d.m.Y');
        } else {
            $club['last_event_date'] = Carbon::now()->format('d.m.Y');
        }

        $response = json_decode($club, true);

        $status = 'false';
        if($club) $status = 'true';

        return  response()->json(['data' => $response, 'status' => $status, 'message' => 'Takýto club neexistuje']);
    }

    public function getUserData(Request $request)
    {

        $user_id = trim($request->user_id);
        $user = User::find($user_id);
        $response = json_decode($user, true);

        $status = 'false';
        $message = 'Takýto užívateľ neexistuje';
        if($user) {
            $status = 'true';
            $message = 'OK';
        }

        return  response()->json(['data' => $response, 'status' => $status, 'message' => $message]);
    }

    public function backendMenuUpdate(Request $request)
    {
        $data = json_decode($request->rank, true);
        $data = $this->run_array_parent($data,'0');

        foreach ($data as $k => $v){
            DB::table('backend_menu')->where('id', $k)->update(['rank' => $v['order'], 'parent' => $v['parent']]);
        }

        $msg = "Data were succesfully updated.";
        return response()->json(array('msg'=> $msg), 200);
    }

    //Function to create id =>[ order , parent] unnested array
    private function run_array_parent($array,$parent){
        $post_db = array();
        foreach($array as $head => $body){
            if(isset($body['children'])){
                $head++;
                $post_db[$body['id']] = ['parent'=>$parent,'order'=>$head];
                $post_db = $post_db + $this->run_array_parent($body['children'],$body['id']);
            }else{
                $head++;
                $post_db[$body['id']] = ['parent'=>$parent,'order'=>$head];
            }
        }

        return $post_db;
    }


}
