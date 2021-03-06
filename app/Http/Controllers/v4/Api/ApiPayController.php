<?php

namespace App\Http\Controllers\v4\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

use App\Http\Controllers\UserCenterController;
use App\Modules\Pay\OrderModel;
use Omnipay;
use Theme;
use QrCode;

class ApiPayController extends BaseController {
    public function getWechatpay() {
        $wechat       = Omnipay::gateway('wechat');
        $out_trade_no = OrderModel::randomCode();

        $params = array(
            'out_trade_no' => $out_trade_no,
            'notify_url' => \CommonClass::getDomain() . '/pay/wechatpay/notify?out_trade_no=' . $out_trade_no,
            'body' => 'goods',
            'total_fee' => 0.01,
            'fee_type' => 'CNY',
        );

        $response = $wechat->purchase($params)->send();

        $img = QrCode::size('200')->generate($response->getRedirectUrl());

        $theme = Theme::uses('default')->layout('usercenter');

        $view = array(
            'img' => $img
        );
        return $this->success($img);

    }


    public function notify() {

        $arrNotify = \CommonClass::xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);

        $content
            = '<xml>
                    <return_code><![CDATA[SUCCESS]]></return_code>
                    <return_msg><![CDATA[OK]]></return_msg>
                    </xml>';

        if ($arrNotify['result_code'] == 'SUCCESS' && $arrNotify['return_code'] = 'SUCCESS') {
            return $this->success($content)->header('Content-Type', 'text/xml');
        }


    }


    public function queryOrder($out_trade_no) {
        $wechat = Omnipay::gateway('wechat');
        $params = array(
            'out_trade_no' => $out_trade_no,

        );

        $response = $wechat->completePurchase($params)->send();

        if ($response->isSuccessful() && $response->isTradeStatusOk()) {
            $responseData = $response->getData();

        }
    }
}
