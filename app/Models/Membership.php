<?php

namespace App\Models;

use App\Models\Finance\Invoice;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Membership extends Model
{
    //
    protected $fillable = [

        'valid_from',
        'valid_to',
        'company_id',
        'club_id',
        'active',
        'price',
        'divide_50',
        'description',
        'renew_id'
    ];



    public function setValidFromAttribute($value)
    {
        $this->attributes['valid_from'] = Carbon::createFromFormat('d.m.Y H:i:s', $value. '00:00:01');
    }

    public function setValidToAttribute($value)
    {
        $this->attributes['valid_to'] = Carbon::createFromFormat('d.m.Y H:i:s', $value. '23:59:59');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company','company_id','id');
    }

    public function club()
    {
        return $this->belongsToMany(Club::class,'membership_club',
            'membership_id','club_id')->withPivot(['active'])->withTimestamps();
    }

    public function user()
    {
        return $this->belongsToMany(User::class,'membership_user',
            'membership_id', 'user_id')->withPivot(['active'])->withTimestamps();
    }

    public function invoice()
    {
        return $this->belongsToMany(Invoice::class,'membership_invoice',
            'membership_id', 'invoice_id')->withPivot(['variable_symbol'])->withTimestamps();
    }


    public function getUsersFromMembersip($membership_id){

        return DB::table('membership_user AS mu')
            ->select(
                DB::raw("CONCAT(u.name,' ',u.surname) as full_name")
                )
            ->join('users AS u', 'u.id', '=', 'mu.user_id')
            ->where('mu.membership_id', intval($membership_id))
            ->get();

    }

    public function getClubsFromMembersip($membership_id){

        return DB::table('membership_club AS mc')
            ->select( 'c.short_title', 'c.title')
            ->join('clubs AS c', 'c.id', '=', 'mc.club_id')
            ->where('mc.membership_id', intval($membership_id))
            ->get();

    }


    // funkcia fracia clenstvo pre uzivatela a klub
    public function getMembershipForUserAndClub($user_id, $club_id, $active=1)
    {

        $user_id = intval($user_id);
        $club_id = intval($club_id);
        $active = intval($active);

        $user_club =  DB::table('membership_club AS mc')
            ->select('mc.club_id','m.*', 'mu.user_id')
            ->join('memberships AS m','mc.membership_id','=', 'm.id' )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->where('mu.user_id', $user_id)
            ->where('m.active', $active )
            ->where('mc.club_id', $club_id)
            ->get();

        return $user_club;

    }


    // funkcia vracia clenstvo pre uzivatela a klub
    // except membership okrem clenstva id
    public function getAllMembershipForUser($user_id, $active=1, $except_membership=0)
    {

        $user_id = intval($user_id);
        $active = intval($active);
        $except_membership = intval($except_membership);

        $query =  DB::table('membership_club AS mc')
            ->select('mc.club_id','m.*', 'mu.user_id')
            ->join('memberships AS m','mc.membership_id','=', 'm.id' )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->where('mu.user_id', $user_id)
            ->where('m.active', $active );

        if ($except_membership > 0){
            $query->where('m.id', '<>', $except_membership );
        }

        $user_club = $query->get();

        return $user_club;

    }



    // funkcia fracia clenstva pre uzivatela
    public function getMembershipsForUsers($user_id, $active=1)
    {

        $user_id = intval($user_id);
        $active = intval($active);
//
        $query = DB::table('memberships AS m')
            ->select( 'm.valid_from', 'm.valid_to', 'm.id AS membership_id', 'mc.club_id AS club_id',
                        'm.active AS membership_active', 'c.title AS club_title', 'm.id as membership_id'
            )
            ->rightJoin('membership_user AS mu', 'mu.membership_id','=', 'm.id' )
            ->rightJoin('membership_club AS mc', 'mc.membership_id','=', 'm.id' )

            ->join('users AS u', 'u.id', '=', 'mu.user_id')
            ->join('clubs AS c', 'c.id', '=', 'mc.club_id')

            ->where('mu.user_id', intval($user_id));

            // ak je aktivne zobrazime  len aktivne inac vsetky
            if($active > 0){
                $query->where('m.active', $active);
            }

            $query->orderByDesc('m.valid_to');

            $user_memberships =  $query->get();


        return $user_memberships;

    }



}
