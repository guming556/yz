<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PushServiceModel extends Model {

    /**
     * @param $deviceToken 机器码
     * @param $message 消息
     * @param int $count 次数
     * @param string $application 是哪一类通知
     * @param string $title 标题
     * @return string
     * 推送给业主
     */
    public static function pushMessageBoss($deviceToken, $message, $count = 1, $application = '', $title = '') {
//        set_time_limit(0);
//        sleep(1);
        $deviceToken = deviceTokenChangeIOS($deviceToken);
        //token错误纠正
        if (empty($deviceToken)) {
            return true;
        }
        if (strpos($deviceToken, '_')) {
            return true;
        }
        if (strpos($deviceToken, '-')) {
            return true;
        }

        // 这里是我们上面得到的deviceToken，直接复制过来（记得去掉空格）
        $environment = env('APP_ENV_SEND_MSG');
        //根据不用环境加载不同的pem
        switch ($environment) {
            case 'test':
                $ck_boss = public_path() . '/device_testing/ck_boss_testing.pem';
                break;
            case 'company':
                $ck_boss = public_path() . '/device_company/ck_boss_company.pem';
                break;
            case 'online':
                $ck_boss = public_path() . '/device_online/ck_boss_online.pem';
                break;
            default:
                $ck_boss = public_path() . '/device_testing/ck_boss_testing.pem';
        }


        $passphrase = env('PASSPHRASE');//通知证书密码
        $ctx        = stream_context_create();

        stream_context_set_option($ctx, 'ssl', 'local_cert', $ck_boss);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);


        switch ($environment) {
            case 'test':
                $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx); //这个是沙盒测试地址，发布到appstore后记得修改哦
                break;
            case 'company':
            case 'online':
                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);//这个为正是的发布地址
                break;
            default:
                $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        }
        if (!$fp)
            exit("Failed to connect: $err $errstr" . PHP_EOL);

        //echo 'Connected to APNS' . PHP_EOL;

        //创建负载
        //前端接收数组（系统字段：badge 图标右上角数字，alert 消息内容，sound 提示音），可自行添加其它数据
        $body['aps'] = array(
            'badge' => $count,
            'alert' => $message,
            'application' => $application,
            'title' => $title,
            'sound' => 'default'
        );

        //对有效载荷为JSON编码
        $payload = json_encode($body);

        //构建二进制文件的通知
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

        //发送到服务器
        $result = fwrite($fp, $msg);
        //关闭连接到服务器
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }


    /**
     * @param $deviceToken 机器码
     * @param $message 消息
     * @param int $count 次数
     * @param string $application 是哪一类通知
     * @param string $title 标题
     * @return string
     * 推送给工作者
     */
    public static function pushMessageWorker($deviceToken, $message, $count = 1, $application = '', $title = '') {
//        set_time_limit(0);
//        sleep(1);
        $deviceToken = deviceTokenChangeIOS($deviceToken);

        if (empty($deviceToken)) {
            return true;
        }
        if (strpos($deviceToken, '_')) {
            return true;
        }
        if (strpos($deviceToken, '-')) {
            return true;
        }

        $pass = env('PASSPHRASE');//通知证书密码

        //前端接收数组（系统字段：badge 图标右上角数字，alert 消息内容，sound 提示音），可自行添加其它数据

        $body['aps'] = array(
            'badge' => $count,
            'alert' => $message,
            'application' => $application,
            'title' => $title,
            'sound' => 'default'
        );
        //把数组数据转换为json数据
        $payload = json_encode($body);
        //这个注释的是上线的地址，下边是测试地址，对应的是发布和开发：ssl://gateway.sandbox.push.apple.com:2195这个是沙盒测试地址，ssl://gateway.push.apple.com:2195正式发布地址创建推送流，然后配置推送流。
        $environment = env('APP_ENV_SEND_MSG');
        //根据不用环境加载不同的pem
        switch ($environment) {
            case 'test':
                $ck_worker = public_path() . '/device_testing/ck_worker_testing.pem';
                break;
            case 'company':
                $ck_worker = public_path() . '/device_company/ck_worker_company.pem';
                break;
            case 'online':
                $ck_worker = public_path() . '/device_online/ck_worker_online.pem';
                break;
            default:
                $ck_worker = public_path() . '/device_testing/ck_worker_testing.pem';
        }

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ck_worker);   //刚刚合成的pem文件
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            print "Failed to connect $err $errstr\n";
            return;
        } else {
            //echo 'Connected to APNS' . PHP_EOL;
        }

        $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;
        //推送和关闭当前流
        $result = fwrite($fp, $msg);
        fclose($fp);
        if (!$result)
            return 'Message not delivered' . PHP_EOL;
        else
            return 'Message successfully delivered' . PHP_EOL;
    }


    /**
     * 安卓单个设备下发通知消息(推送给业主)
     */
    public static function pushMessageBossAndroid($deviceToken, $content = '系统消息', $custom = ['task_id'=>'','task_type'=>'']) {
        $deviceToken = deviceTokenChange($deviceToken);
        if (empty($deviceToken)) {
            return true;
        }
        if (strpos($deviceToken, '_')) {
            return true;
        }
        if (strpos($deviceToken, '-')) {
            return true;
        }
        XingeApp::PushTokenAndroid($content,$deviceToken,$custom);
        return true;
    }

    /**
     * 安卓单个设备下发通知消息(推送给工作者)
     */
    public static function pushMessageWorkerAndroid($deviceToken, $content = '系统消息', $custom = ['task_id'=>'','task_type'=>'']) {
        $deviceToken = deviceTokenChange($deviceToken);
        if (empty($deviceToken)) {
            return true;
        }
        if (strpos($deviceToken, '_')) {
            return true;
        }
        if (strpos($deviceToken, '-')) {
            return true;
        }
        XingeApp::PushTokenAndroid($content,$deviceToken,$custom);
        return true;
    }


}
