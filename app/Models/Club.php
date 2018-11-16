<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Club extends Model
{

    var $club_id = null;

    protected $fillable = [
        'short_title',
        'title',
        'host_name',
        'time_from',
        'time_to',
        'repeat_day',
        'repeat_interval',
        'address_street',
        'address_psc',
        'address_city',
        'address_country',
        'address_description',
        'host_url',
        'description',
        'image',
        'lat',
        'lng',
        'active',
        'price',
        'county_id',
        'district_id',
        'franchisor_id'
    ];


    public function setLngAttribute($value)
    {
        $this->attributes['lng'] = floatval($value);
    }

    public function setLatAttribute($value)
    {
        $this->attributes['lat'] = floatval($value);

    }


    public function getImageThumbAttribute() {

        if(strlen($this->image) > 0){
            $path = asset('images/club'). '/' . $this->id .'/sq/'.$this->image;
        } else {
            $path = asset('images/default'). '/sq/default_alpha.png';
        }

        return $path;
    }

    // id user manager clubu ak nie je vrati franchisora
    public function  getExecutiveManagerFromClub($club_id){

        $res =  DB::table('club_users')

            ->where('club_id',$club_id )
            ->where('user_function_id', 1)
            ->orderByDesc('valid_from')
            ->take(1)
            ->value('user_id');
        if ($res == 0){

            return  DB::table('clubs AS c')
                ->join('franchisors AS f', 'c.franchisor_id', '=', 'f.id', 'LEFT')
                ->where('c.id', $club_id)
                ->value('f.user_id');

        }else{
            return $res;
        }

    }


    // vracia existujuce funckcie vykonneho timu
    public function getExecutiveTeamFromClub($club_id, $function_id=null)
    {

        $query = DB::table('user_function')->orderBy('name')->where('management', 1);

        if ($function_id > 0){

            $query->where('id', intval($function_id) );
        }

        $user_function = $query->get();

        return $user_function;

    }




    //funkcia vracia hosti ktory boli pozvany na ranajky daneho klubu
    public function getGuestsFromClub($club_id, $status=null, $status_not_listing=null, $search=null, $user_member=null, $user_create=null)
    {

        $this->club_id = intval($club_id);

        $query = DB::table('users AS u');
        $query->select('u.*', 'us.id AS status_id', 'us.status AS status_name',
                         'u2.name AS created_user_name','u2.surname AS created_user_surname',
                         DB::raw('CONCAT(u2.name," ", u2.surname ) AS invited_user') );

        $query->join('user_status AS us', 'us.id', '=', 'u.status', 'LEFT' );
        $query->join('users AS u2', 'u.created_user', '=', 'u2.id', 'LEFT OUTER' );


        if(strlen($search)>2){
                $query->where(DB::raw('CONCAT(u.name," ",u.surname)'), 'LIKE', "%{$search}%");
        }

        if($user_create){
            $query->where('u.created_user', '=', $user_create );
        }

        $query->whereIn('u.id',function($query){
            $query->select('user_id')->from('user_club')->where('club_id', $this->club_id);
        });

         // ak su definovane stavy ktore nezobrazujeme
        if($status_not_listing){
            if(!is_array($status_not_listing))  $status_not_listing = [$status_not_listing];
            $query->whereNotIn('u.status', $status_not_listing);
        }

        // ak su definovane stavy
        if($status){
            if(!is_array($status))  $status = [$status];
            $query->whereIn('u.status', $status);
        }

        $query->orderBy('u.surname');

        $users = $query->paginate(50);

        return $users;

    }


    //funkcia vracia pocet uzivatelov ktory maju aktivne clenstvo v danom kluba po termine
    public function countNewUsersFromClub($club_id, $active=1, $start_from)
    {

        $club_id = intval($club_id);
        $active = intval($active);

        $membership_user =  DB::table('membership_user')
            ->select('user_id')
            ->join('memberships','membership_user.membership_id','=', 'memberships.id' )
            ->rightJoin('membership_club', 'membership_club.membership_id','=', 'memberships.id' )
            ->where('membership_club.club_id', '=', $club_id)
            ->where('memberships.active', $active )
            ->where('memberships.valid_from','>', $start_from )
            ->distinct()
            ->count('user_id');

        return $membership_user;

    }

    //funkcia vracia uzivatelov ktory maju aktivne clenstvo v danom kluba
    public function getUsersFromClub($club_id, $active=1)
    {

        $club_id = intval($club_id);
        $active = intval($active);

        $membership_user =  DB::table('membership_user')
            ->select('user_id')
            ->join('memberships','membership_user.membership_id','=', 'memberships.id' )
            ->rightJoin('membership_club', 'membership_club.membership_id','=', 'memberships.id' )
            ->where('membership_club.club_id', '=', $club_id)
            ->where('memberships.active', $active )
            ->distinct()
            ->pluck('user_id');

        $users = User::whereIn('id' , $membership_user->toArray())
                        ->orderBy('surname')
                        ->get();

        return $users;

    }


    //funkcia vracia uzivatelov ktory maju aktivne clenstvo v cluboch
    public function getUsersFromClubs($clubs, $active=1)
    {

        $active = intval($active);

        $membership_user =  DB::table('membership_user AS mu')
            ->select('mu.user_id')
            ->join('memberships AS m','mu.membership_id','=', 'm.id', 'LEFT' )
            ->join('membership_club AS mc', 'mc.membership_id','=', 'm.id', 'LEFT' )
            ->whereIn('mc.club_id', $clubs->toArray())
            ->where('m.active', $active )
            //->distinct()
            ->pluck('mu.user_id');

        $users = User::whereIn('id' , $membership_user->toArray())->get();

        return $users;

    }


    //funkcia vracia uzivatelov ktory maju aktivne clenstvo v danom kluba
    public function getUsersFromClubWithCompany($club_id, $active=1)
    {

        $club_id = intval($club_id);
        $active = intval($active);

        $membership_user =  DB::table('membership_user AS mu')
            ->select('mu.user_id AS id', 'u.name', 'u.surname', 'u.phone',
                        'u.email', 'c.company_name as company_name', 'c.title AS company_title',
                         'uil.name as user_industry', 'm.valid_from', 'm.valid_to', 'm.id AS membership_id',
                         'c.image AS company_image', 'c.id AS company_id', 'mu.id AS membership_user_id')
            ->join('memberships AS m','mu.membership_id','=', 'm.id', 'LEFT' )
            ->join('membership_club as mc', 'mc.membership_id','=', 'm.id' )
            ->join('companies AS c', 'c.id', '=', 'company_id')
            ->join('users AS u', 'u.id', '=', 'mu.user_id')
            ->join('industries_list AS uil', 'uil.id', '=', 'u.industry_id', 'LEFT OUTER')
            ->where('mc.club_id', '=', $club_id)
            ->where('m.active', $active )
            //->distinct()
            ->get();

        return $membership_user;

    }



    // funkcia fracia cluby v ktorych ma user aktivne clenstvo
    public function getClubsFromUsers($user_id, $active=1)
    {

        $user_id = intval($user_id);
        $active = intval($active);

        $membership_club =  DB::table('membership_club AS mc')
            ->select('mc.club_id')
            ->join('memberships AS m','mc.membership_id','=', 'm.id' )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->where('mu.user_id', '=', $user_id)
            ->where('m.active', $active )
            ->distinct()
            ->pluck('mc.club_id');

        $clubs = Club::whereIn('id' , $membership_club->toArray())->get();

        return $clubs;
    }



    // funkcia fracia tru/false ak clen ma aktivne clenstvo v danom klube
    public function hasUserMembershipInClub($user_id, $club_id, $active=1)
    {

        $user_id = intval($user_id);
        $club_id = intval($club_id);
        $active = intval($active);

        $user_club =  DB::table('membership_club AS mc')
            ->select('mc.club_id')
            ->join('memberships AS m','mc.membership_id','=', 'm.id' )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->where('mu.user_id', $user_id)
            ->where('m.active', $active )
            ->where('mc.club_id', $club_id)
            ->distinct()
            ->count('mc.club_id');

        return $user_club;
    }


    // funkcia fracia colekciu klubov v ktorych ma user aktivne clenstvo
    public function getIdClubsFromUsers($user_id, $active=1)
    {

        $user_id = intval($user_id);
        $active = intval($active);

        $membership_club =  DB::table('membership_club AS mc')
            ->select('mc.club_id')
            ->join('memberships AS m','mc.membership_id','=', 'm.id' )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->where('mu.user_id', '=', $user_id)
            ->where('m.active', $active )
            ->distinct()
            ->pluck('mc.club_id');

        return $membership_club;
    }


    // funkcia fracia aktualnych clenov vykonneho timu z klubu
    public function getUserManagersTeamFromClubs($club_id)
    {
        $res = null;
        $user_function = DB::table('user_function')->where('management', 1)->get();

        $i = 0;
        if ($user_function){
            foreach ($user_function as $kuf => $uf){

                $function = DB::table('club_users AS cu')
                    ->join('user_function AS uf', 'uf.id', '=', 'cu.user_function_id'  )
                    ->where('cu.club_id', $club_id)
                    ->where('cu.user_function_id', $uf->id)
                    ->orderByDesc('cu.valid_from')
                    ->first();

                if ($function){

                    $user = User::find($function->user_id);

                    if ($user){
                        $user->function = $function;
                        $res[$i] = $user;
                        $i++;
                    }

                }
            }
        }

        return $res;

    }


    public function referenceCouponsStat($date_from=null, $date_to=null, $club=null, $event_type=null){


        $whereQuery = "";

        if ($date_from) $whereQuery .= " AND rc1.date  >  $date_from  ";
        if ($date_to) $whereQuery .= " AND rc1.date  <  $date_to  ";
        if ($event_type) $whereQuery = " AND e1.event_type = $event_type  ";
        if ($club) $whereQuery = " AND e1.club_id = $club ";


        $query = DB::table('reference_coupons AS rc')

            ->select(
                DB::raw('count(rc.id) as `records`'),
                DB::raw('sum(rc.price) as `price`'),
                DB::raw('YEAR(rc.date) year, 
                                MONTH(rc.date) month'),


                DB::raw('( SELECT count(rc1.id)   FROM  reference_coupons AS rc1
                                LEFT JOIN events as e1 ON e1.id = rc1.reference_id
                                WHERE rc1.reference_type = 1 
                                AND MONTH(rc1.date) = month
                                AND YEAR(rc1.date) = year
                                '. $whereQuery .'
                                 ) as `reference`' ),

                DB::raw('( SELECT count(rc1.id)   FROM  reference_coupons AS rc1
                                LEFT JOIN events as e1 ON e1.id = rc1.reference_id
                                WHERE rc1.reference_type = 3 
                                AND MONTH(rc1.date) = month
                                AND YEAR(rc1.date) = year
                                '. $whereQuery .'
                                 ) as `face_to_face`' ),

                DB::raw('( SELECT count(rc1.id)   FROM  reference_coupons AS rc1
                                LEFT JOIN events as e1 ON e1.id = rc1.reference_id
                                WHERE rc1.reference_type = 4 
                                AND MONTH(rc1.date) = month
                                AND YEAR(rc1.date) = year
                                '. $whereQuery .'
                                 ) as `evidence`' ),

                DB::raw('( SELECT count(rc1.id)   FROM  reference_coupons AS rc1
                                LEFT JOIN events as e1 ON e1.id = rc1.reference_id
                                WHERE rc1.reference_type = 6
                                AND MONTH(rc1.date) = month
                                AND YEAR(rc1.date) = year
                                '. $whereQuery .'
                                 ) as `thank_you`' )


            );

            $query->join('events AS e', 'e.id','=', 'rc.reference_id', 'LEFT');

            if ($date_from) $query->where('rc.date','>', $date_from);
            if ($date_to) $query->where('rc.date','<', $date_to);
            if ($event_type) $query->where('e.event_type','=', $event_type);
            if ($club) $query->where('e.club_id','=', $club);

        $query->groupby('year','month' );

        $res = $query->get();

            //->toSql();

        return $res;
    }




}
