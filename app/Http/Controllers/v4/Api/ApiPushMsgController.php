<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\User\Model\UsersMessageSendModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

class ApiPushMsgController extends BaseController {
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 拉取用户所有消息
     */
    public function getALLPushMsg(Request $request) {
        $uid  = $request->get('uid');
        $data = UsersMessageSendModel::select('id', 'message', 'is_read', 'application','created_at')->where('uid', $uid)->orderBy('id', 'desc')->get();
        return $this->success($data);
    }

    /**
     * @param Request $request
     * 用户已读某一条
     */
    public function userReadMsg(Request $request) {
        $id = $request->json('id');
        if (UsersMessageSendModel::where('id', $id)->update(['is_read'=>1]))
            return $this->success();
        else
            return $this->error();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 用户已读所有
     */
    public function userReadMsgAll(Request $request) {
        $uid = $request->json('uid');
        if (UsersMessageSendModel::where('uid', $uid)->update(['is_read', 1]))
            return $this->success();
        else
            return $this->error();

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除用户已读
     */
    public function delMessageByUserId(Request $request) {
        $uid = $request->json('user_id');
        if (UsersMessageSendModel::where('uid', $uid)->where('is_read',1)->delete())
            return $this->success();
        else
            return $this->error();
    }
    
    /**
     * 删除所有
     */
    public function delAllMessage(Request $request) {
        $uid = $request->json('uid');
        if (UsersMessageSendModel::where('uid', $uid)->delete())
            return $this->success();
        else
            return $this->error();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除某条
     */
    public function delMessageByid(Request $request) {
        $id = $request->json('id');
        if (UsersMessageSendModel::where('id', $id)->delete())
            return $this->success();
        else
            return $this->error();
    }
    
}
