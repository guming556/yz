<?php
namespace App\Respositories;

use App\Modules\Order\Model\OrderModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Task\Model\workDesignerLog;
use App\PushServiceModel;
use DB;
use App\Modules\Task\Model\WorkOfferModel;

use App\Respositories\TaskRespository;
use App\Respositories\ChatRoomRespository;
use App\Respositories\TaskOperaRespository;
use App\Respositories\UserRespository;



class TaskAppointRespository {
    
    protected $taskRespository;
    protected $chatRoomRespository;
    protected $taskOperaRespository;
    protected $userRespository;

    public function __construct(TaskRespository $taskRespository, ChatRoomRespository $chatRoomRespository,TaskOperaRespository $taskOperaRespository,UserRespository $userRespository) {
        $this->taskRespository     = $taskRespository;
        $this->chatRoomRespository = $chatRoomRespository;
        $this->taskOperaRespository = $taskOperaRespository;
        $this->userRespository      = $userRespository;
    }

    /**
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 创建约单订单
     */
    public function createAppoint(array $data, $data_designer_uid) {
		
        $not_pay_task = TaskModel::where('project_position', $data['project_position'])
            ->where('user_type', $data['user_type'])
            ->where('bounty_status', '=', 0)
            ->where('task.status', '<=', 3)->get();

        //循环删除没付款的订单
        if (!empty($not_pay_task)) {
            foreach ($not_pay_task as $k => $v) {
                $res          = TaskModel::find($v->id)->delete();
                $res_work     = WorkModel::where('task_id', $v->id)->delete();
                $res_work_log = workDesignerLog::where('task_id', $v->id)->delete();//log表删除
            }
        }
        //project_position是否在进行中了
        $count_project_position = TaskModel::where('project_position', $data['project_position'])->where('user_type', $data['user_type'])->where('status', '>=', 3)->where('status', '<', 9)->count();

        if ($count_project_position)
            return ['able' => false, 'errMsg' => '该地址已存在进行中的任务'];


        //无论是设计师还是管家,建立,手续费都是统一的
        $data['product'][] = ServiceModel::select('id')->where('identify', 'SHOUXUFEI')->first()->id;//手续费
        $project_detail    = ProjectPositionModel::where('id', $data['project_position'])->first();
        if (empty($project_detail)) return ['able' => false, 'errMsg' => '找不到该地址'];
        $data['square'] = $project_detail->square; //房屋面积

        // TODO 这里从配置找可以中标的数量设置
        $data['worker_num'] = 3;


        // TODO 也可使用增值服务来代替手续费，后期考虑
        //  TODO 这里要判断的是可用余额
        $data['title'] = ProjectPositionModel::where('id', $data['project_position'])->first()->project_position;
        //约单人数限制
        $data_designer['uid'] = $data_designer_uid;//可能为多个
        if (count($data_designer['uid']) > 3)
            return ['able' => false, 'errMsg' => '约谈人数超过三个'];

        $result = TaskModel::createTask($data);

        if ($result) {

            $data_designer['desc']       = '';
            $data_designer['task_id']    = $result->id;
            $data_log['task_id']         = $result->id;
            $data_designer['created_at'] = date('Y-m-d H:i:s', time());

            //循环创建
            foreach ($data_designer['uid'] as $n => $m) {
                $is_work_able         = $this->isWorkAbleAppoint($data_designer['task_id'], $m);
                $data_designer['uid'] = $m;
                $data_log['new_uid']  = $m;
                //返回为何不能投标的原因
                if (!$is_work_able['able']) {
                    return ['able' => false,'error' => $is_work_able['errMsg']];
                }

                //work表插入数据
                $result_designer     = WorkModel::create($data_designer);
                $result_designer_log = workDesignerLog::create($data_log);
            }
            if ($result_designer && $result_designer_log) {
                return ['able' => true,'successMsg' => $result->id];
            }
             return ['able' => false,'errMsg' => '系统错误'];
        }
    }



    /**
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 创建约单订单(方案b)
     */
    public function createAppointPlanB(array $data, $data_designer_uid) {
		
		
        $not_pay_task = TaskModel::where('project_position', $data['project_position'])
            ->where('user_type', $data['user_type'])
            ->where('bounty_status', '=', 0)
            ->where('task.status', '<=', 3)->get();

        //循环删除没付款的订单
        if (!empty($not_pay_task)) {
            foreach ($not_pay_task as $k => $v) {
                $res          = TaskModel::find($v->id)->delete();
                $res_work     = WorkModel::where('task_id', $v->id)->delete();
                $res_work_log = workDesignerLog::where('task_id', $v->id)->delete();//log表删除
            }
        }
        //project_position是否在进行中了
        $count_project_position = TaskModel::where('project_position', $data['project_position'])->where('user_type', $data['user_type'])->where('status', '>=', 3)->where('status', '<', 9)->count();
        if ($count_project_position)
            return ['able' => false, 'errMsg' => '该地址已存在进行中的任务'];

        //无论是设计师还是管家,建立,手续费都是统一的
        $data['product'][] = ServiceModel::select('id')->where('identify', 'SHOUXUFEI')->first()->id;//手续费
        $project_detail    = ProjectPositionModel::where('id', $data['project_position'])->first();
        if (empty($project_detail)) return ['able' => false, 'errMsg' => '找不到该地址'];
        $data['square'] = $project_detail->square; //房屋面积

        // TODO 这里从配置找可以中标的数量设置
        $data['worker_num'] = 1;//这里只能约一个人

        $data['title'] = ProjectPositionModel::where('id', $data['project_position'])->first()->project_position;
        //约单人数限制
        $data_designer['uid'] = $data_designer_uid;//可能为多个

        // 找出该工地的统一编码
        $inHand = TaskModel::where('project_position',$data['project_position'])->where('status','<',9)->first();
        if(!empty($inHand)){
            $data['unique_code'] = $inHand->unique_code;
        }else{
            $data['unique_code'] = time();
        }
        $result = TaskModel::createTask($data);

        if ($result) {

            //创建订单之后直接付费成功
            $order['uid']     = $data['uid'];
            $order['task_id'] = $result['id'];

            $poundage = 0;//预约金变0

            //work和workoffer插入数据
            $data_designer['desc']       = '';
            $data_designer['task_id']    = $result['id'];
            $data_log['task_id']         = $result['id'];
            $data_designer['created_at'] = date('Y-m-d H:i:s', time());

            //循环创建

            $is_work_able        = $this->isWorkAbleAppoint($data_designer['task_id'], $data_designer['uid']);
            $data_log['new_uid'] = $data_designer['uid'];
            //返回为何不能投标的原因
            if (!$is_work_able['able']) {
                return ['able' => false, 'error' => $is_work_able['errMsg']];
            }
            //work表插入数据
            $status = DB::transaction(function () use ($data_designer, $data_log, $order, $poundage) {
                UserDetailModel::where('uid', $data_designer['uid'])->increment('receive_task_num', 1);
                WorkModel::create($data_designer);
                workDesignerLog::create($data_log);
                OrderModel::bountyOrder($order['uid'], $poundage, $order['task_id'], '任务款');//创建订单
            });

            if (is_null($status)) {
                return ['able' => true, 'successMsg' => $result->id];
            }
            return ['able' => false, 'errMsg' => '系统错误'];
        }
        else
        return ['able' => false, 'errMsg' => '传入数据无法找到,系统错误'];
    }


    /**
     * @param $task_id
     * @param $uid
     * @return array
     * 返回约单任务判断
     */
    private function isWorkAbleAppoint($task_id, $uid) {

        //如果是设计师角色，判断是否设定了单价
        $user = UserModel::where('users.id', $uid)->leftjoin('user_detail', 'user_detail.uid', '=', 'users.id')->first();
        if (empty($user)) {
            return ['able' => false, 'errMsg' => '找不到该设计师！'];
        }
        if ($user->user_type == 2) {
            if ($user->cost_of_design <= 0) {
                return ['able' => false, 'errMsg' => '设计师还没设定单价，请到个人中心设置'];
            }
        }

        //判断用户是否为当前任务的投稿人，如果已经是的，就不能投稿
        if (WorkModel::isWorker($uid, $task_id)) {
            return ['able' => false, 'errMsg' => '该设计师已经抢过该单了'];
        }
        // 判断当前用户是否为任务的发布者，如果是用户的发布者，就不能投稿
        if (TaskModel::isEmployer($task_id, $uid)) {
            return ['able' => false, 'errMsg' => '你是任务发布者不能投稿！'];
        }
        return ['able' => true];
    }


    /**
     * @param $task_id
     * @param $user_id
     * @param $refuse_id
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 确认或者拒绝此次的约单任务
     */

    public function ReplyBoss($task_id, $user_id, $refuse_id) {
        $task_info = TaskModel::find($task_id);

        if (empty($task_info))
            return ['able' => false, 'errMsg' => '找不到该任务！'];
        if ($task_info->status >= 9)
            return ['able' => false, 'errMsg' => '业主已取消订单！'];

        $boss_uid        = $task_info->uid;
        $res_change_work = workDesignerLog::where('task_id', $task_id)->where('new_uid', $user_id)->first();

        if (empty($res_change_work))
            return ['able' => false, 'errMsg' => '无法找到该任务！'];

        if ($res_change_work->is_refuse == 1 || $res_change_work->is_refuse == 2)
            return ['able' => false, 'errMsg' => '您已经接受或拒绝过该任务！'];
        //找到类型
        $data['user_type'] = UserModel::find($user_id)->user_type;
        //设计师拒绝,直接结束订单
        if ($refuse_id == 2) {
            $status_cancel_order = $task_info->status . '-' . '0' . '-' . '0';
            TaskModel::where('id', $task_id)->update(['status' => 9, 'cancel_order' => $status_cancel_order]);

            //拒绝
            switch ($data['user_type']) {
                case 2: $application = 40004; break;
                case 3: $application = 40005; break;
                case 4: $application = 40006; break;
                default: $application = 40004;
            }

        } else {
            $task_info->status = 4;
            $task_info->save();
            //接受
            switch ($data['user_type']) {
                case 2: $application = 40001; break;
                case 3: $application = 40002; break;
                case 4: $application = 40003; break;
                default: $application = 40001;
            }
        }
        $res_change_work->is_refuse = $refuse_id;
        if ($res_change_work->save()) {

            if($refuse_id == 2){
                push_accord_by_equip($boss_uid, $application, 'message_reply_order', '', $task_info->id);
                return ['able' => true, 'errMsg' => '操作成功'];
            }else {
                $project_info = ProjectPositionModel::find($task_info->project_position);
                if (empty($project_info)) return ['able' => false, 'errMsg' => '找不到工地'];
                $chat_room_id = (int)$project_info->chat_room_id;
                $userInfo     = UserModel::find($user_id);
                if (empty($userInfo)) return ['able' => false, 'errMsg' => '找不到预约的工作者'];
                $user_name     = $userInfo->name;
                $res_chat_room = $this->chatRoomRespository->addWorkToChatRoom($chat_room_id, $user_name);
                if (empty($res_chat_room)) {
                    return response()->json(['error' => '操作失败'], '500');
                }
                $is_accet = WorkModel::where('task_id', $task_id)->where('uid', $user_id)->where('status', 1)->first();
                if ($is_accet) {
                    return ['able' => false, 'errMsg' => '该任务已确认工作人选'];
                }

                $work = WorkModel::where('task_id', $task_id)->where('uid', $user_id)->first();

                if (!empty($work)) {
                    TaskModel::where('id', $task_id)->update(['status' => 6]);
                    $status = WorkModel::where('task_id', $task_id)->where('uid', $user_id)->update(['status' => 1]);

                    push_accord_by_equip($user_id, 50002, 'message_interview_worker', '', $task_id);
                    if ($task_info->user_type == 2) {
                        $status_log = workDesignerLog::where('task_id', $task_id)->where('new_uid', $user_id)->update(['boss_confirm' => 1]);
                    }
                } else {
                    return ['able' => false, 'errMsg' => '操作失败'];
                }

                $data['title'] = '工作者报价流程';
                $data['sn']    = '0';

                if ($task_info->user_type == 2) {
                    $data['type'] = 'designer';
                }
                if ($task_info->user_type == 3) {
                    $data['type'] = 'housekeeper';
                }
                if ($task_info->user_type == 4) {
                    $data['type'] = 'overseer';
                }


                $data['task_id'] = $task_id;    //任务id
                $data['percent'] = json_encode(array('0.2', '0.4', '0.4'));

                $data['price']    = '0';
                $data['from_uid'] = $boss_uid;
                $data['to_uid']   = $user_id;
                $data['status']   = '0';
                $data['work_id']  = $work['id'];

                $status = WorkOfferModel::create($data);

                if ($status) {
                    return ['able' => true, 'errMsg' => '确认约谈人选成功'];
                }
                return ['able' => false, 'errMsg' => '操作失败'];

            }

         }else {
            return ['able' => false, 'errMsg' => '操作失败'];
         }

    }


    /**
     * 业主在没确认之前更换约单工作者
     */
    public function changeWorker($chang_uid, $task_id, $origin_id) {

        $chang_uid_deatil = UserModel::find($chang_uid);
        if (empty($chang_uid_deatil) || $chang_uid_deatil->user_type !== 2)
            return ['able' => false, 'errMsg' => '找不到该用户或更改的用户不是设计师！'];

        //先去work表业主第一次选了几个人
        $count_work = WorkModel::where('task_id', $task_id)->count();
        if (empty($count_work))
            return ['able' => false, 'errMsg' => '非法操作！'];


        //log表加记录
        $data_log['new_uid'] = $chang_uid;
        $data_log['task_id'] = $task_id;
        //新加入设计师


        if (empty($origin_id) && $count_work < 3) {
            $data_designer['desc']       = '';
            $data_designer['uid']        = $chang_uid;
            $data_designer['task_id']    = $task_id;
            $data_designer['created_at'] = date('Y-m-d H:i:s', time());

            //work表插入数据
            $result_designer     = WorkModel::create($data_designer);
            $result_designer_log = workDesignerLog::create($data_log);
            if ($result_designer && $result_designer_log) return response()->json(['msg' => '更改成功']);
        } else {

            if ($chang_uid == $origin_id)       return ['able' => false, 'errMsg' => '您选了相同的设计师,请核对'];
            //修改原来的人work_log
            $chang_work_log = workDesignerLog::where('task_id', $task_id)->where('new_uid', $origin_id)->where('is_refuse', 2)->first();
            //修改原来的人work
            $chang_work = WorkModel::where('uid', $origin_id)->where('task_id', $task_id)->where('status', 0)->first();

            if (empty($chang_work))
                return ['able' => false, 'errMsg' => '该项目原设计师未找到'];

            //这里要判断下更换的人是否拒绝过这个订单

            //如果原设计师已接受这个订单,不可更改
            $seek_designer_status = workDesignerLog::where('new_uid', $origin_id)->where('is_refuse', 1)->first();

            if (!empty($seek_designer_status))
                return ['able' => false, 'errMsg' => '该设计师已确认接单,不可更改'];


            //如果原用户拒绝过这个订单,就新增记录
            if (!empty($chang_work_log)) {
                $res_change_work_log = workDesignerLog::create($data_log);
            } else {//否则就直接换人
                $chang_work_log_other          = workDesignerLog::where('task_id', $task_id)->where('new_uid', $origin_id)->first();
                $chang_work_log_other->new_uid = $chang_uid;
                $chang_work_log_other->old_uid = $origin_id;
                $res_change_work_log           = $chang_work_log_other->save();
            }

            $chang_work->uid = $chang_uid;
            $res_change_work = $chang_work->save();

            if ($res_change_work && $res_change_work_log) {
                return ['able' => true, 'errMsg' => 'success'];
            } else {
                return ['able' => false, 'errMsg' => 'error'];
            }


        }
    }
}
