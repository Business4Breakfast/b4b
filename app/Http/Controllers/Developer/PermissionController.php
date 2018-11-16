<?php

namespace App\Http\Controllers\Developer;

use App\Permission;
use App\Role;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Validator;

class PermissionController extends Controller
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
                ['name' => 'Pridať nový záznam', 'link' => route('developer.permission.add'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('developer.permission'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }

    public function permission(){

        $permissions = Permission::all();

        $ar = null;
        $grouped = $permissions->groupBy(function ($item, $key) {
            $ar = explode('-', $item['name'] );
            return $ar[0];
        });

        //zoradime podla abecedy
        $items = $grouped->all();
        ksort($items);
        $grouped = collect($items);

        return view('developer.permissions.permission')
            ->with('permissions', $permissions)
            ->with('items', $grouped);
    }

    public function add(){

        return view('developer.permissions.permission-add')
            ->with('permission', null);
    }

    public function edit($id){

        $id = intval($id);
        $permission = DB::table('permissions')->where('id', '=', $id)->first();

        if(empty($permission)){
            return redirect()->route('developer.permission.edit')
                ->with('message', 'Takýto záznam neexistuje');
        }

        return view('developer.permissions.permission-edit')
            ->with('permission', $permission);

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

        DB::table('permissions')
            ->where('id', $request->permission_id)
            ->update($prm);

        return redirect()->route('developer.permission')
            ->with('message', 'Záznam ('.$request->name.') úspesne upravený.');

    }

    public function create(Request $request){

        $rules = [
            'name'      => 'required|max:50|min:4',
            'display_name'      => 'required',
        ];

        $this->validate($request, $rules);

        $createPermission = new Permission();
        $createPermission->name         = str_slug($request->name);
        $createPermission->display_name = trim($request->display_name); // optional
        $createPermission->description  = str_slug($request->description); // optional

        $permissions = Permission::all()->pluck('name')->toArray();

        if(in_array(trim($createPermission->name)  , $permissions)){
            Session::flash('alert-danger', 'Takéto oprávnenie už existuje!');
            return redirect()->route('developer.permission.add')->withInput();
        }

        $createPermission->save();

        return redirect()->route('developer.permission')
            ->with('message', 'Záznam bol pridaný');
    }

    public function destroy(Request $request)
    {
        DB::table('permissions')->where('id', '=', $request->id)->delete();
        return redirect()->route('developer.permission')
            ->with('message', 'Záznam ('.$request->id.') úspesne vymazaný.');
    }

}
