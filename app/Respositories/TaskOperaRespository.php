<?php

namespace App\Respositories;

use App\Modules\Manage\Model\LevelModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\workDesignerLog;
use App\Modules\Task\Model\WorkModel;
use App\Modules\User\Model\CommentModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\User\Model\UserFocusModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\Order\Model\OrderModel as platformOrderModel;
use App\Modules\Order\Model\OrderModel;

use App\PushSentenceList;
use App\PushServiceModel;
use DB;

class TaskOperaRespository
{

    /**
     * @param $task_id
     * @param $to_uid
     * @param $project_conf_list
     * @param $sn
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     *
     */
    public function workSubmit($task_id, $to_uid, $project_conf_list, $sn, $city_id)
    {
//        var_dump($project_conf_list);exit($project_conf_list);
        $task_data = TaskModel::find($task_id);
        $taskUserType = $task_data->user_type;       //任务需要的角色
        $boss_uid = $task_data->uid;
        $work = WorkModel::where('status', 2)->where('task_id', $task_id)->first();

        $userInfo = UserModel::where('id', $to_uid)->first()->user_type;
        if ($taskUserType != $userInfo)
            return ['status' => false, 'errMsg' => '任务和用户对应不上'];

        // 设计师的操作
        if ($taskUserType == 2) {
            $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $task_id)
                ->where('to_uid', $to_uid)
                ->where('sn', '>', 0)
                ->get()->toArray();

            foreach ($ret as $key => $value) {
                //看前一个阶段是不是4,不是的话直接打回去
                if (WorkOfferModel::select('status')->where('sn', $value['sn'] - 1)->where('task_id', $task_id)->first()->status != 4) {
                    return ['status' => true, 'errMsg' => '非法提交'];
                }

                if ($value['status'] == 0 || $value['status'] == 3) {
                    if ($value['sn'] == 2) {
                        //5泥水工,6木工,7水电工
                        if (empty($project_conf_list)) return ['status' => false, 'errMsg' => '未提交配置单'];
                        if (empty($city_id)) return ['status' => false, 'errMsg' => '未提交辅材包城市'];
                        foreach ($project_conf_list as $k => $v) {
                            foreach ($v as $n => $m) {
                                $work_type[] = ProjectConfigureModel::find($m['child_id'])->work_type;
                            }
                        }
                        $count = count(array_unique($work_type));
                        if ($count <= 1) {
                            return ['status' => false, 'errMsg' => '您只提交了水电阶段的泥水工或水电工项目,请补全配置单'];
                        }
                        $data_insert = array(
                            'project_con_list' => serialize($project_conf_list),
                            'task_id' => $task_id,
                            'city_id' => $city_id,
                        );
                        $is_created = ProjectConfigureTask::where('task_id', $task_id)->first();
                        WorkOfferModel::where('id', $value['id'])->update(['status' => 1]);
                        //判断有没有管家的单子,有的话就推给管家
                        $taskInfo = TaskModel::where('id', $task_id)->first();//设计师的单子
                        $task_house_data = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 3)->first();
                        if (empty($is_created)) {
                            //新增配置单成功
                            if (ProjectConfigureTask::create($data_insert)) {
                                //有管家的单子(推送)
                                if (!empty($task_house_data)) {
                                    //推给管家
                                    $house_work = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->where('status', 4)->first();
                                    if (!empty($house_work)) {
                                        $house_uid = $house_work->to_uid;
                                        $application = 20002;
                                        push_accord_by_equip($house_uid, $application, 'message_designer_sub_list', '', $task_id);
                                    }
                                }
                                //推给业主
                                $application = 40011;
                                push_accord_by_equip($boss_uid, $application, 'message_designer_sub_list', $value['title'], $task_id);
                                return ['status' => true, 'successMsg' => '深化设计提交成功'];
                            } else {
                                return ['status' => true, 'errMsg' => '深化设计提交失败'];
                            }
                        } else {
                            $is_created->project_con_list = $data_insert['project_con_list'];
                            $is_created->task_id = $data_insert['task_id'];
                            $is_created->city_id = $data_insert['city_id'];
                            $res_insert = $is_created->save();
                            if ($res_insert) {
                                //有管家的单子
                                if (!empty($task_house_data)) {
                                    //推给管家
                                    $house_work = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->where('status', 4)->first();
                                    if (!empty($house_work)) {
                                        $house_uid = $house_work->to_uid;
                                        $application = 20002;
                                        push_accord_by_equip($house_uid, $application, 'message_designer_sub_list', '', $task_id);
                                    }
                                }
                                //推给业主
                                $application = 40011;
                                push_accord_by_equip($boss_uid, $application, 'message_designer_sub_list', $value['title'], $task_id);
                                return ['status' => true, 'successMsg' => '深化设计提交成功'];
                            } else {
                                return ['status' => true, 'errMsg' => '深化设计提交失败'];
                            }
                        }
                    }
                    WorkOfferModel::where('id', $value['id'])->update(['status' => 1]);
                    break;
                }
            }
            return ['status' => true, 'successMsg' => '提交成功'];
        }


        // 管家提交验收的操作（验收的操作）
        if ($taskUserType == 3) {
            $data['sn'] = $sn;
            if (empty($data['sn'])) {
                return ['status' => false, 'errMsg' => '缺少参数'];
            }
            // 判断任务去到哪一个工程阶段
            $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $task_id)
                ->where('project_type', '>', 0)
                ->where('status', 0)
                ->orderBy('project_type', 'ASC')
                ->first();
            if (empty($ret)) {
                return ['status' => false, 'errMsg' => '未达到验收阶段'];
            }
            if (($data['sn'] + 1) != $ret->sn) {
                return ['status' => false, 'errMsg' => '步骤流程对应不上'];
            }
            //判定下此次提交是否换过人(前端传过来的sn是减了1的)
            $data_labor = ProjectLaborChange::where('task_id', $task_id)->where('sn', $data['sn'] + 1)->orderBy('id', 'desc')->first();
            //如果找不到记录,说明他没更换过人
            if (!empty($data_labor) && $data_labor->status == 6) {//如果此阶段已换过人,改成0,让前端重新提交
                $data_labor->status = 0;
                $data_labor->save();
                WorkOfferModel::where('id', $ret->id)->update(['status' => 1]);     //管家提交
            } else {
                WorkOfferModel::where('id', $ret->id)->update(['status' => 1]);
            }
            //推送(给监理)
            $project_position = $task_data->project_position;
            $super_task = TaskModel::select('id')->where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 4)->first();//监理的单
            $visor_work = WorkOfferModel::select('to_uid')->where('sn', 0)->where('task_id', $super_task->id)->first();
            $data_user['visor_uid'] = empty($visor_work) ? 255 : $visor_work->to_uid;
            change_status_msg($data_user['visor_uid'], $ret['title']);
            return ['status' => true, 'successMsg' => '提交验收申请成功'];
        }
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     */

    public function handling_money($boss_uid, $designer_id, $price_change, $task_id, $title, $work_id, $sn, $boss_refuse_reason_id = 0) {

        //业主返钱
        $all_price = WorkOfferModel::select('price')->where('work_id', $work_id)->where('task_id', $task_id)->where('sn', 0)->first()->price;//找到总价

        if ($sn == 1) {
            $is_ordered = OrderModel::sepbountyOrder($boss_uid, $all_price - $price_change, $task_id, $title . '取消订单返钱(冻结金->余额)', 2);

            $increment_boss        = TaskModel::bounty($all_price - $price_change, $task_id, $boss_uid, $is_ordered->code, 1, 7);//深化和施工的钱要退到业主余额
            $decrement_boss_frozen = TaskModel::bounty($all_price - $price_change, $task_id, $boss_uid, $is_ordered->code, 1, 5, true);//业主冻结金减少
            //扣款记录(业主)
            $decrement = TaskModel::bounty($price_change, $task_id, $boss_uid, $is_ordered->code, 1, 5, true);

            //收款记录(设计师)
            $is_ordered_designer = OrderModel::sepbountyOrder($designer_id, $price_change, $task_id, $title . '取消订单(设计师余额增加)',2);
            $increment_designer = TaskModel::bounty($price_change, $task_id, $designer_id, $is_ordered_designer->code, 1, 2);
            $final_status       = true;

        } elseif ($sn == 2) {
            $end_price   = WorkOfferModel::select('price')->where('work_id', $work_id)->where('task_id', $task_id)->where('sn', 3)->first()->price;//找到施工的钱
            $first_price = WorkOfferModel::select('price')->where('work_id', $work_id)->where('task_id', $task_id)->where('sn', 1)->first()->price;//找到初步的钱

            $is_ordered = OrderModel::sepbountyOrder($boss_uid, $end_price, $task_id, $title . '取消订单返钱(冻结金->余额)', 2);

            $increment_boss        = TaskModel::bounty($end_price, $task_id, $boss_uid, $is_ordered->code, 1, 7);//施工的钱要退到业主余额
            $decrement_boss_frozen = TaskModel::bounty($end_price, $task_id, $boss_uid, $is_ordered->code, 1, 5, true);//业主冻结金减少
            //扣款记录(业主)
            $decrement = TaskModel::bounty($price_change, $task_id, $boss_uid, $is_ordered->code, 1, 5, true);

            //收款记录(设计师)
            $is_ordered_designer = OrderModel::sepbountyOrder($designer_id, $price_change, $task_id, $title . '取消订单(设计师余额增加)', 2);
            $increment_designer  = TaskModel::bounty($price_change, $task_id, $designer_id, $is_ordered_designer->code, 1, 2);

//            $is_ordered_designer_first = OrderModel::sepbountyOrder($designer_id, $first_price, $task_id, $title . '取消订单(初步设计冻结金转入)');
            $decrement_designer_frozen = TaskModel::bounty($first_price, $task_id, $designer_id, $is_ordered->code, 1, 5, true);//初步设计冻结金扣除
            $increment_designer_first  = TaskModel::bounty($first_price, $task_id, $designer_id, $is_ordered->code, 1, 2);//初步设计冻结金扣除,转入余额
            if ($increment_designer_first && $decrement_designer_frozen) {
                $final_status = true;
            } else {
                $final_status = false;
            }

        } elseif ($sn == 3) {
            //最后阶段取消,根据业主的评价来结算设计师的工资

            switch ($boss_refuse_reason_id) {
                case 1:
                    $rate = 0.5;
                    break;
                case 2:
                    $rate = 0.75;
                    break;
                case 3:
                    $rate = 1;
                    break;
                default:
                    $rate = 0.75;
            }

            $second_price = WorkOfferModel::select('price')->where('work_id', $work_id)->where('task_id', $task_id)->where('sn', 2)->first()->price;//找到深化的钱
            $first_price  = WorkOfferModel::select('price')->where('work_id', $work_id)->where('task_id', $task_id)->where('sn', 1)->first()->price;//找到初步的钱

            //收款记录(设计师)
            $is_ordered_designer = OrderModel::sepbountyOrder($designer_id, $price_change * $rate, $task_id, $title . '取消订单(余额增加,业主选择结算' . ($rate * 100) . '%)', 2);

//            $is_ordered_designer_first  = OrderModel::sepbountyOrder($designer_id, $first_price, $task_id, $title . '取消订单(初步设计冻结金转入设计师)');
//            $is_ordered_designer_second = OrderModel::sepbountyOrder($designer_id, $second_price, $task_id, $title . '取消订单(深化设计冻结金转入设计师)');

            $decrement_designer_frozen = TaskModel::bounty($first_price, $task_id, $designer_id, $is_ordered_designer->code, 1, 5, true);//初步设计冻结金扣除
            $increment_designer_first  = TaskModel::bounty($first_price, $task_id, $designer_id, $is_ordered_designer->code, 1, 2);//初步设计冻结金扣除,转入余额

            $decrement_designer_frozen_second = TaskModel::bounty($second_price, $task_id, $designer_id, $is_ordered_designer->code, 1, 5, true);//深化设计冻结金扣除
            $increment_designer_second        = TaskModel::bounty($second_price, $task_id, $designer_id, $is_ordered_designer->code, 1, 2);//深化设计冻结金扣除,转入余额


            $increment_designer = TaskModel::bounty($price_change * $rate, $task_id, $designer_id, $is_ordered_designer->code, 1, 2);//施工指导余额转入
            //扣款记录(业主)
//            $is_ordered_boss = OrderModel::sepbountyOrder($boss_uid, $price_change, $task_id, $title . '取消订单(设计师余额增加,您选择结算了此阶段' . ($rate * 100) . '%)费用');

            //扣除的金额要判断
            $decrement = TaskModel::bounty($price_change * $rate, $task_id, $boss_uid, $is_ordered_designer->code, 1, 5, true);
            //如果扣了0.5或0.75,要返钱给业主
            if ($rate < 1) {
                $is_ordered = OrderModel::sepbountyOrder($boss_uid, $price_change * (1 - $rate), $task_id, $title . '取消订单返钱(冻结金->余额)', 2);

                $increment_boss        = TaskModel::bounty($price_change * (1 - $rate), $task_id, $boss_uid, $is_ordered->code, 1, 7);//剩余的钱要退到业主余额
                $decrement_boss_frozen = TaskModel::bounty($price_change * (1 - $rate), $task_id, $boss_uid, $is_ordered->code, 1, 5, true);//业主冻结金减少
            } else {
                $increment_boss = $decrement_boss_frozen = true;
            }

            if ($increment_designer_first && $decrement_designer_frozen && $decrement_designer_frozen_second && $increment_designer_second && $increment_boss && $decrement_boss_frozen) {
                $final_status = true;
            } else {
                $final_status = false;
            }
            $increment_boss = $decrement_boss_frozen = true;
        }


        if ($increment_boss && $decrement_boss_frozen && $increment_designer && $decrement && $final_status) {
            return true;
        } else {
            return false;
        }

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
        $decrement_house_keeper = TaskModel::bounty($house_or_sup_price * $coefficient, $task_id, $uid_boss, $house_keeper_code, 1, 6, true);

        //管家收入订单
        $is_ordered_house_keeper = platformOrderModel::sepbountyOrder($house_or_sup_uid, $house_or_sup_price * $coefficient, $task_id, $title . $lan, 2, 1, $project_type);

        //管家冻结金增加(多传个参数证明是管家,需要冻结增加而不是余额增加)
        $increment_house_keeper = TaskModel::bounty($house_or_sup_price * $coefficient, $task_id, $house_or_sup_uid, $house_keeper_code, 1, 2, false, false, true);

        if (!$decrement_house_keeper || !$increment_house_keeper) return true;
        return false;

    }


    /**
     * 评价工人
     */
    public function evaluateWorker($v, $work_offer_id) {

        $workerInfo      = UserDetailModel::where('uid', $v['worker_id'])->first();
        $work_offer_data = WorkOfferModel::find($work_offer_id);

        if (empty($work_offer_data)) {
            return false;
        }
        if ($v['score'] <= 2) {
            $score = $workerInfo['score'] - 1;
            $score = $score > 0 ? $score : 0;
        }

        if ($v['score'] == 3 || $v['score'] == 4) {
            $score = $workerInfo['score'];
        }

        if ($v['score'] == 5) {
            $score = $workerInfo['score'] + 1;
        }

//      工人

        $work_type = $workerInfo['work_type'];
        $levelData = DB::table('level')->where('type', $work_type)->first();
        if (empty($levelData)) {
            return false;
        }
        $levelInfo = $levelData->upgrade;
        if (!empty($levelInfo)) {
            $upgrade = json_decode($levelInfo, true);
            krsort($upgrade);
        } else {
            return false;
        }
        $star = $workerInfo['star'];
        foreach ($upgrade as $key => $value) {
            if ($score >= intval($value)) {
                $star = $key;
                break;
            }
        }

        $updateArr['score'] = $score;

        if ($star > $workerInfo['star'] || $star < $workerInfo['star']) {
            $updateArr['star'] = $star;
        }

        $data_comment                     = [
            'total_score' => $v['score'],
            'work_offer_id' => $work_offer_id,
            'comment' => $v['comment'],
            'comment_by' => 1,
            'task_id' => $work_offer_data->task_id,
            'from_uid' => $work_offer_data->from_uid,
            'to_uid' => $v['worker_id'],
            'created_at' => date('Y-m-d H:i:s',time()),
        ];
        $work_offer_data->evaluate_status = 1;
        $work_offer_data->save();
        $status = DB::transaction(function () use ($data_comment, $updateArr, $v) {
            $res_comment   = CommentModel::where('work_offer_id', $data_comment['work_offer_id'])->first();
            if (empty($res_comment)) {
                CommentModel::create($data_comment);
            }else {
                CommentModel::where('work_offer_id', $data_comment['work_offer_id'])->update($data_comment);
            }

            UserDetailModel::where('uid', $v['worker_id'])->update($updateArr);
        });
        if (is_null($status)) {
            //推送给工人
//            small_order_to_worker($v['worker_id'],50007,'message_evaluate_worker',$work_offer_data->title);
            push_accord_by_equip($v['worker_id'],50007,'message_evaluate_worker',$work_offer_data->title,$work_offer_data->task_id);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 评价管家,监理
     */
    public function evaluateHouser($worker_id, $score_get, $work_offer_id, $comment, $task_id, $work_offer_info) {
        $workerInfo  = UserDetailModel::where('uid', $worker_id)->first();
        $worker_role = UserModel::where('id', $worker_id)->first()->user_type;

        //推送给对应工作者
//        small_order_to_worker($worker_id,'50007','message_evaluate_worker',$work_offer_info->title);
        push_accord_by_equip($worker_id,'50007','message_evaluate_worker',$work_offer_info->title,$task_id);
//        if (!empty($res_comment)) {
//              return false;
//        }
        //设计师
        if ($worker_role == 2) {
            $data_comment = [
                'total_score' => $score_get,
                'work_offer_id' => $work_offer_id,
                'comment' => $comment,
                'comment_by' => 1 ,
                'task_id' => $task_id,
                'from_uid' => $work_offer_info->from_uid,
                'to_uid' => $worker_id,
                'created_at' => date('Y-m-d H:i:s',time()),
            ];

            $status = DB::transaction(function () use ($data_comment, $worker_id, $work_offer_info) {
                CommentModel::create($data_comment);
                $work_offer_info->evaluate_status = 1;
                $work_offer_info->save();
            });
        } else {
            if ($score_get <= 2) {
                $score = $workerInfo['score'] - 1;
                $score = $score > 0 ? $score : 0;
            }

            if ($score_get == 3 || $score_get == 4) {
                $score = $workerInfo['score'];
            }

            if ($score_get == 5) {
                $score = $workerInfo['score'] + 1;
            }

            //      管家
            if ($worker_role == 3) {
                $work_type = 1;
            }

            //      监理
            if ($worker_role == 4) {
                $work_type = 2;
            }

            $levelInfo = DB::table('level')->where('type', $work_type)->first()->upgrade;


            if (!empty($levelInfo)) {
                $upgrade = json_decode($levelInfo, true);
                krsort($upgrade);
            } else {
                return false;
            }


            $star = $workerInfo['star'];
            foreach ($upgrade as $key => $value) {
                if ($score >= intval($value)) {
                    $star = $key;
                    break;
                }
            }


            $updateArr['score'] = $score;

            if ($star > $workerInfo['star'] || $star < $workerInfo['star']) {
                $updateArr['star'] = $star;
            }

            $data_comment = [
                'total_score' => $score_get,
                'work_offer_id' => $work_offer_id,
                'comment' => $comment,
                'comment_by' => 1,
                'task_id' => $task_id,
                'from_uid' => $work_offer_info->from_uid,
                'to_uid' => $worker_id,
                'created_at' => date('Y-m-d H:i:s',time()),
            ];

            $status = DB::transaction(function () use ($data_comment, $updateArr, $worker_id, $work_offer_info) {
                $res_comment   = CommentModel::where('work_offer_id', $data_comment['work_offer_id'])->first();
                if (empty($res_comment)) {
                    CommentModel::create($data_comment);
                }else {
                    CommentModel::where('work_offer_id', $data_comment['work_offer_id'])->update($data_comment);
                }

                UserDetailModel::where('uid', $worker_id)->update($updateArr);
                $work_offer_info->evaluate_status = 1;
                $work_offer_info->save();
            });
        }

        if (is_null($status)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 设计师取消订单
     */
    public function cancelOrderDesigner($data, $task, $uid) {
        //是否有人接单
        $work_count = workDesignerLog::where('task_id', $data['task_id'])->where('is_refuse', 1)->count();

        //手续费，到系统配置里面找
        $price_poundage = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;

        //没人接单,返钱
        if (empty($work_count)) {
            $is_ordered            = OrderModel::where('task_id', $data['task_id'])->first();
            $sub_order_res         = platformOrderModel::sepbountyOrder($uid, $price_poundage, $data['task_id'], '无人接单,预约金清退(冻结金->余额)', 2);
            $increment             = TaskModel::bounty($price_poundage, $data['task_id'], $uid, $is_ordered->code, 1, 7);//业主余额增加
            $decrement_boss_frozen = TaskModel::bounty($price_poundage, $data['task_id'], $uid, $is_ordered->code, 1, 5, true);//业主冻结金减少
            $task->cancel_order    = $task->status . '-' . '0' . '-' . '0';//task状态拼接sn
            if (!empty($task)) {
                $task->status = 9;
                $res_task     = $task->save();
            } else {
                $res_task = false;
            }

            if ($increment && $res_task && $sub_order_res && $decrement_boss_frozen) {
                return ['status' => true, 'successMsg' => '取消成功'];
//                return response()->json(['success' => '取消成功'], '200');
            } else {
                return ['status' => false, 'errMsg' => '取消失败'];
//                return response()->json(['error' => '取消失败'], '200');
            }
        } else {
            // 该项目做到哪个阶段
            $work = WorkModel::where('task_id', $data['task_id'])->where('status', '>=', 1)->first();//找到该任务(设计师报价,业主确认)
            //如果是设计师报价阶段就取消订单,扣手续费,结束订单,work表的status为0,offer表无数据
            //设计师报价,业主确认,未付款取消,扣手续费,结束订单,work表的status为0,offer表无数据
            //设计师报价,业主确认,付款取消,扣手续费,结束订单,work表的status为1,offer表有一条数据
            //设计师初步设计,业主不满意,取消,扣手续费和初步设计费,结束订单,work表的status为2,offer表有4条数据

            //设计师接受任务,业主没确认
            if ($work_count && empty($work)) {
                $task->cancel_order = $task->status . '-' . '0' . '-' . '0';//task状态拼接sn
                $task->status       = 9;
                if (!empty($work)) {
                    $work->status = 3;
                    $save_work    = $work->save();
                } else {
                    $save_work = true;
                }

                $is_ordered    = OrderModel::where('task_id', $data['task_id'])->first();
                $sub_order_res = platformOrderModel::sepbountyOrder($uid, $price_poundage, $data['task_id'], '取消订单,预约金扣除(冻结金->系统账户)', 1);
                $decrement     = TaskModel::bounty($price_poundage, $data['task_id'], $uid, $is_ordered->code, 1, 5, true);//冻结金20元减少
                $system_id     = 1;
                //收款记录(系统收款)
                $is_ordered_system = OrderModel::sepbountyOrder($system_id, $price_poundage, $data['task_id'], '取消订单(系统账户余额增加)', 2);
                $increment_system  = TaskModel::bounty($price_poundage, $data['task_id'], $system_id, $is_ordered_system->code, 1, 2);

                if ($task->save() && $save_work && $sub_order_res && $decrement && $increment_system) {
                    return ['status' => true, 'successMsg' => '取消成功'];
//                    return response()->json(['success' => '取消成功'], '200');
                } else {
                    return ['status' => false, 'errMsg' => '取消失败'];
//                    return response()->json(['error' => '取消失败,状态不对'], '500');
                }
            }
            //业主只确认了人,没付款
            if ($work->status == 1) {
                $task->cancel_order = $task->status . '-' . '0' . '-' . '0';//task状态拼接sn
                $task->status       = 9;
                $work->status       = 3;
                $save_work          = $work->save();
                $is_ordered         = OrderModel::where('task_id', $data['task_id'])->first();
                $sub_order_res      = platformOrderModel::sepbountyOrder($uid, $price_poundage, $data['task_id'], '取消订单,预约金扣除(冻结金->系统账户)', 1);
                $decrement          = TaskModel::bounty($price_poundage, $data['task_id'], $uid, $is_ordered->code, 1, 5, true);//冻结金20元减少
                $system_id          = 1;
                //收款记录(系统收款)
                $is_ordered_system = OrderModel::sepbountyOrder($system_id, $price_poundage, $data['task_id'], '取消订单(系统账户余额增加)', 2);
                $increment_system  = TaskModel::bounty($price_poundage, $data['task_id'], $system_id, $is_ordered_system->code, 1, 2);

                if ($task->save() && $save_work && $sub_order_res && $decrement && $increment_system) {
                    return ['status' => true, 'successMsg' => '取消成功'];
//                    return response()->json(['success' => '取消成功'], '200');
                } else {
                    return ['status' => false, 'errMsg' => '取消失败'];
//                    return response()->json(['error' => '取消失败,状态不对'], '500');
                }
            }

            // 该项目做到哪个阶段
            $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])
                ->where('sn', '>', 0)
                ->get()->toArray();

            if (empty($ret)) return ['status' => false, 'errMsg' => '找不到该任务'];
//            return  response(['error' => '找不到该任务'], '500');
            if ($task->status == 7) {

                foreach ($ret as $key => $value) {
                    //1为设计师提交状态,3为设计师驳回状态
                    if ($value['status'] == 1 || $value['status'] == 3) {

                        //设计师提交作品,钱就先打给他
                        if ($value['sn'] == 1) {//初步设计

                            $count_submit = WorkOfferModel::where('id', $value['id'])->first()->count_submit;

                            if ($count_submit == 0) {
                                $count_submit_res = WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit, 'status' => 3]);
                            } else {
                                $count_submit_res = false;
                            }


                            if ($count_submit_res) {
                                $res_handle                  = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                                $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                                $task->status                = 9;
                                $work->status                = 3;
                                $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                                if ($res_handle && $task->save() && $work->save()) {
                                    // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                    WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                    return ['status' => true, 'successMsg' => '取消成功'];
//                                    return response()->json(['success' => '取消成功'], '200');
                                } else {
                                    return ['status' => false, 'errMsg' => '取消失败'];
//                                    return response()->json(['error' => '取消失败'], '500');
                                }

                            }
                        }

                        if ($value['sn'] == 2) {//深化设计
                            $count_submit     = WorkOfferModel::where('id', $value['id'])->first()->count_submit;
                            $count_submit_res = WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit, 'status' => 3]);
                        }

                        //深化设计直接点取消
                        if (!empty($data['boss_refuse']) && $value['sn'] == 2) {//深化设计

                            $count_submit     = WorkOfferModel::where('id', $value['id'])->first()->count_submit;
                            $count_submit_res = WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit, 'status' => 3]);

                            $res_handle                  = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }

                        }

                        //深化设计打回修改
                        if ($value['sn'] == 2 && $count_submit == 3) {

                            $res_handle                  = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }

                        } else {
                            $msg = ['msg' => '已要求设计师重新修改深化设计', 'count_submit' => $count_submit];
                            if ($count_submit_res) {
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return ['status' => true, 'successMsg' => $msg];
//                                return response()->json($msg, '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }
                        }

                        //提交施工指导点取消
                        if ($value['sn'] == 3) {

                            $res_handle         = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }
                        }


                    }

                    //在还没提交的状态
                    if ($value['status'] == 0) {

                        if ($value['sn'] == 1) {

                            $res_handle         = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $front_sn           = $value['sn'] - 1;
                            $task->cancel_order = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }
                        }
                        if ($value['sn'] == 2) {

                            $res_handle         = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $front_sn           = $value['sn'] - 1;
                            $task->cancel_order = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }
                        }
                        if ($value['sn'] == 3) {
                            if (empty($data['boss_refuse_reason_id']))
                                return ['status' => false, 'errMsg' => '未选择取消原因,无法进行结算'];
//                            return response()->json(['error' => '未选择取消原因,无法进行结算'], '500');
                            $res_handle                  = $this->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn'], $data['boss_refuse_reason_id']);
                            $front_sn                    = $value['sn'] - 1;
                            $task->cancel_order          = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                return ['status' => true, 'successMsg' => '取消成功'];
//                                return response()->json(['success' => '取消成功'], '200');
                            } else {
                                return ['status' => false, 'errMsg' => '取消失败'];
//                                return response()->json(['error' => '取消失败'], '500');
                            }
                        }
                    }

                }

            }
        }

    }
}