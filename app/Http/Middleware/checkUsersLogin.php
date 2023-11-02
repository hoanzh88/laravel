<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class checkUsersLogin{
    public function handle(Request $request, Closure $next){
        $route_name = \Request::route()->getName();
        if (Auth::check()){		
            $user = Auth::user();  
            if ($user->status == 1 ){
                return $next($request);
                // $permission = Permission::where('permission',$route_name)->get();
                // if($permission->count()>0)
                // {
                //     return $next($request);
                // }else{
                //     abort(403, 'Unauthorized.');
                // }
            }
            else{
                Auth::logout();
                return redirect('users/login');
            }
        } else{
            return redirect('users/login');
		}
    }
}
