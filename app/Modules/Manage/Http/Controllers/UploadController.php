<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 15:14
 */

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Http\Controllers\BaseController;
use App\Http\Requests;
use App\Modules\Manage\Model\UploadModel;
use Illuminate\Http\Request;



class UploadController extends  ManageController
{

    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('上传图纸');
        $this->theme->set('manageType', 'Upload');
    }


    /**
     * 上传图纸
     *
     * @param Request $request
     * @return mixed
     */

    /**
     * 上传图纸列表
     *
     * @param Request $request
     * @return mixed
     */
    public function uploadList(Request $request)
    {

        $search = $request->all(); //获取全部参数

        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;

        $uploadList = UploadModel:: whereRaw('order_id > 0');


        $uploadList = $uploadList-> orderby('updated_at','desc')
            ->paginate($paginate);

        $data = $uploadList->toArray();

        $count=0;   //计算有多少个订单需要处理

        foreach($data['data'] as $item)
        {
            if($item['status']==1)
                $count++;
        }
        $view = array(
            'uList' => $data,
            'merge' => $search,
            'count' => $count
        );

        return $this->theme->scope('manage.uploadlist', $view)->render();

    }
}