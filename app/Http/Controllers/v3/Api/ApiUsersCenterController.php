<?php

namespace App\Http\Controllers\v3\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\User\Model\AttachmentModel;
// use Theme;



class ApiUsersCenterController extends BaseController
{

	//头像
    public function avatar(Request $request){
        
        // var_dump(DIRECTORY_SEPARATOR);exit;
        $file 	 = $request->file('avatar');
        $user_id = $request->get('user_id');
       // var_dump($_FILES);exit;
// return response()->json([$user_id]);
        //处理上传图片
        $result = \FileClass::uploadFile($file, $path = 'user');

        $result = json_decode($result, true);

        //判断文件是否上传
        if ($result['code'] != 200) {
        	return response()->json( array('error'=>$result['message']) , '400');
        }
// var_dump($result);exit;
        //产生一条新纪录
        $attachment_data = array_add($result['data'], 'status', 1);

        if($attachment_data['size'] > 110){
        	return response()->json( array('error'=>'图片不可大于110Kb') , '400');
        }

        $attachment_data['created_at'] 	= date('Y-m-d H:i:s', time());
        $attachment_data['user_id'] 	= $user_id;
        //将记录写入到attchement表中

        $result2 = AttachmentModel::create($attachment_data);

        if (!$result2)
        	return response()->json( array('error'=>$result['message']) , '500');


        //删除原来的头像
        $avatar = \CommonClass::getAvatar($user_id);
        // var_dump($avatar);exit;
        if (file_exists($avatar)) {
            $file_delete = unlink($avatar);
            if ($file_delete) {
                AttachmentModel::where('url', $avatar)->delete();
            } else {
                AttachmentModel::where('url', $avatar)->update(['status' => 0]);
            }
        }
        //修改用户头像
        $data = [
            'avatar' => $result['data']['url']
        ];
        $result3 = UserDetailModel::updateData($data, $user_id);
        if (!$result3) {
        	return response()->json( array('error'=>'文件上传失败') , '500');
        }
        return response()->json( ['avatar'=>url($result['data']['url'])]);
    }




    //个人资料完善
    public function info(Request $request){

        $uid                    = $request->json('user_id');
        $data['nickname']       = $request->json('nickname');
        $data['lat']            = $request->json('lat');
        $data['lng']            = $request->json('lng');
        $data['city']           = $request->json('city');
        $data['address']        = $request->json('address');
        $data['cost_of_design'] = $request->json('cost_of_design');
        $data['introduce']      = $request->json('introduce');//个人简介
        $data['experience']     = empty($request->json('experience')) ? 0 : $request->json('experience');//经验
        $data['tag']            = empty($request->json('tag')) ? '' : serialize($request->json('tag'));//标签

        if(empty($uid)){
            return response()->json( array('error'=>'用户id为必传参数') , '404');
        }

    	foreach ($data as $key => $value) {
    		if(empty($value)){
                unset($data[$key]);
    		}
    	}

    	$result = UserDetailModel::where('uid', $uid)->update($data);

        if (!$result) {
        	return response()->json( array('error'=>'修改失败！') , '500');
        }

        return response()->json( array('success'=>'修改成功！'));
    }


}
