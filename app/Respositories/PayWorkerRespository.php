<?php

namespace App\Respositories;

use App\Modules\Order\Model\SubOrderModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Project\ProjectDelayDate;
use App\PushSentenceList;
use DB;
use App\Modules\Order\Model\OrderModel;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\Order\Model\OrderModel as platformOrderModel;
use App\Modules\Project\ProjectConfigureTask;


class PayWorkerRespository {
    /**
     * @param $work
     * @param $data
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 结算加上事务
     */
    public function settle_accounts($work, $data) {
        // 该项目做到哪个阶段
        $ret    = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])
            ->where('sn', '>', 0)
            ->get()->toArray();

        $status = DB::transaction(function () use ($ret, $data) {
            foreach ($ret as $key => $value) {
                if ($value['status'] == 1) {
                    $count_submit = WorkOfferModel::where('id', $value['id'])->first()->count_submit;

                    $work_offer_info = WorkOfferModel::find($value['id']);

                    $work_offer_info->status = 2;
                    $work_offer_info->count_submit += 1;
                    $ret_status = $work_offer_info->save();

                    if ($ret_status && $count_submit < 4) {
                        // TODO 确认后吧状态转为4结束，并结算给设计师，并把work表的状态和task表的状态改变

                        //扣款记录(业主)
                        OrderModel::sepbountyOrder($data['from_uid'], $value['price'], $data['task_id'], $value['title'] . '款(冻结金->工作者)', 1);

                        $this->bounty($value['price'], $data['task_id'], $data['from_uid'], 5, 6, true, false);//扣冻结资金

                        //收款记录(设计师)
                        OrderModel::sepbountyOrder($value['to_uid'], $value['price'], $data['task_id'], $value['title'] . '款', 2, 1, $work_offer_info->sn);
                        $this->bounty($value['price'], $data['task_id'], $value['to_uid'], 2, 2);//设计师余额增加

                        //推送
                        change_status_msg($value['to_uid'],$value['title']);
                        //状态改变
                        $work_offer_info->status = 4;
                        $work_offer_info->save();
                        $task_info         = TaskModel::find($data['task_id']);
                        $task_info->status = 7;
                        $task_info->save();
                        return true;
                    }
                }

            }
        });
        return $status;
    }


    /**
     * @param $money 金额
     * @param $task_id 任务id
     * @param $uid 用户id
     * @param int $type 收支行为(1:表示余额 2:表示支付宝 3:表示微信 4:表示银联)
     * @param int $action 收支行为(1:发布任务 2:接受任务 3:用户充值 4:用户提现 5:购买增值服务 6:购买用户商品 7:任务失败退款)
     * @param bool $use_frozen_money 是否使用冻结金扣款
     * @param bool $is_worker 是否是工人
     * @return bool
     * 生成资金记录,自动扣除金额和入金
     */
    public function bounty($money, $task_id, $uid, $type = 1, $action = 1, $use_frozen_money = false, $is_worker = false) {

        //扣除用户的余额
        $query = DB::table('user_detail')->where('uid', '=', $uid);
        if ($action == 2 || $action == 3 || $action == 7) {
            if ($is_worker) {
                $query->where(function ($query) {
                    $query->where('balance_status', '!=', 1);
                })->increment('frozen_amount', $money);
            } else {
                $query->where(function ($query) {
                    $query->where('balance_status', '!=', 1);
                })->increment('balance', $money);
            }
        } else {
            if ($use_frozen_money) {
                $query->where(function ($query) {
                    $query->where('balance_status', '!=', 1);
                })->decrement('frozen_amount', $money);
            } else {
                $query->where(function ($query) {
                    $query->where('balance_status', '!=', 1);
                })->decrement('balance', $money);
            }

        }

        $financial = [
            'action' => $action,
            'pay_type' => $type,
            'cash' => $money,
            'uid' => $uid,
            'created_at' => date('Y-m-d H:i:s', time()),
            'task_id' => $task_id
        ];

        FinancialModel::create($financial);

        //增加用户的发布任务数量
        UserDetailModel::where('uid', $uid)->increment('publish_task_num', 1);

        return true;

    }


    /**
     * 管家结算
     */

    public function house_keeper_accounts($curSnStatus, $userInfo, $value, $work, $data, $taskInfo) {
        //处于需要用户确认的阶段
        if ($curSnStatus['status'] == 1.5) {
            $status = DB::transaction(function () use ($curSnStatus, $data, $value, $work, $taskInfo) {
                //这里开始判断是否到了节点
                $ret_status   = WorkOfferModel::where('id', $curSnStatus['id'])->update(['status' => 2]);     //业主确认 ， TODO 这里的状态是不会存在2的，用户一旦确认，就应该是4
                $count_submit = WorkOfferModel::where('id', $curSnStatus['id'])->first()->count_submit;
                WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit]);//次数加一

                if ($ret_status) {
                    //管家三个结算节点之一（防水，木工，竣工），达到后分别结算管家工资（即第一次报价的30%，30%，40%）
                    //管家的前两个阶段结算
                    $work_data_house = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])->where('sn', 0)->first();//找到管家对应的work_offer信息
                    //根据管家的task_id找监理的task_id
                    $supervisor_task_info = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 4)->first();

                    $house_keeper_price = $work_data_house->price;//应付管家的总价
                    $house_keeper_uid   = $work_data_house->to_uid;//管家的uid
                    if (!empty($supervisor_task_info)) {
                        $visor_data_offer = WorkOfferModel::where('task_id', $supervisor_task_info->id)->where('sn', 0)->first();//找到监理对应的work_offer信息
                        $visor_uid   = $visor_data_offer->to_uid;//管家的uid
                    }

                    $house_keeper_title = $work_data_house->title;//扣费缘由
                    //管家到了防水，木工,看有没有监理的参与,有的话,也要结算监理的工资


                    /**
                     * 辅材提现申请
                     */
//                    $mainOrder = OrderModel::where('task_id',$data['task_id']);
                    $auxiliaryOrder = SubOrderModel::where('task_id',$data['task_id'])->where('uid',255)->where('title','辅材价格')->first();


                    if ($curSnStatus['project_type'] == 3 || $curSnStatus['project_type'] == 5) {

                        //有监理参与,就结算
                        if (!empty($supervisor_task_info)) {
                            $supervisor_data_offer = WorkOfferModel::where('task_id', $supervisor_task_info->id)->where('sn', 0)->first();//找到监理对应的work_offer信息
                            $supervisor_price      = $supervisor_data_offer->price;//应付监理的总价
                            $supervisor_uid        = $supervisor_data_offer->to_uid;//监理的uid
                            $supervisor_title      = $supervisor_data_offer->title;//扣费缘由
                            $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $supervisor_price, $supervisor_title, $curSnStatus['project_type'], $supervisor_task_info->id, $supervisor_uid, 4);

                        }

                        $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $house_keeper_price, $house_keeper_title, $curSnStatus['project_type'], $data['task_id'], $house_keeper_uid);

                        /**
                         * 辅材提现申请
                         */
                        if(!empty($auxiliaryOrder)){
                            $auxiliaryOrderPrice = $auxiliaryOrder->cash;
                            $auxiliaryPrice  = round($auxiliaryOrderPrice*0.3,2);
                            $this->getHouseKeeperAuxiliary($data['task_id'],$curSnStatus['project_type'],$auxiliaryPrice,$house_keeper_uid,$auxiliaryOrder->order_id,$auxiliaryOrder->code);

                        }

                    }

                    //竣工阶段,结算管家最后的40%
                    if ($curSnStatus['project_type'] == 7) {

                        //有监理参与,就结算
                        if (!empty($supervisor_task_info)) {
                            $supervisor_data_offer = WorkOfferModel::where('task_id', $supervisor_task_info->id)->where('sn', 0)->first();//找到监理对应的work_offer信息
                            $supervisor_price      = $supervisor_data_offer->price;//应付监理的总价
                            $supervisor_uid        = $supervisor_data_offer->to_uid;//监理的uid
                            $supervisor_title      = $supervisor_data_offer->title;//扣费缘由
                            $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $supervisor_price, $supervisor_title, $curSnStatus['project_type'], $supervisor_task_info->id, $supervisor_uid, 4);

                        }

                        $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $house_keeper_price, $house_keeper_title, $curSnStatus['project_type'], $data['task_id'], $house_keeper_uid);

                    }

                    //其他阶段单独拿出来,付100%
                    if ($curSnStatus['project_type'] == 7) {
                        //扣款记录(业主)
                        $is_ordered = platformOrderModel::sepbountyOrder($data['from_uid'], $curSnStatus['price'], $data['task_id'], $curSnStatus['title'] . '100%款(冻结金->工作者)', 1, 1, $curSnStatus['project_type']);
                        $decrement  = $this->bounty($curSnStatus['price'], $data['task_id'], $data['from_uid'], $is_ordered->code, 1, 6, true);//扣冻结资金
                    } else {

                        // TODO 扣款记录(业主)
                        $is_ordered = platformOrderModel::sepbountyOrder($data['from_uid'], $curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款(冻结金->工作者)', 1, 1, $curSnStatus['project_type']);

                        $decrement  = $this->bounty($curSnStatus['price'] * 0.8, $data['task_id'], $data['from_uid'], 1, 6, true);//扣冻结资金
                    }


                    //工人冻结金增加(多传个参数证明是工人,需要冻结增加而不是余额增加)
                    //水电阶段,单独拿出来
                    if ($curSnStatus['project_type'] == 2) {
                        $toUidArr = explode('-', $curSnStatus['to_uid']);  // 两个工种的id分解

                        // 算出水电阶段两个工种不同的总价
                        $eachWorkerPrice    = ProjectConfigureTask::where('task_id', $data['task_id'])->where('is_sure', 1)->first();
                        $eachWorkerPriceArr = unserialize($eachWorkerPrice->project_con_list)['parent_2'];
                        $priceArr           = [];
                        foreach ($eachWorkerPriceArr['childs'] as $key => $value) {
                            if (empty($priceArr[$value['work_type']])) {
                                $priceArr[$value['work_type']] = 0;
                            }
                            $priceArr[$value['work_type']] += $value['child_price'];
                        }
                        $workerStar = $taskInfo->workerStar;//找到工人的星级
                        //不同星级不同的百分比
                        $workerStarRate = rate_choose($workerStar);
                        foreach ($priceArr as $key => $value) {
                            foreach ($toUidArr as $key2 => $value2) {
                                $workerType = UserDetailModel::where('uid', $value2)->first()->work_type;
                                if ($workerType == $key) {
                                    //收款记录(工人)
                                    $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($value2, $workerStarRate * $value * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款', 2, 1, $curSnStatus['project_type']);
                                    $is_ordered_designer_10 = platformOrderModel::sepbountyOrder($value2, $workerStarRate * $value * 0.2, $data['task_id'], $curSnStatus['title'] . '20%款(竣工结算)', 2, 0, $curSnStatus['project_type']);
                                    $increment_designer     = $this->bounty($value * 0.8, $data['task_id'], $value2, 1, 2);
                                    //推送
                                    change_status_msg($value2, $curSnStatus['title']);
                                }
                            }
                        }
                        //推送给管家
                        change_status_msg($house_keeper_uid,$curSnStatus['title']);
                        //推送给监理
                        change_status_msg($visor_uid,$curSnStatus['title']);

                    } else {

                        //其他阶段单独拿出来,付100%
                        if ($curSnStatus['project_type'] == 7) {
                            //收款记录(管家)
                            $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'], $data['task_id'], $curSnStatus['title'] . '100%款', 2, 1, $curSnStatus['project_type']);
                            $increment_designer     = $this->bounty($curSnStatus['price'], $data['task_id'], $curSnStatus['to_uid'], 1, 2);

                            $taskInfo->cancel_order             = $taskInfo->status . '-' . $curSnStatus['sn'] . '-' . 4;//task状态拼接sn拼接status
                            $supervisor_task_info->cancel_order = $taskInfo->status . '-' . $curSnStatus['sn'] . '-' . 4;//task状态拼接sn拼接status

                            $taskInfo->save();
                            $supervisor_task_info->save();
                            //推送
                            change_status_msg($curSnStatus['to_uid'],$curSnStatus['title']);
                            //推送给管家
                            change_status_msg($house_keeper_uid,$curSnStatus['title']);
                            //推送给监理
                            change_status_msg($visor_uid,$curSnStatus['title']);
                        } else {
                            //TODO 收款记录(工人)
                            $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款', 2, 1, $curSnStatus['project_type']);
                            $is_ordered_designer_10 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'] * 0.2, $data['task_id'], $curSnStatus['title'] . '20%款(竣工结算)', 2, 0, $curSnStatus['project_type']);
                            $increment_designer     = $this->bounty($curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['to_uid'], 1, 2);
                            //推送给工人
                            change_status_msg($curSnStatus['to_uid'],$curSnStatus['title']);
                            //推送给管家
                            change_status_msg($house_keeper_uid,$curSnStatus['title']);
                            //推送给监理
                            change_status_msg($visor_uid,$curSnStatus['title']);
                        }
                    }

                    WorkOfferModel::where('id', $curSnStatus['id'])->update(['status' => 4]);

                }

            });
            return $status;
        }

    }

    /**
     * 管家结算(后台强制结算)
     */

    public function house_keeper_accounts_other($curSnStatus, $value, $work, $data, $taskInfo) {
        //处于需要用户确认的阶段

            $status = DB::transaction(function () use ($curSnStatus, $data, $value, $work, $taskInfo) {
                //这里开始判断是否到了节点
                $ret_status   = WorkOfferModel::where('id', $curSnStatus['id'])->update(['status' => 2]);     //业主确认
                $count_submit = WorkOfferModel::where('id', $curSnStatus['id'])->first()->count_submit;
                WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit]);//次数加一

                if ($ret_status) {
                    //管家三个结算节点之一（防水，木工，竣工），达到后分别结算管家工资（即第一次报价的30%，30%，40%）
                    //管家的前两个阶段结算
                    $work_data_house = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])->where('sn', 0)->first();//找到管家对应的work_offer信息
                    //根据管家的task_id找监理的task_id
                    $supervisor_task_info = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 4)->first();

                    $house_keeper_price = $work_data_house->price;//应付管家的总价
                    $house_keeper_uid   = $work_data_house->to_uid;//管家的uid
                    $house_keeper_title = $work_data_house->title;//扣费缘由
                    //管家到了防水，木工,看有没有监理的参与,有的话,也要结算监理的工资
                    if ($curSnStatus['project_type'] == 3 || $curSnStatus['project_type'] == 5) {

                        //有监理参与,就结算
                        if (!empty($supervisor_task_info)) {
                            $supervisor_data_offer = WorkOfferModel::where('task_id', $supervisor_task_info->id)->where('sn', 0)->first();//找到监理对应的work_offer信息
                            $supervisor_price      = $supervisor_data_offer->price;//应付监理的总价
                            $supervisor_uid        = $supervisor_data_offer->to_uid;//监理的uid
                            $supervisor_title      = $supervisor_data_offer->title;//扣费缘由
                            $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $supervisor_price, $supervisor_title, $curSnStatus['project_type'], $supervisor_task_info->id, $supervisor_uid, 4);

                        }

                        $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $house_keeper_price, $house_keeper_title, $curSnStatus['project_type'], $data['task_id'], $house_keeper_uid);


                    }

                    //竣工阶段,结算管家最后的40%
                    if ($curSnStatus['project_type'] == 7) {

                        //有监理参与,就结算
                        if (!empty($supervisor_task_info)) {
                            $supervisor_data_offer = WorkOfferModel::where('task_id', $supervisor_task_info->id)->where('sn', 0)->first();//找到监理对应的work_offer信息
                            $supervisor_price      = $supervisor_data_offer->price;//应付监理的总价
                            $supervisor_uid        = $supervisor_data_offer->to_uid;//监理的uid
                            $supervisor_title      = $supervisor_data_offer->title;//扣费缘由
                            $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $supervisor_price, $supervisor_title, $curSnStatus['project_type'], $supervisor_task_info->id, $supervisor_uid, 4);

                        }

                        $this->houseKeeperAndSupervisorGetMoney($data['from_uid'], $house_keeper_price, $house_keeper_title, $curSnStatus['project_type'], $data['task_id'], $house_keeper_uid);

                    }

                    //其他阶段单独拿出来,付100%
                    if ($curSnStatus['project_type'] == 7) {
                        //扣款记录(业主)
                        $is_ordered = platformOrderModel::sepbountyOrder($data['from_uid'], $curSnStatus['price'], $data['task_id'], $curSnStatus['title'] . '100%款(冻结金->工作者)', 1, 1, $curSnStatus['project_type']);
                        $decrement  = $this->bounty($curSnStatus['price'], $data['task_id'], $data['from_uid'], $is_ordered->code, 1, 6, true);//扣冻结资金
                    } else {
                        //扣款记录(业主)
                        $is_ordered = platformOrderModel::sepbountyOrder($data['from_uid'], $curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款(冻结金->工作者)', 1, 1, $curSnStatus['project_type']);
                        $decrement  = $this->bounty($curSnStatus['price'] * 0.8, $data['task_id'], $data['from_uid'], 1, 6, true);//扣冻结资金
                    }


                    //工人冻结金增加(多传个参数证明是工人,需要冻结增加而不是余额增加)
                    //水电阶段,单独拿出来
                    if ($curSnStatus['project_type'] == 2) {
                        $toUidArr = explode('-', $curSnStatus['to_uid']);  // 两个工种的id分解

                        // 算出水电阶段两个工种不同的总价
                        $eachWorkerPrice    = ProjectConfigureTask::where('task_id', $data['task_id'])->where('is_sure', 1)->first();
                        $eachWorkerPriceArr = unserialize($eachWorkerPrice->project_con_list)['parent_2'];
                        $priceArr           = [];
                        foreach ($eachWorkerPriceArr['childs'] as $key => $value) {
                            if (empty($priceArr[$value['work_type']])) {
                                $priceArr[$value['work_type']] = 0;
                            }
                            $priceArr[$value['work_type']] += $value['child_price'];
                        }
                        foreach ($priceArr as $key => $value) {
                            foreach ($toUidArr as $key2 => $value2) {
                                $workerType = UserDetailModel::where('uid', $value2)->first()->work_type;
                                if ($workerType == $key) {
                                    //收款记录(工人)
                                    $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($value2, $value * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款', 2, 1, $curSnStatus['project_type']);
                                    $is_ordered_designer_10 = platformOrderModel::sepbountyOrder($value2, $value * 0.2, $data['task_id'], $curSnStatus['title'] . '20%款(竣工结算)', 2, 0, $curSnStatus['project_type']);
                                    $increment_designer     = $this->bounty($value * 0.8, $data['task_id'], $value2, 1, 2);
                                    //推送
                                    change_status_msg($value2,$curSnStatus['title']);
                                }
                            }
                        }

                    } else {

                        //其他阶段单独拿出来,付100%
                        if ($curSnStatus['project_type'] == 7) {
                            //收款记录(管家)
                            $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'], $data['task_id'], $curSnStatus['title'] . '100%款', 2, 1, $curSnStatus['project_type']);
                            $increment_designer     = $this->bounty($curSnStatus['price'], $data['task_id'], $curSnStatus['to_uid'], 1, 2);

                            //推送
                            change_status_msg($curSnStatus['to_uid'],$curSnStatus['title']);
                            $taskInfo->cancel_order             = $taskInfo->status . '-' . $curSnStatus['sn'] . '-' . 4;//task状态拼接sn拼接status
                            $supervisor_task_info->cancel_order = $taskInfo->status . '-' . $curSnStatus['sn'] . '-' . 4;//task状态拼接sn拼接status

                            $taskInfo->save();
                            $supervisor_task_info->save();
                        } else {
                            //收款记录(工人)
                            $is_ordered_designer_90 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['title'] . '80%款', 2, 1, $curSnStatus['project_type']);
                            $is_ordered_designer_10 = platformOrderModel::sepbountyOrder($curSnStatus['to_uid'], $curSnStatus['price'] * 0.2, $data['task_id'], $curSnStatus['title'] . '20%款(竣工结算)', 2, 0, $curSnStatus['project_type']);
                            $increment_designer     = $this->bounty($curSnStatus['price'] * 0.8, $data['task_id'], $curSnStatus['to_uid'], 1, 2);
                            //推送
                            change_status_msg($curSnStatus['to_uid'],$curSnStatus['title']);
                            //推送给管家
                            change_status_msg($house_keeper_uid,$curSnStatus['title']);
                        }
                    }

                    WorkOfferModel::where('id', $curSnStatus['id'])->update(['status' => 4]);

                }

            });
            return $status;


    }


    /**
     * 结算管家的辅材费用
     */
    public function getHouseKeeperAuxiliary($task_id , $project_type , $cash , $uid , $order_id , $order_code){
        $auxiliaryTitle = '';
        if($project_type == 3){
            $auxiliaryTitle = '防水验收通过30%辅材款';
        }
        if($project_type == 5){
            $auxiliaryTitle = '木工验收通过30%辅材款';
        }
        if($project_type == 7){
            $auxiliaryTitle = '竣工验收通过40%辅材款';
        }

        platformOrderModel::sepbountyOrder($uid, $cash, $task_id, $auxiliaryTitle, 2, 1, $project_type , 1);
//        platformOrderModel::sepbountyOrder(255, $cash, $task_id, $auxiliaryTitle, 1, 1, $project_type , 1);
    }


    /**
     * @param $uid_boss
     * @param $house_or_sup_price
     * @param $title
     * @param $project_type
     * @return bool
     * 整合管家和监理三步扣费的代码
     */
    public function houseKeeperAndSupervisorGetMoney($uid_boss, $house_or_sup_price, $title, $project_type, $task_id, $house_or_sup_uid, $user_type = 3) {

        //扣费系数以及title
        if ($project_type == 7) {
            $coefficient = 0.4;
            if ($user_type == 4) {
                $lan = '支付监理40%款';
            } else {
                $lan = '支付管家40%款';
            }

        } else {
            $coefficient = 0.3;
            if ($user_type == 4) {
                $lan = '支付监理30%款';
            } else {
                $lan = '支付管家30%款';
            }
        }


        //业主sub_order扣费
        $is_ordered_house_keeper = platformOrderModel::sepbountyOrder($uid_boss, $house_or_sup_price * $coefficient, $task_id, $title . $lan . "(冻结金->工作者)", 1, 1, $project_type);

        //管家在sub_order的订单编号
        $house_keeper_code = $is_ordered_house_keeper->code;

        //扣业主冻结资金
        $decrement_house_keeper = $this->bounty($house_or_sup_price * $coefficient, $task_id, $uid_boss, 1, 6, true);

        //管家收入订单
        $is_ordered_house_keeper = platformOrderModel::sepbountyOrder($house_or_sup_uid, $house_or_sup_price * $coefficient, $task_id, $title . $lan, 2, 1, $project_type);

        //管家余额增加(多传个参数证明是管家,余额增加)
        $increment_house_keeper = $this->bounty($house_or_sup_price * $coefficient, $task_id, $house_or_sup_uid, 1, 2);

        if (!$decrement_house_keeper || !$increment_house_keeper) {
            return true;
        } else {
            return false;
        }


    }


}