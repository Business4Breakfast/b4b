<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use App\Permission;
use Auth;
use Illuminate\Http\Request;
use Laratrust;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home.index');
    }


    public function minor()
    {

//        $owner = new Role();
//        $owner->name         = 'dispatcher';
//        $owner->display_name = 'Project Dispatcher'; // optional
//        $owner->description  = 'User is the dispatcher of a given project'; // optional
//        $owner->save();

        //$owner->syncPermissions([$createOffice, $editOffice]);

        if(Auth::check()){

            //aktualny nalogovany user
            dump(Auth::user()->id);

            $user = User::find(Auth::user()->id);

            dump(Auth::check());

            $routes = \Route::getRoutes()->getRoutesByMethod();

            //dump($routes['GET']);

            foreach ($routes['GET'] as $v){
                dump($v->uri);
            }

            //dump( $user->can('update-profile'));

            //dump($user->allPermissions());

            //dump(Role::all());
            //dump(Permission::all());

            dump(\Config::get('languages')[\App::getLocale()] );

            dump(\Session::all());

        }

        return view('home.minor');
    }


}
