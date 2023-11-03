<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    //     $lsPassRoute=[
    //         'login',
    //         'logout'
    //     ];
    //     $route_name = \Request::route()->getName();
    //     foreach ($lsPassRoute as $passRoute){
    //         if($passRoute==$route_name)
    //         {
    //             return $next($request);
    //         }
    //     }

    //     \Auth::shouldUse('admin');
    //     if(\Auth::guard('admin')->check()){
    //         $adminPermission = \Auth::guard('admin')->user()->permissions()->where('permission', $route_name)->get();
    //         if(count($adminPermission)){
    //             $token = str_replace('Bearer ', '', $request->header('Authorization'));
    //             $adminActivityLog = new AdminActivityLog();
    //             $admin = \App\Models\Gotit\Admin::where('api_token',$token)->first();
    //             if($admin){
    //                 $adminActivityLog->admin_id = $admin->id;
    //                 $adminActivityLog->note = $admin->username;
    //             }
    //             $adminActivityLog->params = json_encode($request->all());
    //             $adminActivityLog->agent = $request->header('User-Agent');
    //             $adminActivityLog->name = \Request::route()->getName();
    //             $adminActivityLog->method = $request->method();
    //             $adminActivityLog->save();
    //             return $next($request);
    //         }
    //         return response()->json(["msg"=>"Not have permission",'stt'=>1001],200);
    //     }
    //     return response()->json(["msg"=>"Unauthorized"],401);
    }
}
