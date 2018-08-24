<?php
namespace App\Modules\Manage\Http\Controllers;



use App\Http\Controllers\ManageController;
use Dingo\Blueprint\Annotation\Request;

class OverviewController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('概述');
        $this->theme->set('manageType', 'Overview');
    }

    /**
     * 概述列表
     *
     */
    public function overview(Request $request)
    {
//        $search = $request->all();
//        $data = [
//            'merge' => $search,
//        ];
        $time = date('Y-m-d H:i:s',time()); //当前时间
        $old_time = date('Y-m-d H:i:s',strtotime('-1 day'));//前一天时间

        return $this->theme->scope('manage.overview')->render();
    }
}
