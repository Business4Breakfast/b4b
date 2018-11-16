<?php

namespace App\Http\Controllers\Setting;

use App\Helpers\VATCheck\VatCheck;
use App\Models\Club;
use App\Models\Franchisor;
use App\Models\UploadImages;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Laratrust;

class ClubController extends Controller
{

    private $request = null;

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.club.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('setting.club.index'), 'icon' => 'list', 'class' => 'btn-success']
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
    public function index( Request $request)
    {

        $club_class = new Club();
        $query = Club::query();
        $req = [];
        $this->request = $request;

        $users_clubs = $club_class->getClubsFromUsers(Auth::user()->id)->pluck('id')->toArray();

        if($users_clubs && !Laratrust::can('club-listing-all') ){
            $query = $query->whereIn('id', $users_clubs);
        }

        if (isset($request->search_status)) {
            if ($request->search_status == 1) {
                $query = $query->where('active', 1);
                $req['search_status'] = 1;
            } elseif ($request->search_status == 0){
                $query = $query->where('active', 0);
                $req['search_status']  = 0;
            } else {
                $req['search_status']  = 2;
            }
        } else {
            $query = $query->where('active', 1);
            $req['search_status'] = 1;
        }

        if (isset($request->search_club) &&  strlen($request->search_club) > 0) {
            $query = $query->where(function($query)
            {
                $query->orWhere('title', 'like', '%' . $this->request->search_club . '%')
                    ->orWhere('short_title', 'like', '%' . $this->request->search_club . '%');
            });
            $req['search_club'] = $request->search_club;

        } else $req['search_club'] = null;


        if (isset($request->search_address) &&  strlen($request->search_address) > 0) {
            $query = $query->where(function($query)
            {
                $query->orWhere('address_street', 'like', '%' . $this->request->search_address . '%')
                    ->orWhere('address_city', 'like', '%' . $this->request->search_address . '%');
            });
            $req['search_address'] = $request->search_address;

        } else $req['search_address'] = null;

        $clubs = $query->get();

        return view('setting.club.index')
            ->with('req', $req)
            ->with('items', $clubs);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        view()->share('backend_title', 'Vytvorenie nového klubu'); //title

        $countries = DB::table('countries')->get();
        $counties = DB::table('counties')->get();
        $districts = DB::table('districts')->get();

        $franchisors = Franchisor::all();

        return view('setting.club.add')
            ->with('counties', $counties)
            ->with('franchisors', $franchisors)
            ->with('districts', $districts)
            ->with('countries', $countries);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $club = Club::create($request->all());

        $image = new UploadImages();

        //if update succes and is files
        if ($club && $request->hasFile('files')){

            $image = new UploadImages();
            $image->procesImage($request->files, 'club', $club->id, $club->title);
        }

        return redirect()->route('setting.club.index')->with('message', 'Club úspešne vytvorený');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        view()->share('backend_title', 'Editácia klubu'); //title

        $club = Club::findOrFail($id);
        $countries = DB::table('countries')->get();
        $counties = DB::table('counties')->get();
        $districts = DB::table('districts')->get();

        $franchisors = Franchisor::all();

        return view('setting.club.edit')
            ->with('counties', $counties)
            ->with('franchisors', $franchisors)
            ->with('districts', $districts)
            ->with('countries', $countries)
            ->with('club', $club);
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

        $module = 'club';

        $rules = [
            'title' => 'required|max:50',
            'short_title' => 'required|max:50',
            'address_street' => 'required|max:50',
            'address_psc' => 'required|max:8',
            'address_city' => 'required|max:50',
            'address_country' => 'required|max:50',
            'franchisor_id' => 'required',
        ];

        $this->validate($request,$rules);

        //dd($request->all());

        $club = Club::findOrFail($id);

        $club_update = $club;
        $club_update->update($request->all());

        //if update succes and is files
        if ($club_update && $request->hasFile('files')){

            $image = new UploadImages();

            $image->deleteImage($module, $id);

            $image->procesImage($request->files, $module, $club->id, $club->title);
        }

        return redirect()->route('setting.club.index')->with('message', 'Club úspešne upravený');    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Club::destroy($id);
        return redirect()->route('setting.club.index')->with('message', 'Záznam vymazaný');
    }
}
