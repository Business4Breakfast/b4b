<?php

namespace App\Http\Controllers\Setting;

use App\Permission;
use App\Role;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;

class ProfileController extends Controller
{


    /*
         *  The authenticated user.protected
          *
          * @var \App\User|null
         */
    protected $user;


    /*
     * Is the user signed In?
     *
     * @var \App\User|null
     */
    protected $signedIn;



    public function __construct() {

//        $this->middleware(function ($request, $next) {
//
//            //$this->user = $this->signedIn = Auth::user();
//
//            return $next($request);
//        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('roles')->paginate(50);
        return view('setting.user.index')
            ->with('items', $users);
    }


    /**
     * Display a detail of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileLogged()
    {
        dd(Auth::user());
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();

        return view('setting.user.add')
            ->with('roles', $roles);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'title_before' => 'max:10',
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'title_after' => 'max:10',
            'account' => 'required',
            'email' => 'email|required',
            'phone' => 'required|numeric|phone',
            'password' => 'required|max:200',
            'password_confirmation' => 'required|max:200',
            'birthday' => 'required|date_format:d.m.Y',
        ];

        $this->validate($request,$rules);

        $user = [];
        $user['title_before'] = $request->title_before;
        $user['name'] = $request->name;
        $user['surname'] = $request->surname;
        $user['title_after'] = $request->title_after;
        $user['email'] = $request->email;
        $user['phone'] = $request->phone;
        $user['password'] = bcrypt($request->password);
        $user['created_at'] = Carbon::now()->toDateTimeString();
        $user['updated_at'] = Carbon::now()->toDateTimeString();
        $user['birthday'] = Carbon::createFromFormat('d.m.Y', $request->birthday);

        // check ci existuje taky user
        $user_exist = DB::table('users')->where('email', '=', $user['email'])->first();
        if($user_exist){
            Session::flash('alert-danger', 'Užívateľ s takýmto emailom ('.$user['email'].') už existuje!');
            return redirect()->route('setting.user.create')->withInput();
        }

        // ziskanie info aktualne vlozeneho usera
        $newUserId = DB::table('users')->insertGetId($user);

        $newUser = User::find($newUserId);
        // nastavenie vybranej role
        $userRole = Role::where('name', $request->account)->first();

        //priradenie role
        $newUser->attachRole($userRole);

        return redirect()->route('setting.user.index')->with('message', 'Užívateľ úspešne vytvorený');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $idile
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(Auth::user()->id == $id){
            //vlastny profil


        }else {


        }

        $user = User::find($id);

        dd($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::with('roles')->find($id);
        $roles = Role::all();

        //dd($user->roles);

        return view('setting.user.edit')
            ->with('roles', $roles)
            ->with('user', $user);

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

        $rules = [
            'title_before' => 'max:10',
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'title_after' => 'max:10',
            'account' => 'required',
            'email' => 'email|required',
            'phone' => 'required|numeric|phone',
            'password' => 'min:8|max:200',
            'birthday' => 'required|date_format:d.m.Y',
        ];

        $this->validate($request,$rules);

        $editUser = User::find($id);

        $user = [];
        $user['title_before'] = $request->title_before;
        $user['name'] = $request->name;
        $user['surname'] = $request->surname;
        $user['title_after'] = $request->title_after;
        $user['email'] = $request->email;
        $user['phone'] = $request->phone;
        $user['updated_at'] = Carbon::now()->toDateTimeString();
        $user['birthday'] = Carbon::createFromFormat('d.m.Y', $request->birthday);

        if(strlen($request->password)>7){
            $user['password'] = bcrypt($request->password);
        }

        DB::table('users')->where('id','=', $id )->update($user);

        //roles
        $userRoles=[];
        if($request->account){
            foreach ($request->account as $item){
                $userRoles[] = $item;
            }
        }
        $editUser->syncRoles($userRoles);

        return redirect()->route('setting.user.index')->with('message', 'Užívateľ úspešne upravený');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::destroy($id);

        return redirect()->route('setting.user.index')
            ->with('message', 'Záznam úspešne vymazaný.');
    }

    public function permissionsGet($user_id)
    {
        $user = User::with(['permissions', 'roles'])->find($user_id);
        $permission = Permission::all();
        $userPermission = $user->allPermissions();

        //zistime permisiions z roli (defaultne permision pre rolu)
        $role_user_permission = DB::table('permission_role')->whereIn('role_id', $user->roles->pluck('id'))->pluck('permission_id');

        $ar = null;
        $grouped = $permission->groupBy(function ($item, $key) {
            $ar = explode('-', $item['name'] );
            return $ar[0];
        });

        //zoradime podla abecedy
        $items = $grouped->all();
        ksort($items);
        $grouped = collect($items);

        $grouped->toArray();

        return view('setting.user.perrmision')
            ->with('items', $grouped)
            ->with('user', $user)
            ->with('user_permission', $userPermission)
            ->with('role_user_permission', $role_user_permission);

    }

    public function permissionsSave(Request $request)
    {
        $permission = Permission::all()->toArray();
        $userPermission = $request->permission;

        $user = User::find($request->user_id);

        $res = [];
        if($permission){
            foreach ($permission as $k => $v){
                if($userPermission){
                    foreach ($userPermission as $up => $uk){
                        if($up == $v['name']){
                            $res[] = $v['id'];
                        }
                    }
                }
            }
        }

        $user->syncPermissions($res);

        return redirect()->route('setting.user.index')
            ->with('message', 'Oprávnenia úspešne zmenené.');

    }

}
