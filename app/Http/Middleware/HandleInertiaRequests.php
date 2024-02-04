<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AppController;
use App\Http\Resources\UserResource;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'publicPath'=> env("APP_URL"),
            // 'publicPath'=> function() use ($request){
            //     return env("APP_URL");
            // },
            'auth'=> function() use ($request){
                if (Auth::check())
                    return new UserResource(Auth::user());
                else
                    return null;
            },
            'flash'=>function() use ($request){
                return[
                    'info'      =>$request->session()->get('info'),
                    'success'   =>$request->session()->get('success'),
                    'error'     =>$request->session()->get('error'),
                ];
            },
            'notificationsCount'=> function() use ($request){
                //get user
                $user=(new AppController())->getAuthUser($request);

                if ($user){
                    return $user->userNotifications()->where('read',0)->get()->count();
                }else
                    return 0;
            },
        ]);
    }
}
