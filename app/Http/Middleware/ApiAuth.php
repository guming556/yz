<?php
/**
 * Created by PhpStorm.
 * User: KEKE-1003
 * Date: 2016/6/24
 * Time: 13:56
 */
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Config;
use Cache;
use Illuminate\Support\Facades\Crypt;
use Log;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
			$token = JWTAuth::getToken();
			if($token){
				try {
					$user = JWTAuth::parseToken()->authenticate();
					if ($user&&$user['id']) {
			            return $next($request);
			        }
	
		        } catch (TokenExpiredException $e) {
		            return response()->json(['message'=>'登录信息过期','code'=>'-3'], 401);
		        } catch (TokenInvalidException $e) {
		            return response()->json(['message'=>'用户验证失败','code'=>'-3'], 401);
		        } catch (Exception $e) {
		            return response()->json(['message'=>'用户验证失败','code'=>'-3'], 401);
		        }
			}
			return response()->json(['message'=>'用户验证失败','code'=>'-3'], 401);
           

    }

}
