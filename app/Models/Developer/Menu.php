<?php

namespace App\Models\Developer;

use App\User;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laratrust;

class Menu extends Model
{
    public static function GetMenu($loged_user)
    {

        $userPermission = $loged_user->allPermissions();
        $menuPermission = [];
        foreach ($userPermission as $k => $v ){
            $p = explode('-', $v->name);
                if(strcmp('menu', $p[0]) == 0){
                    $menuPermission[] = $p[1];
                }
        }

        $menu_arr = Menu::getMenuListing();

        $arr = [];
        $i=0; $i1=0; $i2=0;
        foreach ($menu_arr as $k => $v){
            if($v['parent'] == 0  && in_array($v['block'], $menuPermission) == true || strcmp($v['block'],'dashboard') == 0) {
                $arr[$i]['id'] = $v['id'];
                $arr[$i]['content'] = '<i class=" fa ' .$v['icon'].'"></i> ' . $v['title'].'('.$v['id'].')';
                $arr[$i]['icon'] = $v['icon'];
                $arr[$i]['route'] = $v['route'];
                $arr[$i]['rank'] = $v['rank'];
                $arr[$i]['title'] = $v['title'];
                $arr[$i]['parent'] = $v['parent'];
                $arr[$i]['d'] = '0';
                $arr[$i]['group'] = $v['block'];

                foreach ($menu_arr as $k1 => $v1) {
                    //overime prava 1 uroven
                    if ( $v['id'] == $v1['parent']  && Laratrust::can('menu-' . $v1['block']) ){

                        $arr[$i]['children'][$i1]['id'] = $v1['id'];
                        $arr[$i]['children'][$i1]['content'] = '<i class=" fa ' .$v1['icon'].'"></i> ' . $v1['title'].'('.$v1['id'].')';
                        $arr[$i]['children'][$i1]['icon'] = $v1['icon'];
                        $arr[$i]['children'][$i1]['route'] = $v1['route'];
                        $arr[$i]['children'][$i1]['rank'] = $v1['rank'];
                        $arr[$i]['children'][$i1]['title'] = $v1['title'];
                        $arr[$i]['children'][$i1]['parent'] = $v1['parent'];
                        $arr[$i]['children'][$i1]['d'] = '1';
                        $arr[$i]['children'][$i1]['group'] = $v1['block'];

                        foreach ($menu_arr as $k2 => $v2) {
                            if ($v1['id'] == $v2['parent'] && Laratrust::can('menu-' . $v2['block']) ) {

                                $arr[$i]['children'][$i1]['children'][$i2]['id'] = $v2['id'];
                                $arr[$i]['children'][$i1]['children'][$i2]['content'] = '<i class=" fa ' .$v2['icon'].'"></i> ' . $v2['title'].'('.$v2['id'].')';
                                $arr[$i]['children'][$i1]['children'][$i2]['icon'] = $v2['icon'];
                                $arr[$i]['children'][$i1]['children'][$i2]['route'] = $v2['route'];
                                $arr[$i]['children'][$i1]['children'][$i2]['rank'] = $v2['rank'];
                                $arr[$i]['children'][$i1]['children'][$i2]['title'] = $v2['title'];
                                $arr[$i]['children'][$i1]['children'][$i2]['parent'] = $v2['parent'];
                                $arr[$i]['children'][$i1]['children'][$i2]['d'] = '2';
                                $arr[$i]['children'][$i1]['children'][$i2]['group'] = $v2['block'];
                            }

                            if ($v1['id'] == $v2['parent']) $i2++;
                        }
                    }
                    if ($v['id'] == $v1['parent'])  $i1++;
                }
            }
            if($v['parent'] == 0)  $i++;
        }

        return $arr;

    }

    public static function GetMenuSetting()
    {

        $menu_arr = Menu::getMenuListing();

        $arr = [];
        $i=0; $i1=0; $i2=0;
        foreach ($menu_arr as $k => $v){
            if($v['parent'] == 0 ) {
                $arr[$i]['id'] = $v['id'];
                $arr[$i]['content'] = '<i class=" fa ' .$v['icon'].'"></i> ' . $v['title'].'('.$v['id'].')';
                $arr[$i]['icon'] = $v['icon'];
                $arr[$i]['route'] = $v['route'];
                $arr[$i]['rank'] = $v['rank'];
                $arr[$i]['title'] = $v['title'];
                $arr[$i]['parent'] = $v['parent'];
                $arr[$i]['d'] = '0';
                $arr[$i]['group'] = $v['block'];

                foreach ($menu_arr as $k1 => $v1) {
                    if ($v['id'] == $v1['parent']){
                        $arr[$i]['children'][$i1]['id'] = $v1['id'];
                        $arr[$i]['children'][$i1]['content'] = '<i class=" fa ' .$v1['icon'].'"></i> ' . $v1['title'].'('.$v1['id'].')';
                        $arr[$i]['children'][$i1]['icon'] = $v1['icon'];
                        $arr[$i]['children'][$i1]['route'] = $v1['route'];
                        $arr[$i]['children'][$i1]['rank'] = $v1['rank'];
                        $arr[$i]['children'][$i1]['title'] = $v1['title'];
                        $arr[$i]['children'][$i1]['parent'] = $v1['parent'];
                        $arr[$i]['children'][$i1]['d'] = '1';
                        $arr[$i]['children'][$i1]['group'] = $v1['block'];

                        foreach ($menu_arr as $k2 => $v2) {
                            if ($v1['id'] == $v2['parent']) {
                                $arr[$i]['children'][$i1]['children'][$i2]['id'] = $v2['id'];
                                $arr[$i]['children'][$i1]['children'][$i2]['content'] = '<i class=" fa ' .$v2['icon'].'"></i> ' . $v2['title'].'('.$v2['id'].')';
                                $arr[$i]['children'][$i1]['children'][$i2]['icon'] = $v2['icon'];
                                $arr[$i]['children'][$i1]['children'][$i2]['route'] = $v2['route'];
                                $arr[$i]['children'][$i1]['children'][$i2]['rank'] = $v2['rank'];
                                $arr[$i]['children'][$i1]['children'][$i2]['title'] = $v2['title'];
                                $arr[$i]['children'][$i1]['children'][$i2]['parent'] = $v2['parent'];
                                $arr[$i]['children'][$i1]['children'][$i2]['d'] = '2';
                                $arr[$i]['children'][$i1]['children'][$i2]['group'] = $v2['block'];
                            }
                            if ($v1['id'] == $v2['parent']) $i2++;
                        }
                    }
                    if ($v['id'] == $v1['parent'])  $i1++;
                }
            }
            if($v['parent'] == 0)  $i++;
        }

        return $arr;

    }


    public static function getMenuListing()
    {

        $menu = DB::table('backend_menu')
            ->orderBy('rank', 'asc')
            ->get()->toArray();

        $menu_arr = [];
        foreach ($menu as $key => $value){
            $menu_arr[$key]['id'] = $value->id;
            $menu_arr[$key]['title'] = $value->title;
            $menu_arr[$key]['parent'] = $value->parent;
            $menu_arr[$key]['icon'] = $value->icon;
            $menu_arr[$key]['route'] = $value->route;
            $menu_arr[$key]['rank'] = $value->rank;
            $menu_arr[$key]['block'] = $value->block;
        }

        return $menu_arr;
    }

}
