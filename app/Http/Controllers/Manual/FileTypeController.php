<?php

namespace App\Http\Controllers\Manual;

use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FileTypeController extends Controller
{


    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať novú sekciu', 'link' => route('manual.files-type.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('manual.files-type.index'), 'icon' => 'list', 'class' => 'btn-success']
            ]
        ];

        view()->share('action_menu', $action);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $module = 'manual_file_types';

        $items = DB::table('manual_files_type')->orderBy('title')->get();
        $roles = Role::all();

        return view('manual.file-type.index')
            ->with('roles', $roles)
            ->with('module', $module)
            ->with('items', $items);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('manual.file-type.add');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'title' => 'required',
            'icon' => 'required',
        ];

        $this->validate($request,$rules);

        $data['title'] = $request->title;
        $data['icon'] = $request->icon;
        $data['description'] = $request->description;
        $data['created_at'] = Carbon::now();
        $data['created_user'] = \Auth::user()->id;

        DB::table('manual_files_type')->insert($data);

        return redirect()->route('manual.files.index');

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

        $section = DB::table('manual_files_type')->find($id);

        return view('manual.file-type.edit')
                    ->with('section', $section);
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

        $rules = [
            'title' => 'required',
            'icon' => 'required',
        ];

        $this->validate($request,$rules);

        $data['title'] = $request->title;
        $data['icon'] = $request->icon;
        $data['active'] = $request->active;
        $data['description'] = $request->description;

        DB::table('manual_files_type')->where('id',$id)->update($data);

        return redirect()->route('manual.files.index');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
