<?php

namespace App\Http\Controllers\Setting;

use App\Models\Club;
use App\Models\Setting\Industry;
use App\Models\Setting\Interest;
use App\Models\UploadImages;
use App\Permission;
use App\Role;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Session;

class UserController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.user.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('setting.user.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $club_class = new Club();

        $user_clubs = $club_class->getClubsFromUsers(Auth::user()->id);

        $users = User::with('roles')
                        ->whereNotIn('id', [0,1])
                        ->where('admin', 1)->orderBy('surname')
                        ->get();


        $q = DB::table('users AS u')
                    ->select('id', DB::raw('concat(u.name, " ", u.surname) AS user_name'),
                                'phone AS phone', 'email AS email', 'image', 'name', 'surname'
                            )
                    ->where('admin', 1)
                    ->whereNotIn('id', [0,1]);

//        $q->whereExists(function ($query) {
//            $query->select('mu.id')
//                ->from('membership_user AS mu')
//                ->whereRaw('mu.user_id = u.id')
//                ->where('mu.user_id', 10 );
//        });

        $users_db = $q->get();

        //dd($users_db);

        // pridame info o useroch
        if ( $users_db ){
            foreach ($users_db as $v){
                $v->clubs = $club_class->getClubsFromUsers($v->id);

                if(strlen($v->image) > 0){
                    $v->image_thumb = asset('images/user'). '/' . $v->id .'/small_sq/'.$v->image;
                } else {
                    $v->image_thumb  = asset('images/default'). '/sq/default_alpha.png';
                }

                $v->roles = DB::table('role_user AS ru')
                    ->select('r.name AS name', 'r.display_name AS display_name', 'r.id AS role_id')
                    ->join('roles AS r', 'r.id', '=', 'ru.role_id' )
                    ->where('ru.user_id', $v->id)
                    ->get();

            }
        }


        return view('setting.user.index')
            ->with('items', $users_db);
    }


    /**
     * Display a detail of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileLogged()
    {
        $user = Auth::user();
        $roles = Role::all();

        return view('setting.user.profile')
            ->with('roles', $roles)
            ->with('user', $user);
    }

    /**
     * Display a detail of the user.
     *
     * @return \Illuminate\Http\Response
     */
    public function profileEdit($id)
    {
        $user = User::find($id);
        $roles = Role::all();

        return view('setting.user.profile')
            ->with('roles', $roles)
            ->with('user', $user);
    }

    public function profileSave(Request $request)
    {

        $uploadImages = new UploadImages();
        $user = Auth::user();
        $module = 'user';

        $rules = [
            'title_before' => 'max:10',
            'title_after' => 'max:10',
            'phone' => 'required|numeric|phone',
            'password' => 'min:8|max:200',
            'birthday' => 'required',
            'gender' => 'required',
        ];

        $this->validate($request,$rules);


        $u = [];
        $u['title_before'] = $request->title_before;
        $u['title_after'] = $request->title_after;
        $u['phone'] = $request->phone;
        $u['internet'] = $request->internet;
        $u['company'] = $request->company;
        $u['updated_at'] = Carbon::now()->toDateTimeString();
        $u['birthday'] = Carbon::createFromFormat('d.m.Y', $request->birthday);
        $u['gender'] = $request->gender;


        if(strlen($request->password)>7){
            $u['password'] = bcrypt($request->password);
        }

        DB::table('users')->where('id','=', $user->id )->update($u);


        $fullName = str_slug($user->fullName);

        // len ak je odoslany image
        if($request->hasFile('files')){
            $uploadImages->procesImage($request->files, $module, $user->id, $fullName);
        }

        return redirect()->route('dashboard.index');

    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $industry = Industry::orderBy('name')->get();
        $interest = Interest::orderBy('name')->get();

        return view('setting.user.add')
            ->with('industry', $industry)
            ->with('interest', $interest)
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
          'company' => 'required',
          'email'    => ['required', 'unique:users,email'],
          'phone' => 'required|numeric|phone',
          'password' => 'required|max:200',
          'password_confirmation' => 'required|max:200',
          'birthday' => 'required|date|date_format:d.m.Y',
          'gender' => 'required',

        ];

        $this->validate($request,$rules);

        $user = [];
        $user['title_before'] = $request->title_before;
        $user['name'] = $request->name;
        $user['surname'] = $request->surname;
        $user['title_after'] = $request->title_after;
        $user['email'] = $request->email;
        $user['phone'] = $request->phone;
        $user['internet'] = $request->internet;
        $user['password'] = bcrypt($request->password);
        $user['created_at'] = Carbon::now()->toDateTimeString();
        $user['updated_at'] = Carbon::now()->toDateTimeString();
        $user['birthday'] = Carbon::createFromFormat('d.m.Y', $request->birthday);
        $user['gender'] = $request->gender;
        $user['industry_id'] = $request->industry;
        $user['company'] = $request->company;
        $user['job_position'] = $request->job_position;
        $user['password'] = base64_encode($request->surname . 'password');
        $user['token_id'] = md5(uniqid());
        $user['admin'] = 1;
        $user['status'] = 5;


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


        //interest
        $userInterest=[];
        if($request->interest){
            foreach ($request->interest as $item){
                $userInterest[] = $item;
            }
        }
        $newUser->interest()->sync($userInterest);

        return redirect()->route('setting.user.index')->with('message', 'Užívateľ úspešne vytvorený');
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

        dump($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $club_class = new Club();

        $user = User::with('roles')->find($id);
        $roles = Role::all();
        $industry = Industry::orderBy('name')->get();
        $interest = Interest::orderBy('name')->get();
        $user_clubs = $club_class->getClubsFromUsers($id);

        return view('setting.user.edit')
            ->with('roles', $roles)
            ->with('industry', $industry)
            ->with('interest', $interest)
            ->with('user_clubs', $user_clubs)
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
        $uploadImages = new UploadImages();
        $user_class = new User();
        $module = 'user';

        $rules = [
            'title_before' => 'max:10',
            'name' => 'required|max:50',
            'surname' => 'required|max:50',
            'title_after' => 'max:10',
            'account' => 'required',
            'email' => Rule::unique('users')->ignore($id, 'id'),
            'industry' => 'required',
            'phone' => 'required|numeric|phone',
            'company' => 'required',
            'password' => 'min:8|max:200',
            'birthday' => 'required|date_format:d.m.Y',
            'gender' => 'required',
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
        $user['gender'] = $request->gender;
        $user['job_position'] = $request->job_position;
        $user['industry_id'] = $request->industry;
        $user['company'] = $request->company;
        $user['internet'] = $request->internet;
        $user['admin'] = 1;
        $user['status'] = 5;

        if(strlen($request->password)>7){
            $user['password'] = bcrypt($request->password);
        }

        //zalogujeme zmeny v editacii
        $user_class->getUserChangesUpdate($request, $id);

        DB::table('users')->where('id','=', $id )->update($user);


        $fullName = str_slug($editUser->fullName);
        // len ak je odoslany image
        if($request->hasFile('files')){

            $uploadImages->deleteImage($module, $id);

            $uploadImages->procesImage($request->files, $module, $id, $fullName);
        }

        //roles
        $userRoles=[];
        if($request->account){
            foreach ($request->account as $item){
                $userRoles[] = $item;
            }
        }
        $editUser->syncRoles($userRoles);

        //interest
        $userInterest=[];
        if($request->interest){
            foreach ($request->interest as $item){
                $userInterest[] = $item;
            }
        }
        $editUser->interest()->sync($userInterest);

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

    // zmena nalogovaneho usera
    public function changeUserLogin(Request $request)
    {

        if($request->auth_change_user) {
            Session::put('change_user',  $request->auth_change_user );
        }

        Auth::loginUsingId(intval($request->select_user_change));

        // po zmene uzivatela zapiseme jeho funkcie do session
        $user_class = new User();
        $management = $user_class->getActualUserFunction(Auth::user()->id);
        Session::put('user_function',  $management);

        return redirect()->back();

    }


}
