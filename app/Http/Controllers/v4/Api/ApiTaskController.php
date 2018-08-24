<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\Finance\Model\CashoutModel;
use App\Modules\Project\MerchantDetail;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\Task\Model\Auxiliary;
use App\Modules\Task\Model\WorkAttachmentModel;
use App\Modules\Task\Model\WorkOfferApply;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\User\Model\CommentModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\User\Model\RefuseReasonModel;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Project\ProjectWorkOfferChange;
use App\Modules\Task\Model\ProjectSmallOrder;
use App\PushSentenceList;
use App\PushServiceModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\workDesignerLog;
use App\Modules\Employ\Models\EmployModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Order\Model\SubOrderModel;
use App\Modules\Order\Model\OrderModel;
use App\Modules\Manage\Model\MessageTemplateModel;
use App\Modules\User\Model\MessageReceiveModel;
use App\Modules\Task\Model\WorkOfferModel;
use DB;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\User\Model\UserFocusModel;
use App\Modules\Order\Model\OrderModel as platformOrderModel;
use Illuminate\Support\Facades\Config;
use App\Modules\User\Model\UsersMessageSendModel;
use Log;
use App\Modules\Employ\Models\UnionAttachmentModel;

use App\Respositories\TaskAppointRespository;
use App\Respositories\PayWorkerRespository;
use App\Respositories\TaskRespository;
use App\Respositories\TaskOperaRespository;
use App\Respositories\TaskSelectRepository;
use Cache;

class ApiTaskController extends BaseController {

    protected $taskAppointRepository;
    protected $payWorkerRespository;
    protected $taskRespository;
    protected $taskOperaRepository;
    protected $taskSelectRepository;

    public function __construct(TaskAppointRespository $taskAppointRepository, PayWorkerRespository $payWorkerRespository, TaskRespository $taskRespository, TaskOperaRespository $taskOperaRespository, TaskSelectRepository $taskSelectRepository) {
        $this->taskAppointRepository = $taskAppointRepository;
        $this->payWorkerRespository  = $payWorkerRespository;
        $this->taskRespository       = $taskRespository;
        $this->taskOperaRepository   = $taskOperaRespository;
        $this->taskSelectRepository  = $taskSelectRepository;
    }

    // 发抢单任务
    public function userCreateTask(Request $request) {


        // TODO 从后台获取可输入的选项 ，这些选项的字段必须为数字型
        $setting = array(
            'bedroom' => '居室',   //居室
            'living_room' => '客厅',   //客厅
            'kitchen' => '厨房',   //厨房
            'washroom' => '卫生间',   //卫生间
            'balcony' => '阳台'      //  阳台
        );

        $data['room_config'] = '';
        foreach ($setting as $key => $value) {
            $data[$key] = !empty($request->json($key)) ? $request->json($key) : 0;
            if (!empty($request->json($key))) {
                $data['room_config'] .= $request->json($key) . $setting[$key] . ',';
            }
        }

        $data['uid']              = $request->json('user_id');              //发布人id
        $data['favourite_style']  = $request->json('favourite_style');     //喜好风格  风格是否可以固定？
        $data['user_type']        = $request->json('user_type');           // 想要选择的服务类型  2设计师 3管家 4监理
        $data['project_position'] = $request->json('project_position');               // 工程id
        $data['status']           = 0; //接单状态   用本来表的字段，参考下面注释
        $data['desc']             = \CommonClass::removeXss($request->json('description'));      //备注
        $data['created_at']       = date('Y-m-d H:i:s', time());                  //建立时间
        $data['show_cash']        = $request->json('show_cash');       //即预算
        $data['housekeeperStar']  = !empty($request->json('housekeeperStar')) ? $request->json('housekeeperStar') : '1';//用户要求的星级（管家端和监理端需要传的字段）
//        $data['workerStar']       = !empty($request->json('workerStar')) ? $request->json('workerStar') : '1';//管家端需要传的字段

//   任务状态:0暂不发布 1已经发布,未付发布费 2已经付发布费 3审核通过 4威客交稿 5雇主选稿 6任务公示 7交付验收 8双方互评 9已结束 10失败 11维权

//     2.设计师订单设计师流程中增加上传图片（PC），用户增加查看设计图片。
        foreach ($data as $key => $value) {
            if (empty($value) && $value != 0) {
                return $this->error('工程信息不可为空或工地不存在');
            }
        }
        //(未付款的单子删除)
        $not_pay_task = TaskModel::where('project_position', $data['project_position'])
            ->where('user_type', $data['user_type'])
            ->where('bounty_status', '=', 0)
            ->where('task.status', '<=', 3)->get();

        if (!empty($not_pay_task)) {
            foreach ($not_pay_task as $k => $v) {
                $res = TaskModel::find($v->id)->delete();
            }
        }
        //project_position是否在进行中了
        $count_project_position = TaskModel::where('project_position', $data['project_position'])->where('user_type', $data['user_type'])->where('status', '>=', 3)->where('status', '<', 9)->count();

        if ($count_project_position) return $this->error('该地址已存在进行中的任务');

        //无论是设计师还是管家,建立,手续费都是统一的
        $data['product'][] = ServiceModel::select('id')->where('identify', 'SHOUXUFEI')->first()->id;//手续费
        $data['square']    = ProjectPositionModel::where('id', $data['project_position'])->first()->square; //房屋面积


        // TODO 这里从配置找可以中标的数量设置
        $data['worker_num'] = 3;
        // TODO 也可使用增值服务来代替手续费， 后期考虑
        //  TODO 这里要判断的是可用余额
        $data['title'] = ProjectPositionModel::where('id', $data['project_position'])->first()->project_position;
        $result        = TaskModel::createTask($data);
        if ($result) {
            return $this->success($result->id);
        }
        return $this->error('任务发布失败');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 发约单请求
     */

    public function userCreateAppointTask(Request $request) {


        // TODO 从后台获取可输入的选项 ，这些选项的字段必须为数字型
        $setting = array(
            'bedroom' => '居室',   //居室
            'living_room' => '客厅',   //客厅
            'kitchen' => '厨房',   //厨房
            'washroom' => '卫生间',   //卫生间
            'balcony' => '阳台'      //  阳台
        );

        $data['room_config'] = '';
        foreach ($setting as $key => $value) {
            $data[$key] = !empty($request->json($key)) ? $request->json($key) : 0;
            if (!empty($request->json($key))) {
                $data['room_config'] .= $request->json($key) . $setting[$key] . ',';
            }
        }

        $data['uid']              = $request->json('user_id');              //发布人id
        $data['favourite_style']  = $request->json('favourite_style');     //喜好风格  风格是否可以固定？
        $data['user_type']        = $request->json('user_type');           // 想要选择的服务类型  2设计师 3管家 4监理
        $data['project_position'] = $request->json('project_position');               // 工程id
        $data['status']           = 0; //接单状态   用本来表的字段，参考下面注释
        $data['type_id']          = $request->json('task_model'); //抢单是1,约单是2
        $data['desc']             = \CommonClass::removeXss($request->json('description'));      //备注
        $data['created_at']       = date('Y-m-d H:i:s', time());                  //建立时间
        $data['show_cash']        = $request->json('show_cash') ? $request->json('show_cash') : '10000';       //即预算
        $data['housekeeperStar']  = !empty($request->json('housekeeperStar')) ? $request->json('housekeeperStar') : '1';//用户要求的星级（管家端和监理端需要传的字段）
        $data_designer['uid']     = (array)$request->json('designer_id');//可能为多个  //约单人数限制


//  $data['workerStar']       = !empty($request->json('workerStar')) ? $request->json('workerStar') : '1';//管家端需要传的字段

//   任务状态:0暂不发布 1已经发布,未付发布费 2已经付发布费 3审核通过 4威客交稿 5雇主选稿 6任务公示 7交付验收 8双方互评 9已结束 10失败 11维权

//     2.设计师订单设计师流程中增加上传图片（PC），用户增加查看设计图片。
        foreach ($data as $key => $value) {
            if (empty($value) && $value != 0) {
                return $this->error('工程信息不可为空或工地不存在');
            }
        }

        //判断下是否传的是设计师
        foreach ($data_designer['uid'] as $k => $v) {
            $houseKeeperInfo = UserModel::find($v);
            if (empty($houseKeeperInfo)) {
                return $this->error('找不到该设计师');
            }
            if ($houseKeeperInfo->user_type != 2) {
                return $this->error('您选择了非设计师人员');
            }
        }

        $is_work_able = $this->taskAppointRepository->createAppoint($data, $data_designer['uid']);

        //返回原因
        if (!($is_work_able['able'])) {
            return $this->error($is_work_able['errMsg']);
        } else {
            return $this->error($is_work_able['successMsg'], 0);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 发约单请求
     */
    public function userCreateAppointTaskPlanB(Request $request) {

        $data['uid']              = $request->json('user_id');              //发布人id
        $data['favourite_style']  = empty($request->json('favourite_style')) ? 0 : $request->json('favourite_style');     //喜好风格  风格是否可以固定？
        $data['user_type']        = $request->json('user_type');           // 想要选择的服务类型  2设计师 3管家 4监理
        $data['project_position'] = $request->json('project_position');// 工程id
        $data['status']           = Config::get('task.TASK_STATUS_PAY_POUNDGE');//接单状态   默认为已付款
        $data['bounty_status']    = Config::get('task.BOUNTRY_STATUS'); //接单状态   默认为已付款
        $data['type_id']          = Config::get('task.TYPE_ID'); //约单是2
        $data['created_at']       = date('Y-m-d H:i:s', time()); //建立时间
        $data_designer['uid']     = $request->json('designer_id');//约一个设计师,管家或者监理
//   任务状态:0暂不发布 1已经发布,未付发布费 2已经付发布费 3审核通过 4威客交稿 5雇主选稿 6任务公示 7交付验收 8双方互评 9已结束 10失败 11维权

//     2.设计师订单设计师流程中增加上传图片（PC），用户增加查看设计图片。
        foreach ($data as $key => $value) {
            if (empty($value) && $value != 0) {
                return $this->error('工程信息不可为空或工地不存在');
            }
        }

        //判断下是否传的是设计师
        $houseKeeperInfo = UserModel::find($data_designer['uid']);
        if (empty($houseKeeperInfo)) {
            return $this->error('找不到该工作者');
        }
        if ($houseKeeperInfo->user_type != $data['user_type']) {
            return $this->error('选择人员错误');
        }

        $is_work_able = $this->taskAppointRepository->createAppointPlanB($data, $data_designer['uid']);

        //返回原因
        if (!($is_work_able['able'])) {
            return $this->error($is_work_able['errMsg']);
        } else {
            //推送
            switch ($data['user_type']) {
                case 2:
                    $application = 10001;
                    break;
                case 3:
                    $application = 20001;
                    break;
                case 4:
                    $application = 30001;
                    break;
                default:
                    $application = 10001;
            }

            push_accord_by_equip($houseKeeperInfo->id, $application, 'message_create_order', '', $is_work_able['successMsg']);

            return $this->success(['task_id'=>$is_work_able['successMsg']]);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 约单中心
     */
    public function getTasksAppoint(Request $request) {

        $uid       = intval($request->get('user_id'));
        $user_info = UserModel::find($uid);
        if (empty($user_info)) return $this->error('获取不到用户信息');
        $user_type              = $user_info->user_type;//看类型
        $list                   = TaskModel::appFindBy(['user_type' => $user_type, 'type_id' => 2, 'user_designer' => $uid]);//返回列表
        $count_house_keep_robot = WorkModel::where('uid', $uid)->where('status', '>', 0)->where('status', '<', 3)->count();//查找管家已接单的任务

        //抢单管家当前进行中的任务数量是否超过6个，超出不可抢（接）
        if ($user_type == 3 && $count_house_keep_robot > 100) {
            return $this->error('当前进行中的任务超过6个');
        }

        //抢单监理当前进行中的任务数量是否超过15个，超出不可抢（接）
        if ($user_type == 4 && $count_house_keep_robot > 15) {
            return $this->error('当前进行中的任务超过15个');
        }
        //如果管家是1星的,不能看到业主要要求的2星及以上的订单
        if ($user_type == 3) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取管家星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return $this->error();
                }
            }
        }

        //如果管家是1星的,不能看到业主要要求的2星及以上的订单
        if ($user_type == 4) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取监理星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return $this->error();
                }
            }
        }
//var_dump($list['user_type']);exit();
        foreach ($list->toArray() as $key => $value) {
            $value['avatar'] = url($value['avatar']);
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::where('task_id', $value['task_id'])->orderBy('sn', 'ASC')->get()->toArray();

                foreach ($work_offer_status as $key2 => $value2) {
                    if ($key2 == count($work_offer_status) - 1) {
                        $list[$key]['node']   = $value['status'];
                        $list[$key]['sn']     = $value2['sn'];
                        $list[$key]['status'] = $value2['status'];
                        $list[$key]['type_id'] = $user_type;
                        break;
                    }

                    if ($value['status'] != 4) {
                        $list[$key]['node']   = $value['status'];
                        $list[$key]['sn']     = $value2['sn'];
                        $list[$key]['status'] = $value2['status'];
                        $list[$key]['type_id'] = $user_type;
                        break;
                    }
                }

            } else {
                $list[$key]['node']   = $value['status'];
                $list[$key]['sn']     = 0;
                $list[$key]['status'] = 0;
                $list[$key]['type_id'] = $user_type;
            }
        }

        $list_new = array_values($list->toArray());
        foreach ($list_new as $item => $value) {
            foreach ($value as $n => $m) {
                if ($m === null) {
                    $list_new[$item][$n] = '';
                }
            }
        }
        return $this->success($list_new);
    }

    /**
     * @param $chang_uid
     * @param $task_id
     * @param $origin_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 业主在没确认之前更换约单设计师
     */
    public function userChangeAppointWorker(Request $request) {
        $chang_uid = $request->json('chang_uid');
        $task_id   = $request->json('task_id');
        $origin_id = $request->json('origin_id');

        $is_work_able = $this->taskAppointRepository->changeWorker($chang_uid, $task_id, $origin_id);

        //返回原因
        if (!$is_work_able['able']) {
            return $this->error($is_work_able['errMsg']);
        } else {
            return $this->error('操作成功',0);
        }
    }


    /**
     * 筛选掉用户关注列表里面,该任务已选的设计师
     */
    public function filterDesignerOfAppoint(Request $request) {
        $user_id    = $request->get('user_id');
        $task_id    = $request->get('task_id');
        $user_type  = !empty($request->get('user_type')) ? $request->get('user_type') : 2;
        $focus_data = UserFocusModel::select('user_focus.focus_uid', 'user_focus.created_at', 'ud.avatar', 'ud.realname', 'ud.nickname as nickname', 'us.user_type', 'ud.cost_of_design', 'realname_auth.serve_area')
            ->where('user_focus.uid', $user_id)
            ->where('us.user_type', $user_type)
            ->join('user_detail as ud', 'user_focus.focus_uid', '=', 'ud.uid')
            ->join('realname_auth', 'user_focus.focus_uid', '=', 'realname_auth.uid')
            ->leftjoin('users as us', 'user_focus.focus_uid', '=', 'us.id')
            ->get();

        if ($focus_data->isEmpty()) {
            $result = [];
        } else {
            //找到该任务已选的设计师
            $already_choose = WorkModel::select('uid')->where('task_id', $task_id)->where('status', 0)->get();
            /*            $refuse_and_change = workDesignerLog::select('new_uid','old_uid')->where('task_id', $task_id)->get();
                        dd($refuse_and_change->toArray());*/
            foreach ($already_choose as $n => $m) {
                foreach ($focus_data as $k => $v) {
                    if ($m['uid'] == $v['focus_uid']) {
                        unset($focus_data[$k]);
                    }
                }
            }
        }

        foreach ($focus_data as $k => $v) {
            $v['avatar']   = !empty($v['avatar']) ? url($v['avatar']) : '';
            $v['nickname'] = !empty($v['nickname']) ? $v['nickname'] : $v['realname'];
            unset($v['realname']);
            $result['focus_list'][] = $v;
        }

        return $this->success($result);
    }


    /**
     * 设计师确认或者拒绝此次的约单任务
     */
    public function designerReplyBoss(Request $request) {
        $task_id      = $request->json('task_id');
        $user_id      = $request->json('user_id');//设计师id
        $refuse_id    = $request->json('refuse_id');//0接单(抢单),1接受,2拒绝,3超时
        $is_work_able = $this->taskAppointRepository->ReplyBoss($task_id, $user_id, $refuse_id);

        if (!$is_work_able['able']) {
            return $this->error($is_work_able['errMsg']);
        } else {
            return $this->error('操作成功',0);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 支付回调更改任务是否已支付手续费（定金）
     */

    public function payPoundage(Request $request) {

        $data['uid']      = $request->json('user_id');
        $data['task_id']  = $request->json('task_id');
        $data['password'] = $request->json('password');        // 若是余额支付，要提交密码

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error($key . '必要参数为空');
            }
        }

        $userInfo = UserModel::where('id', $data['uid'])->where('status', 1)->where('user_type', 1)->first();
        $task     = TaskModel::select('status', 'uid')->where('id', $data['task_id'])->first()->toArray();

        if ($task['uid'] != $data['uid'] || $task['status'] >= 2) {
            // 已支付手续费或用户id不匹配
            return $this->error('非法操作！', 403);
        }

        // TODO 手续费，到系统配置里面找
        $poundage_service = ServiceModel::where('identify', 'SHOUXUFEI')->first();
        $poundage         = (float)$poundage_service['price'];
        $balance          = UserDetailModel::where(['uid' => $data['uid']])->first();
        $balance          = (float)$balance['balance'];        //用户余额
        $difference       = $balance - $poundage;        // 余额不足，提示欠缺多少，通知前端调起第三方支付
        $data['product']  = $poundage_service['id'];


        if ($difference < 0) {
            // TODO 这里使用第三方支付补缴
            return $this->error('余额不足，需使用第三方补缴', ['difference' => abs($difference)]);
        } else {
            // 这里是使用余额支付
            $password = UserModel::encryptPassword($data['password'], $userInfo['salt']);
            if ($password != $userInfo['password']) {
                return $this->error('您的支付密码不正确', 403);
            }
            //创建订单
            $is_ordered = OrderModel::bountyOrder($data['uid'], $poundage, $data['task_id'], '任务款');

            if (!$is_ordered) {
                return $this->error('任务订单创建失败', 403);
            }


            //余额支付产生订单  修改对应的订单状态

            $result           = TaskModel::bounty($poundage, $data['task_id'], $data['uid'], $is_ordered->code, 1, 1, false, true);
            $user_boss        = UserDetailModel::where('uid', $data['uid'])->first();            //找到用户
            $res_frozen_money = UserDetailModel::where('uid', $data['uid'])->update(['frozen_amount' => $user_boss->frozen_amount += $poundage]);//把扣除金额写进冻结资金

            if (!$result && empty($res_frozen_money)) {
                return $this->error('支付失败', 403);
            }

            return $this->error( '任务创建并支付成功',0);
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 工作端抢单  （等于投稿）
     */
    public function robOrder(Request $request) {
        $data['desc']       = '';
        $data['uid']        = $request->json('designer_id');
        $data['task_id']    = $request->json('task_id');
        $data['created_at'] = date('Y-m-d H:i:s', time());

        //找到业主
        $task_info = TaskModel::find($data['task_id']);
        if (empty($task_info)) return $this->error('订单无法找到！', 403);

        $is_work_able = $this->isWorkAble($data['task_id'], $data['uid']);
        //返回为何不能投标的原因
        if (!$is_work_able['able']) {
            return $this->error($is_work_able['errMsg'], 403);
        }
        //创建一个新的稿件
        $workModel = new WorkModel();
        $result    = $workModel->workCreate($data);

        $user_boss = UserModel::find($task_info->uid);
        //推送
        if (!empty($user_boss)) {
            $application = Config::get('pushMessage.ORDER_ACCEPT');
            $message     = Config::get('pushMessage.MESSAGE_ROB_ORDER');
            $user_boss->send_num += 1;
            $user_boss->save();

            //保存发送的消息
            $data_send['message']     = $message;
            $data_send['application'] = $application;
            $data_send['uid']         = $task_info->uid;
            UsersMessageSendModel::create($data_send);

            PushServiceModel::pushMessageBoss($user_boss->device_token, $message, $user_boss->send_num, $application);
        }
        if (!$result) return $this->error('投稿失败！', 403);
        return $this->error('已抢单，请等待用户确认',0);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 消息清零
     */
    public function messageClear(Request $request) {
        $user_id         = $request->json('user_id');
        $res_del_msg_num = UserModel::where('id', $user_id)->update(['send_num' => 0]);
        if ($res_del_msg_num) {
            return $this->error('success',0);
        }
        return $this->error('error');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 获取用户待发送的消息
     */
    public function getUsersWaitMessage(Request $request) {
        $user_id      = $request->get('user_id');
        $data_message = UsersMessageSendModel::where('uid', $user_id)->get();
        return $this->success($data_message);
    }


    /**
     * @param $task_id 任务id
     * @param $to_uid 工作者id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 设计师提交工程配置单
     */
    public function workConfirmation(Request $request) {
        $data['task_id']           = $request->json('task_id');
        $data['to_uid']            = $request->json('to_uid');
        $data['project_list_conf'] = $request->json('project_list_conf');
        $data['sn']                = $request->json('sn');
        $data['city_id']           = empty($request->json('city_id')) ? 291 : $request->json('city_id');
        if (empty($data['task_id']) || empty($data['to_uid'])) {
            return $this->error('非法参数');
        }
        $res_work_submit = $this->taskOperaRepository->workSubmit($data['task_id'], $data['to_uid'], $data['project_list_conf'], $data['sn'], $data['city_id']);

        if (empty($res_work_submit['status'])) {
            return $this->error($res_work_submit['errMsg']);
        } else {
            return $this->error($res_work_submit['successMsg'],0);
        }

        return $this->error('提交失败');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 判断设计师有没有上传过图纸
     */
    public function detectUploadImg(Request $request) {

        $data['task_id'] = $request->get('task_id');
        $data['sn']      = $request->get('sn');
        $work            = WorkModel::where('status', 2)->where('task_id', $data['task_id'])->first();

        //未提交初步设计图纸驳回
        if ($data['sn'] == 1) {
            $workOffer = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', 1)->where('work_id', $work->id)->first();
            if ($workOffer->upload_status == 0) return $this->error('请在PC端上传初步设计图纸');
            return $this->error( 'success',0);
        }

        if ($data['sn'] == 2) {
            $workOffer = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', 2)->where('work_id', $work->id)->first();
            $ProjectConfig = ProjectConfigureTask::where('task_id', $data['task_id'])->orderBy('id', 'desc')->first();
            if ($workOffer->upload_status == 0){
                    return $this->error('请在PC端上传深化设计图纸');
            }
            return $this->error('success',0);
        }
        return $this->error('非法提交');
    }


    //
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 业主或监理确认（用于设计师任务的确认,或者管家工程阶段的验收流程）
     */
    public function ownerConfirmation(Request $request) {
        $data['task_id']  = $request->json('task_id');//323
        $data['from_uid'] = $request->json('user_id');  // 当为设计师任务时为业主id；当为管家任务时为业主id或者监理id

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('非法参数');
            }
        }
        $work         = WorkModel::where('status', 2)->where('task_id', $data['task_id'])->first();
        $taskInfo     = TaskModel::find($data['task_id']);
        $taskUserType = $taskInfo->user_type;
        //设计师结算
        if ($taskUserType == 2) {
            //事务处理
            $res_status = $this->payWorkerRespository->settle_accounts($work, $data);

            $ret        = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])
                ->where('sn', '>', 0)
                ->get()->toArray();
            foreach ($ret as $k => $v) {
                if ($v['status'] == 0) {
                    unset($ret[$k]);
                }
            };
            $count_submit = empty($ret[count($ret) - 1]) ? 0 : $ret[count($ret) - 1]['count_submit'];
            if ($res_status) {
                $msg = ['meassage'=>'确认成功,钱已付设计师','count_submit' => $count_submit];
                return $this->success($msg);
            } else {
                $msg =['count_submit' => $count_submit];
                return $this->error('确认失败', -1,$msg);
            }
        }

        //管家工人结算
        if ($taskUserType == 3) {
            // 查询去到那个工程阶段和工程阶段处于什么状态
            $data['sn']  = $request->json('sn');
            $curSnStatus = [];    //初始化阶段状态数组合

            if (empty($data['sn'])) {
                return $this->error('缺少参数');
            }

            // 判断任务去到哪一个工程阶段
            $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])
                ->where('status', '>', 0)
//                ->where('project_type', '>', 0)
                ->orderBy('project_type', 'ASC')
                ->get()->toArray();

            if (empty($ret)) {
                return $this->error('未达到验收阶段');
            }


            foreach ($ret as $key => $value) {
                // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
                if ($value['status'] == 1.5) {
                    $curSnStatus = $value;
                    break;
                }
            }

            if (empty($curSnStatus)) return $this->error('非法操作');
            // 数据库的记录与传递过来的阶段参数不匹配
            if ($curSnStatus['sn'] != $data['sn']) {
                return $this->error('阶段参数不匹配或处于整改阶段');
            }
            if ($curSnStatus['status'] == 0 || $curSnStatus['status'] == 1) {
                return $this->error('工程阶段状态不正确');
            }

            // 判断操作人合法性
            $userInfo = UserModel::where('id', $data['from_uid'])->first()->user_type;

            //处于需要用户确认的阶段
            $res_accounts = $this->payWorkerRespository->house_keeper_accounts($curSnStatus, $userInfo, $value, $work, $data, $taskInfo);

            if (is_null($res_accounts)) {
                return $this->error('确认成功,钱已付工人',0);
            }

        }

        //监理确认
        if ($taskUserType == 4) {

            // 查询去到那个工程阶段和工程阶段处于什么状态(管家的sn)
            $data['sn'] = $request->json('sn');

            $curSnStatus = [];    //初始化阶段状态数组合

            if (empty($data['sn'])) {
                return $this->error('缺少参数');
            }

            if (empty($data['sn'])) {
                return $this->error('缺少参数');
            }
            //2：设计师 3：管家 4：监理
            $supervisor_task_data = TaskModel::where('status', '<', 9)->where('id', $data['task_id'])->first();
            if (empty($supervisor_task_data)) return $this->error('找不到监理订单');

            $project_ppsition  = $supervisor_task_data->project_position;
            $house_keeper_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<', 9)->where('user_type', 3)->first();
            if (empty($house_keeper_task)) return $this->error('找不到对应的管家订单');
            $house_keeper_work = WorkModel::where('task_id', $house_keeper_task->id)->where('status', '>', 0)->first();

            // 判断任务去到哪一个工程阶段
            $ret = WorkOfferModel::where('work_id', $house_keeper_work->id)->where('task_id', $house_keeper_task->id)
                ->where('status', '>', 0)
                ->orderBy('project_type', 'ASC')
                ->get()->toArray();

            if (empty($ret)) {
                return $this->error('找不到任何阶段');
            }

            foreach ($ret as $key => $value) {
                // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
                if ($value['status'] == 1) {
                    $curSnStatus = $value;
                    break;
                }
            }
            // 数据库的记录与传递过来的阶段参数不匹配
            if ($curSnStatus['sn'] != $data['sn']) {
                return $this->error('阶段参数不匹配或处于整改阶段');
            }

            $res_change_status = WorkOfferModel::where('task_id', $house_keeper_task->id)->where('sn', $data['sn'])->update(['status' => 1.5]);

            if ($res_change_status) {
                //推送给业主
                $boss_uid = $taskInfo->uid;
                change_status_msg_to_boss($boss_uid, $curSnStatus['title']);
                return $this->error('确认成功',0);
            }
            return $this->error('确认失败');
        }
        return $this->error('确认失败');

    }

    /**
     * 批量注册环信
     */
    public function UserRegistEaseMob() {
        $users = \App\Modules\User\Model\UserModel::all();
        foreach ($users as $k => $v) {
            //发送的数据
            $params = [
                'username' => $v->name,
                'nickname' => get_user_type_name($v['user_type']),
                'password' => env('EASEMOB_USER_PASSWORD')
            ];

            $header   = array();
            $header[] = 'Authorization: Bearer ' . getEaseMobToken();

            $url = config('chat-room.easemob_users_url');//接收地址

            $ch = curl_init(); //初始化curl

            curl_setopt($ch, CURLOPT_URL, $url);//设置链接

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

            curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

            $response      = curl_exec($ch);//接收返回信息
            $response_data = json_decode($response, true);
        }

        dd('success');
    }


    /**
     * 检测环信账户是否存在,不存在就注册一个
     */
    public function detectChatAccount(Request $request) {
        $username  = $request->get('username');
        $user_type = $request->get('user_type');
        if(!empty($username) && !empty($user_type)){
            if(Cache::has('IM_'.md5(env('EASEMOB_CLIENT_ID')).'_'.$username.'_'.$user_type))
            {
                return $this->responseSuccess();
            } else {
                $url      = config('chat-room.easemob_users_url') . '/' . $username;//接收地址

                $header   = array();
                $header[] = 'Authorization: Bearer ' . getEaseMobToken();
//                var_dump($header);exit();
                $ch       = curl_init(); //初始化curl
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽
                //设置抓取的url
                curl_setopt($ch, CURLOPT_URL, $url);
                //设置头文件的信息作为数据流输出
                //curl_setopt($ch, CURLOPT_HEADER, 1);
                //设置获取的信息以文件流的形式返回，而不是直接输出。
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec($ch);//接收返回信息
                $res_data = json_decode($response, true);

                if (!empty($res_data['error'])) {
                    $params = [
                        'username' => $username,
                        'nickname' => get_user_type_name($user_type),
                        'password' => env('EASEMOB_USER_PASSWORD')
                    ];

                    $header   = array();
                    $header[] = 'Authorization: Bearer ' . getEaseMobToken();

                    $url = config('chat-room.easemob_users_url');//接收地址

                    $ch = curl_init(); //初始化curl

                    curl_setopt($ch, CURLOPT_URL, $url);//设置链接

                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

                    curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

                    $response = curl_exec($ch);//接收返回信息

                    return $this->success();
                } else {
                    Cache::forever('IM_' . md5(env('EASEMOB_CLIENT_ID')) . '_' . $username . '_' . $user_type, $res_data);
                }
            }
        }


        return $this->success();


    }

    /**
     * 批量注册聊天室
     */
    public function UserCreateChatRooms() {
        //发送的数据

        $ProjectPosition = \App\Modules\Task\Model\ProjectPositionModel::all();
        $new_array       = [];
        foreach ($ProjectPosition as $k => $v) {
            $new_array[] = [
                'members' => \App\Modules\User\Model\UserModel::find($v['uid'])->name,
                'address' => $v['region'] . $v['project_position'],
                'id' => $v['id'],
            ];
        }

        foreach ($new_array as $n => $m) {
            $params = [
                'owner' => '2907173277',
                'maxusers' => 100,
                'name' => $m['address'],
                'members' => [$m['members']],
                'roles' => ['admin' => [$m['members']]]
            ];


            $header   = array();
            $header[] = 'Authorization: Bearer ' . getEaseMobToken();

            $url = config('chat-room.easemob_chat_room_url');//接收地址

            $ch = curl_init(); //初始化curl

            curl_setopt($ch, CURLOPT_URL, $url);//设置链接

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

            curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

            $response      = curl_exec($ch);//接收返回信息
            $response_data = json_decode($response, true);

            if (empty($response_data['data']['id'])) return $this->error('失败');
            $rooms_id = $response_data['data']['id'];
            \App\Modules\Task\Model\ProjectPositionModel::where('id', $m['id'])->update(['chat_room_id' => $rooms_id]);
        }
        curl_close($ch); //关闭curl链接
        //聊天室id需要保存到数据库
        ///{org_name}/{app_name}/chatrooms
        dd('success');
        $rooms_id = $response_data['data']['id'];

        dd($rooms_id);
    }


    /**
     * @param Request $request
     * @return mixed
     * 聊天室加人
     */
    public function UseraddWorkToChatRoom(Request $request) {
        $rooms_id = $request->json('rooms_id');
        $username = $request->json('username');

        $url      = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id . '/users/' . $username;//url
        $params   = '';//参数
        $header   = array();
        $header[] = 'Authorization: Bearer ' . getEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response = curl_exec($ch);//接收返回信息

        $response_data = json_decode($response, true);

        curl_close($ch); //关闭curl链接

        return ($response_data);//true或者false
    }

    /**
     * 获取成员
     */
    public function UsergetChatRoomAllMembers(Request $request) {
        $rooms_id = $request->get('rooms_id');

        $url = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id . '/users?version=v3&pagenum=1&pagesize=100';


        $header   = array();
        $header[] = 'Authorization: Bearer ' . getEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        //设置抓取的url
        curl_setopt($ch, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
//        curl_setopt($ch, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);
//        return $this->error($response,0);
//        dd($response, $response_data);
//
        //显示获得的数据

        curl_close($ch); //关闭curl链接
        return $response_data;//true或者false
    }

    /**
     * 最终确认,结算所有工人和设计师的钱
     */
    public function ownerFinalConfirmation(Request $request) {

        $task_id        = $request->json('task_id');
        $uid_boss       = $request->json('uid_boss');
        $all_should_pay = SubOrderModel::where('task_id', $task_id)->where('status', 0)->get();
        if ($all_should_pay->isEmpty()) {
            return $this->error('无法找到需要支付给工人的单');
        }
        $all_pay_price = 0;
        foreach ($all_should_pay as $k => $v) {
            $all_pay_price += $v['cash'];
        }

        //根据管家订单id找设计师订单id
        $taskInfo             = TaskModel::find($task_id);
        $designer_task_info   = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 2)->first();
        $supervisor_task_info = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 4)->first();
        if (empty($designer_task_info)) return $this->error('无法找到设计师订单');
        $designer_task_id   = $designer_task_info->id;//设计师订单id
        $supervisor_task_id = $supervisor_task_info->id;//监理订单id

        //设计师的单和管家和监理的成交量加一
        $userall['designer']    = WorkOfferModel::where('task_id', $designer_task_id)->where('sn', 0)->first()->to_uid;
        $userall['supervisor']  = WorkOfferModel::where('task_id', $supervisor_task_id)->where('sn', 0)->first()->to_uid;
        $userall['houseKeeper'] = WorkOfferModel::where('task_id', $task_id)->where('sn', 0)->first()->to_uid;

        foreach ($userall as $k => $v) {
            UserDetailModel::where('uid', $v)->increment('employee_num', 1);
            UserDetailModel::where('uid', $v)->increment('receive_task_num', 1);
        }

        $work_offer = WorkOfferModel::where('task_id', $designer_task_id)->where('sn', 3)->first();
        if (empty($work_offer)) return $this->error('无法找到设计师的施工指导阶段');
        //拿到设计师施工指导的钱

        //质保金
        $quanlity_service_money = TaskModel::find($task_id)->quanlity_service_money;
        $designer_price         = $work_offer->price;
        $designer_uid           = $work_offer->to_uid;
        $all_pay_price += $designer_price;
        $all_pay_price += $quanlity_service_money;
        $boss_info = UserDetailModel::where('uid', $uid_boss)->first();
        if ($boss_info->frozen_amount < $all_pay_price) {
            return $this->error('冻结金不足以支付');
        }
        //付钱啦!!
        //扣款记录(业主)

        $housekeeperId = $userall['houseKeeper'];
        $status = DB::transaction(function () use ($uid_boss, $all_pay_price, $task_id, $designer_price, $designer_task_id, $designer_uid, $supervisor_task_id, $quanlity_service_money,$housekeeperId) {

            //设计师的单和管家的单子都正常结束
            TaskModel::where('id', $designer_task_id)->update(['status' => 9, 'end_order_status' => 1]);
            TaskModel::where('id', $task_id)->update(['status' => 9, 'end_order_status' => 1]);
            TaskModel::where('id', $supervisor_task_id)->update(['status' => 9, 'end_order_status' => 1]);
            //设计师施工指导状态完成
            WorkOfferModel::where('task_id', $designer_task_id)->where('sn', 3)->update(['status' => 4]);
            //work表状态改变
            WorkModel::whereIn('task_id', [$designer_task_id, $task_id, $supervisor_task_id])->update(['status' => 3]);
            //业主总扣费订单
            platformOrderModel::sepbountyOrder($uid_boss, $all_pay_price, $task_id, '总结算(付款工人和设计师施工指导)', 1, 1);
            //设计师的施工指导订单
            platformOrderModel::sepbountyOrder($designer_uid, $designer_price, $designer_task_id, '施工指导结算', 2, 1);
            $this->payWorkerRespository->bounty($all_pay_price, $task_id, $uid_boss, 1, 6, true);//扣冻结资金
            $this->payWorkerRespository->bounty($designer_price, $designer_task_id, $designer_uid, 1, 2);//设计师余额增加
            //质保服务结算
            //平台账号直接收取
            $system_uid = Config::get('task.SYSTEAM_UID');
            //默认系统直接获得质保金收入
            platformOrderModel::sepbountyOrder($system_uid, $quanlity_service_money, $task_id, '质保金收入', 2, 1);
            $this->payWorkerRespository->bounty($quanlity_service_money, $task_id, $system_uid, 1, 2);//系统的余额增加


            //最终确认，还要生成管家可以提现申请的辅材款
            $auxiliaryOrder = SubOrderModel::where('task_id',$task_id)->where('uid',255)->where('title','辅材价格')->first();
            if(!empty($auxiliaryOrder)){
                $auxiliaryOrderPrice = $auxiliaryOrder->cash;
                $auxiliaryPrice  = round($auxiliaryOrderPrice*0.4,2);
                $this->payWorkerRespository->getHouseKeeperAuxiliary($task_id,7,$auxiliaryPrice,$housekeeperId,$auxiliaryOrder->order_id,$auxiliaryOrder->code);

            }

        });

        if (is_null($status)) {
            foreach ($all_should_pay as $k => $v) {
                DB::transaction(function () use ($v, $task_id) {
                    $plat_order_info         = SubOrderModel::find($v['id']);
                    $plat_order_info->status = 1;
                    $plat_order_info->title  = $plat_order_info->title . '(已支付)';
                    $plat_order_info->save();
                    $this->payWorkerRespository->bounty($v['cash'], $task_id, $v['uid'], 1, 2);
                });
            }
        } else {
            return $this->error('确认失败,无法扣款');
        }
        return $this->error('确认成功',0);
    }


    /**
     * 查询工程结算阶段的结算清单
     */
    public function getSettlementList(Request $request) {
        $task_id = $request->get('task_id');
        $sn      = $request->get('sn');
        $ret     = WorkOfferModel::where('task_id', $task_id)
//            ->where('sn', '>', 2)
            ->where('status', '>', 0)
            ->where('project_type', '>', 0)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();

        if (empty($ret)) {
            return $this->error('未达到结算阶段');
        }
        $newRetArr = [];
        foreach ($ret as $key => $value) {
            // 处于需要业主确认的阶段才会有这份清单
            if ($value['status'] == 1.5) {
                $newRetArr = $value;
            }
        }

        if (empty($newRetArr)) {
            return $this->error('未达到结算阶段');
        }

        $list['housekeeper_price'] = 0;

        $list['project_type'] = $newRetArr['project_type'];
        $list['worker_price'] = $newRetArr['price'];

        if ($newRetArr['project_type'] == 3 || $newRetArr['project_type'] == 5) {
            $housekeeper_price         = WorkOfferModel::where('task_id', $task_id)->where('sn', 0)->first()->price;
            $list['housekeeper_price'] = $housekeeper_price * 0.3;
        }

        if ($newRetArr['project_type'] == 7) {
            $housekeeper_price         = WorkOfferModel::where('task_id', $task_id)->where('sn', 0)->first()->price;
            $list['housekeeper_price'] = $housekeeper_price * 0.4;
        }

        return $this->success($list);

    }

    /**
     * 清除数据
     */

    public function truncate() {
        TaskModel::truncate();
        WorkOfferModel::truncate();
        WorkModel::truncate();
        workDesignerLog::truncate();
        SubOrderModel::truncate();
        OrderModel::truncate();
        ProjectLaborChange::truncate();
        ProjectConfigureTask::truncate();
        ProjectDelayDate::truncate();
        ProjectWorkOfferChange::truncate();
        FinancialModel::truncate();
        UsersMessageSendModel::truncate();
//        WorkAttachmentModel::truncate();
//        AttachmentModel::truncate();
//        UserModel::truncate();
//        UserDetailModel::truncate();
//        UnionAttachmentModel::truncate();
//        ShopModel::truncate();
//        RealnameAuthModel::truncate();
//        UserFocusModel::truncate();
//        ProjectPositionModel::truncate();
//        GoodsModel::truncate();
        CommentModel::truncate();
        DB::table('user_detail')->update(['frozen_amount' => 0, 'balance' => 0]);
        DB::table('user_detail')->where('uid', 1)->update(['balance' => 2000000]);
        DB::table('user_detail')->where('uid', 17)->update(['balance' => 2000000]);
        return $this->error('success',0);
    }

    /**
     * 一键删除所有测试数据
     */
    public function deleteAllDataByTaskid(Request $request) {
        $task_id = $request->get('task_id');
        TaskModel::destroy($task_id);
        WorkModel::where('task_id', $task_id)->delete();
        WorkOfferModel::where('task_id', $task_id)->delete();
        workDesignerLog::where('task_id', $task_id)->delete();
        SubOrderModel::where('task_id', $task_id)->delete();
        FinancialModel::where('task_id', $task_id)->delete();
        CashoutModel::where('task_id', $task_id)->delete();
        ProjectConfigureTask::where('task_id', $task_id)->delete();
        ProjectLaborChange::where('task_id', $task_id)->delete();
        ProjectDelayDate::where('task_id', $task_id)->delete();
        WorkAttachmentModel::where('task_id', $task_id)->delete();
        CommentModel::where('task_id', $task_id)->delete();
        return $this->error('success',0);
    }

    /**
     * @param $task_id 取消任务
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 取消任务(多处调用,需要判断的情况较多)
     */
    public function cancelOrder(Request $request) {
        $data['boss_refuse']           = !empty($request->json('boss_refuse')) ? $request->json('boss_refuse') : '';//深化设计时,直接取消订单而不是打回和三次驳回
        $data['boss_refuse_reason_id'] = !empty($request->json('boss_refuse_reason_id')) ? $request->json('boss_refuse_reason_id') : 0;//取消原因id保存,只有深化和初步有
        //1差评,2一般,3.满意对应施工指导给设计师金额为50%,75%,100%
        $data['task_id'] = $request->json('task_id');
        $task            = TaskModel::find($data['task_id']);
        $uid             = $task->uid;
        if (empty($task)) {
            return $this->error('找不到该项目');
        }

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
                $res_task = $task->save();
            } else {
                $res_task = false;
            }
            if ($increment && $res_task && $sub_order_res && $decrement_boss_frozen) {
                return $this->success(['msg'=>'取消成功','count_submit'=>$work_count]);
            } else {
                return $this->error('取消失败');
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
                    return $this->success(['msg'=>'取消成功','count_submit'=>$work_count]);
                } else {
                    return $this->error('取消失败,状态不对');
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
                    return $this->success(['msg'=>'取消成功','count_submit'=>$work_count]);
                } else {
                    return $this->error('取消失败,状态不对');
                }
            }

            // 该项目做到哪个阶段
            $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $data['task_id'])
                ->where('sn', '>', 0)
                ->get()->toArray();

            if (empty($ret)) return $this->error('找不到该任务');

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
                                $res_handle                  = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                                $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                                $task->status                = 9;
                                $work->status                = 3;
                                $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                                if ($res_handle && $task->save() && $work->save()) {
                                    // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                    WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                    return $this->success(['msg'=>'取消成功','count_submit'=>$work_count]);
                                } else {
                                    return $this->error('取消失败');
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

                            $res_handle                  = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return $this->success(['msg'=>'取消成功','count_submit'=>$work_count]);
                            } else {
                                return $this->error('取消失败');
                            }

                        }

                        //深化设计打回修改
                        if ($value['sn'] == 2 && $count_submit == 3) {

                            $res_handle                  = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order          = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                // 驳回或取消时，都需要把该步骤的图纸状太改为废弃状态
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return $this->error('取消成功',0);
                            } else {
                                return $this->error('取消失败');
                            }

                        } else {
                            $msg = ['message' => '已要求设计师重新修改深化设计', 'count_submit' => $count_submit];
                            if ($count_submit_res) {
                                WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $value['sn'])->where('work_id', $work['id'])->update(['upload_status' => 0]);
                                return $this->error($msg);
                            } else {
                                return $this->error('取消失败');
                            }
                        }

                        //提交施工指导点取消
                        if ($value['sn'] == 3) {

                            $res_handle         = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $task->cancel_order = $task->status . '-' . $value['sn'] . '-' . $value['status'];//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return $this->error('取消成功',0);
                            } else {
                                return $this->error('取消失败');
                            }
                        }


                    }

                    //在还没提交的状态
                    if ($value['status'] == 0) {

                        if ($value['sn'] == 1) {

                            $res_handle         = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $front_sn           = $value['sn'] - 1;
                            $task->cancel_order = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return $this->error('取消成功',0);
                            } else {
                                return $this->error('取消失败');
                            }
                        }
                        if ($value['sn'] == 2) {

                            $res_handle         = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn']);
                            $front_sn           = $value['sn'] - 1;
                            $task->cancel_order = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status       = 9;
                            $work->status       = 3;
                            if ($res_handle && $task->save() && $work->save()) {
                                return $this->error('取消成功',0);
                            } else {
                                return $this->error('取消失败');
                            }
                        }
                        if ($value['sn'] == 3) {
                            if (empty($data['boss_refuse_reason_id'])) return $this->error('未选择取消原因,无法进行结算');
                            $res_handle                  = $this->taskOperaRepository->handling_money($uid, $value['to_uid'], $value['price'], $data['task_id'], $value['title'], $work['id'], $value['sn'], $data['boss_refuse_reason_id']);
                            $front_sn                    = $value['sn'] - 1;
                            $task->cancel_order          = $task->status . '-' . $front_sn . '-' . 4;//task状态拼接sn拼接status
                            $task->status                = 9;
                            $work->status                = 3;
                            $task->boss_refuse_reason_id = $data['boss_refuse_reason_id'];
                            if ($res_handle && $task->save() && $work->save()) {
                                return $this->error('取消成功',0);
                            } else {
                                return $this->error('取消失败');
                            }
                        }
                    }

                }

            }

            return $this->error('取消失败');
        }

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 获取当前订单状态
     */
    public function getCurrentOrderStatus(Request $request) {
        $data['task_id'] = $request->get('task_id');
        $task_status     = TaskModel::where('id', $data['task_id'])->first();
        if ($task_status['status'] == 7) {
            $work_offer_status = WorkOfferModel::where('task_id', $data['task_id'])->orderBy('sn', 'ASC')->get()->toArray();
            foreach ($work_offer_status as $key => $value) {
                if ($key == count($work_offer_status) - 1) {
                    $status = ['node' => $task_status['status'], 'sn' => $value['sn'], 'status' => $value['status']];
                    break;
                }
                if ($value['status'] != 4) {
                    $status = ['node' => $task_status['status'], 'sn' => $value['sn'], 'status' => $value['status']];
                    break;
                }
            }
        } else {
            $status = ['node' => $task_status['status'], 'sn' => 0, 'status' => 0];
        }

        return $this->success($status);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 任务设计师投稿列表
     */
    public function workList(Request $request) {
        $data['task_id'] = $request->get('task_id');
        $works           = WorkModel::appFindAll($data['task_id']);
        foreach ($works as $key => &$value) {
            $value['avatar'] = !empty($value['avatar']) ? url($value['avatar']) : '';
            if (empty($value['mobile'])) {
                $value['mobile'] = '';
            }
        }
        return $this->success($works);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 设计师,管家获取所有任务列表
     * 'node':状态节点 ， 3为任务已发布  4为有人接（抢）单  5为接（抢）单人数已满 6为确认约谈设计师  7为任务交付中
     * 'sn':步骤节点（当node为7时，此参数才有意义）， 0为设计师报价阶段  1为初步设计  2为深化设计  3为施工指导
     * 'status':步骤节点状态（当node为7时，此参数才有意义，配合sn使用） 0为未开始 1为工作者提交 2为业主确定 3为业主驳回 4为完成
     */
    public function getTasks(Request $request) {

        $uid       = intval($request->get('user_id'));
        $res_tasks = $this->taskSelectRepository->getTasks($uid);
        if ($res_tasks['status']) {
            return $this->error($res_tasks['successMsg'],0);
        } else {
            return $this->error($res_tasks['errMsg']);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据uid获取管家和监理的订单详细
     */

    public function designerOrder(Request $request) {

        $uid = $request->get('user_id');

        //获取业主选定该管家或监理的订单(0业主还没确认,1,选的人就是传过来的人,2选的人不是传给来的人)
        $boss_sure_designer = workDesignerLog::where('new_uid', $uid)->where('is_refuse', 1)->where('boss_confirm', 1)->lists('task_id');
        //获取业主没有选定该管家或监理的单
        $boss_not_sure_designer = workDesignerLog::where('new_uid', $uid)->where('is_refuse', 1)->where('boss_confirm', 0)->lists('task_id');

        $task_info       = [];
        $task_info_other = [];



        $task_info = $this->taskSelectRepository->selectDbInfoByTaskId($boss_sure_designer);


//        foreach ($boss_sure_designer as $item => $value) {
//            $task_info[] = $this->taskSelectRepository->selectDbInfo($value);
//        }

        //这是业主确认的该设计师的单
        foreach ($task_info as $item => $value) {
            $task_info[$item]['is_sure_designer'] = 1;
            $task_info[$item]['avatar']           = url($value['avatar']);
            $task_info[$item]['nickname']         = empty($value['nickname']) ? '' : $value['nickname'];
            $task_info[$item]['favourite_style']  = empty($value['favourite_style']) ? '' : $value['favourite_style'];
        }

        //业主没确认该设计师的单,有两种情况


        $task_info_other = $this->taskSelectRepository->selectDbInfoByTaskId($boss_not_sure_designer);
//        foreach ($boss_not_sure_designer as $item => $value) {
//            $task_info_other[] = $this->taskSelectRepository->selectDbInfo($value);
//        }


        $taskid = array_unique(array_column($task_info_other,'task_id'));

        $arr = workDesignerLog::select(DB::raw('task_id,count(id) count'))->where('boss_confirm',1)->whereIn('task_id',$taskid)->groupBy('task_id')->get()->toArray();
        $brr = [];
        foreach($arr as $key => $value){
            $brr[$value['task_id']] = $value['count'];
        }

        foreach ($task_info_other as $item => $value) {
            //看这个单有没有确认过别人
//            $whether_confirm_designer                  = workDesignerLog::where('task_id', $value['task_id'])->where('boss_confirm', 1)->count();
            $whether_confirm_designer                  = isset($brr[$value['task_id']]) ? $brr[$value['task_id']] : 0;
            $task_info_other[$item]['avatar']          = url($value['avatar']);
            $task_info_other[$item]['nickname']        = empty($value['nickname']) ? '' : $value['nickname'];
            $task_info_other[$item]['favourite_style'] = empty($value['favourite_style']) ? '' : $value['favourite_style'];
            //如果确认过别人接这个单
            if ($whether_confirm_designer) {
                $task_info_other[$item]['is_sure_designer'] = 2;
            } else {
                $task_info_other[$item]['is_sure_designer'] = 0;
            }
        }

        $total = array_merge(collect($task_info_other)->toArray(), collect($task_info)->toArray());

        $order_total = [];
        //合并后根据时间排序

        foreach ($total as $k => $v) {

            $order_total[$v['created_at']] = $v;
        }

        krsort($order_total);

        //加状态
        $total_new = $this->taskSelectRepository->addStatus(array_values($order_total));

        return $this->success($total_new);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 判断有没有管家的订单
     */
    public function judgeHouseExist(Request $request) {
        $position_id = $request->get('position_id');
        $res         = TaskModel::where('project_position', $position_id)->where('user_type', 3)->first();
        if ($res)
            return $this->error('success',0);
        else
            return $this->error('你需要先预约管家,才能进行监理预约', 404);
    }


    /**
     * @param Request $request
     * @return array
     * 获取设计师接过的单和业主选定他的单
     */
    public function getDesignerTask(Request $request) {

        $uid = $request->get('user_id');
        //获取业主选定该设计师的订单(0业主还没确认,1,选的人就是传过来的人,2选的人不是传给来的人)
        $boss_sure_designer = workDesignerLog::where('new_uid', $uid)->where('boss_confirm', 1)->lists('task_id');
        //获取业主没有选定该设计师的单
        $boss_not_sure_designer = workDesignerLog::where('new_uid', $uid)->where('boss_confirm', 0)->lists('task_id');
        $task_info       = [];
        $task_info_other = [];
        foreach ($boss_sure_designer as $item => $value) {
            $task_info[] = $this->taskSelectRepository->selectDbInfo($value);
        }
        //这是业主确认的该设计师的单
        foreach ($task_info as $item => $value) {
            $task_info[$item]['is_sure_designer'] = 1;
            $task_info[$item]['nickname']         = empty($value['nickname']) ? $value['boss_mobile'] : $value['nickname'];
            $task_info[$item]['avatar']           = url($value['avatar']);
        }
        //业主没确认该设计师的单,有两种情况
        foreach ($boss_not_sure_designer as $item => $value) {
            $task_info_other[] = $this->taskSelectRepository->selectDbInfo($value);
        }
        foreach ($task_info_other as $item => $value) {
            //看这个单有没有确认过别人
            $whether_confirm_designer           = workDesignerLog::where('task_id', $value['task_id'])->where('boss_confirm', 1)->count();
            $task_info_other[$item]['avatar']   = url($value['avatar']);
            $task_info_other[$item]['nickname'] = empty($value['nickname']) ? $value['boss_mobile'] : $value['nickname'];
            //如果确认过别人接这个单
            if ($whether_confirm_designer) {
                $task_info_other[$item]['is_sure_designer'] = 2;
            } else {
                $task_info_other[$item]['is_sure_designer'] = 0;
            }
        }
        $total = array_merge(collect($task_info_other)->toArray(), collect($task_info)->toArray());
        $order_total = [];
        //合并后根据时间排序
        foreach ($total as $k => $v) {
        	if(isset($v['created_at'])){
        		$order_total[$v['created_at']] = $v;
        	}
        }
        krsort($order_total);
        //加状态
        $total_new = $this->taskSelectRepository->addStatus(array_values($order_total));
        return $this->success($total_new);
    }


    /**
     * 获取任务的整改历史记录
     */
    public function getChangeListHistory(Request $request) {
        $task_id      = $request->get('task_id');
        $task_id_real = FindHouseTaskId($task_id);
        $task_data    = TaskModel::find($task_id_real);
        if (empty($task_data)) {
            return $this->error('找不到该订单');
        }
        $history = ProjectLaborChange::select('project_type', 'status', 'list_detail', 'is_confirm', 'created_at')->where('task_id', $task_id_real)->get();
        foreach ($history as $k => $v) {
            if (!empty($v['list_detail'])) {
                foreach (unserialize($v['list_detail']) as $n => $m) {
                    if (!is_numeric($m)) {
                        $history[$k]['list_detail'] = $m['childs'];
                    }
                }
            } else {
                $history[$k]['list_detail'] = [];
            }
        }
        return $this->success($history);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 获取创建过的工程地址列表
     */
    public function getProjectList(Request $request) {
        $user_id = $request->json('user_id');
        return $this->success(ProjectPositionModel::getProjectList($user_id));
    }

    /**
     * @return Response
     * 获取发布费
     */
    public function getTaskPoundage() {
        $task_poundage = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;
        return $this->success(['task_poundage' => $task_poundage]);
    }


    /**
     * @param $task_id
     * @param $uid
     * @return array
     * APP端接口使用
     */
    private function isWorkAble($task_id, $uid) {
        //判断当前任务是否处于投稿期间
        $task_data = TaskModel::where('id', $task_id)->first();
        if ($task_data['status'] != (3 || 4)) {
            return ['able' => false, 'errMsg' => '任务暂不可抢！'];
        }
        //如果是设计师角色，判断是否设定了单价
        $user = UserModel::where('users.id', $uid)->leftjoin('user_detail', 'user_detail.uid', '=', 'users.id')->first();
        if ($user->user_type == 2) {
            if ($user->cost_of_design <= 0) {
                return ['able' => false, 'errMsg' => '还没设定单价，请到个人中心设置'];
            }
        }
        //判断用户是否为当前任务的投稿人，如果已经是的，就不能投稿
        if (WorkModel::isWorker($uid, $task_id)) {
            return ['able' => false, 'errMsg' => '你已经抢过该单了'];
        }
        // 判断当前用户是否为任务的发布者，如果是用户的发布者，就不能投稿
        if (TaskModel::isEmployer($task_id, $uid)) {
            return ['able' => false, 'errMsg' => '你是任务发布者不能投稿！'];
        }
        return ['able' => true];
    }


    /**
     *
     * 获取附近人员
     * @return mixed
     *
     */
    public function getDesignerShow(Request $request) {
        $uid_boss = empty($request->get('user_id')) ? 0 : $request->get('user_id');
        $lat      = $request->get('lat');
        $lng      = $request->get('lng');
        $selectStr = ['user_detail.uid', 'user_detail.avatar', 'user_detail.nickname', 'user_detail.star', 'user_detail.realname', 'user_detail.experience',
                      'user_detail.employee_num', 'user_detail.city', 'shop.pageviews', 'shop.total_comment', 'shop.id as shop_id',
                      'users.user_type', 'users.name as user_mobile', 'user_detail.cost_of_design', 'user_detail.lat', 'user_detail.lng', 'user_detail.work_type' ,'user_detail.serve_area_id'];
        $detail_designer = ShopModel::select($selectStr)->where('is_recommend', 1)->leftJoin('user_detail', 'shop.uid', '=', 'user_detail.uid')->leftJoin('users', 'shop.uid', '=', 'users.id')->where('users.status', 1);
        $plus_lat  = $lat + 0.5;
        $plus_lng  = $lng + 0.5;
        $minus_lat = $lat - 0.5;
        $minus_lng = $lng - 0.5;
        $focus_uid_arr = UserFocusModel::where('uid', $uid_boss)->lists('focus_uid')->toArray();
        $alldata = $detail_all = $detail_designer->whereIn('users.user_type',[2,3,4,5])->whereBetween('user_detail.lat', [$minus_lat, $plus_lat])->whereBetween('lng', [$minus_lng, $plus_lng])->get()->toArray();
        $list = [];
        $designerData = $housekeeperData = $supervisorData = $workerData = [];
        foreach($alldata as $key => $value){
            if (empty($value['total_comment'])) {
                $value['total_comment'] = 0;
            }
            $value['already_focus'] = 0;
            $value['is_foucus'] = 0;
            $value['goods_list'] = [];
            if($value['user_type'] == 2){ $designerData[] = $value; }
            if($value['user_type'] == 3){ $housekeeperData[] = $value; }
            if($value['user_type'] == 4){ $supervisorData[] = $value; }
            if($value['user_type'] == 5){ $workerData[] = $value; }
            $list[] = $value;
        }
        $setting = array('bedroom' => '居室','living_room' => '客厅','kitchen' => '厨房', 'washroom' => '卫生间','balcony' => '阳台');
        $construction = ProjectPositionModel::select('project_position.id', 'project_position.project_position', 'project_position.room_config', 'project_position.square', 'project_position.region', 'project_position.live_tv_url', 'project_position.lat', 'project_position.lng', 'task.status', 'task.id as task_id', 'task.status', 'task.unique_code')
            ->where('project_position.deleted', 0)
            ->whereBetween('project_position.lat', [$minus_lat, $plus_lat])
            ->whereBetween('project_position.lng', [$minus_lng, $plus_lng])
            ->join('task', 'project_position.id', '=', 'task.project_position')
            ->where('task.user_type', 3)
            ->where('task.hidden_status', 2)
            ->groupBy('project_position.id')
            ->orderBy('task_id', 'desc')
            ->get()->toArray();
        $handleConstruction = [];
        if (!empty($construction)) {
            $unique_code = array_column($construction,'unique_code');
            $project_ids = array_column($construction,'id');
            $task_ids = array_column($construction,'task_id');
            $workofferArr = [];
            $workofferdetail  = WorkOfferModel::whereIn('task_id',$task_ids)->orderBy('task_id','desc')->orderBy('sn', 'desc')->get()->toArray();
            foreach($workofferdetail as $key => $value){
                $workofferArr[$value['task_id']][] = $value;
            }
            foreach ($construction as $key => $value) {
                if (!empty($value['room_config'])) {
                    $decode_room_config = json_decode($value['room_config'], true);
                    $construction[$key]['room_config']    = '';
                    foreach ($decode_room_config as $key2 => $value2) {
                        if (!empty($value2) && isset($setting[$key2])) {
                            $construction[$key]['room_config'] .= $value2 . $setting[$key2] . ' ';
                        }
                    }
                    $construction[$key]['user_type']   = 6;
                }
                $construction[$key]['sn']      = 0;
                $construction[$key]['status']  = "0";
                if(isset($workofferArr[$value['task_id']])){
                    foreach ($workofferArr[$value['task_id']] as $key2 => $value2) {
                        if ($value2['status'] > 0) {
                            $construction[$key]['sn']     = $value2['sn'];
                            $construction[$key]['status'] = $value2['status'];
                            break;
                        }
                    }
                }
            }

            $designerTaskIds = TaskModel::where('user_type', 2)->whereIn('unique_code', $unique_code)->orderBy('id', 'DESC')->groupBy('project_position')->get()->toArray();
            //设计师任务id集合
            $preliminary = array_column($designerTaskIds,'id');
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
                $value['first_image'] = '';
                if(isset($taskSecondImage[$value['id']][1])){
                    $value['first_image'] = $taskSecondImage[$value['id']][1];
                }
                $value['square'] = (string)$value['square'];
                $value['status'] = (string)$value['status'];
                if (intval($value['status']) < 9) { $handleConstruction[] = $value; }
            }
        }

        $newConstructionArr = [];
        if (count($handleConstruction) == 1) {
            foreach ($handleConstruction as $key => $value) {
                $newConstructionArr = [$value];
            }
        } else {
            $newConstructionArr = $handleConstruction;
        }
        $MerchantDetail = MerchantDetail::whereBetween('lat', [$minus_lat, $plus_lat])->whereBetween('lng', [$minus_lng, $plus_lng])->get()->toArray();
        foreach ($MerchantDetail as $item => $value) {
            $MerchantDetail[$item]['brand_logo']  = empty($value['brand_logo']) ? '' : url($value['brand_logo']);
            $MerchantDetail[$item]['popular_img'] = empty($value['popular_img']) ? '' : url($value['popular_img']);
            $MerchantDetail[$item]['mobile']      = trim($value['mobile']);
            if (empty($value['lat'])) {
                $MerchantDetail[$item]['lat'] = 0;
            }
            if (empty($value['lng'])) {
                $MerchantDetail[$item]['lng'] = 0;
            }
        }
        if (empty($list)) {
            $mt_rand_num = mt_rand(3,6);
            $handleList = [];
            $designerDetailArr  = !empty($designerData) ? array_rand($designerData,     min(count($designerData),$mt_rand_num)):[];
            $housekeeperDataArr = !empty($housekeeperData) ? array_rand($housekeeperData,  min(count($housekeeperData),$mt_rand_num)):[];
            $supervisorDataArr  = !empty($supervisorData) ? array_rand($supervisorData,   min(count($supervisorData),$mt_rand_num)):[];
            $workerDataArr      = !empty($workerData) ? array_rand($workerData    ,   min(count($workerData),$mt_rand_num)):[];
           for($i=0;$i<$mt_rand_num;$i++){
               if(isset($designerDetailArr[$i]))    { $handleList[] = $designerData[$designerDetailArr[$i]]; }
               if(isset($housekeeperDataArr[$i]))   { $handleList[] = $housekeeperData[$housekeeperDataArr[$i]]; }
               if(isset($supervisorDataArr[$i]))    { $handleList[] = $supervisorData[$supervisorDataArr[$i]];   }
               if(isset($workerDataArr[$i]))        { $handleList[] = $workerData[$workerDataArr[$i]];           }
           }
            $shop_ids = array_column($handleList,'shop_id');
            $goods_list = GoodsModel::select('goods.shop_id','goods.id', 'cover', 'goods_address', 'title', 'cate.name as cate_name')
                ->join('cate', 'cate.id', '=', 'goods.cate_id')
                ->where('status', 1)->where('type', 1)->where('is_delete', 0)->whereIn('shop_id', $shop_ids)->get()->toArray();
            foreach($handleList as $key => $value){
                $handleList[$key]['total_goods'] = 0;
                if(in_array($value['uid'],$focus_uid_arr)){
                    $value['already_focus'] = 1;
                    $value['is_foucus'] += 1;
                }else{
                	unset($handleList[$key]);
					continue;
                }
                $handleList[$key]['avatar']   = empty($value['avatar']) ? '' : url($value['avatar']);
                $handleList[$key]['nickname'] = empty($value['nickname']) ? $value['realname'] : $value['nickname'];
                unset($handleList[$key]['shop_id'], $handleList[$key]['realname'], $handleList[$key]['user_mobile']);
                foreach($goods_list as $key2 => $value2){
                    if($value2['shop_id'] == $value['shop_id']){
                        $handleList[$key]['total_goods'] += 1;
                        $value2['cover'] = !empty($value2['cover']) ? url($value2['cover']) : '';
                        $handleList[$key]['goods_list'][] = $value2;
                        unset($goods_list[$key2]);
                    }
                }
            }
            return $this->success(['personnel' => array_values($handleList), 'project' => $newConstructionArr , 'MerchantDetail'=>$MerchantDetail]);
        } else {
            return $this->success(['personnel' => $list, 'project' => $newConstructionArr , 'MerchantDetail'=>$MerchantDetail]);
        }
    }


    public function getTaskImgStatus(Request $request) {
        $task_id       = $request->get('task_id');
        $taskDetail    = TaskModel::select('id', 'user_type', 'uid', 'project_position', 'status')->where('id', $task_id)->first();
        $arr['node']   = $taskDetail['status'];
        $arr['sn']     = 0;
        $arr['status'] = 0;
        if (empty($taskDetail)) {
            return $this->error('不存在的任务', 400);
        }
        $designer_task_id = $task_id;
        if ($taskDetail->user_type != 2) {
            $taskDetail = TaskModel::select('id', 'user_type', 'uid', 'project_position', 'status')
                ->where('project_position', $taskDetail['project_position'])
                ->where('user_type', 2)
                ->orderBy('id', 'desc')->first();
            if (empty($taskDetail)) {
                return $this->error('该工地尚未确认管家，暂无日志', 400);
            }
            $designer_task_id = $taskDetail->id;
        }
        $images = WorkAttachmentModel::select('work_attachment.id', 'attachment.url', 'work_attachment.task_id')
            ->join('attachment', 'attachment.id', '=', 'work_attachment.attachment_id')
            ->where('work_attachment.img_type', 1)
            ->where('work_attachment.task_id', $designer_task_id)
            ->offset(1)->limit(1)->get()->first();

//        $status = TaskModel::select('id','user_type','uid','project_position','status')
//                                ->where('project_position',$taskDetail['project_position'])
//                                ->where('user_type',3)
//                                ->orderBy('id','desc')->first();
//
//        $workDetail = WorkOfferModel::where('task_id',$task_id)->orderBy('sn','DESC')->get()->toArray();
//        foreach($workDetail as $key => $value){
//            if($value['status'] > 0){
//                $arr['sn'] = $value['sn'];
//                $arr['status'] = $value['status'];
//                break;
//            }
//        }
//var_dump($images);exit;
        if (!empty($images)) {
            return $this->success(['task_img_url'=>url($images->url)]);
        } else {
            return $this->error();
        }
    }


    /**
     * 获取商家信息
     */
    public function getMerchantDetails(Request $request) {

        $lat = empty($request->get('lat')) ? 0 : $request->get('lat');
        $lng = empty($request->get('lng')) ? 0 : $request->get('lng');
        $plus_lat  = $lat + 0.7;
        $plus_lng  = $lng + 0.7;
        $minus_lat = $lat - 0.7;
        $minus_lng = $lng - 0.7;
//        $MerchantDetail = MerchantDetail::all();
        if (empty($lat) && empty($lng)) {
            $MerchantDetail = MerchantDetail::all();
        } else {
            $MerchantDetail = MerchantDetail::where('lng','>',0)->whereBetween('lat', [$minus_lat, $plus_lat])->whereBetween('lng', [$minus_lng, $plus_lng])->get()->toArray();
        }
        foreach ($MerchantDetail as $item => $value) {
            $MerchantDetail[$item]['brand_logo']  = empty($value['brand_logo']) ? '' : url($value['brand_logo']);
            $MerchantDetail[$item]['popular_img'] = empty($value['popular_img']) ? '' : url($value['popular_img']);
            $MerchantDetail[$item]['mobile']      = trim($value['mobile']);
            if (empty($value['lat'])) {
                $MerchantDetail[$item]['lat'] = 0;
            }
            if (empty($value['lng'])) {
                $MerchantDetail[$item]['lng'] = 0;
            }
        }
        return $this->success($MerchantDetail);

    }

    /*************************************************新约单(整改方案2)*****************************************************************/
    /**
     * 管家发起换人请求(新约单)
     */
    public function bossApplyOrderPlanB(Request $request) {
        $data['task_id'] = $request->json('task_id');
        $data['sn']      = $request->json('sn');
        $task_info       = TaskModel::find($data['task_id']);
        $data['boss_id'] = $task_info->uid;
        if (empty($task_info)) return $this->error('找不到该任务');
        $work_info = WorkModel::where('task_id', $data['task_id'])->first();
        if (empty($work_info)) return $this->error('找不到经手人');
        $work_offer_data = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $data['sn'])->first();
        if (empty($work_offer_data)) return $this->error('找不到该阶段');
        $work_offer_data->status = 3;
        $work_offer_data->save();

        $data['project_type']     = $work_offer_data->project_type;//阶段类型
        $data['status']           = 1;//状态
        $data['project_position'] = $task_info->project_position;//工地
        $data['house_keeper_id']  = $work_info->uid;//经手人
        if (WorkOfferApply::create($data))
            return $this->error('发起请求成功',0);
        else
            return $this->error('发起请求失败');
    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 管家提交更改项(新约单)
     */
    public function houseKeeperReChangeListPlanB(Request $request) {
        //        $ids_new  = $request->json('data_lists');//管家提交的该工程需要整改的东西
        $sn                = $request->json('sn');
        $task_id           = $request->json('task_id');
        $cash              = $request->json('cash');//金额(支付工人)
        $cash_house_keeper = empty($request->json('cash_house_keeper')) ? 0 : $request->json('cash_house_keeper');//金额(支付管家)
        $end_date          = $request->json('end_date');
        $labor_id          = $request->json('labor_id');
        $desc              = empty($request->json('desc')) ? '' : $request->json('desc');
        $data_offer       = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
        $data_offer_house = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->first();
//        if (empty($data_offer)) return $this->error('找不到该阶段');
        if (empty($data_offer_house)) return $this->error('找不到经手人');
        $task_data          = TaskModel::find($task_id);
        $original_date      = $task_data['end_at'];//原定项目时间
        $boss_id            = $task_data->uid;
        $data_small_order   = [
            'sn' => $sn,
            'task_id' => $task_id,
            'offer_change_price' => $cash,
            'offer_change_detail' => '',
            'change_date' => $end_date,
            'labor' => $labor_id,
            'original_date' => $original_date,
            'status' => 1,
            'project_position' => $task_data->project_position,
            'desc' => $desc,
            'project_type' => empty($data_offer) ? 0 : $data_offer->project_type,
            'house_keeper_id' => $data_offer_house->to_uid,
            'boss_id' => $boss_id,
            'cash_house_keeper' => $cash_house_keeper,
            'small_order_id' => OrderModel::randomCode($task_data->uid),
        ];
        $insert_small_order = ProjectSmallOrder::create($data_small_order);
        //1.业主发起,2.管家确认,3.管家提交该工程,4.业主确认付款,5.管家提交验收,6.监理确认,7.业主确认,8.结算完成
        if ($insert_small_order) {
            //推送给业主
            $application = 40014;
            /*            $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_small_order')->first()->chn_name;
                        $woker_info  = UserModel::find($boss_id);
                        $woker_info->send_num += 1;
                        $woker_info->save();
                        //保存发送的消息
                        save_push_msg($message, $application, $boss_id);
                        PushServiceModel::pushMessageBoss($woker_info->device_token, $message, $woker_info->send_num, $application);*/
            push_accord_by_equip($boss_id, $application, 'message_small_order', '', $task_id);
            return $this->error('提交成功',0);
        } else {
            return $this->error('提交失败');
        }
    }

    /**
     * 获取管家刚提交的整改单(新约单)
     */
    public function getLatestChangeListOfProjectPlanB(Request $request) {
        $task_id          = $request->get('task_id');
        $user_type        = TaskModel::find($task_id)->user_type;
        $project_position = TaskModel::find($task_id)->project_position;
        //监理查看管家
        if ($user_type == 4) {
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $house_keeper_task->id;
        }
        $small_order_info = ProjectSmallOrder::select('id', 'sn', 'small_order_id', 'created_at', 'status', 'project_type')->where('task_id', $task_id)->where('is_confirm', 0)->orderBy('created_at', 'desc')->get();//管家刚提交的整改单
        foreach ($small_order_info as $k => $v) {
            $small_order_info[$k]['project_type'] = get_project_type($v['project_type']);
        }
        return $this->success($small_order_info);
        /*        if (empty($latest_submit_info)) return $this->error('无法找到该管家刚提交的整改单');
                $list_detail = unserialize($latest_submit_info->offer_change_detail);
                return $this->error($list_detail);*/
    }

    /**
     * 获取管家刚提交的整改单里层(新约单)
     */
    public function getChangeListDataPlanB(Request $request) {
        $id          = $request->get('id');
        $small_order = ProjectSmallOrder::select('id', 'sn', 'small_order_id', 'created_at', 'status', 'project_type', 'desc', 'labor', 'offer_change_price', 'cash_house_keeper')->where('id', $id)->first();
        if (!empty($small_order['labor'])) {
            $labor_detail             = UserDetailModel::select('avatar', 'uid', 'users.name', 'user_detail.realname', 'user_detail.work_type')->leftJoin('users', 'users.id', '=', 'user_detail.uid')->where('uid', $small_order['labor'])->first();
            $labor_detail['avatar']   = url($labor_detail['avatar']);
            $labor_detail['realname'] = empty($labor_detail['realname']) ? '' : $labor_detail['realname'];

        } else {
            $labor_detail = [];
        }
        if (!empty($labor_detail)) {
            $small_order['labor_detail'] = $labor_detail;
        }
        $small_order['project_type'] = get_project_type($small_order['project_type']);
        $small_order['total_price']  = $small_order['offer_change_price'] + $small_order['cash_house_keeper'];
        return $this->success($small_order);
    }

    /**
     * 业主支付管家的小订单
     */
    public function bossPaySmallOrderPlanB(Request $request) {

        $small_order_id   = $request->json('small_order_id');
        $boss_uid         = $request->json('boss_uid');
        $password         = $request->json('password');
        $small_order_info = ProjectSmallOrder::find($small_order_id);
        if (empty($small_order_info)) return $this->error('系统错误！', 403);
        if ($small_order_info->status == 3) {
            return $this->error('您已支付过该订单！', 403);
        }
        if (($small_order_info->status == 2) || ($small_order_info->status == 6)) {
            return $this->error('非法提交', 403);
        }
        $task_id = $small_order_info->task_id;
        $task    = TaskModel::select('status', 'uid')->where('id', $task_id)->first()->toArray();
        if ($task['uid'] != $boss_uid) {
            //用户id不匹配
            return $this->error('非法操作！', 403);
        }
        $house_keeper_id = $small_order_info->house_keeper_id;
        $only_order_code = OrderModel::where('uid', $boss_uid)->where('task_id', $task_id)->first()->code;//找到唯一的订单编号
        //找到要支付的金额
        $should_pay        = $small_order_info->offer_change_price;
        $cash_house_keeper = $small_order_info->cash_house_keeper;
        $should_pay += $cash_house_keeper;
//        if (empty($should_pay)) return $this->error('系统错误！'], '403');
        //支付做个余额判定
        $boss_detail               = UserDetailModel::where('uid', $boss_uid)->first();
        $userInfo                  = UserModel::where('id', $boss_uid)->where('status', 1)->where('user_type', 1)->first();
        $boss_detail_frozen_amount = $boss_detail->frozen_amount;
        if ($should_pay > $boss_detail->balance) {
            return $this->error('余额不足，需使用第三方补缴', ['difference' => abs($boss_detail->balance - $should_pay)]);
        } else {
            // 这里是使用余额支付
            $password = UserModel::encryptPassword($password, $userInfo['salt']);
            if ($password != $userInfo['password']) {
                return $this->error('您的支付密码不正确', 403);
            }
        }
        $work_offer_data = WorkOfferModel::where('task_id', $task_id)->where('sn', $small_order_info->sn)->first();//找到哪个阶段
        $title_sn        = $work_offer_data->title;
        //事务处理
        $status = DB::transaction(function () use ($task_id, $boss_uid, $should_pay, $only_order_code, $title_sn, $boss_detail_frozen_amount, $small_order_info) {
            $res_create = platformOrderModel::sepbountyOrder($boss_uid, $should_pay, $task_id, $title_sn . '支付工程变更单(余额->冻结金)', 1);
            $this->payWorkerRespository->bounty($should_pay, $task_id, $boss_uid, 1, 6);
            //找到该用户,扣除余额补充到保证金里面
            $boss_detail_frozen_amount += $should_pay;
            UserDetailModel::where('uid', $boss_uid)->update(['frozen_amount' => $boss_detail_frozen_amount]);//把扣除金额写进冻结资金
            $small_order_info->status       = 3;//付款自动进入平台匹配
            $small_order_info->sub_order_id = $res_create->id;//记录业主的小订单id
            $small_order_info->save();
            TaskModel::where('id', $task_id)->update(['end_at' => $small_order_info->change_date]);
        });
        if (is_null($status)) {
            //推送给管家
//            small_order_to_worker($house_keeper_id, 20007);
            push_accord_by_equip($house_keeper_id, 20007, 'message_small_order', '', $task_id);
            return $this->error('付款成功',0);
        } else {
            return $this->error('付款失败');
        }
    }

    /**
     * 管家提交验收
     * 状态:1.管家提交该工程,2.业主驳回管家整改单,3.业主已付款,4.管家提交验收,5.业主确认,6.结算完成(新约单),7.异常结单
     */
    public function houseKeeperSubmitSmallOrder(Request $request) {
        $small_order_id   = $request->json('small_order_id');
        $small_order_info = ProjectSmallOrder::find($small_order_id);
        if (empty($small_order_info)) {
            return $this->error('非法提交');
        }
        if ($small_order_info->status >= 4) {
            return $this->error('非法提交');
        }
        $small_order_info->status = 4;
//        small_order_to_boss($small_order_info->boss_id, 40016);
        push_accord_by_equip($small_order_info->boss_id, 40016, 'message_small_order', '', $small_order_info->task_id);
        if ($small_order_info->save()) {
            return $this->error( '提交验收成功', 0);
        } else {
            return $this->error('提交验收失败');
        }

    }

    /**
     * 业主驳回管家的验收请求
     */
    public function bossRefuseSmallOrder(Request $request) {
        $small_order_id   = $request->json('small_order_id');
        $small_order_info = ProjectSmallOrder::find($small_order_id);
        if (empty($small_order_info)) {
            return $this->error('非法提交');
        }
        if ($small_order_info->status > 4) {
            return $this->error('非法提交');
        }
        $small_order_info->status = 2;
        //推送给工作者
        $house_uid = $small_order_info->house_keeper_id;
        if ($small_order_info->save()) {
//            small_order_to_worker($house_uid,20008);
            push_accord_by_equip($house_uid, 20008, 'message_small_order', '', $small_order_info->task_id);
            return $this->error('驳回成功', 0);
        } else {
            return $this->error('驳回失败');
        }

    }

    /**
     * 业主驳回后,管家修改参数再提交
     */
    public function houseKeeperReSubmitList(Request $request) {
        $data['sn']                 = $request->json('sn');
        $data['offer_change_price'] = $request->json('cash');//管家提交的该工程需要整改的东西  项目总价
        $data['cash_house_keeper']  = empty($request->json('cash_house_keeper')) ? 0 : $request->json('cash_house_keeper');//金额(支付管家)  材料费
        $data['change_date']        = $request->json('end_date');
        $data['labor']              = $request->json('labor_id');
        $data['desc']               = empty($request->json('desc')) ? '' : $request->json('desc');
        $data['status']             = 1;
        $small_order_id             = $request->json('small_order_id');
        $origin_data                = ProjectSmallOrder::find($small_order_id);
        //return $small_order_id;
        if (empty($origin_data)) {
            return $this->error('非法提交');
        }
        $res_update = ProjectSmallOrder::where('id', $small_order_id)->update($data);
        if ($res_update) {
//            small_order_to_boss($origin_data->boss_id, 40015);
            push_accord_by_equip($origin_data->boss_id, 40015, 'message_small_order', '', $origin_data->task_id);
            return $this->error('提交成功', 0);
        } else {
            return $this->error('提交失败');
        }

    }

    /**
     * 业主确认管家的验收请求
     * 状态:1.管家提交该工程,2.业主驳回管家整改单,3.业主付款,4.管家提交验收,5.业主确认,6.结算完成(新约单),7.异常结单
     */
    public function bossSureSmallOrder(Request $request) {
        //验收结算,该阶段自动变为待提交
        $small_order_id   = $request->json('small_order_id');
        $small_order_info = ProjectSmallOrder::find($small_order_id);
        $task_id          = $small_order_info->task_id;
        if ($small_order_info->status < 3) {
            return $this->error('非法提交');
        }
        //更改sub_ordre文字
        $sub_order_id = $small_order_info->sub_order_id;
        SubOrderModel::where('id', $sub_order_id)->update(['title' => '支付工程变更单(已支付)']);
        $small_order_info->status = 6;
        $small_order_info->save();
        //找到对应的工人和监理以及应付的工资
        $all_price = $small_order_info->offer_change_price + $small_order_info->cash_house_keeper;
        $labor     = $small_order_info->labor;
        //找到要付给管家的工资
        $cash_house_keeper = $small_order_info->cash_house_keeper;
        $house_keeper_id   = $small_order_info->house_keeper_id;
        //扣钱啦
        $work_offer_data = WorkOfferModel::where('task_id', $task_id)->where('sn', $small_order_info->sn)->first();//找到哪个阶段
        $title_sn        = $work_offer_data->title;
        $boss_id         = $small_order_info->boss_id;
        $project_type    = $work_offer_data->project_type;
        $status = DB::transaction(function () use ($all_price, $task_id, $boss_id, $labor, $title_sn, $project_type, $cash_house_keeper, $house_keeper_id) {
            $this->payWorkerRespository->bounty($all_price, $task_id, $boss_id, 5, 6, true, false);//扣冻结资金
            //收款记录(工人)
            if (!empty($labor)) {
                $cash = $all_price - $cash_house_keeper;
                platformOrderModel::sepbountyOrder($labor, $cash, $task_id, $title_sn . '工程变更单收入', 2, 1, $project_type);
                $this->payWorkerRespository->bounty($cash, $task_id, $labor, 1, 2);//工人余额增加
            }
            //收款记录(管家)
            if (!empty($cash_house_keeper)) {
                platformOrderModel::sepbountyOrder($house_keeper_id, $cash_house_keeper, $task_id, $title_sn . '工程变更单收入', 2, 1, $project_type);
                $this->payWorkerRespository->bounty($cash_house_keeper, $task_id, $house_keeper_id, 1, 2);//管家余额增加
            }
        });
        if (is_null($status)) {
//            small_order_to_worker($house_keeper_id, 20009);
            push_accord_by_equip($house_keeper_id, 20009, 'message_small_order', '', $task_id);
            return $this->error('钱已付工人',0);
        } else {
            return $this->error('付款失败');
        }


    }

    /**
     * 业主取消这次小订单
     * 状态:1.管家提交该工程,2.业主驳回管家整改单,3.业主付款,4.管家提交验收,5.业主确认,6.结算完成(新约单),7.异常结单
     */
    public function cancelSmallOrder(Request $request) {
        $small_order_id = $request->json('small_order_id');
        $data           = ProjectSmallOrder::find($small_order_id);
        if ($data->status > 2) {
            return $this->error('无法取消');
        }
        $data->status = 7;
//        small_order_to_worker($data->house_keeper_id, 20010);
        push_accord_by_equip($data->house_keeper_id, 20010, 'message_small_order', '', $data->task_id);
        if ($data->save()) {
            return $this->error('取消成功', 0);
        } else {
            return $this->error('取消失败');
        }

    }

    /**
     * 设计师不接单,废弃订单处理
     */
    public function workerNotReply() {
        //找出所有已发出的单子
        $tasks = TaskModel::select('created_at', 'id')->where('status', 3)->where('bounty_status', 1)->get();
        //去找设计师
        foreach ($tasks as $k => $v) {
            $work_log = workDesignerLog::select('created_at', 'task_id')->where('task_id', $v['id'])->where('is_refuse', 0)->where('boss_confirm', 0)->first();
            if ($work_log) {
                //当前时间减去创建时间
                $time_cha = time() - strtotime($work_log->created_at);
                //超过两天未处理,结束订单
                if ($time_cha > (24 * 3600 * 2)) {
                    TaskModel::where('id', $work_log->task_id)->update(['status', 9]);
                }
            }
        }
        return $this->responseSuccess();
    }

    /**
     * 根据配置单获取城市和拼音
     */
    public function getCityByLists() {
        $data = ProjectConfigureModel::select('city_id', 'district.spelling', 'district.name')->distinct('city_id')->leftJoin('district', 'district.id', '=', 'project_configure_list.city_id')->orderBy('district.spelling')->get();
        return $this->success($data);
    }

    /**
     * 辅材包城市列表
     */
    public function getAuxAllCity() {
        $city_data = Auxiliary::select('auxiliary.city_id', 'district.spelling', 'district.name')->distinct('auxiliary.city_id')
            ->leftJoin('district', 'district.id', '=', 'auxiliary.city_id')
            ->orderBy('district.id')->get();
        return $this->success($city_data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 自助改状态
     */
    public function changeStatusByTest(Request $request) {
        $task_id = $request->get('task_id');
        $sn      = $request->get('sn');
        $status  = $request->get('status');
        WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->update(['status' => $status]);
        return $this->error( 'success',0);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 自助改匹配工人完成
     */
    public function LaborMatchStatusByTest(Request $request) {
        $task_id = $request->get('task_id');
        WorkOfferModel::where('task_id', $task_id)->where('project_type', 0)->where('sn', 2)->update(['status' => 4]);
        return $this->error('success',0);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 自助传图
     */
    public function detectUploadImgByTest(Request $request) {
        $data['task_id'] = $request->get('task_id');
        $data['sn']      = $request->get('sn');
        $work            = WorkModel::where('status', 2)->where('task_id', $data['task_id'])->first();
        //未提交初步设计图纸驳回
        if ($data['sn'] == 1) {
            $workOffer                = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', 1)->where('work_id', $work->id)->first();
            $workOffer->upload_status = 1;
            if ($workOffer->save())
                return $this->error('success',0);
        }
        if ($data['sn'] == 2) {
            $workOffer                = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', 2)->where('work_id', $work->id)->first();
            $workOffer->upload_status = 1;
            if ($workOffer->save())
                return $this->error( 'success',0);
        }
        return $this->error('非法提交');
    }

    /**
     * 查看辅材包详细内容
     */
    public function auxDetail($id) {
        $agree = Auxiliary::find($id);
        return view('auxdetailByapi', compact('agree'));
    }
    /**
     * 根据城市id获取对应的辅材包信息
     */
    public function getAuxDetailByCityid(Request $request) {
        $city_id  = empty($request->get('city_id')) ? 291 : $request->get('city_id');
        $aux_data = Auxiliary::select('name', 'price', 'id', 'detail_url')->where('city_id', $city_id)->orderBy('id')->get();
        foreach ($aux_data as &$v) {
            $v['detail_url'] = empty($v['detail_url']) ? '' : url($v['detail_url']);
        }
        return $this->success($aux_data);
    }
}
