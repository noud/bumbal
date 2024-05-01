<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
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
        try {
            $user = JWTAuth::parseToken()->authenticate();
            // $token = JWTAuth::getToken();
            // if (! $token = JWTAuth::parseToken()) {
            //     //throw an exception
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Authorization Token not found2'
            //     ], 401);
            // }
            } catch (Exception $e) {
            if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token is Invalid'
                ], 401);
            } else if ($e instanceof \PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token is Expired'
                ], 401);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authorization Token not found'
                ], 401);
            }
        }
        return $next($request);
    }
}