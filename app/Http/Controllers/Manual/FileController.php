<?php

namespace App\Http\Controllers\Manual;

use App\Models\UploadFiles;
use App\Role;
use Hashids\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{


    private $hashid;

    public function __construct()
    {

        $this->hashid = new Hashids('salt', 10);


        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Prehľad', 'link' => route('manual.files.index'), 'icon' => 'list', 'class' => 'btn-success']
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

        $file_classs = new UploadFiles();
        $files = null;
        $data_encode = null;
        $role = null;

        $types = DB::table('manual_files_type')->orderBy('title')->get();
        $roles = Role::all();

        $files_raw = DB::table('files')
                    ->where('module', 'manual-files')
                    ->get();

        if ($files_raw){
            foreach ($files_raw as $k => $file){

                $files[$k] = $file;
                $files[$k]->icon =  $file_classs->getIconMimeText($file->ext);

                //odstranime hash v nazve suboru
                $file_arr = explode('-',$file->file);
                $files[$k]->short_file_name =  str_replace("-" . $file_arr[count($file_arr) - 1], "", $file->file) . "." . $file->ext;


                //timestamp
                $data_encode =  [ time(), $file->module_id,$file->id ];
                $files[$k]->download =  $this->hashid->encode($data_encode);

                if($file->role){
                    foreach (json_decode($file->role) as $r) {
                        $role[] = "$r";
                    }
                }

                $files[$k]->role_string =  $role;

            }
        }

        return view('manual.file.index')
            ->with('roles', $roles)
            ->with('files', $files)
            ->with('types', $types);

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $res = null;

        $rules = [
            'files' => 'required',
            'section_id' => 'required',
        ];

        $this->validate($request,$rules);

        $module = 'manual_files';

        //if update succes and is files
        if ($request->hasFile('files') && $request->section_id > 0){

            $file_classs = new UploadFiles();

            $res = $file_classs->procesUploadFiles($request->files, $module, $request->section_id, $request->title );

            if($res){
                // nastavime prava pre subory
                foreach ($res as $r){
                    $roles = json_encode($request->role);
                    DB::table('files')->where('id', $r)->update( ['role' => $roles]);
                }

            }

        }

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
        $files = null;

        $types = DB::table('manual_files_type')->orderBy('title')->get();
        $roles = Role::all();

        $file = DB::table('files')->find($id);

        $file_role = json_decode($file->role);

        return view('manual.file.edit')
            ->with('file', $file)
            ->with('file_role', $file_role)
            ->with('roles', $roles)
            ->with('types', $types);

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

        $res = null;

        $rules = [
            'role' => 'required',
            'section_id' => 'required',
        ];

        $this->validate($request,$rules);

        $roles = json_encode($request->role);
        DB::table('files')->where('id', $id)->update( ['role' => $roles, 'module_id' => $request->section_id]);

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

        $file_class = new UploadFiles();

        $file_class->deleteFile($id);

        return redirect()->route('manual.files.index')
                 ->with('message', 'Záznam č:' . $id .' zmazaný.');
    }


    public function getFileToDownload($file)
    {

        $file_name = null;
        $res = $this->hashid->decode($file);

        if ($res && count($res) == 3){

            $data['time'] = $res[0];
            $data['module_id'] = $res[1];
            $data['id'] = $res[2];

            //subor data
            $file_download = DB::table('files')->find($data['id'] );

            //opravnenia suboru
            $file_role = json_decode($file_download->role);

            //$file_name = storage_path( $file_download->path . $file_download->file );
            $file_name = public_path( $file_download->path . $file_download->file );


            $headers = [
                'Content-Type' => $file_download->mime,
            ];

            return response()->download($file_name, $file_download->file, $headers);

        }


    }


}
