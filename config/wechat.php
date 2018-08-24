<?php

return [
    /**
     * Debug 模式，bool 值：true/false
     *
     * 当值为 false 时，所有的日志都不会记录
     */
    'debug'  => false,

    /**
     * 使用 Laravel 的缓存系统
     */
    'use_laravel_cache' => true,

    /**
     * 账号基本信息，请从微信公众平台/开放平台获取
     */
    'app_id'  => env('WECHAT_APPID', 'wx5ddd1d3684d4a266'),         // AppID
    'secret'  => env('WECHAT_SECRET', 'cfd3e6bd68a820e9fcd1e7b0d2d0220f'),     // AppSecret
    'token'   => env('WECHAT_TOKEN', 'yizhuang'),          // Token
    'aes_key' => env('WECHAT_AES_KEY', ''),                    // EncodingAESKey

    /**
     * 日志配置
     *
     * level: 日志级别，可选为：
     *                 debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'level' => env('WECHAT_LOG_LEVEL', 'debug'),
        'file'  => env('WECHAT_LOG_FILE', '/tmp/easywechat.log'),
    ],

    'oauth' => [
        'scopes'   => 'snsapi_userinfo',
        'callback' => '',
    ],


//    'enable_mock' => true,
//    'mock_user' => [
//        'openid' => 'odh7zsgI75iT8FRh0fGlSojc9PWM',
//        // 以下字段为 scope 为 snsapi_userinfo 时需要
//        'nickname' => 'overtrue',
//        'sex' => '1',
//        'province' => '北京',
//        'city' => '北京',
//        'country' => '中国',
//        'headimgurl' => 'http://wx.qlogo.cn/mmopen/C2rEUskXQiblFYMUl9O0G05Q6pKibg7V1WpHX6CIQaic824apriabJw4r6EWxziaSt5BATrlbx1GVzwW2qjUCqtYpDvIJLjKgP1ug/0',
//    ],

];