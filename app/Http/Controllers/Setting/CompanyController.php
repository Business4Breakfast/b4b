<?php

namespace App\Http\Controllers\Setting;


use App\Models\Company;
use App\Models\UploadImages;
use App\User;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use League\Flysystem\Directory;
use Session;
use View;

class CompanyController extends Controller
{


    public function __construct()
    {

        $action = [
            'dropdown' => 'no',
            'class' => 'btn-warning',
            'name' => 'Akcie',
            'items' => [
                ['name' => 'Pridať nový záznam', 'link' => route('setting.company.create'), 'icon' => 'plus', 'class' => 'btn-warning'],
                ['name' => 'Prehľad', 'link' => route('setting.company.index'), 'icon' => 'list', 'class' => 'btn-success']
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
        $companies = Company::orderByDesc('id')->get();

        return view('setting.company.index')
            ->with('items', $companies);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $countries = DB::table('countries')->get();
        $users = User::whereNotIn('id', [0,1])->get();

        return view('setting.company.add')
            ->with('users', $users)
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

        $request->merge(['ico' => str_replace(" ", "", $request->ico) ]);

        $rules = [
            'company_name' => 'required|max:50',
            'contact_person' => 'required|max:50',
            'address_street' => 'required|max:50',
            'address_psc' => 'required|max:8',
            'address_city' => 'required|max:50',
            'address_country' => 'required|max:50',
            'ico'    => 'numeric|required|unique:companies,ico',
            'email' => 'email|required',
            'phone' => 'required|numeric|phone',
            'url' => 'required|url',
        ];

        $this->validate($request,$rules);

        //zapiseme noveho
        $company = Company::create($request->all());

        $image = new UploadImages();

        //if update succes and is files
        if ($company && $request->hasFile('files')){

            $image = new UploadImages();
            $image->procesImage($request->files, 'company', $company->id, $company->title);
        }

        return redirect()->route('setting.company.index')->with('message', 'Spoločnosť úspešne vytvorená');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {


        return view('setting.company.test');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $countries = DB::table('countries')->get();

        return view('setting.company.edit')
            ->with('countries', $countries)
            ->with('company', $company);

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

        $module = 'company';
        $rules = [
            'company_name' => 'required|max:50',
            'contact_person' => 'required|max:50',
            'address_street' => 'required|max:50',
            'address_psc' => 'required|max:8',
            'address_city' => 'required|max:50',
            'address_country' => 'required|max:50',
            'ico' => Rule::unique('companies')->ignore($id, 'id'),
            'dic' => 'required',
            'email' => 'email|required',
            'phone' => 'required|numeric|phone',
            'url' => 'required|url',
        ];

        $this->validate($request,$rules);

        $company = Company::findOrFail($id);

        $company_update = $company;
        $company_update->update($request->all());

        //if update succes and is files
        if ($company_update && $request->hasFile('files')){

            $image = new UploadImages();

            $image->deleteImage($module, $id);

            $image->procesImage($request->files, $module, $company->id, $company->title);
        }

        return redirect()->route('setting.company.index')->with('message', 'Spoločnosť úspešne upravená');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Company::destroy($id);
        return redirect()->route('setting.company.index')->with('message', 'Záznam vymazaný');

    }
}
