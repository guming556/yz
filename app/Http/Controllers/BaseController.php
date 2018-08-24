<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Dingo\Api\Routing\Helpers;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	
	public function success($data=''){
		return response()->json([
            'message' => '请求成功',
            'code' => 0,
            'data' => $data
        ], 200);
	}
	
	
	public function error($message='请求失败',$code =-1,$data=''){
		return response()->json([
            'message' => $message,
            'code' => $code,
            'data' => $data
        ], 200);
	}
	
	
	public function denied($message='没有访问权限',$data=''){
		return response()->json([
            'message' => $message,
            'code' => -3
        ], 200);
	}
}
