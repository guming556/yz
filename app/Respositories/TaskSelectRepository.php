<?php

namespace App\Respositories;

use App\Modules\Manage\Model\LevelModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\User\Model\UserFocusModel;
use DB;
class TaskSelectRepository {


    /**
     * @param $list
     * @return mixed
     * 根据list添加其他内容
     */
    public function addStatus($list) {

        foreach (($list) as $key => $value) {

            $value['avatar'] = url($value['avatar']);

            if ($value['status'] == 7) {

                $change_task_id = '';
                //监理订单类型
                //2：设计师 3：管家 4：监理
                if ($value['user_type'] == 4) {

                    $project_position        = $value['project_position_id'];
                    $house_keeper_task       = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();//发了管家的单
                    $house_keeper_work       = WorkModel::where('task_id', $house_keeper_task->id)->first();//有管家抢单
                    $house_keeper_work_payed = WorkModel::where('task_id', $house_keeper_task->id)->where('status', 2)->first();//已支付管家
                    $jianli_work             = WorkModel::where('task_id', $value['task_id'])->where('status', 2)->first();

                    if (!empty($house_keeper_task) && !empty($house_keeper_work) && !empty($house_keeper_work_payed) && $jianli_work) {
                        $change_task_id = $house_keeper_task->id;
                    } else {
                        $change_task_id = $value['task_id'];
                    }
                } else {
                    $change_task_id = $value['task_id'];
                }

                $work_offer_status = WorkOfferModel::select('status', 'sn', 'count_submit', 'updated_at as task_status_time', 'project_type')->where('task_id', $change_task_id)->orderBy('sn', 'ASC')->get()->toArray();


                $is_have_dismantle = 0;
                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                    //是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }
                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'is_have_dismantle' => 0];
                } else {
                    $last_work_offer_status = $work_offer_status[count($work_offer_status) - 1];
                }
                $list[$key]['node']              = $value['status'];
                $list[$key]['sn']                = $last_work_offer_status['sn'];
                $list[$key]['status']            = $last_work_offer_status['status'];
                $list[$key]['count_submit']      = $last_work_offer_status['count_submit'];
                $list[$key]['is_have_dismantle'] = $is_have_dismantle;
                $list[$key]['task_status_time']  = date('m-d H:i', strtotime($last_work_offer_status['task_status_time']));

            } else {

                $list[$key]['node']              = $value['status'];
                $list[$key]['sn']                = 0;
                $list[$key]['status']            = 0;
                $list[$key]['count_submit']      = 0;
                $list[$key]['is_have_dismantle'] = 0;
                $list[$key]['task_status_time']  = '0000-00-00 00:00:00';
            }
        }


        return $list;
    }


    /**
     * @param $task_id
     * @return mixed
     * 根据task_id找到对应的订单信息
     */
    public function selectDbInfo($task_id) {
        $task_info = TaskModel::select('task.status', 'task.created_at', 'task.type_id as type_model', 'ud.avatar', 'ud.nickname', 'task.id as task_id', 'task.square', 'task.room_config', 'c.name as favourite_style', 'task.show_cash', 'p.lat', 'p.lng', 'p.project_position', 'p.region', 'task.view_count', 'task.project_position as project_position_id', 'task.user_type', 'us.name as boss_mobile')
            ->join('task_type as b', 'task.type_id', '=', 'b.id')
            ->leftjoin('users as us', 'us.id', '=', 'task.uid')
            ->leftjoin('user_detail as ud', 'us.id', '=', 'ud.uid')
            ->leftjoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftjoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->where('task.status', '>', 2)
            ->where('task.status', '<=', 9)
            ->where('task.bounty_status', 1)
            ->where('task.id', $task_id)->orderBy('task.created_at', 'desc')->first();

        return $task_info;
    }


    public function selectDbInfoByTaskId($taskIdArr) {
        $task_info = TaskModel::select('task.status', 'task.created_at', 'task.type_id as type_model', 'ud.avatar', 'ud.nickname', 'task.id as task_id', 'task.square', 'task.room_config', 'c.name as favourite_style', 'task.show_cash', 'p.lat', 'p.lng', 'p.project_position', 'p.region', 'task.view_count', 'task.project_position as project_position_id', 'task.user_type', 'us.name as boss_mobile')
            ->whereIn('task.id', $taskIdArr)
            ->where('task.bounty_status', 1)
            ->whereBetween('task.status', [3 , 9])
            ->join('project_position as p', 'p.id', '=', 'task.project_position')
            ->join('users as us', 'us.id', '=', 'task.uid')
            ->join('user_detail as ud', 'us.id', '=', 'ud.uid')
            ->leftjoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->orderBy('task.created_at', 'desc')->get()->toArray();

        return $task_info;
    }

    /**
     * @param $uid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 获取可以抢单的任务
     */
    public function getTasks($uid) {
        if (empty(UserModel::find($uid))) return ['status' => false, 'errMsg' => '获取不到用户信息'];
        $user_type              = UserModel::find($uid)->user_type;//看类型
        $list                   = TaskModel::appFindBy(['user_type' => $user_type, 'type_id' => 1]);//返回列表
        $count_house_keep_robot = WorkModel::where('uid', $uid)->where('status', '>', 0)->where('status', '<', 3)->count();//查找管家已接单的任务

        //抢单管家当前进行中的任务数量是否超过6个，超出不可抢（接）
        if ($user_type == 3 && $count_house_keep_robot > 6) {
            return ['status' => false, 'errMsg' => '当前进行中的任务超过6个'];
        }

        //抢单监理当前进行中的任务数量是否超过15个，超出不可抢（接）
        if ($user_type == 4 && $count_house_keep_robot > 15) {
            return ['status' => false, 'errMsg' => '当前进行中的任务超过15个'];
        }
        //如果管家是1星的,不能看到业主要要求的2星及以上的订单
        if ($user_type == 3) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取管家星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return ['status' => true, 'successMsg' => []];
                    return response()->json([]);
                }
            }
        }

        //如果监理是1星的,不能看到业主要要求的2星及以上的订单
        if ($user_type == 4) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取监理星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return ['status' => true, 'successMsg' => []];
                }
            }
        }
        foreach ($list->toArray() as $key => $value) {

            $work_count = DB::table('work')->where('task_id', $value['task_id'])->count();
            $work       = WorkModel::select('task_id', 'status', 'uid')->where('uid', $uid)->where('task_id', $value['task_id'])->first();
            //是否有业主选定了
            if (empty($work) && ($work_count !== 3)) {
                $list[$key]['is_owner'] = 0;
            } else {
                $list[$key]['is_owner'] = 1;
            }

            $value['avatar'] = url($value['avatar']);
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::where('task_id', $value['task_id'])->orderBy('sn', 'ASC')->get()->toArray();
                //返回work_offer表最新的状态
                foreach ($work_offer_status as $key2 => $value2) {
                    if ($key2 == count($work_offer_status) - 1) {
                        $list[$key]['node']   = $value['status'];
                        $list[$key]['sn']     = $value2['sn'];
                        $list[$key]['status'] = $value2['status'];
                        break;
                    }

                    if ($value['status'] != 4) {
                        $list[$key]['node']   = $value['status'];
                        $list[$key]['sn']     = $value2['sn'];
                        $list[$key]['status'] = $value2['status'];
                        break;
                    }
                }

            } else {
                $list[$key]['node']   = $value['status'];
                $list[$key]['sn']     = 0;
                $list[$key]['status'] = 0;

            }

        }
        //有业主选定人了或业主取消了订单了
        foreach ($list as $o => $p) {
            if ($p['is_owner'] == 1 || $p['node'] >= 7) {
                unset($list[$o]);
            }
        }
        //重建索引
        $list_new = array_values($list->toArray());
        //循环修改为null的值
        foreach ($list_new as $item => $value) {
            foreach ($value as $n => $m) {
                if ($m === null) {
                    $list_new[$item][$n] = '';
                }
            }
        }
        return ['status' => true, 'successMsg' => $list_new];
    }

}