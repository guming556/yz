<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\BasicController;
use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\Manage\Model\ConfigModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class LevelController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('人员管理');
        $this->theme->set('manageType', 'Level');
    }

    //服务者 - 列表
    public function getService()
    {
        //管家
        $config1 = LevelModel::getConfigByType(1)->toArray();
        $housekeeper = LevelModel::getConfig($config1);

        //监理
        $config2 = LevelModel::getConfigByType(2)->toArray();
        $supervisor = LevelModel::getConfig($config2);

        $data = [
            'housekeeper' => $housekeeper,
            'supervisor' => $supervisor
        ];

        return $this->theme->scope('manage.seeting.service', $data)->render();
    }

    //格式化数据
//    private function getConfig($config)
//    {
//        $result['upgrade'] = json_decode($config['upgrade']);
//        unset($config['upgrade']);
//        $result['score'] = json_decode($config['score']);
//        unset($config['score']);
//        foreach ($config as $key => $item) {
//            $result['price'][] = json_decode($item);
//        }
//        return $result;
//    }

    //工人 - 列表
    public function getWorker($id = '')
    {
        $worker = ConfigModel::getConfigByAlias('worker');

        if(empty($worker)) {
            return redirect('/manage/addWorker')->with(['message'=>'请先添加工种！']);
        } else {
            $worker = $worker->toArray();
            $rule = json_decode($worker['rule'], true);
//        var_dump($rule);exit;
            //如果id为空，以第一个添加的工人键值为key去拿数据
            $key = $id?$id:current(array_keys($rule));

            $config = LevelModel::getConfigByType($key)->toArray();
            $worker =  LevelModel::getConfig($config);
//            var_dump($worker);exit;
            $data = [
                'active' => $key,
                'rule' => $rule,
                'worker' => $worker
            ];
            return $this->theme->scope('manage.seeting.worker', $data)->render();
        }
    }

    public function addWorker()
    {
        $worker = ConfigModel::getConfigByAlias('worker');
//var_dump($worker->toArray());exit;
        if($worker) {
            $data = [
                'worker' => json_decode($worker['rule'])
            ];
        } else {
            $data = [
                'worker' => []
            ];
        };


        return $this->theme->scope('manage.seeting.addWorker', $data)->render();
    }

    public function saveWorker(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $worker = ConfigModel::getConfigByAlias('worker');
        $rule = json_encode($data['name']);
        if(!$worker) {
            $addData = [
                'alias' => 'worker',
                'rule' => $rule,
                'type' => 'worker',
                'title' => '工种配置',
                'desc' => '工种配置',
            ];
            $res = ConfigModel::create($addData);
        } else {
            $updateData = [
                'rule' =>$rule,
            ];
            $res = ConfigModel::where('id',$worker->id)->update($updateData);
        }
        if($res){
            foreach($data['name'] as $key => $value) {
                LevelModel::isCreate($key);
            }
            return redirect('/manage/worker')->with(['message'=>'操作成功！']);
        }
        return redirect('/manage/worker')->with(['message'=>'操作失败！']);
    }

    public function deleteWorker($id)
    {
        $worker = ConfigModel::getConfigByAlias('worker')->toArray();
        $rule = json_decode($worker['rule'], true);
        $delete = array_pull($rule, $id);
        $updateData = [
            'rule' =>json_encode($rule),
        ];
        $res = ConfigModel::where('id',$worker['id'])->update($updateData);
        if($res) {
            LevelModel::where('type',$id)->delete();
            return response()->json(['errCode'=>1,'id'=>$id]);
        } else {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
    }

    public function getStaffing($id)
    {
        $config = LevelModel::getConfigByType($id)->toArray();
        $staffing =  LevelModel::getConfig($config);
        $data = [
            'id' => $id,
            'staffing' => $staffing
        ];
        return $this->theme->scope('manage.seeting.staffing', $data)->render();
    }

    public function saveStaffing(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $type = $data['id'];
        $updateDate = [
            'offer_1' => json_encode($data['offer_1']),
            'offer_2' => json_encode($data['offer_2']),
            'offer_3' => json_encode($data['offer_3']),
            'offer_4' => json_encode($data['offer_4']),
            'offer_5' => json_encode($data['offer_5']),
            'upgrade' => json_encode($data['upgrade']),
            'score' => json_encode($data['score']),
            'updated_at' => date('Y-m-d H:i:s',time())
        ];
        $res = LevelModel::where('type',$type)->update($updateDate);

        if($res){
            return redirect('/manage/service')->with(['message'=>'操作成功！']);
        }
        else{
            return redirect('/manage/service')->with(['message'=>'操作失败！']);
        }
    }
}
