<?php

namespace App\Http\Controllers\Developer;

use App\Models\Developer\SystemLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $modules = SystemLog::groupBy('module')->pluck('module');

        $items_count = SystemLog::all()->count();

        $req['search_type'] = null;
        $req['search_text'] = null;

        if($items_count > 0){

            $date_min = SystemLog::min('log_date');
            $date_max = SystemLog::max('log_date');

            $query = SystemLog::orderByDesc('id');

            if(isset($request->date_from) ) {
                if($request->date_from != Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y') ) {
                    $query->where('log_date', '>', Carbon::createFromFormat('d.m.Y H:i', $request->date_from . ' 00:59')->format('Y-m-d H:i:s'));
                }
                $req['date_from'] = $request->date_from;
            } else {
                $req['date_from'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_min)->format('d.m.Y');
            }


            if(isset($request->date_to) ) {
                if($request->date_to != Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y') ) {
                    $query->where('log_date', '<', Carbon::createFromFormat('d.m.Y H:i', $request->date_to . ' 23:59')->format('Y-m-d H:i:s'));
                }
                $req['date_to'] = $request->date_to;
            } else {
                $req['date_to'] = Carbon::createFromFormat('Y-m-d H:i:s', $date_max)->format('d.m.Y');
            }


            if (isset($request->search_type) &&  strlen($request->search_type) > 0) {
                $query->where('module', $request->search_type);
                $req['search_type'] = $request->search_type;
            }


            if (isset($request->search_text) &&  strlen($request->search_text) > 0) {
                $query->where('transaction', 'like', '%' . $request->search_text . '%')
                    ->orWhere('description', 'like', '%' . $request->search_text . '%')
                    ->orWhere('description', 'like', '%' . $request->search_text . '%')
                    ->orWhere('id', 'like', '%' . $request->search_text . '%');

                $req['search_text'] = $request->search_text;
            }

            $items = $query->get();


        } else {

            $items = null;
            $req['date_from'] = Carbon::now()->format('d.m.Y');
            $req['date_to'] = Carbon::now()->format('d.m.Y');
        }


        return view('developer.sys-log.index')
            ->with('modules', $modules)
            ->with('req', $req)
            ->with('items', $items);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SystemLog::destroy($id);
        return redirect()->route('developer.sys-log.index')->with('message', 'Záznam bol vymazaný');
    }
}
