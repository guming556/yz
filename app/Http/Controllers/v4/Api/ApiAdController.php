<?php

namespace App\Http\Controllers\v4\Api;


use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use App\Http\Controllers\BaseController;

use App\Modules\Advertisement\Model\AdModel;


class ApiAdController extends BaseController
{

    /**
     * 广告 - 列表
     *
     */
    public function adList()
    {

        $adList = AdModel::where('is_open','1')
        	->select('id','ad_file', 'ad_content', 'ad_url', 'start_time', 'end_time', 'listorder', 'is_open', 'view')
        	->orderBy('listorder', 'asc')
        	->get()
        	->toArray();
        if (!empty($adList)) {
            foreach ($adList as $key => $value) {
                $adList[$key]['ad_file'] = url($value['ad_file']);
                $adList[$key]['ad_content'] = $value['ad_content']?$value['ad_content']:'';
            }
        }

        return $this->success($adList);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * bug修复
     */
    public function bugFix() {
        return $this->responseSuccess();
    }

    /**
     * 广告 - 更新点击数
     *
     * @param $ad_id 广告id
     */
    public function adClick($ad_id)
    {
        $adInfo = AdModel::find($ad_id);

        if (empty($adInfo)) {
            return $this->error('参数错误');
        }

        $res = $adInfo->update(['view' => $adInfo->view + 1]);
        if ($res) {
            return $this->success();
        } else {
            return $this->error('请求失败');
        }
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     * 返回版本号
     */
    public function getApiVision() {
        $API_VERSION_BAK  = env('BACK_UP_VERSION');
        $API_VERSION_NEW  = env('API_VERSION');
        $android_url      = env('ANDROID_URL');
        $android_url_work      = env('ANDROID_URL_WORKER');
        $OwnerSideVersion = env('OwnerSideVersion');
        $WorkSideVersion  = env('WorkSideVersion');
        $versionCode      = env('versionCode');
        $data = [
            'AvailableVersion' => $API_VERSION_NEW . ',' . $API_VERSION_BAK,
            'LatestVersion' => $API_VERSION_NEW,
            'OwnerSideVersion' => $OwnerSideVersion,
            'WorkSideVersion' => $WorkSideVersion,
            'versionCode' => (int)$versionCode,
            'versionName' => $API_VERSION_NEW,
            'AndroidUrl' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $android_url,
            'AndroidWorkUrl' => 'https://' . $_SERVER['HTTP_HOST'] . '/' . $android_url_work
        ];
        return $this->success($data);
    }

    /**
     * @return string
     * 把一些业主的余额改回
     */
    public function testChangeBossBalance() {
        $user = [15399900130,
                 13631555470,
                 13828886707,
                 13902910213,
                 18565638548,
                 13928437133,
                 18566562626,
                 15899764639,
                 13714835136,
                 13360096812,
                 13501598970,
                 13302922218,
                 13823338230,
                 13528819770,
                 13926579682,
                 15162113529,
                 15018417776,
                 17722687613];
        foreach ($user as $k => $v) {
            $user_id[] = UserModel::select('id')->where('name', $v)->first();
        }

        foreach ($user_id as $k => $v) {
            if (!empty($v)) {
                UserDetailModel::where('uid', $v['id'])->update(['balance' => 0]);
            }
        }

        return $this->success();
    }

}
