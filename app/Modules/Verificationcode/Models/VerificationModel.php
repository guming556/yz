<?php

namespace App\Modules\Verificationcode\Models;

use Illuminate\Database\Eloquent\Model;
//use Redis;
use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Redis;

class VerificationModel extends Model {


    /**
     * @param $code_type
     * @param $user_type
     * @param $tel
     * @return bool
     * 发送验证码
     */
    static function sendVerificationCode($code_type, $tel) {

        switch ($code_type) {
            case 'reg':
                $sign     = '易装平台注册验证';
                $template = 'SMS_83085020';
                break;
            case 'forget':
                $sign     = '易装平台修改密码验证';
                $template = 'SMS_82930016';
                break;
            default:
                $sign     = '易装平台注册验证';
                $template = 'SMS_83085020';
        }

        $config      = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'alidayu',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'alidayu' => [
                    'app_key' => env('MESSAGE_APP_KEY'),
                    'app_secret' => env('MESSAGE_APP_SECRTE'),
                    'sign_name' => $sign,
                ],
            ],
        ];
        $easySms     = new EasySms($config);
        $code        = rand(100000, 999999);
        $key         = 'user:code:' . $tel;
        $mobile_code = $tel . '-' . $code;

        Redis::set($key, $mobile_code, 'EX', 120);

        $res_msg = $easySms->send($tel,
            [
                'template' => $template,
                'data' => [
                    'code' => (string)$code,
                    'product' => '易装',
                ],
            ]);

        if (is_array($res_msg)) {
            return true;
        } else {
            return $res_msg;
        }

    }


    /**
     * @param $tel
     * @return array|bool
     * 易装平台欢迎短信
     */
    static function sendWelcomeMsg($tel) {

        $sign     = '易装平台欢迎短信';
        $template = 'SMS_105445011';

        $config      = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'alidayu',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'alidayu' => [
                    'app_key' => env('MESSAGE_APP_KEY'),
                    'app_secret' => env('MESSAGE_APP_SECRTE'),
                    'sign_name' => $sign,
                ],
            ],
        ];
        $easySms     = new EasySms($config);
        $res_msg = $easySms->send($tel,
            [
                'template' => $template,
                'data' => [
                    'product' => '易装',
                ],
            ]);

        if (is_array($res_msg)) {
            return true;
        } else {
            return $res_msg;
        }

    }


    /**
     * @param string $code
     * @param $tel
     * @param string $user_type
     * @param string $code_type
     * @return bool
     * 检查验证码是否匹配
     */
    static function checkCode($code = '1234', $tel) {

        $key          = 'user:code:' . $tel;
        $redis_detail = Redis::get($key);
        $receive      = $tel . '-' . $code;

        if ($receive == $redis_detail)
            return true;
        else
            return false;
    }


}


