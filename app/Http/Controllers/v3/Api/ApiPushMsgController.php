<?php

namespace App\Http\Controllers\v3\Api;

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
        $data = UsersMessageSendModel::select('id', 'message', 'is_read')->where('uid', $uid)->orderBy('id', 'desc')->get();
        return response()->json($data);
    }

    /**
     * @param Request $request
     * 用户已读某一条
     */
    public function userReadMsg(Request $request) {
        $id = $request->json('id');
        if (UsersMessageSendModel::where('id', $id)->update(['is_read', 1]))
            return $this->responseSuccess();
        else
            $this->responseError();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 用户已读所有
     */
    public function userReadMsgAll(Request $request) {
        $uid = $request->json('uid');
        if (UsersMessageSendModel::where('uid', $uid)->update(['is_read', 1]))
            return $this->responseSuccess();
        else
            $this->responseError();

    }
    
    /**
     * 删除所有
     */
    public function delAllMessage(Request $request) {
        $uid = $request->json('uid');
        if (UsersMessageSendModel::where('uid', $uid)->delete())
            return $this->responseSuccess();
        else
            $this->responseError();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 删除某条
     */
    public function delMessageByid(Request $request) {
        $id = $request->json('id');
        if (UsersMessageSendModel::where('id', $id)->delete())
            return $this->responseSuccess();
        else
            $this->responseError();
    }
    
}
