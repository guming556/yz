<?php

namespace App\Http\Controllers\Api;

use App\Modules\Employ\Models\EmployWorkOfferModel;
use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Employ\Models\EmployModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\Order\Model\ShopOrderModel;
use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\Employ\Models\EmployWorkModel;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\ServiceModel;

class ApiTaskAppointController extends BaseController {
    /**
     * @param $uid
     * @return mixed
     * 拉取业主已关注的人
     */
    public function makeAppointment() {
        //拉取业主已关注的人
        $uid  = 75;
        $info = UserFocusModel::where('uid', $uid)->get()->toArray();
        //return $info;
        dd($info);
    }


    /**
     * 提交约单任务
     * @param Request $request
     */
    public function employUpdate() {

        $data = array(
            "employee_uid" => "0",//设计师id
            "employer_uid" => "8",//雇主id
            "service_id" => "0",
            "title" => "这是第三个测试项目",
            "desc" => "<p>创建项目</p>",
            "phone" => "15399900130",
            "bounty" => "0",
            "delivery_deadline" => "2017年05月30日",
            "editorValue" => "<p>创建项目</p>",

            //需要新加入的字段
            "bedroom" => "3",
            "living_room" => "3",
            "kitchen" => "3",
            "washroom" => "3",
            //            "user_id" => "133",
            "square" => "",
            "favourite_style" => "245",
            "user_type" => "2",
            "project_position" => "20",
            //            "description" => "备注，这是保留字段，目前传空",
            //            "show_cash" => "1000"
        );

        $setting = array(
            'bedroom' => '居室',   //居室
            'living_room' => '客厅',   //客厅
            'kitchen' => '厨房',   //厨房
            'washroom' => '卫生间',   //卫生间
            'balcony' => '阳台'      //  阳台
        );

        $data['room_config'] = '';
        foreach ($setting as $key => $value) {
            $data[$key] = !empty($data[$key]) ? $data[$key] : 0;
            if (!empty($data[$key])) {
                $data['room_config'] .= $data[$key] . $setting[$key] . ',';
            }
        }
        $data['square']    = ProjectPositionModel::where('id', $data['project_position'])->first()->square; //房屋面积
        $data['product'][] = ServiceModel::select('id')->where('identify', 'SHOUXUFEI')->first()->id;


        $time = date('Y-m-d H:i:s', time());
//        //雇佣最小金额限定
//        $employ_bounty_min_limit = \CommonClass::getConfig('employ_bounty_min_limit');
//
//        //验证赏金最小值
//        $task_bounty_min_limit = $employ_bounty_min_limit;

        //验证赏金大小合法性
//        if ($data['bounty'] < $task_bounty_min_limit) {
//            return response()->json(['error' => '不能低于最小限定金' . $task_bounty_min_limit], '500');
//        }
        //创建一条雇佣记录
        $data['employee_uid']      = intval($data['employee_uid']);
        $data['employer_uid']      = $data['employer_uid'];
        $data['delivery_deadline'] = preg_replace('/([\x80-\xff]*)/i', '', $data['delivery_deadline']);
        $data['status']            = 0;
        $data['created_at']        = $time;
        $data['updated_at']        = $time;

        //判断   `employ_type` '0表示雇佣 1表示服务雇佣'
        if ($data['service_id'] != 0) {
            $data['employ_type'] = 1;
        }

        $id_employ = EmployModel::employCreate($data)->id;


        //创建一条雇佣记录
        if ($id_employ) {
            return $this->error('约单成功', 0,['id_employ' => $id_employ]);
        }
        return $this->error('约单失败');
    }


    /**
     * 业主确认哪几个人可以接这个单
     */
    public function bossChooseDesigner() {
        $id               = 3;//约单任务的主id
        $user_designer_id = [131, 133, 130];//设计师id
        $from_uid         = 8;//业主id

        if (!empty($user_designer_id)) {
            foreach ($user_designer_id as $item => $value) {
                $data[] = [
                    'employ_id' => $id,
                    'designer' => $value,
                    'desc' => '',
                    'pay_to_user_cash' => '',
                    'from_uid' => $from_uid
                ];
            }
            foreach ($data as $n => $m) {
                $data[$n]['desc'] = \CommonClass::removeXss($data[$n]['desc']);
                //创建三条employ_work记录，修改当前任务状态
                $result = EmployWorkModel::employDilivery($m, $m['designer']);
            }

            if ($result) {
                return $this->error('已要求设计师处理您的订单',0);
            } else {
                return $this->error('系统错误');
            }
        } else {            //三个人都没接受,超过24小时,订单关闭
            $time_now           = time();
            $time_employ_create = EmployModel::find($id)->created_at;
            if ($time_now > strtotime($time_employ_create + 3600 * 24)) {

                return $this->error('订单超过24小时,请重新下单');
            } else {
                return$this->error('系统错误');
            }
        }

    }


    /**
     * 设计师接受这次约单并报价
     * @param $id
     * @param $type
     * @return \Illuminate\Http\RedirectResponse
     * work状态 0表示没有验收 1表示验收(0,1走网站) 2表示威客投稿 3表示威客中标  4表示威客交付 5表示验收成功 6表示验收失败(交易维权)
     */
    public function designerAccept() {

//        $id               = 2;//约单任务的主id
//        $user_designer_id = 133;//设计师id
//        $type = 2;//1:雇主取消 2:接受雇佣 3:拒绝雇佣
//        //有两个或者三个人都接受任务,在employ_work表插入对应的几条数据
//        $pay_to_user_cash = EmployModel::find($id)->bounty;

        $id          = 3;//约单任务的主id
        $square      = 50;     //面积
        $designer_id = 131;     //设计师id
        $unit_price  = UserDetailModel::where('uid', $designer_id)->first()->cost_of_design; //单价

        if ($square <= 0) return $this->error('参数不正确！');
        if ($unit_price <= 0) return $this->error('参数不正确！');

        $total = $square * $unit_price;

        // TODO 这里后期从数据库获取
        $list            = [
            'total' => $total,
            '1' => [
                "title" => "初步设计",
                "price" => 0.2 * $total
            ],
            '2' => [
                "title" => "深化设计",
                "price" => 0.4 * $total
            ],
            '3' => [
                "title" => "施工指导",
                "price" => 0.4 * $total
            ]
        ];
        $is_accet        = EmployWorkModel::where('employ_id', $id)->where('uid', $designer_id)->first();
        $is_accet->pay_to_user_cash = $total;
        //报价完成,往work_Offer表插入数据
        if ($is_accet->save()) {

            $data = [
                'title' => '工作者报价流程',
                'sn' => '0',
                'employ_work_id' => $is_accet->id,
                'percent' => json_encode(array('0.2', '0.4', '0.4')),
                'pay_to_user_cash' => $total,
                'from_uid' => $is_accet->from_uid,
                'to_uid' => $designer_id,
                'status' => '1',
                'employ_id' => $id,
            ];

            EmployWorkOfferModel::create($data);

            return $this->success($list);
        }

    }


    /**
     * 业主确认接单的人   这个接口不需要用了，使用的是 ApiTaskController 里的接口
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * employ状态 0表示雇佣创建 1表示接受雇佣 2表示已经交付 3表示验收成功 4表示完成 5表示拒绝雇佣 6表示雇主取消任务 7表示雇主维权 8表示威客维权 9表示雇佣过期
     * work状态 0表示没有验收 1表示验收(0,1走网站) 2表示威客投稿 3表示威客中标  4表示威客交付 5表示验收成功 6表示验收失败(交易维权)
     * work_offer状态 0未开始 1设计师submit 2用户commit 3业主退回 4done
     */
    public function interview() {

        $data['from_uid'] = 8;//雇主
        $data['task_id']  = 3;//任务id
        $data['to_uid']   = 133;//雇佣者id

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('参数不完整');
            }
        }
        //修改work表,表明已中标
        $is_accet_res = EmployWorkModel::where('employ_id', $data['task_id'])->where('uid', $data['to_uid'])->update(['status' => 3]);
        //修改任务表,把设计师写进去
        $employ_res = EmployModel::where('id', $data['task_id'])->update(['status' => 2, 'employee_uid' => $data['to_uid']]);

        if ($is_accet_res && $employ_res) {
            return $this->error('确认设计师成功',0);
        } else {
            return $this->error('操作失败');
        }

    }


    /**
     * 业主已选人,确认支付设计师的报价单
     * employ状态 0表示雇佣创建 1表示接受雇佣 2表示已经交付 3表示验收成功 4表示完成 5表示拒绝雇佣 6表示雇主取消任务 7表示雇主维权 8表示威客维权 9表示雇佣过期
     * work状态 0表示没有验收 1表示验收(0,1走网站) 2表示威客投稿 3表示威客中标  4表示威客交付 5表示验收成功 6表示验收失败(交易维权)
     * work_offer状态 0未开始 1设计师submit 2用户commit 3业主退回 4done
     */

    public function bossPayDesigner() {

        $data = [
            'from_uid' => 8,
            'id' => 3,
            'to_uid' => 133,
            'pay_canel' => 0,
            'password' => 123456
        ];
        //找到work_offer的数据
        $work       = EmployWorkModel::where('employ_id', $data['id'])->where('status', 3)->first();//获取任务
        if(empty($work)){
            return $this->error('该项目状态不对');
        }
        $work_offer = EmployWorkOfferModel::where('employ_id', $data['id'])->where('to_uid', $data['to_uid'])->where('sn', 0)->first();
        //创建订单
        $is_ordered = ShopOrderModel::employOrder($data['from_uid'], $work->pay_to_user_cash, $data, '付款' . $work_offer->title);
        $res_pay_designer = EmployModel::employBounty($work->pay_to_user_cash, $data['id'], $data['from_uid'], $is_ordered->code);

        //找到该用户,扣除余额补充到保证金里面
        $user_boss        = UserDetailModel::where('uid', $data['from_uid'])->first();
        $res_frozen_money = UserDetailModel::where('uid', $data['from_uid'])->update(['frozen_amount' => $user_boss->frozen_amount += $work->pay_to_user_cash]);//把扣除金额写进冻结资金

        if ($is_ordered && $res_pay_designer && $res_frozen_money) {

            // 支付完成,修改off表第一阶段为4
            $ret_offer_sn = EmployWorkOfferModel::where('employ_id', $data['id'])->where('to_uid', $data['to_uid'])->where('sn', 0)
                ->update(['status' => 4]);

            // 设计步骤开始
            $ret_work_status = EmployWorkModel::where('id', $work['id'])->where('uid', $data['to_uid'])->update(['status' => 4]);

            if ($ret_work_status && $ret_offer_sn) {
                $data_offer = EmployWorkOfferModel::where('employ_id', $data['id'])->where('to_uid', $data['to_uid'])->where('sn', 0)->first();

                $arr = array(
                    '1' => array(
                        'type' => 'designer',
                        'employ_id' => $data_offer['employ_id'],
                        'sn' => 1,
                        'title' => "初步设计",
                        'percent' => '0.2',
                        'pay_to_user_cash' => 0.2 * $data_offer['pay_to_user_cash'],
                        'employ_work_id' => $data_offer['employ_work_id'],
                        'from_uid' => $data_offer['from_uid'],
                        'to_uid' => $data_offer['to_uid'],
                        'status' => 0
                    ),
                    '2' => array(
                        'type' => 'designer',
                        'employ_id' => $data_offer['employ_id'],
                        'sn' => 2,
                        'title' => "深化设计",
                        'percent' => '0.4',
                        'pay_to_user_cash' => 0.4 * $data_offer['pay_to_user_cash'],
                        'employ_work_id' => $data_offer['employ_work_id'],
                        'from_uid' => $data_offer['from_uid'],
                        'to_uid' => $data_offer['to_uid'],
                        'status' => 0
                    ),
                    '3' => array(
                        'type' => 'designer',
                        'employ_id' => $data_offer['employ_id'],
                        'sn' => 3,
                        'title' => "施工指导",
                        'percent' => '0.4',
                        'pay_to_user_cash' => 0.4 * $data_offer['pay_to_user_cash'],
                        'employ_work_id' => $data_offer['employ_work_id'],
                        'from_uid' => $data_offer['from_uid'],
                        'to_uid' => $data_offer['to_uid'],
                        'status' => 0
                    )
                );

                foreach ($arr as $key => $value) {
                    EmployWorkOfferModel::create($value);
                }

                return $this->error('支付成功',0);
            } else {
                return $this->error('支付失败');
            }

        }


    }


    /**
     * 付款约单任务(平台)
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function employBountyUpdate() {
        //$data = $request->except(['_token','_url']);
        $data = ['id' => 3,//约单订单主id
                 'employer_uid' => 8,//业主
                 'pay_canel' => 0,
                 'password' => 123456];

        $data['id'] = intval($data['id']);
        //查询用户发布的数据
        $employ = EmployModel::where('id', $data['id'])->first();

        //判断用户所要支付的是否是自己的任务和任务是否已经支付
        if ($employ['employer_uid'] != $data['employer_uid'] || $employ['bounty_status'] != 0) {
            return $this->error('该订单已付款');
        }

        //查询用户的余额
        $balance = UserDetailModel::where('uid', $employ->employer_uid)->first();
        $balance = $balance['balance'];

        //创建订单
        $is_ordered = ShopOrderModel::employOrder($data['employer_uid'], $employ['bounty'], $data, '约单创建订单');

        if (!$is_ordered) return $this->error('创建订单失败');
        //判断用户如果选择的余额支付
        if ($balance >= $employ['bounty'] && $data['pay_canel'] == 0) {
            //验证用户的密码是否正确
            $salt               = UserModel::find($data['employer_uid'])->salt;
            $alternate_password = UserModel::find($data['employer_uid'])->alternate_password;
            $password           = UserModel::encryptPassword($data['password'], $salt);
            if ($password != $alternate_password) {
                return $this->error('您的支付密码不正确');
            }
            //支付产生订单
            $res = EmployModel::employBounty($employ['bounty'], $employ['id'], $data['employer_uid'], $is_ordered->code);

            if ($res) {
                return $this->error('支付成功!',0);
            } else {
                return $this->error('支付失败!');
            }

        }

    }


    /**
     * 设计师查看约单任务详情页面
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function workin() {
        $id      = 2;//约单任务的主id
        $user_id = 8;//设计师id
        //查询当前
        $employ = EmployModel::where('id', $id)->first();

        //判断是否是一个雇佣任务
        if (empty($employ)) {
            return $this->error('找不到该任务');
        }
        //判断当前用户的角色是雇主还是被雇佣人和游客
        if ($employ['employer_uid'] == $user_id) {
            $role      = 1;//表示雇主
            $user_data = UserDetailModel::employeeData($employ['employee_uid']);
        } else if ($employ['employee_uid'] == $user_id) {
            $role      = 2;//表示威客
            $user_data = UserDetailModel::employerData($employ['employer_uid']);
        } else {
            return $this->error('参数错误');
        }
        //查询任务详细
        $employ_detail = EmployModel::employDetail($id);

        //查询任务的附件id
        $attatchment_ids = UnionAttachmentModel::where('object_id', '=', $id)->where('object_type', 2)->lists('attachment_id')->toArray();
        //多维数组转换成一维数组,laravel帮助函数
        $attatchment_ids = array_flatten($attatchment_ids);

        $attatchment = AttachmentModel::whereIn('id', $attatchment_ids)->get();
        dd($employ_detail['status']);
        //根据任务进度查询任务的稿件以及评价等信息
        $work            = array();
        $work_attachment = array();
        if ($employ_detail['status'] >= 2 && $employ_detail['status'] < 6) {
            //查询稿件
            $work = EmployWorkModel::where('employ_id', $id)->first();
            //查询稿件附件
            $work_attachment = UnionAttachmentModel::where('object_id', $work['id'])->where('object_type', 3)->lists('attachment_id')->toArray();
            $work_attachment = AttachmentModel::whereIn('id', $work_attachment)->get();
        }
        $comment        = array();
        $comment_status = false;
        if ($employ_detail['status'] == 4 || $employ_detail['status'] == 3) {
            //查询评论
            $comment = EmployCommentsModel::where('employ_id', $id)->get();
            //判断当前角色是否已经评价
            $comment_status = EmployCommentsModel::where('employ_id', $id)->where('from_uid', $user_id)->first();
        }
        //查询是否被关注
        $isFocus = UserFocusModel::where('uid', $user_id)->where('focus_uid', $user_data['uid'])->first();

        //查询当前用户店铺是否开启
        $user_shop = ShopModel::where('status', 1)->where('uid', $user_data['uid'])->first();
        $domain    = url();
        $this->theme->set('employ_status', $employ_detail['status']);
        $this->theme->set('employ_bounty_status', $employ_detail['bounty_status']);

        $view = [
            'role' => $role,
            'user_data' => $user_data,
            'employ_data' => $employ_detail,
            'attachment' => $attatchment,
            'work' => $work,
            'comment' => $comment,
            'domain' => $domain,
            'contact' => Theme::get('is_IM_open'),
            'work_attachment' => $work_attachment,
            'comment_status' => $comment_status,
            'user_shop' => $user_shop,
            'isFocus' => $isFocus
        ];

        return $this->theme->scope('employ.workin', $view)->render();
    }


    // 输入面积后，根据后台设定比例 生成设计师报价清单
    public function designerOffer() {

        //$square      = floatval($request->get('square'));     //面积
//        $designer_id = $request->get('designer_id');     //设计师id
        $square      = 50;     //面积
        $designer_id = 136;     //设计师id
        $unit_price  = UserDetailModel::where('uid', $designer_id)->first()->cost_of_design; //单价

        if ($square <= 0) return $this->error('参数不正确！');
        if ($unit_price <= 0) return $this->error('参数不正确！');

        $total = $square * $unit_price;

        // TODO 这里后期从数据库获取
        $list = [
            'total' => $total,
            '1' => [
                "title" => "初步设计",
                "price" => 0.2 * $total
            ],
            '2' => [
                "title" => "深化设计",
                "price" => 0.4 * $total
            ],
            '3' => [
                "title" => "施工指导",
                "price" => 0.4 * $total
            ]
        ];

        return $this->success($list);
    }


    public function boss_choose() {
        $id          = 2;//约单任务的主id
        $type        = 2;//1:雇主取消 2:接受雇佣 3:拒绝雇佣
        $uid_desiner = 130;//设计师id

        EmployWorkModel::where('employ_id', $id)->where('uid', $uid_desiner)->update(['status' => 3]);//设计师中标
        $result = EmployModel::employHandle($type, $id, $uid_desiner);

    }



    /**
     * @param $task_id 任务id
     * @param $to_uid 设计师id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 工作端提交（设计师）(约单)
     * employ状态 0表示雇佣创建 1表示接受雇佣 2表示已经交付 3表示验收成功 4表示完成 5表示拒绝雇佣 6表示雇主取消任务 7表示雇主维权 8表示威客维权 9表示雇佣过期
     * work状态 0表示没有验收 1表示验收(0,1走网站) 2表示威客投稿 3表示威客中标  4表示威客交付 5表示验收成功 6表示验收失败(交易维权)
     * work_offer状态 0未开始 1设计师submit 2用户commit 3业主退回 4done
     */
    public function designerCommit() {

        $data['task_id'] = 3;
        $data['to_uid']  = 133;

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('非法参数');
            }
        }


        $work = EmployWorkModel::where('status', 4)->where('employ_id', $data['task_id'])->first();


        $ret  = EmployWorkOfferModel::where('employ_work_id', $work['id'])->where('employ_id', $data['task_id'])
            ->where('to_uid', $data['to_uid'])
            ->where('sn', '>', 0)
            ->get()->toArray();

        foreach ($ret as $key => $value) {
            if ($value['status'] == 0 || $value['status'] == 3) {
                EmployWorkOfferModel::where('id', $value['id'])->update(['status' => 1]);
                return $this->error('提交成功',0);
                break;
            }
        }

        return $this->error('提交失败');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 业主确认(约单)
     */
    public function BossCommit() {

        $data['id']       = 3;
        $data['from_uid'] = 8;

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('非法参数');
            }
        }
        $work = EmployWorkModel::where('status', 4)->where('employ_id', $data['id'])->first();

        // 该项目做到哪个阶段
        $ret = EmployWorkOfferModel::where('employ_work_id', $work['id'])->where('employ_id', $data['id'])
            ->where('sn', '>', 0)
            ->get()->toArray();

        foreach ($ret as $key => $value) {

            if ($value['status'] == 1) {

                $employ_offer_date = EmployWorkOfferModel::where('id', $value['id'])->first();
                $count_submit      = $employ_offer_date->count_submit;
                $ret_status        = EmployWorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit, 'status' => 2]);

                if ($ret_status && $count_submit < 4) {

                    //扣款记录(业主)
                    $is_ordered      = ShopOrderModel::employOrder($work['from_uid'], $employ_offer_date['pay_to_user_cash'], $data, $value['title'] . '款');
                    $res_deduct_boss = EmployModel::employBounty($employ_offer_date['pay_to_user_cash'], $work['from_uid'], $work['from_uid'], $is_ordered->code, 1, true);//扣冻结资金

                    //收款记录(设计师)
                    $is_ordered_designer = ShopOrderModel::employOrder($work['uid'], $employ_offer_date['pay_to_user_cash'], $data, $value['title'] . '款');
                    $res_pay_designer    = EmployModel::employBounty($employ_offer_date['pay_to_user_cash'], $data['id'], $work['uid'], $is_ordered_designer->code, 1, false, 2);//设计师余额增加

                    if ($res_pay_designer && $res_deduct_boss) {
                        $msg = ['message' => '确认成功,钱已付设计师', 'count_submit' => $count_submit];
                        return $this->success($msg);
                    } else {
                        $msg = ['message' => '确认失败', 'count_submit' => $count_submit];
                        return $this->error($msg);
                    }

                } else {
                    $msg = ['message' => '确认失败,状态不对', 'count_submit' => $count_submit];
                    return $this->error($msg);
                }
                break;
            }
        }

        return $this->error('系统错误');

    }



    /**
     * 交付稿件
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function workCreate() {
        $data = [
            'employ_id' => 2,
            'designer' => 8,
            'desc' => '了为了我聊天了',
            //'file_id' => [88]
        ];

        //$data = $request->except(['_token','_url']);
        $data['desc'] = \CommonClass::removeXss($data['desc']);
        //判断当前用户是否是被雇佣者
        $uid       = $data['designer'];
        $employ_id = intval($data['employ_id']);

        $employ = EmployModel::where('id', $employ_id)->where('employee_uid', $uid)->first();

        if (!$employ)
            return $this->error('你不是被雇佣者不需要交付当前任务稿件');
        //判断当前稿件是否处于投稿期间
        if ($employ['status'] != 1) {
            return $this->error('当前任务不是处于交稿状态');
        }

        //创建一条employ_work记录，修改当前任务状态
        $result = EmployWorkModel::employDilivery($data, $uid);

        if (!$result)
            return $this->error('提交失败');

        return $this->error('提交成功',0);
    }


    /**
     * 验收成功
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptWork() {
        $id  = 2;
        $uid = 133;
        //验证当前任务验收合法性
        $employ = EmployModel::where('id', $id)->first();

        if ($employ['status'] != 2)
            return $this->error('当前任务不是处于验收状态');
        if ($employ['employer_uid'] != $uid)
            return $this->error('你不是当前雇佣任务的雇主，不能验收');
        //dd($employ);
        //验收操作
        $result = EmployModel::acceptWork($id, $uid, false);

        if (!$result)
            return $this->error('验收失败');

        return $this->error('确认是当前雇佣任务的雇主，验收成功',0);
    }
}
