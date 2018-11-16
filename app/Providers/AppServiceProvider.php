<?php

namespace App\Providers;

use App\Models\Developer\Menu;
use App\User;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Request;
use View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Validator::extend('phone', function($attribute, $value, $parameters, $validator) {
            return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i', $value) && strlen($value) >= 10;
        });

        Validator::replacer('phone', function($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute',$attribute, ':attribute is invalid phone number');
        });


        //compose all the views....
        view()->composer('layouts.app', function ($view)
        {
            $logged_user = Auth::user();
            $user = User::with(['permissions', 'roles'])->find($logged_user->id);

            //generate left menu
            $arr = Menu::GetMenu($logged_user);

            view()->share('backend_menu', $arr);
            view()->share('logged_user', $user);

            view()->share('backend_title', null);
            view()->share('action_menu', null);

        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        require_once __DIR__ . '/../Http/Helpers/Navigation.php';

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Way\Generators\GeneratorsServiceProvider::class);
            $this->app->register(\Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class);
        }
    }


}

