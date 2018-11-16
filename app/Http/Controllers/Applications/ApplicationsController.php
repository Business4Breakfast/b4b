<?php

namespace App\Http\Controllers\Applications;

use App\Models\Club;
use App\Models\Setting\Industry;
use App\Models\Setting\Interest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class ApplicationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $applications = DB::table('application_form AS af')
                            ->select('af.id as id', 'af.data as data', DB::raw('concat(u.name," ",u.surname) as name'))
                            ->join('users as u', 'af.user_id', '=', 'u.id', 'LEFT')
                            ->get();
        foreach ($applications as $ap){
            $ap->data = json_decode($ap->data);
        }

        return view('applications.index')->with('applications', $applications);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) //Todo store application process
    {
        //Todo - vyrobit noveho hosta, novu firmu ak neexistuje (napr. podľa IČO) ...
        dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $application = DB::table('application_form AS af')
            ->select('af.id as id', 'af.data as data', DB::raw('concat(u.name," ",u.surname) as name'))
            ->join('users as u', 'af.user_id', '=', 'u.id', 'LEFT')
            ->where('af.id', '=',$id)
            ->limit(1)
            ->get();

        $application[0]->data = json_decode($application[0]->data);

        $industry = Industry::orderBy('name')->where('active', 1)->get();
        $countries = DB::table('countries')->get();
        $clubs = Club::orderByDesc('active')->orderBy('short_title')->where('active', 1)->get();
        $interest = Interest::orderBy('name')->where('active', 1)->get();

        //dd($application[0]);

        return view('applications.detail')
            ->with('data', $application[0])
            ->with('industry', $industry)
            ->with('interest', $interest)
            ->with('clubs', $clubs)
            ->with('countries', $countries);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
