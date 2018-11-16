<?php

namespace App\Http\Controllers\Developer;

use App\Permission;
use App\Role;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;

class RoleController extends Controller
{

    public function __construct()
    {

        $this->middleware('auth');

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('developer.role.add'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('developer.role'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }


    public function role(){

        $roles = Role::all();
        return view('developer.role.role')
            ->with('roles', $roles);
    }


    public function add(){

        return view('developer.role.role-add')
            ->with('role', null);
    }

    public function edit($id){

        $id = intval($id);
        $role = DB::table('roles')->where('id', '=', $id)->first();

        if(empty($role)){
            return redirect()->route('developer.role.edit')
                ->with('message', 'Takýto záznam neexistuje');
        }

        return view('developer.role.role-edit')
            ->with('role', $role);

    }

    public function save(Request $request){

        $rules = [
            'name'      => 'required|max:50|min:4',
            'display_name'      => 'required',
        ];

        $this->validate($request, $rules);

        $prm['name']         = str_slug($request->name);
        $prm['display_name'] = trim($request->display_name); // optional
        $prm['description']  = $request->description; // optional

        DB::table('roles')
            ->where('id', $request->role_id)
            ->update($prm);

        return redirect()->route('developer.role')
            ->with('message', 'Záznam ('.$request->name.') úspesne upravený.');

    }

    public function create(Request $request){

        $rules = [
            'name'      => 'required|max:50|min:4',
            'display_name'      => 'required',
        ];

        $this->validate($request, $rules);

        $createRole = new Role();
        $createRole->name         = str_slug($request->name);
        $createRole->display_name = trim(str_slug($request->display_name)); // optional
        $createRole->description  = $request->description; // optional

        $role = Role::all()->pluck('name')->toArray();

        if(in_array(trim($createRole->name)  , $role)){
            Session::flash('alert-danger', 'Takéto oprávnenie už existuje!');
            return redirect()->route('developer.role.add')->withInput();
        }

        $createRole->save();

        return redirect()->route('developer.role')
            ->with('message', 'Záznam bol pridaný');
    }

    public function destroy(Request $request)
    {
        DB::table('roles')->where('id', '=', $request->id)->delete();
        return redirect()->route('developer.role')
            ->with('message', 'Záznam ('.$request->id.') úspesne vymazaný.');
    }


    public function permissionsGet($role_id)
    {

        $permission = Permission::all();
        foreach (Role::all() as $r){
            if($role_id == $r->id){
                $role = $r;
            }
        }

        if(empty($role)){
            return redirect()->route('developer.role')
                ->with('message', 'Takýto záznam neexistuje');
        }

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

        $id = intval($role_id);
        $role_permission = DB::table('permission_role')->where('role_id', '=', $id)->pluck('permission_id');

        return view('developer.role.perrmision')
            ->with('items', $grouped)
            ->with('role_permission',$role_permission)
            ->with('role', $role)
            ->with('grouped', $grouped);

    }

    public function permissionsSave(Request $request)
    {

        $rolePermission = $request->permission;
        $role = Role::all();

       // vybrana rola
        $res = null;
        if($role){
            foreach ($role as $k => $v){
                if($v->id == $request->role_id) {
                    $roleSelected = $v;
                }
            }
        }

        $roleSelected->syncPermissions($rolePermission);

        return redirect()->route('developer.role')
            ->with('message', 'Oprávnenia úspešne zmenené.');

    }

}
