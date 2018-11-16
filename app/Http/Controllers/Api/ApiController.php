<?php

namespace App\Http\Controllers\Api;

use App\Models\Club;
use App\Models\Event\Event;
use App\Models\Membership;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function __construct()
    {

    }


    public function clubsListings()
    {

        $club_class = new Club();
        $club = Club::where('active', 1)->get();

        if ($club){
            foreach ($club as $c){

                $c->image_full = url('/images/club/' . $c->id ) . '/' . $c->image;
                $c->front_end_url = str_slug('bforb ' . $c->short_title . ' ' . $c->address_city);
                $c->event = Event::where('club_id', $c->id)
                    ->where('event_from', '>' , Carbon::now() )
                    ->orderBy('event_from')
                    ->first();

                // vrati membereships a company pre dany club
                $res_membership = null;
                $membership = null;
                $membership = $club_class->getUsersFromClubWithCompany($c->id);

                if ($membership){
                    foreach ($membership as $k =>$m){

                        $res_membership[$k] = $m;

                        if ($m->company_image) {
                            $res_membership[$k]->company_image_full = url('/images/company/' . $m->company_id ) . '/' . $m->company_image;
                        }else{
                            $res_membership[$k]->company_image_full = null;
                        }

                    }
                }

                $c->memberships = $res_membership;

                $res[] = $c;

            }
        }

        return  response()->json(['auth' => 'true', 'data' =>$res]);

    }


    public function clubDetail($club)
    {

        $id = intval($club);
        $res = null;

        $club = null;

        $club_class = new Club();

        $club = Club::find($id);

        if($club){

            $id_manager = $club_class->getExecutiveManagerFromClub($club->id);
            $manager = User::find($id_manager);
            if($manager){
                $club->manager = $manager->full_name;
            }else{
                $club->manager = null;
            }
            $club->front_end_url = str_slug('bforb ' . $club->short_title . ' ' . $club->address_city);
            $club->image_full = url('/images/club/' . $club->id ) . '/' . $club->image;
            $club->event = Event::where('club_id', $club->id)
                ->where('event_from', '>' , Carbon::now() )
                ->orderBy('event_from')
                ->first();

            // vrati membereships a company pre dany club
            $res_membership = null;
            $membership = null;
            $membership = $club_class->getUsersFromClubWithCompany($club->id);

            if ($membership){
                foreach ($membership as $k =>$m){

                    $res_membership[$k] = $m;

                    if ($m->company_image) {
                        $res_membership[$k]->company_image_full = url('/images/company/' . $m->company_id ) . '/' . $m->company_image;
                    }else{
                        $res_membership[$k]->company_image_full = null;
                    }

                }
            }

            $club->memberships = $res_membership;

            $res = $club;

        }

        return  response()->json(['auth' => 'true', 'data' =>$res]);

    }

    public function priceTickets()
    {

        $club_class = new Club();

        $res = $club_class->referenceCouponsStat();

        return  response()->json(['auth' => 'true', 'data' => $res]);


    }

    public function priceTicketsTotal()
    {

        $club_class = new Club();

        $tickets = $club_class->referenceCouponsStat();

        $res['tickets'] = $tickets->sum('records');
        $res['price'] = $tickets->sum('price');

        // kluby okrem bforb.sk
        $res['clubs'] = DB::table('clubs')->where('active', 1)->count() - 1;

        //pocet clenov
        $res['members']= DB::table('memberships')->where('active', 1)->count();


        return  response()->json(['auth' => 'true', 'data' => $res]);


    }

}
