<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
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
        
        $message = "";

        //comprueba el token
        try {
           JWTAuth::parseToken()->authenticate();
           return $next($request);
        } 
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
            $message = "Token is Expired";

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
            $message ="Token is Invalid";

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e){
            $message ="Authorization Token not found";

        }

        return response()->json(['success' => false, 'status' => $message]);
     

    }
}
