<?php

namespace App\Http\Controllers\v3\Api;

use Illuminate\Http\Request;


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

        return response()->json($adList);
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
            return response()->json( ['error'=>'参数错误'] , '403');
        }

        $res = $adInfo->update(['view' => $adInfo->view + 1]);
        if ($res) {
            return response()->json(['success' => '成功']);
        } else {
            return response()->json(['error' => '失败'], '503');
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
            'status_code_api' => 200
        ];
        return response()->json($data,200);
    }

}
