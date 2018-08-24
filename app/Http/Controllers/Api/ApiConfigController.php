<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Manage\Model\ConfigModel;

class ApiConfigController extends BaseController
{
    //预约金设置
    public function advanceConfig()
    {
        $config = ConfigModel::where('type', 'pay_config_advance')->get()->toArray();
        $result = array_map(array($this,"getAdvance"), $config);
        $data = [
            'success'  => '成功',
            'data' => $result
        ];
        return $this->success($data);
    }

    private function getAdvance($data)
    {
        switch ($data['title'])
        {
            case '预约设计师':
                $result['type'] = 1;
                break;
            case '预约施工管家':
                $result['type'] = 2;
                break;
            case '预约施工监理':
                $result['type'] = 3;
                break;
            default:
                $result['type'] = 0;
                break;
        }
        $rule = json_decode($data['rule'], true);
        $result['money'] = $rule['money'];
        return $result;
    }

    /**
     * @param cash
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 获取支付回撤金(最大最小),保险,充值卡
     */
    public function payConfig()
    {
        $config = ConfigModel::getConfigByAlias('cash')->toArray();
        $rule = json_decode($config['rule'], true);
        //只从给定数组中返回指定键值对array_only
        $result = array_only($rule, array('recharge_min', 'withdraw_min', 'withdraw_max', 'insurance'));
        $data = [
            'success'  => '成功',
            'data' => $result
        ];
        return $this->success($data);
    }

    //接单设置
    public function orderConfig()
    {

    }

    //设计费用设置
    public function designConfig()
    {
        $config = ConfigModel::getConfigByAlias('design_config')->toArray();
        $rule = json_decode($config['rule'], true);
        $data = [
            'success'  => '成功',
            'data' => $rule
        ];
        return $this->success($data);
    }
}
