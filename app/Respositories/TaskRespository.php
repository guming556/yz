<?php

namespace App\Respositories;

use App\Modules\Manage\Model\LevelModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\User\Model\UserFocusModel;
use DB;


class TaskRespository{

    /**
     * @param $task_id
     * 根据task_id获取设计师的订单详细
     */
    public function getDesignerTaskDetail($task_id) {
        $left_days = '';//剩余多少天
        //手续费，到系统配置里面找
        $poundage_service_price = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;

        $room_config_arr = array('bedroom' => '房', 'living_room' => '厅', 'kitchen' => '厨', 'washroom' => '卫', 'balcony' => '阳台');

        $tasks = TaskModel::select(
            'task.uid', 'task.cancel_order','task.end_order_status', 'task.type_id as type_model',
            'p.room_config', 'task.created_at', 'p.square', 'task.status',
            'task.project_position as project_position_id', 'c.name as favourite_style',
            'task.id as task_id', 'task.user_type', 'task.view_count',
            'task.show_cash', 'p.region', 'p.project_position',
            'users.name', 'user_detail.mobile', 'user_detail.avatar as boss_avatar',
            'user_detail.nickname as boss_nike_name'
        )->where('task.id', $task_id)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('users', 'users.id', '=', 'task.uid')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->distinct('task.id')
            ->get();
//        return $tasks;
            $task_data = $tasks[0];
        foreach ($tasks as $key => &$value) {
            $str                  = '';
            $task_data['created_at'] = date('Y-m-d', strtotime($value['created_at']));
            $task_data['real_mobile'] = !empty($value['mobile']) ? $value['mobile'] : $value['name'];
            $task_data['boss_avatar'] = !empty($value['boss_avatar']) ? url($value['boss_avatar']) : '';
            $task_data['boss_nike_name'] = !empty($value['boss_nike_name']) ? $value['boss_nike_name'] : '';
            unset($task_data['mobile'], $task_data['name']);
            $room_config_decode = json_decode($value['room_config']);
            foreach ($room_config_decode as $key2 => $value2) {
                if (isset($room_config_arr[$key2])) {
                    $str .= $value2 . $room_config_arr[$key2];
                }
            }

            $task_data['room_config'] = $str;
            //定金抵扣显示
            $task_data['poundage_service_price'] = (int)$poundage_service_price;
            //如果是设计师的单子,要连表
            if ($value['type_model'] == 2) {
                $workers = WorkModel::select(
                    'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                    'user_detail.mobile', 'user_detail.city as address', 'work.status',
                    'user_detail.cost_of_design', 'work.price', 'work.actual_square', 'work_designer_logs.is_refuse'
                )->where('work.task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
                    ->where('work_designer_logs.task_id', $value['task_id'])
                    ->distinct('work.uid')
                    ->get()->toArray();
            } else {
                $workers = WorkModel::select(
                    'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                    'user_detail.mobile', 'user_detail.city as address', 'work.status',
                    'user_detail.cost_of_design', 'work.price', 'work.actual_square', 'work.is_refuse'
                )->where('task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->get()->toArray();
            }
            $work_data = WorkModel::where('task_id', $value['task_id'])->where('status', '>=', 1)->first();
            $task_data['actual_pay']  = 0;
            $task_data['total_price'] = 0;
            if (!empty($work_data)) {
                $task_data['actual_pay']  = $work_data->price - (int)$poundage_service_price;
                $task_data['total_price'] = doubleval($work_data->price);
            }
            //2：设计师 3：管家 4：监理
            $data_id['house_keeper_task_id'] = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 3)->get();
            $data_id['supervisor_task_id']   = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 4)->get();
            $data_id['designer_task_id']     = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 2)->get();
            $employ = [];
            foreach ($data_id as $n => $m) {
                if (empty($m)) {
                    unset($data_id[$n]);
                }
                foreach ($m as $o => $p) {
                    $employ[$n] = WorkModel::select(
                        'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                        'user_detail.mobile', 'user_detail.city as address'
                    )->where('task_id', $p['id'])
//                        ->where('status', '>', 0)
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                        ->get()->toArray();
//                    var_dump($employ);exit();
                    if (!empty($employ[$n])) {
                        $employ[$n][0]['identify'] = $p['user_type'];
                    }
                    $employ_iden = $employ;
                    unset($employ_iden[$n][0]['identify']);
                }
            }
            if (!empty($employ_iden)) {
                foreach ($employ_iden as $n => $m) {
                    if (!empty($m[0])) {
                        foreach ($m as $o => $p) {
                            $employ[$n][$o]['avatar'] = !empty($p['avatar']) ? url($p['avatar']) : '';
                            $employ[$n][$o]['mobile'] = !empty($p['mobile']) ? $p['mobile'] :  UserModel::find($p['uid'])->name;
                        }
                    }
                }
            }
//            $task_data['employs'] = empty($employ['designer_task_id']) && empty($employ['house_keeper_task_id']) && empty($employ['supervisor_task_id']) ? (object)[] :$employ;
//            foreach ($task_data['employs'] as $e => $t) {
//                if (empty($t)) {
//                    unset($task_data['employs'][$e]);
////                    unset($task_data['employs']->$e);
//                }
//            }
            $task_data['employs'] = empty($employ['designer_task_id']) && empty($employ['house_keeper_task_id']) && empty($employ['supervisor_task_id']) ? (object)[] : (object)$employ;
            foreach ($task_data['employs'] as $e => $t) {
                if (empty($t)) {
//                    $value_temp = $task_data['employs'][$e];
                    unset($task_data['employs']->$e);
                }
            }
            $task_obj = $task_data['employs'];
            $task_data['employs'] = (array)$task_obj;
//            var_dump($task_data['employs']);exit();
            //$refuse_id = 0;
            foreach ($workers as $key3 => $value3) {
                $serve_area                = RealnameAuthModel::where('uid', $value3['uid'])->first()->serve_area;
                $workers[$key3]['avatar']  = !empty($value3['avatar']) ? url($value3['avatar']) : '';
                $workers[$key3]['mobile']  = !empty($value3['mobile']) ? $value3['mobile'] : UserModel::find($value3['uid'])->name;
                $task_data['actual_square']    = $value3['actual_square'];
                $workers[$key3]['address'] = empty($serve_area) ? '' : $serve_area;//服务区域
                $task_data['address']          = $value3['address'];
                $task_data['cost_of_design']   = $value3['cost_of_design'];
                $task_data['refuse_id'] = $value3['is_refuse'];
            }
            $task_data['workers'] = $workers;
            unset($value['uid']);
            $task_data['node_android']             = 0;
            $task_data['sn_android']               = 0;
            $task_data['status_android']           = 0;
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $value['task_id'])
                    ->orderBy('sn', 'ASC')
                    ->get()->toArray();
//                return $work_offer_status;
                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                    //给第一次报价的钱
                    if ($m['sn'] == 0) {
                        $task_data['total_price'] = doubleval($m['price']);
                    }
                }
                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'task_id' => $value['task_id'], 'project_type' => 0];
                } else {
                    $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                }
                $task_data['node']             = $value['status'];
                $task_data['sn']               = $last_work_offer_status['sn'];
                $task_data['status']           = floatval($last_work_offer_status['status']);
                $task_data['count_submit']     = $last_work_offer_status['count_submit'];
                $task_data['task_status_time'] = date('m-d H:i', strtotime($last_work_offer_status['task_status_time']));
                $task_data['cancel_order_android'] = '0';
                //查看剩余多少天
                $end_data  = TaskModel::find($task_id)->end_at;
                $left_days = empty($end_data) ? 0 : ceil((strtotime($end_data) - time()) / (3600 * 24));

            } else {
                $rob_time_offer = WorkOfferModel::where('task_id', $value['task_id'])->where('sn', 0)->first();
                $rob_time_work  = WorkModel::where('task_id', $value['task_id'])->first();

                if (!empty($rob_time_offer)) {
                    $rob_time = $rob_time_offer->created_at->format('m-d H:i:s');
                } elseif (!empty($rob_time_work)) {
                    $rob_time = date('m-d H:i', strtotime($rob_time_work->created_at));
                } else {
                    $rob_time = TaskModel::find($task_id)['created_at']->format('m-d H:i:s');
                }
                $task_data['cancel_order'] = '0';
                if (empty($value['cancel_order'])) {
                    $task_data['cancel_order_android'] = '0';
                    //$task_data['cancel_order'] = ['cancel_order_node' => 0, 'cancel_order_sn' => 0, 'cancel_order_status' => 0];
                } else {
                    $task_data['cancel_order_android'] = intval(!$value['end_order_status']);
                    $cancel_order_node           = explode('-', $value['cancel_order']);
                    //$task_data['cancel_order'] = ['cancel_order_node' => $cancel_order_node[0], 'cancel_order_sn' => $cancel_order_node[1], 'cancel_order_status' => $cancel_order_node[2]];

                    $task_data['node_android']             = intval($cancel_order_node[0]);
                    $task_data['sn_android']               = intval($cancel_order_node[1]);
                    $task_data['status_android']           = $cancel_order_node[2];

                }

                $task_data['node']             = $value['status'];
                $task_data['sn']               = 0;
                $task_data['status']           = 0;
                $task_data['count_submit']     = 0;
                $task_data['task_status_time'] = $rob_time;
            }
            $task_data['left_days'] = intval($left_days);

        }
        //筛选出锁定人员
            foreach ($task_data['workers'] as $n => $m) {
                if ($m['status'] > 0) {

                    $task_data['lock_uid'] = $m['uid'];
                }
            }

        return $task_data;
    }



    /**
     * @param $task_id
     * 根据task_id获取管家的订单详细
     */
    public function getHousekeeperTaskDetail($task_id) {
        $last_amendment_sheet_status = 0;//客诉中心状态
        $last_delay_date_status      = 0;//延期单状态
        $is_have_dismantle           = 0;//判断是否有拆除
        $left_days                   = '';//剩余多少天
        $work_offer_id               = 0;
        //手续费，到系统配置里面找
        $poundage_service_price = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;

        $room_config_arr = array('bedroom' => '房', 'living_room' => '厅', 'kitchen' => '厨', 'washroom' => '卫', 'balcony' => '阳台');

        $tasks = TaskModel::select(
            'task.uid', 'task.cancel_order', 'task.end_order_status', 'task.type_id as type_model',
            'p.room_config', 'task.created_at', 'p.square', 'task.status',
            'task.project_position as project_position_id', 'c.name as favourite_style',
            'task.id as task_id', 'task.user_type', 'task.view_count',
            'task.show_cash', 'p.region', 'p.project_position',
            'users.name', 'user_detail.mobile', 'user_detail.avatar as boss_avatar',
            'user_detail.nickname as boss_nike_name'
        )->where('task.id', $task_id)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('users', 'users.id', '=', 'task.uid')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->distinct('task.id')
            ->get()->toArray();
//        var_dump($tasks);exit();
        $task_data = $tasks[0];
        foreach ($tasks as $key => &$value) {
            $str                      = '';
            $task_data['created_at'] = date('Y-m-d', strtotime($value['created_at']));
            $task_data['real_mobile'] = !empty($value['mobile']) ? $value['mobile'] : $value['name'];
            $task_data['favourite_style'] = empty($value['favourite_style']) ? '' : $value['favourite_style'];
            $task_data['boss_avatar'] = !empty($value['boss_avatar']) ? url($value['boss_avatar']) : '';
            $task_data['boss_nike_name'] = !empty($value['boss_nike_name']) ? $value['boss_nike_name'] : '';
            unset($task_data['mobile'], $task_data['name']);
            $room_config_decode = json_decode($value['room_config']);

            foreach ($room_config_decode as $key2 => $value2) {
                if (isset($room_config_arr[$key2])) {
                    $str .= $value2 . $room_config_arr[$key2];
                }
            }

            $task_data['room_config'] = $str;
            //定金抵扣显示
            $task_data['poundage_service_price'] = (int)$poundage_service_price;

            //如果是设计师的单子,要连表
            if ($value['type_model'] == 2) {
                $workers = WorkModel::select(
                    'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                    'user_detail.mobile', 'user_detail.city as address', 'work.status',
                    'user_detail.cost_of_design', 'work.price', 'work.actual_square', 'work_designer_logs.is_refuse'
                )->where('work.task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
                    ->where('work_designer_logs.task_id', $value['task_id'])
                    ->distinct('work.uid')
                    ->get()->toArray();
            } else {
                $workers = WorkModel::select(
                    'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                    'user_detail.mobile', 'user_detail.city as address', 'work.status',
                    'user_detail.cost_of_design', 'work.price', 'work.actual_square', 'work.is_refuse'
                )->where('task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->get()->toArray();
            }

            $work_data = WorkModel::where('task_id', $value['task_id'])->where('status', '>=', 1)->first();

            $task_data['actual_pay']  = 0;
            $task_data['total_price'] = 0;
            if (!empty($work_data)) {
                $task_data['actual_pay']  = $work_data->price - (int)$poundage_service_price;
                $task_data['total_price'] = doubleval($work_data->price);
            }
            //2：设计师 3：管家 4：监理
            $data_id['house_keeper_task_id'] = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 3)->get();
            $data_id['supervisor_task_id']   = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 4)->get();
            $data_id['designer_task_id']     = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 2)->get();
//            var_dump($data_id);exit();
            $employ = [];
            foreach ($data_id as $n => $m) {
                if (empty($m)) {
                    unset($data_id[$n]);
                }
                foreach ($m as $o => $p) {
                    $employ[$n] = WorkModel::select(
                        'user_detail.uid',
                        'user_detail.nickname',
                        'user_detail.avatar',
                        'user_detail.mobile',
                        'user_detail.city as address'
                    )->where('task_id', $p['id'])
//                        ->where('status', '>', 0)
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                        ->get()
                        ->toArray();

                    $employ[$n][0]['identify'] = $p['user_type'];
                    $employ_iden = $employ;
                    unset($employ_iden[$n][0]['identify']);
                }
            }
            if (!empty($employ_iden)) {
                foreach ($employ_iden as $n => $m) {
                    if (!empty($m[0])){
                        foreach ($m as $o => $p) {
                            $employ[$n][$o]['avatar'] = !empty($p['avatar']) ? url($p['avatar']) : '';
                            $employ[$n][$o]['mobile'] = !empty($p['mobile']) ? $p['mobile'] : UserModel::find($p['uid'])->name;
                        }
                    }
                }
            }
            $task_data['employs'] = empty($employ['designer_task_id']) && empty($employ['house_keeper_task_id']) && empty($employ['supervisor_task_id']) ? (object)[] : (object)$employ;
            foreach ($task_data['employs'] as $e => $t) {
                if (empty($t)) {
                    unset($task_data['employs']->$e);
                }
            }
            $task_obj = $task_data['employs'];
            $task_data['employs'] = (array)$task_obj;
            foreach ($workers as $key3 => $value3) {
                $serve_area                = RealnameAuthModel::where('uid', $value3['uid'])->first()->serve_area;
                $workers[$key3]['avatar']  = !empty($value3['avatar']) ? url($value3['avatar']) : '';
                $workers[$key3]['mobile']  = !empty($value3['mobile']) ? $value3['mobile'] : UserModel::find($value3['uid'])->name;
                $task_data['actual_square']    = $value3['actual_square'];
                $workers[$key3]['address'] = empty($serve_area) ? '' : $serve_area;//服务区域
                $task_data['address']          = $value3['address'];
                $task_data['cost_of_design']   = $value3['cost_of_design'];
                $task_data['refuse_id']        = $value3['is_refuse'];
            }
            $task_data['workers'] = $workers;
            unset($task_data['uid']);
            $task_data['node_android']             = 0;
            $task_data['sn_android']               = 0;
            $task_data['status_android']           = 0;
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::select('id as work_offer_id', 'task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $value['task_id'])
                    ->orderBy('sn', 'ASC')
                    ->get()->toArray();
                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                    //给第一次报价的钱
                    if ($m['sn'] == 0) {
                        $task_data['total_price'] = doubleval($m['price']);
                    }
                    // 判断是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }
                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'task_id' => $value['task_id'], 'project_type' => 0, 'work_offer_id' => 0];
                } else {
                    $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                    $work_offer_id          = $last_work_offer_status['work_offer_id'];
                }
                $task_data['cancel_order_android'] = '0';
                $task_data['node']             = $value['status'];
                $task_data['sn']               = $last_work_offer_status['sn'];
                $task_data['status']           = floatval($last_work_offer_status['status']);
                $task_data['count_submit']     = $last_work_offer_status['count_submit'];
                $task_data['task_status_time'] = date('m-d H:i', strtotime($last_work_offer_status['task_status_time']));

                if ($value['user_type'] == 4) {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $last_work_offer_status['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();

                    $last_delay_date_status = ProjectDelayDate::where('task_id', $last_work_offer_status['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                } else {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $value['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();
                    $last_delay_date_status = ProjectDelayDate::where('task_id', $value['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                }

                if (!empty($amendment_sheet_status)) {
                    $last_amendment_sheet_status = $amendment_sheet_status->status;
                }
                if (!empty($last_delay_date_status)) {
                    $last_delay_date = $last_delay_date_status->is_sure;
                    if ($last_delay_date == 5) {
                        $last_delay_date_status = 0;
                    } else {
                        $last_delay_date_status = $last_delay_date;
                    }
                } else {
                    $last_delay_date_status = 0;
                }

                //查看剩余多少天
                $end_data  = TaskModel::find($task_id)->end_at;
                $left_days = empty($end_data) ? '' : ceil((strtotime($end_data) - time()) / (3600 * 24));

            } else {

                $work_offer_status = WorkOfferModel::select('id as work_offer_id', 'task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $value['task_id'])
                    ->orderBy('sn', 'ASC')
                    ->get();

                if (!$work_offer_status->isEmpty()) {
                    //返回work_offer中status为0的前一条数据
                    foreach ($work_offer_status as $n => $m) {
                        // 判断是否有拆除
                        if ($m['project_type'] == 1) {
                            $is_have_dismantle = 1;
                        }
                    }
                }

                $rob_time_offer = WorkOfferModel::where('task_id', $value['task_id'])->where('sn', 0)->first();
                $rob_time_work  = WorkModel::where('task_id', $value['task_id'])->first();
                if (!empty($rob_time_offer)) {
                    $rob_time = $rob_time_offer->created_at->format('m-d H:i:s');
                } elseif (!empty($rob_time_work)) {
                    $rob_time = date('m-d H:i', strtotime($rob_time_work->created_at));
                } else {
                    $rob_time = TaskModel::find($task_id)['created_at']->format('m-d H:i:s');
                }
                $task_data['cancel_order'] = '0';
                if (empty($value['cancel_order'])) {
                    $task_data['cancel_order_android'] = '0';
                    //$task_data['cancel_order'] = ['cancel_order_node' => 0, 'cancel_order_sn' => 0, 'cancel_order_status' => 0];
                } else {
                    $task_data['cancel_order_android'] = intval(!$value['end_order_status']);
                    $cancel_order_node           = explode('-', $value['cancel_order']);
                    //$task_data['cancel_order'] = ['cancel_order_node' => $cancel_order_node[0], 'cancel_order_sn' => $cancel_order_node[1], 'cancel_order_status' => $cancel_order_node[2]];
                    $task_data['node_android']             = intval($cancel_order_node[0]);
                    $task_data['sn_android']               = intval($cancel_order_node[1]);
                    $task_data['status_android']           = floatval($cancel_order_node[2]);

                }

                $task_data['node']             = $value['status'];
                $task_data['sn']               = 0;
                $task_data['status']           = 0;
                $task_data['count_submit']     = 0;
                $task_data['task_status_time'] = $rob_time;
            }
            $task_data['is_have_dismantle']           = $is_have_dismantle;
            $task_data['last_amendment_sheet_status'] = $last_amendment_sheet_status;
            $task_data['last_delay_date_status']      = $last_delay_date_status;
            $task_data['left_days']                   = intval($left_days);
            $task_data['work_offer_id']               = intval($work_offer_id);

        }
        //筛选出锁定人员

            foreach ($task_data['workers'] as $n => $m) {
                if ($m['status'] > 0) {
                    $task_data['lock_uid'] = $m['uid'];
                }
            }

        return $task_data;
    }


    /**
     * @param $task_id
     * 根据task_id获取监理的订单详细
     */
    public function getSupervisorTaskDetail($task_id) {
        $last_amendment_sheet_status = 0;//客诉中心状态
        $last_delay_date_status      = 0;//延期单状态
        $is_have_dismantle           = 0;//判断是否有拆除
        $left_days                   = '';//剩余多少天
        $need_others                 = 0;//是否需要其他人的参与
        //手续费，到系统配置里面找
        $poundage_service_price = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;

        $room_config_arr        = array('bedroom' => '房','living_room' => '厅','kitchen' => '厨', 'washroom' => '卫','balcony' => '阳台');

        $tasks = TaskModel::select(
            'task.uid','task.cancel_order','task.end_order_status','task.type_id as type_model',
            'p.room_config','task.created_at','p.square','task.status','task.quanlity_service_money',
            'task.project_position as project_position_id','c.name as favourite_style',
            'task.id as task_id', 'task.user_type','task.view_count',
            'task.show_cash','p.region','p.project_position',
            'users.name','user_detail.mobile','user_detail.avatar as boss_avatar',
            'user_detail.nickname as boss_nike_name','task.unique_code'
        )->where('task.id', $task_id)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('users', 'users.id', '=', 'task.uid')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->distinct('task.id')
            ->get()->toArray();
        $task_data = $tasks[0];
        foreach ($tasks as $key => &$value) {

            $str                      = '';
            $task_data['created_at'] = date('Y-m-d', strtotime($value['created_at']));
            $task_data['real_mobile'] = !empty($value['mobile']) ? $value['mobile'] : $value['name'];
            $task_data['boss_avatar'] = !empty($value['boss_avatar']) ? url($value['boss_avatar']) : '';
            $task_data['favourite_style'] = empty($value['favourite_style']) ? '' : $value['favourite_style'];
            $task_data['boss_nike_name'] = !empty($value['boss_nike_name']) ? $value['boss_nike_name'] : '';
            $task_data['quanlity_service_money'] = !empty($value['quanlity_service_money']) ? doubleval($value['quanlity_service_money']) : 0;
            unset($value['mobile'], $value['name']);
            $room_config_decode = json_decode($value['room_config']);

            foreach ($room_config_decode as $key2 => $value2) {
                if (isset($room_config_arr[$key2])) {
                    $str .= $value2 . $room_config_arr[$key2];
                }
            }

            $task_data['room_config'] = $str;
            //定金抵扣显示
            $task_data['poundage_service_price'] = (int)$poundage_service_price;

            if ($value['type_model'] == 2) {
                $workers = WorkModel::select(
                    'user_detail.uid','user_detail.nickname','user_detail.avatar',
                    'user_detail.mobile','user_detail.city as address','work.status',
                    'user_detail.cost_of_design','work.price','work.actual_square','work_designer_logs.is_refuse'
                )->where('work.task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
                    ->where('work_designer_logs.task_id', $value['task_id'])
                    ->distinct('work.uid')
                    ->get()->toArray();
            } else {
                $workers = WorkModel::select(
                    'user_detail.uid','user_detail.nickname','user_detail.avatar',
                    'user_detail.mobile','user_detail.city as address','work.status',
                    'user_detail.cost_of_design','work.price','work.actual_square','work.is_refuse'
                )->where('task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->get()->toArray();
            }

            $work_data = WorkModel::where('task_id', $value['task_id'])->where('status', '>=', 1)->first();

            $task_data['actual_pay']  = 0;
            $task_data['total_price'] = 0;
            if (!empty($work_data)) {
                $task_data['actual_pay']  = $work_data->price - (int)$poundage_service_price;
                $task_data['total_price'] = doubleval($work_data->price);
            }

            //2：设计师 3：管家 4：监理
            $data_id['house_keeper_task_id'] = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 3)->get();
            $data_id['supervisor_task_id']   = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 4)->get();
            $data_id['designer_task_id']     = TaskModel::select('id','user_type')->where('project_position', $value['project_position_id'])->where('status', '<=', 9)->where('user_type', 2)->get();

            $employ = [];
            foreach ($data_id as $n => $m) {
                if (empty($m)) {
                    unset($data_id[$n]);
                }
                foreach ($m as $o => $p) {
                    $employ[$n] = WorkModel::select(
                        'user_detail.uid', 'user_detail.nickname', 'user_detail.avatar',
                        'user_detail.mobile', 'user_detail.city as address'
                    )->where('task_id', $p['id'])
//                        ->where('status', '>', 0)
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                        ->get()->toArray();
                    $employ[$n][0]['identify'] = $p['user_type'];
                    $employ_iden = $employ;
                    unset($employ_iden[$n][0]['identify']);

                }
            }

            if (!empty($employ_iden)) {
                foreach ($employ_iden as $n => $m) {
                    if (!empty($m[0])) {
                        foreach ($m as $o => $p) {
                            $employ[$n][$o]['avatar'] = !empty($p['avatar']) ? url($p['avatar']) : '';
                            $employ[$n][$o]['mobile'] = !empty($p['mobile']) ? $p['mobile'] :  UserModel::find($p['uid'])->name;
                        }
                    }
                }
            }

            $task_data['employs'] = empty($employ['designer_task_id']) && empty($employ['house_keeper_task_id']) && empty($employ['supervisor_task_id']) ? (object)[] : (object)$employ;
            foreach ($task_data['employs'] as $e => $t) {
                if (empty($t)) {
                    unset($task_data['employs']->$e);
                }
            }
            $task_obj = $task_data['employs'];
            $task_data['employs'] = (array)$task_obj;
            foreach ($workers as $key3 => $value3) {
                $serve_area                = RealnameAuthModel::where('uid', $value3['uid'])->first()->serve_area;
                $workers[$key3]['avatar']  = !empty($value3['avatar']) ? url($value3['avatar']) : '';
                $workers[$key3]['mobile']  = !empty($value3['mobile']) ? $value3['mobile'] : UserModel::find($value3['uid'])->name;
                $task_data['actual_square']    = $value3['actual_square'];
                $workers[$key3]['address'] = empty($serve_area) ? '' : $serve_area;//服务区域
                $task_data['address']          = $value3['address'];
                $task_data['cost_of_design']   = $value3['cost_of_design'];
                $task_data['refuse_id']        = $value3['is_refuse'];
            }

            $task_data['workers'] = $workers;
            unset($task_data['uid']);
            $task_data['node_android']             = 0;
            $task_data['sn_android']               = 0;
            $task_data['status_android']           = 0;
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $value['task_id'])
                    ->orderBy('sn', 'ASC')
                    ->get()->toArray();

                //监理订单类型
                if ($value['user_type'] == 4) {

                    $is_replace              = false;
                    $project_position        = $value['project_position_id'];

                    $house_keeper_task = TaskModel::where('unique_code', $value['unique_code'])->where('status', '<', 9)->where('user_type', 3)->first();

                    if (empty($house_keeper_task)) {
                        $chang_task_id = $value['task_id'];
                        $need_others   = 1;
                    } else {
                        $house_keeper_work       = WorkModel::where('task_id', $house_keeper_task->id)->first();//有管家抢单
                        $house_keeper_work_payed = WorkModel::where('task_id', $house_keeper_task->id)->where('status', 2)->first();//已支付管家
                        $jianli_work             = WorkModel::where('task_id', $value['task_id'])->where('status', 2)->first();//监理已付款

                        if (!empty($house_keeper_task) && !empty($house_keeper_work) && !empty($house_keeper_work_payed) && $jianli_work) {
                            $chang_task_id = $house_keeper_task->id;
                        } else {
                            $chang_task_id = $value['task_id'];
                        }
                    }


                    $work_offer_status_1 = WorkOfferModel::select('project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price', 'task_id')
                        ->where('task_id', $chang_task_id)
                        ->orderBy('sn', 'ASC')
                        ->get()->toArray();

                    foreach ($work_offer_status_1 as $o => $p) {
                        if ($p['sn'] > 0 && $p['status'] > 0) {
                            $is_replace = true;
                            break;
                        }
                    }
                    if ($is_replace) {
                        $work_offer_status = $work_offer_status_1;
                    }
                }

                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                    //给第一次报价的钱（这里全部用的是管家的工作流程，导致进度条上的总价显示的是管家的）
//                    if ($m['sn'] == 0) {
//                        $value['total_price'] = $m['price'];
//                    }
                    // 判断是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }


                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'task_id' => $value['task_id'], 'project_type' => 0];
                } else {
                    $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                }
                $task_data['cancel_order_android'] = '0';
                $task_data['node']             = $value['status'];
                $task_data['sn']               = $last_work_offer_status['sn'];
                $task_data['status']           = floatval($last_work_offer_status['status']);
                $task_data['count_submit']     = $last_work_offer_status['count_submit'];
                $task_data['task_status_time'] = date('m-d H:i', strtotime($last_work_offer_status['task_status_time']));

                if ($value['user_type'] == 4) {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $last_work_offer_status['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();

                    $last_delay_date_status = ProjectDelayDate::where('task_id', $last_work_offer_status['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                } else {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $value['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();
                    $last_delay_date_status = ProjectDelayDate::where('task_id', $value['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                }


                if (!empty($amendment_sheet_status)) {
                    $last_amendment_sheet_status = $amendment_sheet_status->status;
                }
                if (!empty($last_delay_date_status)) {
                    $last_delay_date = $last_delay_date_status->is_sure;
                    if ($last_delay_date == 5) {
                        $last_delay_date_status = 0;
                    } else {
                        $last_delay_date_status = $last_delay_date;
                    }
                } else {
                    $last_delay_date_status = 0;
                }

                //查看剩余多少天
                $end_data  = TaskModel::find($task_id)->end_at;

                $h_task = TaskModel::where('user_type',3)->where('status','<',9)->where('project_position',$value['project_position_id'])->first();
                if(!empty($h_task) && !empty($h_task->end_at)){
                    if($h_task->end_at != $end_data){
                        TaskModel::where('id',$task_id)->update(['end_at'=>$h_task->end_at]);
                        $end_data = $h_task->end_at;
                    }
                }


                $left_days = empty($end_data) ? 0 : ceil((strtotime($end_data) - time()) / (3600 * 24));

            } else {

                $rob_time_offer = WorkOfferModel::where('task_id', $value['task_id'])->where('sn', 0)->first();
                $rob_time_work  = WorkModel::where('task_id', $value['task_id'])->first();
                if (!empty($rob_time_offer)) {
                    $rob_time = $rob_time_offer->created_at->format('m-d H:i:s');
                } elseif (!empty($rob_time_work)) {
                    $rob_time = date('m-d H:i', strtotime($rob_time_work->created_at));
                } else {
                    $rob_time = TaskModel::find($task_id)['created_at']->format('m-d H:i:s');
                }

                //监理订单类型
                if ($value['user_type'] == 4) {
                    $project_position  = $value['project_position_id'];
                    $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<=', 9)->where('user_type', 3)->orderBy('id','desc')->first();
                    if (empty($house_keeper_task)) {
                        $chang_task_id = $value['task_id'];
                    } else {
                        $chang_task_id = $house_keeper_task->id;
                    }
                    $work_offer_status_1 = WorkOfferModel::select('project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price', 'task_id')
                        ->where('task_id', $chang_task_id)
                        ->orderBy('sn', 'ASC')
                        ->get()->toArray();
                }
   
                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status_1 as $n => $m) {
                    // 判断是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }
                $task_data['cancel_order'] = '0';
                if (empty($value['cancel_order'])) {
                    $task_data['cancel_order_android'] = '0';
                   // $task_data['cancel_order'] = ['cancel_order_node' => 0, 'cancel_order_sn' => 0, 'cancel_order_status' => 0];
                } else {
                    $task_data['cancel_order_android'] = intval(!$value['end_order_status']);
                    $cancel_order_node           = explode('-', $value['cancel_order']);
                    //$task_data['cancel_order'] = ['cancel_order_node' => $cancel_order_node[0], 'cancel_order_sn' => $cancel_order_node[1], 'cancel_order_status' => $cancel_order_node[2]];
                    $task_data['node_android']             = intval($cancel_order_node[0]);
                    $task_data['sn_android']               = intval($cancel_order_node[1]);
                    $task_data['status_android']           = $cancel_order_node[2];
                }

                $task_data['node']             = $value['status'];
                $task_data['sn']               = 0;
                $task_data['status']           = 0;
                $task_data['count_submit']     = 0;
                $task_data['task_status_time'] = $rob_time;
            }
            $task_data['is_have_dismantle']           = $is_have_dismantle;
            $task_data['last_amendment_sheet_status'] = $last_amendment_sheet_status;
            $task_data['last_delay_date_status']      = $last_delay_date_status;
            $task_data['left_days']                   = intval($left_days);
            $task_data['need_others']                 = $need_others;

        }
        //筛选出锁定人员
            foreach ($task_data['workers'] as $n => $m) {
                if ($m['status'] > 0) {
                    $task_data['lock_uid'] = $m['uid'];
                }
            }

        return $task_data;
    }


    /**
     * @param $detail
     * @param $uid_boss
     * @return mixed
     * 随机给设计师作品
     */
    public function WorkersShow($detail,$uid_boss) {
        $a = $b = $c = $d= 0;

        $limit    = mt_rand(3, 6);
        foreach ($detail as $n => $m) {
            //设计师
            if ($m['user_type'] == 2) {
                if ($a > $limit) {
                    unset($detail[$n]);
                    continue;
                }
                unset($detail[$n]['star']);
                $detail[$n]['goods_list'] = GoodsModel::select('goods.id', 'cover', 'goods_address', 'title', 'cate.name as cate_name')
                    ->join('cate', 'cate.id', '=', 'goods.cate_id')
                    ->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', $m['shop_id'])->get()->toArray();

                $designer_uid = UserFocusModel::where('uid', $uid_boss)->lists('focus_uid')->toArray();


                if (!empty($uid_boss) && !empty($designer_uid) && in_array($m['uid'], $designer_uid)) {
//                if (in_array($m['uid'], $designer_uid)) {
                    $detail[$n]['already_focus'] = 1;
                } else {
                    $detail[$n]['already_focus'] = 0;
                }

                foreach ($detail[$n]['goods_list'] as $key => &$value) {
                    $value['cover'] = !empty($value['cover']) ? url($value['cover']) : '';
                }
                $detail[$n]['total_goods'] = count($detail[$n]['goods_list']);
                $detail[$n]['is_foucus']   = DB::table('user_focus')->where('focus_uid', $m['uid'])->where('uid',$uid_boss)->count();
                $a++;
            }
            //管家
            if ($m['user_type'] == 3) {

                //10个管家,拿第三个,其他的就unset掉
                if ($b > $limit) {
                    unset($detail[$n]);
                    continue;
                }

                $config1                      = LevelModel::getConfigByType(1)->toArray();
                $workerStarPrice              = LevelModel::getConfig($config1, 'price');
                $star_house_keeper            = UserDetailModel::where('uid', $m['uid'])->first()->star;
                $star_house_keeper            = empty($star_house_keeper) ? '1' : $star_house_keeper;
                $unit_price                   = $workerStarPrice[$star_house_keeper - 1]->price;
                $detail[$n]['cost_of_design'] = $unit_price;

                $detail[$n]['goods_list'] = GoodsModel::select('goods.id', 'cover', 'goods_address', 'title', 'cate.name as cate_name')
                    ->join('cate', 'cate.id', '=', 'goods.cate_id')
                    ->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', $m['shop_id'])->get()->toArray();

                $designer_uid = UserFocusModel::where('uid', $uid_boss)->lists('focus_uid')->toArray();


                if (!empty($uid_boss) && !empty($designer_uid) && in_array($m['uid'], $designer_uid)) {
//                if (in_array($m['uid'], $designer_uid)) {
                    $detail[$n]['already_focus'] = 1;
                } else {
                    $detail[$n]['already_focus'] = 0;
                }

                foreach ($detail[$n]['goods_list'] as $key => &$value) {
                    $value['cover'] = !empty($value['cover']) ? url($value['cover']) : '';
                }
                $detail[$n]['total_goods'] = count($detail[$n]['goods_list']);
                $detail[$n]['is_foucus']   = DB::table('user_focus')->where('focus_uid', $m['uid'])->where('uid',$uid_boss)->count();
                $b++;
            }
            //监理
            if ($m['user_type'] == 4) {
                if ($c > $limit) {
                    unset($detail[$n]);
                    continue;
                }

                $config1                      = LevelModel::getConfigByType(2)->toArray();
                $workerStarPrice              = LevelModel::getConfig($config1, 'price');
                $star_house_keeper            = UserDetailModel::where('uid',$m['uid'])->first()->star;
                $star_house_keeper            = empty($star_house_keeper) ? '1' : $star_house_keeper;
                $unit_price                   = $workerStarPrice[$star_house_keeper - 1]->price;
                $detail[$n]['cost_of_design'] = $unit_price;

                $detail[$n]['goods_list'] = GoodsModel::select('goods.id', 'cover', 'goods_address', 'title', 'cate.name as cate_name')
                    ->join('cate', 'cate.id', '=', 'goods.cate_id')
                    ->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', $m['shop_id'])->get()->toArray();

                $designer_uid = UserFocusModel::where('uid', $uid_boss)->lists('focus_uid')->toArray();


                if (!empty($uid_boss) && !empty($designer_uid) && in_array($m['uid'], $designer_uid)) {
//                if (in_array($m['uid'], $designer_uid)) {
                    $detail[$n]['already_focus'] = 1;
                } else {
                    $detail[$n]['already_focus'] = 0;
                }

                foreach ($detail[$n]['goods_list'] as $key => &$value) {
                    $value['cover'] = !empty($value['cover']) ? url($value['cover']) : '';
                }
                $detail[$n]['total_goods'] = count($detail[$n]['goods_list']);
                $detail[$n]['is_foucus']   = DB::table('user_focus')->where('focus_uid', $m['uid'])->where('uid',$uid_boss)->count();
                $c++;
            }

            if ($m['user_type'] == 5) {

                if ($d > $limit) {
                    unset($detail[$n]);
                    continue;
                }

                $config1                      = LevelModel::getConfigByType($m['work_type'])->toArray();
                $workerStarPrice              = LevelModel::getConfig($config1, 'price');
                $star_house_keeper            = UserDetailModel::where('uid',$m['uid'])->first()->star;
                $star_house_keeper            = empty($star_house_keeper) ? '1' : $star_house_keeper;
                $unit_price                   = $workerStarPrice[$star_house_keeper - 1]->price;
                $detail[$n]['cost_of_design'] = $unit_price;

                $detail[$n]['goods_list'] = [];

                $designer_uid = UserFocusModel::where('uid', $uid_boss)->lists('focus_uid')->toArray();


                if (!empty($uid_boss) && !empty($designer_uid) && in_array($m['uid'], $designer_uid)) {
//                if (in_array($m['uid'], $designer_uid)) {
                    $detail[$n]['already_focus'] = 1;
                } else {
                    $detail[$n]['already_focus'] = 0;
                }

//                foreach ($detail[$n]['goods_list'] as $key => &$value) {
//                    $value['cover'] = !empty($value['cover']) ? url($value['cover']) : '';
//                }
                $detail[$n]['total_goods'] = 0;
                $detail[$n]['is_foucus']   = DB::table('user_focus')->where('focus_uid', $m['uid'])->where('uid',$uid_boss)->count();
                $c++;
            }


            $detail[$n]['avatar']   = empty($detail[$n]['avatar']) ? '' : url($detail[$n]['avatar']);
            $detail[$n]['nickname'] = empty($detail[$n]['nickname']) ? $detail[$n]['realname'] : $detail[$n]['nickname'];
            unset($detail[$n]['shop_id'], $detail[$n]['realname'], $detail[$n]['user_mobile']);
        }

        return $detail;
    }


    /**
     * @param $boss_uid
     * @param $room_config_arr
     * @return mixed
     * 根据uid拼接数据
     */
    public function getUserTasks($boss_uid,$room_config_arr) {
        $tasks       = TaskModel::select('task.uid',
            'p.room_config',
            'task.created_at',
            'p.square',
            'task.status',
            'task.type_id as type_model',
            'c.name as favourite_style',
            'task.project_position as project_position_id',
            'task.user_type',
            'task.id as task_id',
            'task.user_type',
            'task.view_count',
            'p.region',
            'p.project_position'
        )
            ->where('task.uid', $boss_uid)
//            ->where('type_id', 1)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->orderBy('task.created_at','desc ')
            ->get()->toArray();


        foreach ($tasks as $key => &$value) {
			
            $str                 = '';
            $room_config_decode  = json_decode($value['room_config'],true);
			if(!empty($room_config_decode)&&is_array($room_config_decode)){
				foreach ($room_config_decode as $key2 => $value2) {
                	if (isset($room_config_arr[$key2])) {
                    $str .= $value2 . $room_config_arr[$key2];
                	}
            	}
			}
            
			
            $value['room_config'] = $str;
            $workers              = WorkModel::select(
                'user_detail.uid',
                'user_detail.nickname',
                'user_detail.avatar',
                'user_detail.mobile',
                'work.status',
                'work_designer_logs.is_refuse',
                'work.price'
            )
                ->where('work.task_id', $value['task_id'])
                ->where('work_designer_logs.task_id', $value['task_id'])
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
                ->get()
                ->toArray();

            $value['total_price'] = 0;
            foreach ($workers as $key3 => $value3) {
                $workers[$key3]['avatar']    = !empty($value3['avatar']) ? url($value3['avatar']) : '';
                $workers[$key3]['mobile']    = !empty($value3['mobile']) ? $value3['mobile'] : '';
                $value['total_price']        = doubleval($value3['price']);
                $value['is_refuse_designer'] = $value3['is_refuse'];
            }

            $value['workers'] = $workers;

            unset($value['uid']);

            if ($value['status'] == 7) {

                //监理订单类型
                if ($value['user_type'] == 4) {
                    $project_position        = $value['project_position_id'];
                    $house_keeper_task       = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();//发了管家的单
                    $house_keeper_task_id    = empty($house_keeper_task) ? 0 : $house_keeper_task->id;
                    $house_keeper_work       = WorkModel::where('task_id', $house_keeper_task_id)->first();//有管家抢单
                    $house_keeper_work_payed = WorkModel::where('task_id', $house_keeper_task_id)->where('status', 2)->first();//已支付管家
                    $jianli_work             = WorkModel::where('task_id', $value['task_id'])->where('status', 2)->first();//监理已付款

                    if (!empty($house_keeper_task) && !empty($house_keeper_work) && !empty($house_keeper_work_payed) && $jianli_work) {
                        $change_task_id = $house_keeper_task->id;
                    } else {
                        $change_task_id = $value['task_id'];
                    }
                } else {
                    $change_task_id = $value['task_id'];
                }



                $work_offer_status = WorkOfferModel::select('status', 'sn', 'count_submit')->where('task_id', $change_task_id)->orderBy('sn', 'ASC')->get()->toArray();
                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                }
                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0];
                } else {
                    $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                }

                $tasks[$key]['node']   = $value['status'];
                $tasks[$key]['sn']     = $last_work_offer_status['sn'];
                $tasks[$key]['status'] = $last_work_offer_status['status'];
                $tasks[$key]['count_submit'] = $last_work_offer_status['count_submit'];
            } else {
                $tasks[$key]['node']   = $value['status'];
                $tasks[$key]['sn']     = 0;
                $tasks[$key]['status'] = 0;
                $tasks[$key]['count_submit'] = 0;
            }

        }
        return $tasks;
    }
}
