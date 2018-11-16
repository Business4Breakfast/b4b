<?php

namespace App\Http\Controllers\Guests;

use App\Http\Controllers\Setting\IndustryController;
use App\Models\Club;
use App\Models\Setting\Industry;
use App\User;
use DeepCopy\f001\A;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GuestListingController extends Controller
{

    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('guests.guest-listings.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('guests.guest-listings.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request)
    {

        $guests = DB::table('users')
            ->whereNotIn('id',[0,1])
            ->orderBy('surname')
            ->paginate(1000);

        view()->share('backend_title', 'Prehľad hostí'); //title

        return view('guests.index')
            ->with('items', $guests);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $logged_user = Auth::user();
        $industry = Industry::orderBy('name')->get();

        //ak je admin a superadmin vidi vsetky kluby
        if($logged_user->hasRole(['superadministrator','administrator'])){
            $clubs = Club::where('active', 1)->get();
        }else{
            $club_class = new Club();
            //kluby v ktorych ma clen aktivne clenstva
            $clubs = $club_class->getClubsFromUsers( $logged_user->id );
        }

        return view('guests.add')
            ->with('clubs', $clubs)
            ->with('industry', $industry);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user_id = Auth::user()->id;

        $rules = [
            'title' => 'max:10',
            'phone' => 'required|numeric|phone',
            'name' => 'required',
            'surname' => 'required',
            'gender' => 'required',
            'internet' => 'required',
            'email'    => ['required', 'unique:users,email'],
            'industry_id' => 'required',

        ];

        $messages = [   'email.required' => 'Email je povinný',
                        'email.unique'=>'Zadaný email už existuje!'
        ];

        $this->validate($request,$rules, $messages);

        $request->merge([   'admin' => 0,
                            'token_id' => md5(uniqid()),
                            'status' => 1,
                            'created_user' => $user_id
                        ]);

        $data = $request->except(['_token', '/guests/guest-listings', 'club_id']);

        $user_id = DB::table('users')->insertGetId($data);

        // ak host priradeny klubu
        if($user_id > 0 && $request->club_id > 0){
            DB::table('user_club')->insert(['user_id' => $user_id, 'club_id' => $request->club_id] );
        }

        return redirect()->route('guests.guest-listings.index')->with('message', 'Kontakt  úspešne vytvorený');

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
        $industry = Industry::orderBy('name')->get();
        $clubs = Club::orderByDesc('active')->orderBy('short_title')->get();
        $user_statuses = DB::table('user_status')->get();

        $guest = DB::table('users')->find($id);

        $user_class = new User();
        $club_class = new Club();

        $user_clubs =  $user_class->getClubBelongsUserGuest($id);

        //funkcia vracia uzivatelov ktory maju aktivne clenstvo v cluboch
        $users_from_clubs =  $club_class->getUsersFromClubs($user_clubs->pluck('club_id'), 1);

        if(!$users_from_clubs->contains('id', $guest->created_user)){
            if ($guest->created_user > 0){
                $users_from_clubs->push(User::find($guest->created_user));
            }
        }

        return view('guests.edit')
            ->with('user_clubs', $user_clubs)
            ->with('user_statuses', $user_statuses)
            ->with('users_from_clubs', $users_from_clubs)
            ->with('clubs', $clubs)
            ->with('guest', $guest)
            ->with('industry', $industry);
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

        $user_class = new User();

        $rules = [
            'title_before' => 'max:10',
            'title_after' => 'max:10',
            'phone' => 'required|numeric|phone',
            'name' => 'required',
            'surname' => 'required',
            'gender' => 'required',
            'industry_id' => 'required',
            'internet' => 'url',
            'status' => 'required',
            //'created_user' => 'required',

            // oeverenie na db
            'email' => Rule::unique('users')->ignore($id, 'id')
        ];

        $messages = [   'email.required' => 'Email je povinný',
            'email.unique'=>'Zadaný email už existuje!'
        ];

        $this->validate($request,$rules, $messages);

        $user_clubs =  $user_class->getClubBelongsUserGuest($id);


        if(isset($request->club)){
            //pridat
            $add_record = array_diff($request->club, $user_clubs->pluck('club_id')->toArray());
            if ($add_record){
                foreach ($add_record as $add){

                        // zapiseme do updatov ze boli pridane kluby
                        $user_class->addUserUpdateStatus($id, '0', $add, 'user_club', $id, 'Pridaný klub : ');
                        DB::table('user_club')->insert(['user_id' => $id, 'club_id' => $add]);
                }
            }

            //odobrat
            $del_record = array_diff($user_clubs->pluck('club_id')->toArray(), $request->club);
            if ($del_record){
                foreach ($del_record as $del){
                    // zapiseme do updatov ze boli odobrane kluby
                    $user_class->addUserUpdateStatus($id, $del, '0', 'user_club', $id, 'Odobraný klub : ');
                    DB::table('user_club')->where('club_id', $del )->where('user_id', $id)->delete();
                }
            }

        } else {
            // ak nie je ziadny klub zmazeme existujuce pre usera
            DB::table('user_club')->where('user_id', $id)->delete();
        }

        //zalogujeme zmeny v editacii
        $user_class->getUserChangesUpdate($request, $id);

        $data = $request->only("gender","title_before", "name", "surname", "title_after", "status",
                                    "industry_id" , "email" , "phone", "internet", "company", "description", "created_user");

        DB::table('users')->where('id', $id )->update($data);

        return redirect()->route('guests.guest-listings.index')->with('message', 'Kontakt úspešne upravený');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('users')->delete($id);

        return redirect()->route('guests.guest-listings.index')
            ->with('message', 'Záznam úspešne vymazaný.');    }
}
