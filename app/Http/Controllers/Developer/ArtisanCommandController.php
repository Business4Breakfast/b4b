<?php

namespace App\Http\Controllers\Developer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class ArtisanCommandController extends Controller
{

 public function getAction($action){

     $res = null;

         switch ($action){

             case 'config-clear':

                 $exitCode = Artisan::call('config:clear');
                 $res =  ['code' => $exitCode, 'message' => 'Config Clear'];
                 break;

             case 'cache-clear':

                 $exitCode = Artisan::call('cache:clear');
                 $res =  ['code' => $exitCode, 'message' => 'Cache facade value cleared'];
                 break;

             case 'route-cache':

                 $exitCode = Artisan::call('route:cache');
                 $res =  ['code' => $exitCode, 'message' => 'Routes cached'];
                 break;

             case 'view-clear':

                 $exitCode = Artisan::call('view:clear');
                 $res =  ['code' => $exitCode, 'message' => 'View cache cleared'];
                 break;

             case 'route-clear':

                 $exitCode = Artisan::call('route:clear');
                 $res =  ['code' => $exitCode, 'message' => 'Route cache cleared'];
                 break;

             case 'config-cache':

                 $exitCode = Artisan::call('config:cache');
                 $res =  ['code' => $exitCode, 'message' => 'Clear Config cleared'];
                 break;

             case 'optimize':

                 $exitCode = Artisan::call('optimize');
                 $res =  ['code' => $exitCode, 'message' => 'Reoptimized class loader'];
                 break;

             default:
                 return view('developer.artisan-command.index');

         }

         return redirect()->route('developer.artisan.action', 'index')->with('message', $res['message']);


    }



}
