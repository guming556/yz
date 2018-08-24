<?php

namespace App\Http\Controllers\v3\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\RealnameAuthModel;


class ApiAuthController extends BaseController
{

    /**
     * @param $user_id
     * @return array
     * 实名认证状态
     */
	public function judgeAuthState($user_id){
		$error = array();
		$realnameInfo = RealnameAuthModel::where('uid', $user_id)->orderBy('created_at', 'desc')->first();
        if (isset($realnameInfo->status)) {
            switch ($realnameInfo->status) {
                case 0:
                	return $error = array( ['error'=>'您的信息审核中，请耐心等候'] , '202' );
                    break;
                case 1:
                	return $error = array( ['error'=>'已经认证并成功'] , '201' );
                    break;
                case 2:
                	return $error = array( ['error'=>'上次认证失败，重新认证'] , '205' );
                    break;
            }
        }
	}

	// 判断是否已经进行过实名认证
    public function realnameAuthState(Request $request)
    {
        $user_id = $request->json('user_id');
        $error   = $this->judgeAuthState($user_id);
        if(!empty($error)){
        	return response()->json( $error[0] , $error[1] );
        }
        return response()->json( ['success'=>'未进行过认证'] );
    }




    /**
     * 身份认证		TODO 是否加一个手持身份证的照片？
     */
    public function postRealnameAuth(Request $request)
    {	
        $realnameInfo = array();
        $authRecordInfo = array();
        $allowExtension = array('jpg' , 'jpeg', 'bmp', 'png');

        $now = time();
		$user_type		 				= $request->json('user_type');
		$realnameInfo['uid'] 			= $request->json('user_id');
        $realnameInfo['username'] 		= $request->json('user_name');
        $realnameInfo['realname'] 		= $request->json('realname');
        $realnameInfo['card_number']	= $request->json('card_number');
        $realnameInfo['serve_area']		= $request->json('serve_area');
        $realnameInfo['card_front_side']= $request->json('card_front_side');
        $realnameInfo['card_back_dside']= $request->json('card_back_dside');
        $realnameInfo['created_at'] 	= date('Y-m-d H:i:s', $now);
        $realnameInfo['updated_at'] 	= date('Y-m-d H:i:s', $now);
        // TODO 这里后台界面未改，这个参数又用不上，故暂时赋值，防止报错
        $realnameInfo['validation_img'] = $realnameInfo['card_front_side'];
        
        // 为防止直接调用这个接口，再检测一次
        $error   = $this->judgeAuthState($realnameInfo['uid'] );
        if(!empty($error)){
        	return response()->json( $error[0] , $error[1] );
        }

        foreach ($realnameInfo as $key => $value) {
        	if(empty($value)){
        		return response()->json( ['error'=>'认证信息不可为空'] , '411' );
        	}
        }
        if(empty($user_type)){
        	return response()->json( ['error'=>'未选择认证角色'] , '412' );
        }


        $authRecordInfo['uid'] 		= $realnameInfo['uid'];
        $authRecordInfo['username'] = $realnameInfo['username'];
        $authRecordInfo['auth_code']= 'realname';

        $RealnameAuthModel = new RealnameAuthModel();
        $status = $RealnameAuthModel->createRealnameAuth($realnameInfo, $authRecordInfo, $user_type);

        if ($status){
			return response()->json( ['success'=>'请等待管理员审核通过'] );
        }
    }



}
