<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use Teepluss\Theme\Theme;
use DB;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkAttachmentModel;

use App\Respositories\UserRespository;
//use App\Respositories\TaskRespository;
//use App\Respositories\TaskAppointRespository;

class WxController extends IndexController
{
    protected $userRespository;
    public function __construct(UserRespository $userRespository)
    {
        parent::__construct();
        $this->userRespository       = $userRespository;
        $this->initTheme('wx');
        $this->theme->setTitle('装修报装');
    }
    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
//        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志
//        $app = app('wechat.official_account');
        $app = app('wechat');
        $app->server->setMessageHandler(function($message){
            return "欢迎关注 overtrue！";
        });

        return $app->server->serve();
    }



    public function subRegData(Request $request){

        $data['openid'] = $request->get('openid');
        $data['name'] = $request->get('name');
        $data['tel']   = $request->get('tel');
        $data['password']   = $request->get('password');
        $data['confirm_password']   = $request->get('confirm_password');
        $building_id   = $request->get('building_id');
        if(empty($building_id)){
            return response()->json( ['message'=>'必要参数为空' , 'code'=>400]);
        }
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json( ['message'=>'必要参数为空' , 'code'=>400]);
            }
        }
        if($data['password'] !== $data['confirm_password']){
            return response()->json( ['message'=>'两次密码不一致' , 'code'=>400]);
        }

        $ret = DB::table('building_property')->where('tel',$data['tel'])->where('deleted',0)->first();

        if (!empty($ret)) {
            return response()->json( ['message'=>'该号码已注册' , 'code'=>400]);
        } else {

            $data['password'] = md5($data['password']);
            unset($data['confirm_password']);
            $ret_id = DB::table('building_property')->insertGetId($data);

            if(!empty($ret_id)){
                DB::table('building')->where(['id'=>$building_id])->update(['property_id'=>$ret_id]);
                return response()->json( ['message'=>'注册成功' , 'code'=>200]);
            }
            return response()->json( ['message'=>'注册失败，请联系客服！' , 'code'=>500]);
        }

    }



    public function getUserInfo()
    {
        $this->initTheme('main');
        $building = DB::table('building')->where('deleted',0)->where('property_id',0)->get();
        $userinfo = session('wechat.oauth_user')->original; // 拿到授权用户资料

        $data['building'] = $building;
        $data['openid'] = $userinfo['openid'];

        if(empty($data['openid'])){
            echo '无法获取openid';exit;
        }
        return $this->theme->scope('regProperty.reg',$data)->render();
    }

    /****************************************************************************************
     *          微信端装修报装
     *****************************************************************************************/
    //首页（微信端）
    public function wxRepairIndex(){
        $data['ad'] = DB::table('ad')->select('ad_file')->orderBy('id','desc')->limit(4)->get();
        foreach($data['ad'] as $key => &$value){
            $value->ad_file = url($value->ad_file);
        }
        unset($value);

        $construction = ProjectPositionModel::select('project_position.id', 'project_position.project_position',  'project_position.square', 'project_position.region', 'task.id as task_id',  'task.unique_code')
            ->where('project_position.deleted', 0)
            ->join('task', 'project_position.id', '=', 'task.project_position')
            ->where('task.user_type', 3)
            ->where('task.hidden_status', 2)
            ->groupBy('project_position.id')
            ->orderBy('task_id', 'desc')
            ->limit(10)->get()->toArray();



            $unique_code = array_column($construction,'unique_code');

            $designerTaskIds = TaskModel::select('id')->where('user_type', 2)->whereIn('unique_code', $unique_code)->orderBy('id', 'DESC')->groupBy('project_position')->get()->toArray();
            $preliminary = !empty($designerTaskIds)?array_column($designerTaskIds,'id'):[];

            $imageOfPreliminary = WorkAttachmentModel::select('work_attachment.id', 'attachment.url', 'work_attachment.task_id', 'task.project_position')
                ->join('attachment', 'attachment.id', '=', 'work_attachment.attachment_id')
                ->join('task', 'task.id', '=', 'work_attachment.task_id')
                ->where('work_attachment.img_type', 1)
                ->whereIn('work_attachment.task_id', $preliminary)
                ->orderBy('work_attachment.id', 'asc')->get()->toArray();

            $taskSecondImage = [];
            if(!empty($imageOfPreliminary)){
                foreach($imageOfPreliminary as $key => $value){
                    $imageArrExplode = explode('.',$value['url']);
                    $taskSecondImage[$value['project_position']][] = url($imageArrExplode[0].'_small.'.$imageArrExplode[1]);
                }
            }

            foreach ($construction as $key => $value) {
                $construction[$key]['first_image'] = '';
                unset($construction[$key]['unique_code']);
                if(isset($taskSecondImage[$value['id']][1])){
                    $construction[$key]['first_image'] = $taskSecondImage[$value['id']][1];
                }
            }

//var_dump($construction);exit;
        $data['construction'] = $construction;


        return $this->theme->scope('wxRepair.index',$data)->render();
    }

    public function buildingList(Request $request){

        $data['id'] = intval($request->get('id'));

        $data['province'] = DB::table('building as b')->join('coordinate as c','b.province_id','=','c.id')->select('c.id','c.name')->groupBy('c.id')->get();

        if(empty($data['id'])){
            if(!empty($data['province'])){
                $data['id'] = $data['province'][0]->id;
            }
        }

        $data['current_province'] = !empty($data['id'])?$data['province'][0]->name:'城市';

        $data['building'] = DB::table('building')->select('id','building_name')->where('deleted',0)->where('province_id',$data['id'])->get();

        return $this->theme->scope('wxRepair.buildingList',$data)->render();
    }


    //装修报装
    public function renovation(Request $request){
        $data['id'] = intval($request->get('id'));
        $data['building'] = DB::table('building')->select('id','building_name')->where('deleted',0)->get();
        $data['housekeeper'] = DB::table('users as u')->join('user_detail as ud','u.id','=','ud.uid')
            ->select('ud.avatar','u.id','ud.realname','ud.cost_of_design','ud.star')->where('u.user_type',3)
            ->limit(10)->get();

        $data['ad'] = DB::table('ad')->select('ad_file')->orderBy('id','desc')->limit(4)->get();
        foreach($data['ad'] as $key => &$value){
            $value->ad_file = url($value->ad_file);
        }
//        var_dump($data['ad']);exit;
        unset($value);
        foreach($data['housekeeper'] as $key => &$value){
            $value->cost_of_design = mt_rand(10,50).'元/平方米';
            $data['housekeeper'][$key]->good = mt_rand(1,10);
            $data['housekeeper'][$key]->commonly = mt_rand(1,10);
            $data['housekeeper'][$key]->bad = mt_rand(1,10);
            $data['housekeeper'][$key]->avatar = !empty($value->avatar)?url($value->avatar):'';
        }
        unset($value);
        if(empty($data['id'])){
            if(!empty($data['building'])){
                $data['id'] = $data['building'][0]->id;
            }
        }
        return $this->theme->scope('wxRepair.renovation',$data)->render();
    }

    //我要报装
    public function userRenovation(){
        return $this->theme->scope('wxRepair.userRenovation')->render();
    }

    //我的报装
    public function myRenovationRecord(){
        return $this->theme->scope('wxRepair.myRenovationRecord')->render();
    }

    //登录
    public function wxLogin(){
        return $this->theme->scope('wxRepair.login')->render();
    }

    //注册
    public function wxReg(){
        return $this->theme->scope('wxRepair.register')->render();
    }

    //找管家
    public function findHousekeeper(Request $request){
        $data['id'] = intval($request->get('id'));
        $data['building'] = DB::table('building')->select('id','building_name')->where('deleted',0)->get();
        $data['housekeeper'] = DB::table('users as u')->join('user_detail as ud','u.id','=','ud.uid')
            ->select('ud.avatar','u.id','ud.realname','ud.cost_of_design','ud.star')->where('u.user_type',3)
            ->limit(10)->get();


        foreach($data['housekeeper'] as $key => &$value){
            $value->cost_of_design = mt_rand(10,50).'元/平方米';
            $data['housekeeper'][$key]->good = mt_rand(1,10);
            $data['housekeeper'][$key]->commonly = mt_rand(1,10);
            $data['housekeeper'][$key]->bad = mt_rand(1,10);
            $data['housekeeper'][$key]->avatar = !empty($value->avatar)?url($value->avatar):'';
        }

        unset($value);
        if(empty($data['id'])){
            if(!empty($data['building'])){
                $data['id'] = $data['building'][0]->id;
            }
        }
        return $this->theme->scope('wxRepair.findHousekeeper',$data)->render();
    }

    //找工人
    public function findWorker(Request $request){
        $data['id'] = intval($request->get('id'));
        $data['building'] = DB::table('building')->select('id','building_name')->where('deleted',0)->get();

        $data['worker'] = DB::table('users as u')->join('user_detail as ud','u.id','=','ud.uid')
            ->select('ud.avatar','u.id','ud.realname','ud.cost_of_design','ud.star','ud.native_place','ud.work_type')->where('u.user_type',5)
            ->limit(10)->get();

        $worker = [
            "5"=> "泥水工",
            "6"=> "木工",
            "7"=>"水电工",
            "8"=>"油漆工",
            "9"=>"安装工",
            "10"=>"拆除工"
        ];

        foreach($data['worker'] as $key => &$value){
            $value->cost_of_design = mt_rand(10,50).'元/平方米';
            $data['worker'][$key]->good = mt_rand(1,10);
            $data['worker'][$key]->commonly = mt_rand(1,10);
            $data['worker'][$key]->bad = mt_rand(1,10);
            $data['worker'][$key]->work_type = $worker[$value->work_type];
            $data['worker'][$key]->avatar = !empty($value->avatar)?url($value->avatar):'';
        }

        unset($value);
        if(empty($data['id'])){
            if(!empty($data['building'])){
                $data['id'] = $data['building'][0]->id;
            }
        }
        return $this->theme->scope('wxRepair.findWorker',$data)->render();
    }

    //管家详细
    public function housekeeperDetail(Request $request){
        $housekeeper_id     = $request->get('id');
//        $user_id            = $request->get('user_id');

        $data['houseKeeper_detail'] = $this->userRespository->getHouseKeeperAndSuperVisorDetailByid($housekeeper_id, $housekeeper_id)->toArray();

        foreach($data['houseKeeper_detail']['goods_list'] as $key => $value){
            $data['houseKeeper_detail']['goods_list'][$key]->cover = str_replace('{@fill}','_medium',$value->cover);
        }
//        return response()->json($houseKeeper_detail->toArray());
//        $data
        return $this->theme->scope('wxRepair.housekeeperDetail',$data)->render();
    }




    public function LUpload(Request $request){
        $file = $request->get('formFile');;
        $ret = \FileClass::LUploaderImage($file);
        if($ret){
            return response()->json(['code'=>200,'message'=>'上传成功','status'=>0 , 'path'=>$ret['path']]);
        }
        return response()->json(['code'=>400,'message'=>'上传出现错误','status'=>1 , 'path'=>$ret['path']]);
    }



}
