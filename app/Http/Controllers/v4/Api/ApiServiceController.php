<?php

namespace App\Http\Controllers\v4\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\UserFocusModel;

class ApiServiceController extends BaseController
{
    //关注
	public function focus(Request $request){

		$uid 		= $request->json('user_id');
		$focus_uid 	= $request->json('focus_id');

		$data = array(
			'uid' => $uid,
			'focus_uid' => $focus_uid,
			'created_at' => date('Y-m-d H:i:s')
			);

		$info = UserFocusModel::where('uid',$uid)->where('focus_uid',$focus_uid)->first();

		if(empty($info)){

			$re = UserFocusModel::insertGetId($data);
			
			if ($re) {
				return $this->success( ['focus_id' => $re] );
			}

		}else{
			return $this->error( '已关注过该用户');

		}

	}










}
