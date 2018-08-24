<?php

namespace App\Respositories;

class ChatRoomRespository {

    /**
     * @param $name
     * @param $members
     * @param $roles
     * @return mixed
     *
     * 创建聊天室
     */
    public function CreateChatRooms($name, $members, $roles) {
        return 0;
        //发送的数据
//        $params = [
//            'owner' => 13311111111,
//            'maxusers' => config('chat-room.max_users'),
//            'name' => '测试3',
//            'members' => ["13322222222","13333333333","13344444444"],
//            'roles' => ['admin' => ["13322222222"]]
//        ];

        $params = [
            'owner' => config('chat-room.super_manager'),
            'maxusers' => config('chat-room.max_users'),
            'name' => $name,
            'members' => $members,
            'roles' => $roles
        ];


        $header   = array();
        $header[] = 'Authorization: Bearer ' . $this->getEaseMobToken();

        $url = config('chat-room.easemob_chat_room_url');//接收地址

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);
        //聊天室id需要保存到数据库

        $rooms_id = $response_data['data']['id'];
        curl_close($ch); //关闭curl链接
        return $rooms_id;
    }



    /**
     * 获取token
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getEaseMobToken() {
        //发送的数据
        $params = [
            'client_id' => env('EASEMOB_CLIENT_ID'),
            'client_secret' => env('EASEMOB_CLIENT_SECRET'),
            'grant_type' => config('chat-room.grant_type'),
        ];

        $url = config('chat-room.easemob_token_url');//接收XML地址

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);
        if(empty($response_data['access_token'])){
            return response()->json(['error' => $response_data ], 500);
        }
        if (curl_errno($ch)) {//出错则显示错误信息
            return response()->json(['error' => '数据获取失败-', curl_error($ch)], 500);
        }

        return $response_data['access_token'];
    }


    /**
     * 添加聊天室成员
     */
    public function addWorkToChatRoom($rooms_id,$username) {
        return true;
        $url      = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id . '/users/' . $username;//url
        $params   = '';//参数
        $header   = array();
        $header[] = 'Authorization: Bearer ' . $this->getEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息

        $response_data = json_decode($response, true);

        curl_close($ch); //关闭curl链接

        if(isset($response_data['data']['result'])){
            return $response_data['data']['result'];//true或者false
        }else{
            return false;//true或者false
        }

    }


    /**
     * 注册环信
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function RegistEaseMob($username) {
        return true;
        //发送的数据
        $params = [
            'username' => $username,
            'password' => env('EASEMOB_USER_PASSWORD')
        ];

        $header = array();
        $header[] = 'Authorization: Bearer '.getEaseMobToken();

        $url = config('chat-room.easemob_users_url');//接收地址

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        // 屏蔽打开
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);

        if (curl_errno($ch)) {//出错则显示错误信息
            return response()->json(['error' => '数据获取失败-', curl_error($ch)], 500);
        }

        return true;
//        return $response_data['entities'][0]['username'];

    }


}