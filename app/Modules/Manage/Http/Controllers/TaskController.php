<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\Manage\Model\MessageTemplateModel;
use App\Modules\Manage\Model\ServiceModel;
use App\Modules\Manage\Model\SubOrderModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\Task\Model\Auxiliary;
use App\Modules\Task\Model\ProjectSmallOrder;
use App\Modules\Task\Model\TaskAttachmentModel;
use App\Modules\Task\Model\TaskExtraModel;
use App\Modules\Task\Model\TaskExtraSeoModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\TaskTypeModel;
use App\Modules\Task\Model\WorkAttachmentModel;
use App\Modules\Task\Model\WorkCommentModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\User\Model\MessageReceiveModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Excel;
use Theme;
use App\Respositories\TaskRespository;

class TaskController extends ManageController
{
    protected $taskRespository;
    public function __construct(TaskRespository $taskRespository)
    {
        parent::__construct();
        $this->taskRespository = $taskRespository;
        $this->initTheme('manage');
        $this->theme->setTitle('任务列表');
        $this->theme->set('manageType', 'task');
    }
    /**
     * 任务列表
     *
     * @param Request $request
     * @return mixed
     */
    public function taskList(Request $request)
    {
        $search = $request->all();
//        var_dump($search);exit;
        $by = $request->get('by') ? $request->get('by') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'desc';
        $paginate = $request->get('paginate') ? $request->get('paginate') : 15;

        $taskList = TaskModel::select('task.id','task.broadcastOrderBy','task.hidden_status','task.user_type', 'us.name',  'task.created_at', 'task.status', 'task.verified_at', 'task.bounty_status','p.region', 'p.project_position','user_detail.nickname as boss_nike_name','task.type_id as type_model','o.code');

        if ($request->get('task_title')) {
//            var_dump($request->get('task_title'));exit;
            $taskList = $taskList->where('p.region','like','%'.$request->get('task_title').'%')->orWhere('p.project_position','like','%'.$request->get('task_title').'%');
        }
        if ($request->get('username')) {
            $taskList = $taskList->where('us.name','like','%'.e($request->get('username')).'%');
        }
        if ($request->get('code')) {
            $taskList = $taskList->where('o.code',$request->get('code'));
        }
        //状态筛选
        if ($request->get('status') && $request->get('status') != 0) {
            switch($request->get('status')){
                case 1:
                    $status = [0];
                    break;
                case 2:
                    $status = [1,2];
                    break;
                case 3:
                    $status = [3,4,5,6,7,8];
                    break;
                case 4:
                    $status = [9];
                    break;
                case 5:
                    $status = [10];
                    break;
                case 6:
                    $status = [11];
                    break;
            }
            $taskList = $taskList->whereIn('task.status',$status);
        }
        //时间筛选
        if($request->get('time_type')){
            if($request->get('start')){
                $start = date('Y-m-d H:i:s',strtotime($request->get('start')));
                $taskList = $taskList->where($request->get('time_type'),'>',$start);
            }
            if($request->get('end')){
                $end = date('Y-m-d H:i:s',strtotime($request->get('end')));
                $taskList = $taskList->where($request->get('time_type'),'<',$end);
            }

        }

//        if(!empty($request->get('time_type'))){
//
//        }

        $taskList = $taskList->orderBy($by, $order)
            ->leftJoin('users as us', 'us.id', '=', 'task.uid')
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('order as o' , 'o.task_id' , '=' , 'task.id')
        ->paginate($paginate);

        $data = array(
            'task' => $taskList,
        );
        $data['merge'] = $search;

        return $this->theme->scope('manage.tasklist', $data)->render();
    }








    public function cityStationTask(Request $request)
    {
        $search = $request->all();

        $by = $request->get('by') ? $request->get('by') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'desc';
        $paginate = $request->get('paginate') ? $request->get('paginate') : 15;

        $taskList = TaskModel::select('task.id','task.broadcastOrderBy','task.hidden_status','task.user_type', 'us.name',  'task.created_at', 'task.status', 'task.verified_at', 'task.bounty_status','p.region', 'p.project_position','user_detail.nickname as boss_nike_name','task.type_id as type_model','o.code');

        if ($request->get('task_title')) {
//            var_dump($request->get('task_title'));exit;
            $taskList = $taskList->where('p.region','like','%'.$request->get('task_title').'%')->orWhere('p.project_position','like','%'.$request->get('task_title').'%');
        }
        if ($request->get('username')) {
            $taskList = $taskList->where('us.name','like','%'.e($request->get('username')).'%');
        }
        if ($request->get('code')) {
            $taskList = $taskList->where('o.code',$request->get('code'));
        }
        //状态筛选
        if ($request->get('status') && $request->get('status') != 0) {
            switch($request->get('status')){
                case 1:
                    $status = [0];
                    break;
                case 2:
                    $status = [1,2];
                    break;
                case 3:
                    $status = [3,4,5,6,7,8];
                    break;
                case 4:
                    $status = [9];
                    break;
                case 5:
                    $status = [10];
                    break;
                case 6:
                    $status = [11];
                    break;
            }
            $taskList = $taskList->whereIn('task.status',$status);
        }
        //时间筛选
        if($request->get('time_type')){
            if($request->get('start')){
                $start = date('Y-m-d H:i:s',strtotime($request->get('start')));
                $taskList = $taskList->where($request->get('time_type'),'>',$start);
            }
            if($request->get('end')){
                $end = date('Y-m-d H:i:s',strtotime($request->get('end')));
                $taskList = $taskList->where($request->get('time_type'),'<',$end);
            }

        }

//        if(!empty($request->get('time_type'))){
//
//        }
//
//        $manageInfo = Session::get('manager');
        if($this->manager->id != 1){
            $taskList = $taskList->where('position_city_id',$this->manager->manage_city);
        }

        $taskList = $taskList->orderBy($by, $order)
            ->leftJoin('users as us', 'us.id', '=', 'task.uid')
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('order as o' , 'o.task_id' , '=' , 'task.id')
            ->paginate($paginate);

        $data = array(
            'task' => $taskList,
        );
        $data['merge'] = $search;

        return $this->theme->scope('manage.cityTaskList', $data)->render();
    }
















    /**
     * 任务处理
     *
     * @param $id
     * @param $action
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function taskHandle($id, $action)
    {
        if (!$id) {
            return \CommonClass::showMessage('参数错误');
        }
        $id = intval($id);

        switch ($action) {
            //审核通过
            case 'pass':
                $status = 3;
                break;
            //审核失败
            case 'deny':
                $status = 10;
                break;
        }
        //审核失败和成功 发送系统消息
        $task = TaskModel::where('id',$id)->first();
        $user = UserModel::where('id',$task['uid'])->first();
        $site_name = \CommonClass::getConfig('site_name');
        if($status==3)
        {
            $result = TaskModel::where('id', $id)->whereIn('status', [1,2])->update(array('status' => $status));
            if(!$result)
            {
                return redirect()->back()->with(['error'=>'操作失败！']);
            }
            $task_audit_failure = MessageTemplateModel::where('code_name','audit_success ')->where('is_open',1)->where('is_on_site',1)->first();
            if($task_audit_failure)
            {
                //发送系统消息
                $messageVariableArr = [
                    'username'=>$user['name'],
                    'website'=>$site_name,
                    'task_number'=>$task['id'],
                ];
                $message = MessageTemplateModel::sendMessage('audit_success',$messageVariableArr);
                $data = [
                    'message_title'=>$task_audit_failure['name'],
                    'code'=>'audit_success',
                    'message_content'=>$message,
                    'js_id'=>$user['id'],
                    'message_type'=>2,
                    'receive_time'=>date('Y-m-d H:i:s',time()),
                    'status'=>0,
                ];
                MessageReceiveModel::create($data);
            }
        }elseif($status==10)
        {
            $result = DB::transaction(function() use($id,$status,$task){
                 TaskModel::where('id', $id)->whereIn('status', [1,2])->update(array('status' => $status));
                //判断任务是否需要退款
                if($task['bounty_status']==1)
                {
                    UserDetailModel::where('uid',$task['uid'])->increment('balance',$task['bounty']);
                    //生成财务记录
                    $finance = [
                        'action'=>7,
                        'pay_type'=>1,
                        'cash'=>$task['bounty'],
                        'uid'=>$task['uid'],
                        'created_at'=>date('Y-m-d H:i:d',time()),
                        'updated_at'=>date('Y-m-d H:i:d',time())
                    ];
                    FinancialModel::create($finance);
                }
            });
            if(!is_null($result))
            {
                return redirect()->back()->with(['error'=>'操作失败！']);
            }
            $task_audit_failure = MessageTemplateModel::where('code_name','task_audit_failure ')->where('is_open',1)->where('is_on_site',1)->first();
            if($task_audit_failure)
            {
                //发送系统消息
                $messageVariableArr = [
                    'username'=>$user['name'],
                    'task_title'=>$site_name,
                    'website'=>$site_name,
                ];
                $message = MessageTemplateModel::sendMessage('task_audit_failure',$messageVariableArr);
                $data = [
                    'message_title'=>$task_audit_failure['name'],
                    'code'=>'task_audit_failure',
                    'message_content'=>$message,
                    'js_id'=>$user['id'],
                    'message_type'=>2,
                    'receive_time'=>date('Y-m-d H:i:s',time()),
                    'status'=>0,
                ];
                MessageReceiveModel::create($data);
            }

        }
        return redirect()->back()->with(['message'=>'操作成功！']);
    }


    /**
     * 任务批量处理
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|mixed
     */
    public function taskMultiHandle(Request $request)
    {
        if (!$request->get('ckb')) {
            return \CommonClass::adminShowMessage('参数错误');
        }
        switch ($request->get('action')) {
            case 'pass':
                $status = 3;
                break;
            case 'deny':
                $status = 10;
                break;
            default:
                $status = 3;
                break;
        }

        $status = TaskModel::whereIn('id', $request->get('ckb'))->where('status', 1)->orWhere('status', 2)->update(array('status' => $status));
        if ($status)
            return back();

    }


    //进入工程详细配置页
    public function projectConfDetail( $task_id ){
        $deatil     = ProjectConfigureTask::where('task_id',$task_id)->where('is_sure',1)->first();     //当期最终的配置单
        $taskDetail = TaskModel::where('id',$task_id)->first();

        $deatil = unserialize($deatil->project_con_list);

        $total  = $deatil['all_parent_price'];
        unset($deatil['all_parent_price']);
        $type = 'projectConfDetail';
        $arr = [];
        foreach($deatil as $key => $value){

            $arr[$key]['project_type_name'] = $value['parent_name'];
            $arr[$key]['row'] = count($value['childs'])+1;
            $arr[$key]['child'] = $value['childs'];
            $arr[$key]['id'] = $key;
            $arr[$key]['project_type'] = $value['parent_project_type'];
//            TODO 5点了，好困，不想改得更好了 ， 写得废了点，迟些再改回来
            $needWorks = ProjectConfigureModel::where('pid',0)->where('project_type',$value['parent_project_type'])->first();
            if($value['parent_project_type'] != 2){
                $arr[$key]['need_work'] = UserModel::select('user_detail.realname','user_detail.uid')
                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                    ->where('users.user_type',5)
                    ->where('user_detail.work_type' , $needWorks->work_type)
                    ->where('star',$taskDetail->workerStar)
                    ->get()->toArray();
            }else{
                $need = explode('-',$needWorks->work_type);
                $arr[$key]['need_work'] = UserModel::select('user_detail.realname','user_detail.uid')
                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                    ->where('users.user_type',5)
                    ->where('user_detail.work_type' , $need[0])
                    ->where('star',$taskDetail->workerStar)
                    ->get()->toArray();

                $arr[$key]['need_work_2'] = UserModel::select('user_detail.realname','user_detail.uid')
                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                    ->where('users.user_type',5)
                    ->where('star',$taskDetail->workerStar)
                    ->where('user_detail.work_type' , $need[1])
                    ->get()->toArray();
            }
        }

        $data = [
            'deatil' => $arr,
            'task_id'=>$task_id,
            'type'=>$type
        ];

        return $this->theme->scope('manage.project_conf_list.firstConfDetail',$data)->render();
    }


    /**
     * 任务详情
     * @param $id
     */
    public function taskDetail($id) {
        $task = TaskModel::where('id', $id)->first();
        if (!$task) {
            return back()->with(['error' => '当前任务不存在，无法查看稿件！']);
        }

        $taskDetail = TaskModel::select(
            'task.uid', 'task.cancel_order', 'task.end_order_status', 'task.type_id as type_model',
            'p.room_config', 'task.created_at', 'p.square', 'task.status','task.boss_agree_star','task.designer_actual_price',
            'task.project_position as project_position_id', 'c.name as favourite_style','task.quanlity_service_money',
            'task.id as task_id', 'task.user_type', 'task.view_count','task.workerStar',
            'task.show_cash', 'p.region', 'p.live_tv_url', 'p.project_position',
            'users.name', 'user_detail.mobile', 'user_detail.avatar as boss_avatar',
            'user_detail.nickname as boss_nike_name','task.end_order_status','task.unique_code',
            'pt.auxiliary_id'
        )->where('task.id', $id)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('project_configure_tasks as pt', 'pt.task_id', '=', 'task.id')
            ->leftJoin('users', 'users.id', '=', 'task.uid')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->distinct('task.id')
            ->first();

        $auxiliary_data = Auxiliary::find($taskDetail['auxiliary_id']);
        $taskDetail['auxiliary_id'] = empty($auxiliary_data) ? '' : $auxiliary_data->name;

        //设计师详细
        $workers = WorkModel::select(
            'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
            'user_detail.mobile', 'user_detail.city as address', 'work.status',
            'user_detail.cost_of_design', 'work.price', 'work.actual_square', 'work_designer_logs.is_refuse'
        )->where('work.task_id', $id)->where('work_designer_logs.task_id', $id)
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
            ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
            ->distinct('work.uid')
            ->first()
            ->toArray();
        if (empty($workers['mobile'])) {
            $workers['mobile'] = UserModel::find($workers['uid'])->name;
        }

        //工程配置单
        $user_type_user = $task->user_type;

        //管家或者监理的单子,先找设计师的订单id

        if ($user_type_user == 3 || $user_type_user == 4) {
            $project_position = $task->project_position;
            if($taskDetail->status >= 9){
                if($taskDetail->end_order_status == 1){
                    $designer_task    = TaskModel::where('unique_code', $taskDetail->unique_code)
                                                    ->where('status',9)->where('end_order_status',1)
                                                    ->where('user_type', 2)->first();
                }else{
                    $designer_task = [];
                }
            }else{
                $designer_task    = TaskModel::where('unique_code', $taskDetail->unique_code)
                                    ->where('status','<',9)->where('user_type', 2)->first();
            }

            if(empty($designer_task)){
                $conf = [];
            }else{
                $conf = ProjectConfigureTask::where('task_id', $designer_task->id)->where('is_sure', 0)->orderBy('id', 'desc')->first();//拿到配置单(设计师提交)
            }

            //设计师未提交配置单
            if (empty($conf)) {
                $confList = [];
            } else {
                $real_id = $this->baseHouseKeeperFindSuperVioson($user_type_user,$task,$id);
                $deatil_final_conf = ProjectConfigureTask::where('task_id', $real_id)->where('is_sure', 1)->first();     //当期最终的配置单
                if (!empty($deatil_final_conf)) {
                    $confList = unserialize($deatil_final_conf->project_con_list);
                } else {
                    $confList = unserialize($conf->project_con_list);
                    $confList = create_configure_lists($confList);
                }
            }

        } else {
            $conf = ProjectConfigureTask::where('task_id', $id)->where('is_sure', 0)->orderBy('id', 'desc')->first();//拿到配置单(设计师提交)
            if (empty($conf)) {
                $confList = [];
            }else{
                $confList = unserialize($conf->project_con_list);
                $confList = create_configure_lists($confList);
            }
        }


        //找到进行到哪一步了
        $work_offer_status = [];

        if ($taskDetail['status'] == 7) {
//            $real_id           = $this->baseHouseKeeperFindSuperVioson($user_type_user, $task, $id);
            $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'title', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                ->where('task_id', $id)
                ->orderBy('sn', 'ASC')
                ->get()->toArray();

            //返回work_offer中status为0的前一条数据
            foreach ($work_offer_status as $n => $m) {
                if ($m['status'] == 0) {
                    unset($work_offer_status[$n]);
                }
            }

            if (!empty($work_offer_status)) {
                $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
            }
        }


        //没有work_offer状态,取work的状态
        if (empty($work_offer_status)) {
            $work_status = work_status($workers['status']);

        } else {

            $work_status = $last_work_offer_status['title'] . work_offer_status($last_work_offer_status['status']);

        }

        $first_work_offer          = WorkOfferModel::select('price','actual_square')->where('task_id', $id)->where('sn', 0)->first();
        $taskDetail['status_work'] = $work_status;
        $taskDetail['first_price'] = empty($first_work_offer) ? '暂无报价' : $first_work_offer->price;//第一次报价总价


        if ($user_type_user == 3) {
            $config1 = LevelModel::getConfigByType(1)->toArray();
        } elseif ($user_type_user == 4) {
            $config1 = LevelModel::getConfigByType(2)->toArray();
            $quanlity_price_total = $taskDetail->quanlity_service_money;
            $quanlity_price       = ServiceModel::select('price')->where('identify', 'QUANLITYSERVICE')->first();
            $taskDetail['quanlity_price_unit'] =  $quanlity_price->price;
            $taskDetail['quanlity_price_total'] = empty($quanlity_price_total) ? '暂无' : $quanlity_price_total;
        }

        $taskDetail['area']       = empty($workers['actual_square']) ? '暂无' : $workers['actual_square'];
        if ($user_type_user == 3 || $user_type_user == 4) {
            $workerStarPrice          = LevelModel::getConfig($config1, 'price');
            $taskDetail['unit_price'] = empty($taskDetail->boss_agree_star) ? '暂无' : $workerStarPrice[$taskDetail->boss_agree_star - 1]->price;
        } else {
            $taskDetail['unit_price'] = empty($taskDetail->designer_actual_price) ? '暂无' : $taskDetail->designer_actual_price;
        }

        //找到该项目对应的图纸
        $imgList = WorkAttachmentModel::select('attachment.name', 'attachment.url', 'img_type')->where('task_id', $id)->leftJoin('attachment', 'work_attachment.attachment_id', '=', 'attachment.id')->get();
        foreach ($imgList as $item => $value) {
            $img_type_name                   = img_type($value['img_type']);
            $img_name                        = img_name($value['name']);
            $imgList[$item]['img_type_name'] = $img_type_name;
            $imgList[$item]['img_name']      = $img_name;
        }

        //找到该项目对应的账单记录
        $sub_order_info = SubOrderModel::select('sub_order.order_code', 'sub_order.created_at', 'sub_order.project_type','sub_order.cash', 'sub_order.title', 'sub_order.id','sub_order.fund_state','user_detail.nickname','user_detail.work_type')
            ->leftJoin('user_detail','user_detail.uid','=','sub_order.uid')->where('cash', '>', 0)->where('task_id', $id)->get();
        $arr = $data_labor = [];
        if (!empty($confList)) {
            unset($confList['all_parent_price']);
            //去找工人
            $real_id = $this->baseHouseKeeperFindSuperVioson($user_type_user,$task,$id);
            $data_labor = WorkOfferModel::select('work_offer.to_uid', 'work_offer.project_type', 'user_detail.realname')->where('task_id', $real_id)->where('project_type', '>=', 1)
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'work_offer.to_uid')
                ->get();

            foreach ($confList as $key => $value) {

                $arr[$key]['labor_name'] = $arr[$key]['labor_id'] = '';

                foreach ($data_labor as $n => $m) {
                    if ($value['parent_project_type'] == $m['project_type']) {
                        if ($m['project_type'] == 2) {

                            $one_labor               = UserDetailModel::select('realname','work_type')->where('uid', explode('-', $m['to_uid'])[0])->first();
                            if(isset(explode('-', $m['to_uid'])[1])){
                                $other_labor             = UserDetailModel::select('realname','work_type')->where('uid', explode('-', $m['to_uid'])[1])->first();
                            }else{
                                $other_labor = [];
                            }

                            $arr[$key]['labor_name'] = (empty($one_labor) ? '' : $one_labor->realname.'（'.get_work_type_name($one_labor->work_type)) . '）' . (empty($other_labor) ? '' : '和'.$other_labor->realname.'（'.get_work_type_name($other_labor->work_type).'）');
                            $arr[$key]['labor_id']   = $m['to_uid'];
                        } else {
                            $arr[$key]['labor_name'] = $m['realname'];
                            $arr[$key]['labor_id']   = $m['to_uid'];
                        }
                    }
                }
                $arr[$key]['project_type_name'] = $value['parent_name'];
                $arr[$key]['row']               = count($value['childs']) + 1;
                $arr[$key]['child']             = $value['childs'];
                $arr[$key]['id']                = $key;
                $arr[$key]['project_type']      = $value['parent_project_type'];
            }
        }

        //小订单详细

        $user_type        = TaskModel::find($id)->user_type;
        $project_position = TaskModel::find($id)->project_position;
        //监理查看管家
        if ($user_type == 4) {
            $real_id = $this->baseHouseKeeperFindSuperVioson($user_type_user,$task,$id);
//            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $real_id;

        }else{
            $task_id = $id;
        }

        $small_order_info = ProjectSmallOrder::select('id', 'sn', 'small_order_id', 'created_at', 'status', 'project_type', 'desc', 'labor', 'offer_change_price', 'cash_house_keeper','is_confirm')->where('task_id', $task_id)->where('is_confirm', 0)->orderBy('created_at','desc')->get();

        //管家刚提交的整改单

        foreach ($small_order_info as $k => $v) {

            if (!empty($v['labor'])) {
                $labor_deatil             = UserDetailModel::select('avatar', 'uid', 'users.name', 'user_detail.realname', 'user_detail.work_type')->leftJoin('users', 'users.id', '=', 'user_detail.uid')->where('uid', $v['labor'])->first();
                $labor_deatil['realname'] = empty($labor_deatil['realname']) ? $labor_deatil['name'] : $labor_deatil['realname'];
            } else {
                $labor_deatil['realname'] = '';
            }
            $small_order_info[$k]['labor_deatil'] = $labor_deatil['realname'];
            $small_order_info[$k]['project_type'] = get_project_type($v['project_type']);
            $small_order_info[$k]['status'] = small_order_status($v['status']);
            $small_order_info[$k]['total_price']  = $v['offer_change_price'] + $v['cash_house_keeper'];
        }

        //主材选购单
        $principal_material_order = DB::table('principal_material_order')->where('unique_code',$task->unique_code)->orderBy('id','desc')->get();

        $data = [
            'taskDetail' => $taskDetail,
            'deatil' => $arr,
            'sub_order_info' => $sub_order_info,
            'imgList' => $imgList,
            'workers' => $workers,
            'small_order_info' => $small_order_info,
            'principal_material_order' => $principal_material_order,
        ];

        return $this->theme->scope('manage.taskdetail', $data)->render();
    }


    /**
     * 主材单材料列表
     */
    public function getMaterialList($id){
        $data['orderStatus'] = DB::table('principal_material_order')->where('id',$id)->first()->status;
        $data['list'] = DB::table('principal_material_order_detail')->where('order_id',$id)->get();
        echo \GuzzleHttp\json_encode($data);
    }


    /**
     * 业主确认辅材
     */
    public function sureMaterial(Request $request){
        $id = $request->get('listId');
        $ret = DB::table('principal_material_order_detail')->where('id',$id)->update(['receiving_state'=>1]);
        if(!empty($ret)){
            echo \GuzzleHttp\json_encode(['msg'=>'success','code'=>200]);exit;
        }else{
            echo \GuzzleHttp\json_encode(['msg'=>'fail','code'=>400]);exit;
        }
    }

    /**
     * 根据监理订单找管家订单（有管家就一定有监理，同理，有监理就一定有管家）
     * @param $task
     */
    public function baseHouseKeeperFindSuperVioson($user_type_user, $task, $id) {
        if ($user_type_user == 4) {
//            $project_position = $task->project_position;

            if($task->status >= 9){
                if($task->end_order_status == 1){
                    $housekeeper_task = TaskModel::where('unique_code', $task->unique_code)->where('end_order_status', 1)->where('user_type', 3)->first();
                }
            }else{
                $housekeeper_task = TaskModel::where('unique_code', $task->unique_code)->where('status','<', 9)->where('user_type', 3)->first();
            }

            $real_id          = $housekeeper_task->id;
        } else {
            $real_id = $id;
        }

        return $real_id;
    }

    /**
     * 任务详情提交
     * @param Request $request
     */
    public function taskDetailUpdate(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $task_extra = [
            'task_id'=>intval($data['task_id']),
            'seo_title'=>$data['seo_title'],
            'seo_keyword'=>$data['seo_keyword'],
            'seo_content'=>$data['seo_content'],
        ];
        $result = TaskExtraSeoModel::firstOrCreate(['task_id'=>$data['task_id']])
            ->where('task_id',$data['task_id'])
            ->update($task_extra);
        //修改任务数据
        $task = [
            'title'=>$data['title'],
            'desc'=>$data['desc'],
            'phone'=>$data['phone']
        ];
        //修改任务数据
        $task_result = TaskModel::where('id',$data['task_id'])->update($task);

        if(!$result || !$task_result)
        {
            return redirect()->back()->with(['error'=>'更新失败！']);
        }

        return redirect()->back()->with(['massage'=>'更新成功！']);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * 删除任务留言
     */
    public function taskMassageDelete($id)
    {
        $result = WorkCommentModel::destroy($id);

        if(!$result)
        {
            return redirect()->to('/manage/taskList')->with(['error'=>'留言删除失败！']);
        }
        return redirect()->to('/manage/taskList')->with(['massage'=>'留言删除成功！']);
    }

    /**
     * @return mixed
     * 获取辅材包列表
     */
    public function auxManage(){
        $list = Auxiliary::withTrashed()->select('district.name as city_name','auxiliary.*')->leftJoin('district','district.id','=','auxiliary.city_id')->paginate(10);
        $data = array(
            'list' => $list
        );

        return $this->theme->scope('manage.auxiliary.auxiliary', $data)->render();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 编辑
     */
    public function auxAdd(Request $request) {
        $id                  = $request->get('id');
        $data['content']     = $request->get('content');
        $data['price']       = $request->get('aux_price');
        $data['city_id']     = $request->get('serve_city');
        $data['province_id'] = $request->get('serve_province');
        $data['name']        = $request->get('name');
        //新增
        if (empty($id)) {
            $res    = Auxiliary::create($data);
            $aux_id = $res->id;
            $url    = 'api/v4/auxDetail/' . $res->id;
            Auxiliary::where('id', $aux_id)->update(['detail_url' => $url]);
        } else {//编辑
            $data['detail_url'] = 'api/v4/auxDetail/' . $id;
            $res                = Auxiliary::where('id', $id)->update($data);
        }
        if ($res)
            return redirect()->to('/manage/auxManage')->with(['message' => '操作成功']);
        else
            return redirect()->to('/manage/auxManage')->with(['message' => '操作失败']);

    }

    /**
     * 辅材包编辑页面渲染
     */
    public function auxEdit($id = 0) {
//        $id = empty($request->get('id'))?'':$request->get('id');
        $data = [
            'city' => DistrictModel::select('id', 'name')->where('name', 'like', '%' . '市' . '%')->get(),
            'province' => DistrictModel::findTree(0),
            'aux_data' => empty($id) ? '' : Auxiliary::find($id)
        ];
        return $this->theme->scope('manage.auxEdit', $data)->render();
    }

    /**
     * 查看辅材包详细内容
     */
    public function auxDetail($id) {
        $agree = Auxiliary::find($id);
        $data = array(
            'agree' => $agree
        );
        $this->theme->setTitle($agree['name']);
        return $this->theme->scope('bre.agree',$data)->render();
    }

    /**
     * @param Request $request
     * 删除辅材包
     */
    public function auxDelete($id) {
        if(Auxiliary::destroy($id)){
            return back()->with(['message' => '删除成功']);
        }else{
            return back()->with(['message' => '删除失败']);
        }
    }

    /**
     * @param Request $request
     * 恢复辅材包
     */
    public function auxRestore($id) {
        if (Auxiliary::where('id', $id)->restore()) {
            return back()->with(['message' => '恢复成功']);
        } else {
            return back()->with(['message' => '恢复失败']);
        }
    }
}
