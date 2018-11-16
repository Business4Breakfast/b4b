<?php

namespace App\Http\Controllers\Developer;

use App\Models\Developer\Menu;
use App\Permission;
use App\Role;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Session;
use Validator;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('developer.menu-add'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('developer.menu'), 'icon' => 'list', 'class' => 'btn-success'],
                ['name' => 'Usporiadanie menu', 'link' => route('developer.menu-reorder'), 'icon' => 'list', 'class' => 'btn-success']

            ]
        ];

        view()->share('action_menu', $action);
        view()->share('backend_title', 'Msnu'); //title
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function menu()
    {

        $routeColectors = Route::getRoutes()->getRoutesByMethod();
        if($routeColectors['GET']){
            foreach ($routeColectors['GET'] as $v){
                $route[$v->uri] = $v->uri;
            }
        }
        $arr = Menu::GetMenuSetting();
        $menu_arr = Menu::GetMenuListing();

        $roles = Role::all();

        return view('developer.admin-menu.menu')
            ->with('route', $route)
            ->with('menu', $arr)
            ->with('menu_arr', $menu_arr)
            ->with('roles', $roles);
    }



    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function menuAdd()
    {

        $routeColectors = Route::getRoutes()->getRoutesByMethod();
        if($routeColectors['GET']){
            foreach ($routeColectors['GET'] as $v){
                $route[$v->uri] = $v->uri;
            }
        }
        $arr = Menu::GetMenuSetting();
        $menu_arr = Menu::GetMenuListing();

        $roles = Role::all();

        return view('developer.admin-menu.add')
            ->with('route', $route)
            ->with('menu', $arr)
            ->with('menu_arr', $menu_arr)
            ->with('roles', $roles);
    }


    public function menuReorder()
    {

        $routeColectors = Route::getRoutes()->getRoutesByMethod();
        if($routeColectors['GET']){
            foreach ($routeColectors['GET'] as $v){
                $route[$v->uri] = $v->uri;
            }
        }
        $arr = Menu::GetMenuSetting();
        $menu_arr = Menu::GetMenuListing();

        $roles = Role::all();

        return view('developer.admin-menu.reorder')
            ->with('route', $route)
            ->with('menu', $arr)
            ->with('menu_arr', $menu_arr)
            ->with('roles', $roles);
    }


    public function menuStore(Request $request)
    {
        $rules = [
            'title'      => 'required|max:30|min:4',
            'route'      => 'required',
            'rank'       => 'numeric',
        ];

        $this->validate($request, $rules);

        $menu = [];
        $menu['title'] = $request->title;
        $menu['route'] = $request->route;
        $menu['icon'] = $request->icon;
        $menu['block'] = $request->block;
        $menu['rank'] = intVal($request->rank);
        $menu['created_at'] = Carbon::now()->toDateTimeString();
        $menu['updated_at'] = Carbon::now()->toDateTimeString();

        $newMenu = DB::table('backend_menu')->insert($menu);

        if($newMenu){

            $createPermission = new Permission();
            $permissions = Permission::all()->pluck('name')->toArray();
            if(in_array(trim($createPermission->name)  , $permissions)){
                Session::flash('alert-danger', 'Takéto oprávnenie už existuje!');
                return redirect()->route('developer.permission.add')->withInput();
            }

            $createPermission->name = str_slug($request->permission_name);
            $createPermission->display_name = trim($request->permission_display);
            $createPermission->description = $request->permission_description;
            $createPermission->save();

            if($request->role && $createPermission->id > 0){
                $role_menu = $request->role;
                $roles = Role::all();
                foreach ($roles as $r){
                    if(in_array($r->id, $role_menu)){
                        $r->permissions()->attach([$createPermission->id]);
                    }
                }
            }
        }

        return redirect()->route('developer.menu')
            ->with('message', 'Uspesne ulozeny');
    }


    public function menuEdit($id)
    {
        $route =[];
        $routeColectors = Route::getRoutes()->getRoutesByMethod();
        if($routeColectors['GET']){
            foreach ($routeColectors['GET'] as $v){
                $route[$v->uri] = $v->uri;
            }
        }

        $id = intval($id);
        $menu = DB::table('backend_menu')->where('id', '=', $id)->first();

        if(empty($menu)){
            return redirect()->route('developer.menu')
                ->with('message', 'Takýto záznam neexistuje');
        }

        return view('developer.admin-menu.menu-edit')
            ->with('route', $route)
            ->with('menu', $menu);

    }


    public function menuSave(Request $request)
    {
        $rules = [
            'title'      => 'required|max:30|min:4',
            'route'      => 'required',
            'rank'       => 'numeric',
        ];

        $this->validate($request, $rules);
        $id = intval($request->menu_id);

        $menu = [];
        $menu['title'] = $request->title;
        $menu['route'] = $request->route;
        $menu['icon'] = $request->icon;
        $menu['block'] = $request->block;
        $menu['rank'] = intVal($request->rank);
        $menu['parent'] = intVal($request->parent);
        $menu['updated_at'] = Carbon::now()->toDateTimeString();

        DB::table('backend_menu')
            ->where('id', $id)
            ->update($menu);

        return redirect()->route('developer.menu')
            ->with('message', 'Záznam úspesne upravený.');

    }

    public function menuDelete(Request $request)
    {
        DB::table('backend_menu')->where('id', '=', $request->id)->delete();
        return redirect()->route('developer.menu')
            ->with('message', 'Záznam ('.$request->id.') úspesne vymazaný.');

    }


}
