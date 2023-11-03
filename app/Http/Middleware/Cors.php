<?php
/**
 * Created by PhpStorm.
 * User: VNM05YPG
 * Date: 3/27/2017
 * Time: 2:26 PM
 */

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
        // $str_Domain = config ('CORS_ADMIN_BIZ');
        
        // return $next($request)->header('Access-Control-Allow-Origin', $str_Domain)
        //     ->header('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS')
        //     ->header('Access-Control-Allow-Credentials', 'true')
        //     ->header('Access-Control-Max-Age', '10000')
        //     ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Authorization, x-gi-authorization, X-Requested-With, Accept, x-xsrf-token, x_csrftoken');
    }
}
