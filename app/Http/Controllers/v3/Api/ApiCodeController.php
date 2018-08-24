<?php

namespace App\Http\Controllers\v3\Api;

use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Verificationcode\Models\VerificationModel;

class ApiCodeController extends BaseController
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function sendCode( Request $request )
    {
        $code_type = $request->json('code_type');       //验证码的性质，是注册还是忘记密码还是其他
        $tel       = $request->json('tel');
        $is_exist  = UserModel::select('id')->where('name', $tel)->first();
        if ($is_exist && $code_type == "reg") {
            return response()->json(['message' => '您已经注册过,请登录']);
        }
        $code      = VerificationModel::sendVerificationCode($code_type, $tel);

        if(is_string($code)){
		    return response()->json( ['error'=>'发送失败,'.$code] , '500');
        }else{
            return response()->json( ['message'=>'验证码已发送成功,请留意']);
        }
        
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function sendRegCode( Request $request )
    {
        $code_type = $request->code_type;       //验证码的性质，是注册还是忘记密码还是其他
        $tel       = $request->tel;
        $is_exist  = UserModel::select('id')->where('name', $tel)->first();
        if ($is_exist && $code_type == "reg") {
            return response()->json(['message' => '您已经注册过,请登录']);
        }
        $code      = VerificationModel::sendVerificationCode($code_type, $tel);

        if (is_string($code)) {
            return response()->json(['message' => '发送失败,' . $code]);
        } else {
            return response()->json(['message' => '验证码已发送成功,请留意']);
        }

    }


    public function checkCode( Request $request ){
        // 以下两个为保留字段
        $code_type = $request->json('code_type');       //验证码的性质，是注册还是忘记密码还是其他
        $user_type = $request->json('user_type');       //验证码隶属的用户类型，是普通用户还是设计师还是其他

        $tel       = $request->json('tel');
        $code      = $request->json('code');
        $checkCode = VerificationModel::checkCode($code , $tel);
        if($checkCode){
            return response()->json( ['message'=>'验证成功']);
        }else{
            return response()->json( ['error'=>'验证失败'] , '404');
        }

    }



}
