<?php

namespace App\Http\Controllers\Developer;

use App\Models\Developer\Menu;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Route;
use Validator;

class DeveloperController extends Controller
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


}
