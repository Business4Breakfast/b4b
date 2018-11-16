<?php

namespace App;

use App\Models\Club;
use App\Models\Setting\Industry;
use App\Models\Setting\Interest;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laratrust\Traits\LaratrustUserTrait;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','verification_code','verified', 'birthday', 'image', 'ext'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function industry()
    {
        return $this->belongsTo('App\Models\Setting\Industry', 'industry_id', 'id');
    }


    /**
     *get name of type ticket
     */
    public function getImageThumbAttribute() {

        if(strlen($this->image) > 0){
            $path = asset('images/user'). '/' . $this->id .'/small_sq/'.$this->image;
        } else {
            $path = asset('images/default'). '/sq/default_alpha.png';
        }

        return $path;
    }


    /**
     *get name of type ticket
     */
    public function getFullNameAttribute() {

        return $this->name . ' ' . $this->surname;
    }

    public function interest()
    {
        return $this->belongsToMany(Interest::class,'user_interest',
            'user_id','interest_id')->withPivot(['active']);
    }


    // vrati cluby v ktorych je host registrovany
    public function getClubBelongsUserGuest($user_id){

        $user_clubs = DB::table('user_club AS uc')
            ->select('c.title AS club', 'c.short_title AS club_short', 'c.id AS club_id', 'uc.user_id AS user_id')
            ->join('clubs as c', 'c.id', '=', 'uc.club_id')
            ->where('user_id', $user_id)->get();

        return $user_clubs;
    }


    // vrati zakladne data uzivatela na zaklade emailu
    public function getBasicUserDataFromEmail($email){

        $email = strtolower(trim(toAscii($email)));

        $user = DB::table('users AS u')
            ->select( 'u.name AS name','u.surname AS surname', 'u.id AS id', 'u.email AS email',
                'u.phone AS phone', 'u.status AS status_id', 'us.status AS status', 'u.company AS user_company', 'u.internet AS internet',
                'i.name AS industry'   )
            ->join('industries_list AS i', 'i.id', '=', 'u.industry_id', 'LEFT OUTER')
            ->join('user_status AS us', 'us.id', '=', 'u.status')
            ->where('email', '=', $email)
            ->whereNotIn('u.id', [0,1])
            ->get();

        return $user;
    }

    // vrati zakladne data uzivatela na zaklade mena a priezviska
    public function getBasicUserDataFromName($name, $surname){

        $name = strtolower(trim(toAscii($name)));
        $surname = strtolower(trim(toAscii($surname)));

        if (strlen($name) > 2 &&  strlen($surname) > 2 ){

            $user = DB::table('users AS u')
                ->select( 'u.name AS name','u.surname AS surname', 'u.id AS id', 'u.email AS email',
                    'u.phone AS phone', 'u.status AS status_id', 'us.status AS status', 'u.company AS user_company', 'u.internet AS internet',
                    'i.name AS industry'   )
                ->join('industries_list AS i', 'i.id', '=', 'u.industry_id', 'LEFT OUTER')
                ->join('user_status AS us', 'us.id', '=', 'u.status')
                ->whereRaw('LOWER(`u`.`surname`) = ? ',[  $surname ])
                ->whereRaw('LOWER(`u`.`name`) = ? ',[  $name ])
                ->whereNotIn('u.id', [0,1])
                ->get();

            return $user;

        } else null;

    }


    // vrati zakladne data uzivatela na zaklade telefonu
    public function getBasicUserDataFromPhone($phone){

        $phone = strtolower(trim(toAscii($phone)));

        $user = DB::table('users AS u')
            ->select( 'u.name AS name','u.surname AS surname', 'u.id AS id', 'u.email AS email',
                'u.phone AS phone', 'u.status AS status_id', 'us.status AS status', 'u.company AS user_company', 'u.internet AS internet',
                'i.name AS industry'   )
            ->join('industries_list AS i', 'i.id', '=', 'u.industry_id', 'LEFT OUTER')
            ->join('user_status AS us', 'us.id', '=', 'u.status')
            ->where('phone', '=', $phone)
            ->whereNotIn('u.id', [0,1])
            ->get();

        return $user;
    }


    public function addNewGuestToDb($data, $club_id){

        $new_user_id = 0;
        $created_user = 0;

        if($data && $club_id > 0){

            if (isset($data->created_user) && $data->created_user > 0){
                $created_user = $data->created_user;
            } else {
                $created_user = $data->user_id;
            }

            $user = [];
            $user['title_before'] = $data->title_before;
            $user['name'] = ucfirst($data->name);
            $user['surname'] = ucfirst($data->surname);
            $user['title_after'] = $data->title_after;
            $user['email'] = $data->email;
            $user['phone'] = $data->phone;
            $user['internet'] = $data->internet;
            $user['company'] = $data->company;
            $user['created_at'] = Carbon::now()->toDateTimeString();
            $user['updated_at'] = Carbon::now()->toDateTimeString();
            $user['birthday'] = Carbon::createFromFormat('d.m.Y', '01.01.1970');
            $user['gender'] = $data->gender;
            $user['industry_id'] = $data->industry;
            $user['password'] = base64_encode($data->surname . md5($data->email) );
            $user['token_id'] = md5(uniqid());
            $user['admin'] = 0;
            $user['status'] = 1;
            $user['created_user'] = $created_user;

            // ziskanie info aktualne vlozeneho usera
            $new_user_id = DB::table('users')->insertGetId($user);

        }

        if($new_user_id > 0){
            if ($club_id > 0) {
                // vlozenie info o hostovi k prislusnemu klubu
                $this->addGuestInfoBelongsClub($new_user_id, $club_id);
            }
            return $new_user_id;

        } else {

            return null;
        }

    }



    //vlozi id uzivatela do tabulky ku ktorym klubom prislucha ako host
    public function addGuestInfoBelongsClub($user_id, $club_id){

        $user_id = intval($user_id);
        $club_id = intval($club_id);

        if($user_id > 0 && $club_id > 0){

            //overime ci existuje zaznam
            $count = DB::table('user_club')
                ->where('user_id', $user_id)
                ->where('club_id', $club_id)
                ->count();

            // ak neexistuje vlozime
            if ($count == 0 ){

                DB::table('user_club')->insert(['user_id' => $user_id, 'club_id' => $club_id ]);

                return true;

            }

        }

        return false;
    }



    public function getUserChangesUpdate($request, $user_id){


        if($request && $user_id > 0){

            $module = 'user_edit';
            $module_id = $user_id;
            $description = 'Editácie užívateľa :';

            $user_old = User::find($user_id);

            if(isset($request->name ) && $user_old->name != trim($request->name)) {
                $this->addUserUpdateStatus($user_id, $user_old->name, $request->name, $module, $module_id, $description);
            }

            if(isset($request->surname ) && $user_old->surname != trim($request->surname)) {
                $this->addUserUpdateStatus($user_id, $user_old->surname, $request->surname, $module, $module_id, $description);
            }

            if(isset($request->email ) && $user_old->email != trim($request->email)) {
                $this->addUserUpdateStatus($user_id, $user_old->email, $request->email, $module, $module_id, $description);
            }

            if($user_old->phone != trim($request->phone)) {
                $this->addUserUpdateStatus($user_id, $user_old->phone, $request->phone, $module, $module_id, $description);
            }

            if( isset($request->password) && strlen($request->password) > 0 ) {
                $this->addUserUpdateStatus($user_id, '', '', 'user_password', $module_id, 'heslo bolo zmenene');
            }

            if(isset($request->birthday) && strcmp(Carbon::createFromFormat('Y-m-d', $user_old->birthday)->format('d.m.Y'), trim($request->birthday)) != 0) {
                $this->addUserUpdateStatus($user_id, Carbon::createFromFormat('Y-m-d', $user_old->birthday)->format('d.m.Y'), $request->birthday, $module, $module_id, $description);
            }

            if(isset($request->internet ) && $user_old->internet != trim($request->internet)) {
                $this->addUserUpdateStatus($user_id, $user_old->internet, $request->internet, $module, $module_id, $description);
            }
            if(isset($request->company ) &&  $user_old->company != trim($request->company)) {
                $this->addUserUpdateStatus($user_id, $user_old->company, $request->company, $module, $module_id, $description);
            }
            if(isset($request->industry_id ) && $user_old->industry_id != trim($request->industry_id)) {

                $industry = Industry::all()->toArray();
                $desc = $industry[array_search( $user_old->industry_id, array_column($industry, 'id'))]['name'];
                $desc .= ' -> '. $industry[array_search( $request->industry_id, array_column($industry, 'id'))]['name'];

                $this->addUserUpdateStatus($user_id, $user_old->industry_id, $request->industry_id, $module, $module_id,
                    $description . ',  ' . $desc);

            }
            if(isset($request->status) && $user_old->status != trim($request->status)) {

                $statuses = DB::table('user_status')->get()->toArray();

                $desc = $statuses[array_search( $user_old->status, array_column($statuses, 'id'))]->status;
                $desc .= ' -> '. $statuses[array_search( $request->status, array_column($statuses, 'id'))]->status;

                $this->addUserUpdateStatus($user_id, $user_old->status, $request->status, 'user_status', $module_id,
                    $description . ',  ' . $desc);
            }
            if($user_old->description != trim($request->description)) {
                $this->addUserUpdateStatus($user_id, $user_old->description, $request->description, $module, $module_id, $description);
            }

        }

    }


    public function addUserUpdateStatus($user_id, $value_before, $value_after, $module, $module_id, $description){


        if(intval($user_id) > 0 && strlen($module) > 0 && $module_id > 0){

            $data['user_id'] = intval($user_id);
            $data['value_before'] = trim($value_before);
            $data['value_after'] = trim($value_after);
            $data['module'] = strtolower($module);
            $data['module_id'] = intval($module_id);
            $data['module_id'] = trim($module_id);
            $user['created_at'] = Carbon::now()->toDateTimeString();
            $data['updated_at'] = Carbon::now()->toDateTimeString();
            $data['description'] = trim($description);

            $res = DB::table('user_updates')->insertGetId($data);

            return $res;

        } else return null;

    }

    // funkcia vrati aktualne funkcie v kluboch pre daneho usera
    public function getActualUserFunction($user_id)
    {

        $management = null;

        // zistime v ktorych kluboch ma user clenstvo
        $club_class = new Club();
        $user_clubs = $club_class->getClubsFromUsers($user_id);

        if ($user_clubs){
            //zistime typy funkcii managementu
            $user_function = DB::table('user_function')->where('management', 1)->get();

            // najdeme funcie pre dany klub a a typ funkcie
            foreach ($user_clubs as $uc){
                if($user_function){
                    foreach ($user_function as $uf){
                        $management_users = DB::table('club_users AS cu')
                            ->select('cu.*', 'c.title AS club_title', 'c.short_title AS club_short_title', DB::raw('concat(u.name, " ", u.surname) AS user_name'),
                                'uf.name AS function_name', 'uf.display_name AS display_name')
                            ->where('cu.club_id', $uc->id )
                            ->where('cu.user_function_id', $uf->id)
                            ->leftJoin('user_function as uf', 'uf.id', '=', 'cu.user_function_id')
                            ->leftJoin('clubs as c', 'c.id', '=', 'cu.club_id')
                            ->leftJoin('users as u', 'u.id', '=', 'cu.user_id')
                            ->orderBy('cu.user_function_id')
                            ->latest('cu.id')
                            ->take(1)
                            ->first();

                        // ak najdena funkcia je pre daneho uzivatela ulozime do pola
                        if($management_users && $management_users->user_id > 0 && $management_users->user_id == $user_id ){
                            $management[] = $management_users;
                        }
                    }
                }

            }

            return $management;
        }

        return null;
    }




}
