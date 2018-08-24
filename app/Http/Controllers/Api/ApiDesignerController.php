<?php

namespace App\Http\Controllers\Api;

use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Pay\OrderModel;
use App\Modules\Order\Model\OrderModel as platformOrderModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\Task\Model\WorkAttachmentModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Project\ProjectLaborChange;
use App\PushSentenceList;
use App\PushServiceModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\Manage\Model\ChargeModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\Project\ProjectConfigureChangeModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\User\Model\RealnameAuthModel;
use DB;

use App\Respositories\UserRespository;
use App\Respositories\TaskRespository;
use App\Respositories\PayWorkerRespository;
use Illuminate\Support\Facades\Config;


class ApiDesignerController extends BaseController {


    protected $userRespository;
    protected $taskRespository;
    protected $payWorkerRespository;

    public function __construct(UserRespository $userRespository, TaskRespository $taskRespository, PayWorkerRespository $payWorkerRespository) {
        $this->userRespository      = $userRespository;
        $this->taskRespository      = $taskRespository;
        $this->payWorkerRespository = $payWorkerRespository;
    }


    /**
     * 设计师上传作品  基本参数
     *
     * @return \Illuminate\Http\Response
     */
    public function workRelease(Request $request) {
        // 主要
        $data['uid']           = $request->get('user_id');      //用户id
        $data['title']         = $request->get('title');        //标题
        $data['style_id']      = $request->get('style_id');     //风格id
        $data['square']        = $request->get('square');       //面积
        $data['house_id']      = $request->get('house_id');     //户型id
        $data['cover']         = $request->file('cover');       //封面图
        $data['goods_address'] = $request->get('goods_address');       //作品地址
        $data['status']        = 1;
        $data['cate_id']       = $data['style_id'];     //这个参数是冗余的，保留一下

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json(array('error' => $key . '参数不可为空'), '404');
            }
        }

        $data['shop_id']      = ShopModel::where('uid', $data['uid'])->first()->id;
        $data['is_recommend'] = 0;

        $result = \FileClass::uploadFile($data['cover'], 'sys',null,true);

        if ($result) {
            $result        = json_decode($result, true);
            $data['cover'] = $result['data']['url'];
        } else {
            return response()->json(array('error' => '上传失败'), '500');
        }

        // TODO 判断配置项商品上架是否需要审核
        // $config = ConfigModel::getConfigByAlias('goods_check');designerOrder
        // if(!empty($config) && $config->rule == 1){
        //     $goodsCheck = 0;
        // }else{
        //     $goodsCheck = 1;
        // }
        // $data['status'] = $goodsCheck;
        $goods = GoodsModel::create($data);
        if ($goods) {
            return response()->json(array('object_id' => $goods->id));
        }

        return response()->json(array('error' => '上传失败'), '500');
    }

    // 添加版块
    public function workAttachment(Request $request) {

        $data['object_id'] = $request->get('object_id');        //发布的作品id
        $data['picture']   = $request->file('picture');       //图片
        $data['user_id']   = $request->get('user_id');       //用户id
        $data['title']     = $request->get('title');       //位置名称
        $data['desc']      = $request->get('desc');       //描述
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json(array('error' => '参数不可为空'), '404');
            }
        }

        $attachment_id = $this->fileUpload($data);

        $arrAttachment[] = [
            'object_id' => $data['object_id'],
            'object_type' => 4,
            'attachment_id' => $attachment_id,
            'created_at' => date('Y-m-d H:i:s', time())
        ];

        $status = UnionAttachmentModel::insert($arrAttachment);
        if ($status) {
            return response()->json(array('message' => '上传成功'));
        }

        return response()->json(array('error' => '上传失败'), '500');
    }


    // 上传完主图信息后再上传副图
    public function fileUpload($data) {
        $file = $data['picture'];

        //将文件上传的数据存入到attachment表中
        $attachment = \FileClass::uploadFile($file, 'user',null,true);
        $attachment = json_decode($attachment, true);
        //判断文件是否上传
        if ($attachment['code'] != 200) {
            return response()->json(array('error' => '文件上传失败！'), '500');
        }

        $attachment_data               = array_add($attachment['data'], 'status', 1);
        $attachment_data['created_at'] = date('Y-m-d H:i:s', time());
        //将记录写入到attchement表中
        $attachment_data['user_id'] = $data['user_id'];
        $attachment_data['title']   = $data['title'];
        $attachment_data['desc']    = $data['desc'];
        $result                     = AttachmentModel::create($attachment_data);
        $result                     = json_decode($result, true);
        if (!$result) {
            return response()->json(array('error' => '文件上传失败！'), '500');
        }
        //回传附件id
        return $result['id'];
    }


// 某个设计师作品列表
    public function designerGoods(Request $request) {
        $designer_id = intval($request->get('designer_id'));
        $ret         = GoodsModel::select('user_detail.uid as designer_id',
            'goods.created_at',
            'goods.id as goods_id',
            'goods.title', 'goods.cover',
            'user_detail.avatar',
            'user_detail.nickname',
            'goods.goods_address',
            'cate.name as style',
            'house.name as house'
        )->where('goods.status', 1)
            ->where('goods.type', 1)
            ->where('is_delete', 0)
            ->where('goods.uid', $designer_id)
            ->leftjoin('user_detail', 'user_detail.uid', '=', 'goods.uid')
            ->leftJoin('cate','cate.id','=','goods.style_id')
            ->leftJoin('house','house.id','=','goods.house_id')
            ->orderBy('goods.updated_at', 'desc')
            ->get()->toArray();

        foreach ($ret as $key => &$value) {

            $value['avatar'] = !empty($value['avatar']) ? url($value['avatar']) : '';
            $value['cover'] = url(str_replace('.','{@fill}.',$value['cover']));
//            $value['cover']  = !empty($value['cover']) ? url($value['cover']) : '';
        }
        return response()->json($ret);

    }



    // TODO    若根据比例算出的价格大于总价时未处理
    // 输入面积后，根据后台设定比例 生成设计师报价清单   
    public function designerOffer(Request $request) {

        $square            = floatval($request->get('square'));     //面积
        $unit_price        = $request->get('unit_price');      //单价以前台为准
        $task_id           = $request->get('task_id');      //任务id

        if ($square <= 0) return response()->json(array('error' => '参数不正确！'), '500');
        if ($unit_price <= 0) return response()->json(array('error' => '参数不正确！'), '500');

        $change_unit_price = TaskModel::where('id', $task_id)->update(['designer_actual_price' => $unit_price]); //每个任务的实际单价

        if (empty($change_unit_price)) return response()->json(array('error' => '保存单价失败'), '500');

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

        return response()->json($list);
    }


    /**
     * @param $task_id
     * @return int
     * 管家提交第一次报价（面积*业主选择的星级价钱）
     */
    public function getHouseKeeperPrice(Request $request) {

        $task_id           = $request->json('task_id');
        $square            = $request->json('square');
        $star_house_keeper = empty($request->json('star_get')) ? '1' : $request->json('star_get');
        $uid_houseKeeper   = WorkModel::where('task_id', $task_id)->where('status', 1)->first();

        if (empty($uid_houseKeeper)) return response()->json(['error' => '该管家还没中标'], 500);
        $uid_houseKeeper = $uid_houseKeeper->uid;   //获取中标管家或者监理

        $users     = UserDetailModel::select('star')->where('uid', $uid_houseKeeper)->first();
        $user_info = UserModel::find($uid_houseKeeper);
        if (empty($users) || empty($user_info)) {
            return response()->json(['找不到用户'], 500);
        }
        $star_select = $users->star;

        if ($star_house_keeper > $star_select) {
            return response()->json(['error' => '您选择的星级大于您本身的星级'],500);
        }

        $user_type = UserModel::find($uid_houseKeeper)->user_type;

        if ($user_type == 3) {
            $config1 = LevelModel::getConfigByType(1)->toArray();
        } elseif ($user_type == 4) {
            $config1 = LevelModel::getConfigByType(2)->toArray();
        }

        $workerStarPrice = LevelModel::getConfig($config1, 'price');

        $task_info = TaskModel::find($task_id);
        if ($task_info->type_id != 2) {
            //约单以要约的人的星级为准
            //$star_house_keeper = UserDetailModel::select('star')->where('uid', $uid_houseKeeper)->first()->star;
            //这里的星级以业主的所选星级为准
            $star_house_keeper = $task_info->housekeeperStar;
        }

        $res_price = $workerStarPrice[$star_house_keeper - 1]->price;
        $salary    = $res_price * $square;

        return response()->json(['salary' => $salary]);
    }


    /**
     * 业主支付工程配置单
     */
    public function bossPayHouseKeeperOffer(Request $request) {

        $data['from_uid'] = $request->json('boss_uid');
        $data['task_id']  = $request->json('task_id');
        $data['password'] = $request->json('password');        // 若是余额支付，要提交密码

        $data_lists = ProjectConfigureTask::where('task_id', $data['task_id'])->where('is_sure', 1)->first();//拿到配置单
        if (empty($data_lists)) return response()->json(['error' => '找不到该配置单'], 500);
        $data_offer    = unserialize($data_lists->project_con_list);
        $auxiliary_id  = $data_lists->auxiliary_id;
        $auxiliaryInfo = DB::table('auxiliary')->where('id', $auxiliary_id)->first()->price;
        $actual_square = WorkModel::where('task_id', $data['task_id'])->first()->actual_square;

        $work_off_data = WorkOfferModel::where('from_uid', $data['from_uid'])
            ->where('task_id', $data['task_id'])
            ->where('sn', 1)->first();
        if ($work_off_data->status == 4) return response()->json(['message' => '您已支付过该订单'], 200);


        //业主选择工人的星级
        $task_data  = TaskModel::find($data['task_id']);//找到工人的星级
        $workerStar = $task_data->workerStar;//找到工人的星级
        //循环出可以供选择的工人
        foreach ($data_offer as $k => $v) {
            if ($k !== 'all_parent_price') {
                foreach ($v['need_work_type'] as $n => $m) {
                    $data_offer[$k]['can_choose_labor'] = UserDetailModel::where('star', $workerStar)->where('work_type', $m)->lists('uid')->toArray();
                }
            }
        }
        $work = WorkModel::where('task_id', $data['task_id'])->where('status', 2)->first();//获取任务
        if (empty($work)) {
            return response()->json(['error' => '找不到该任务'], 500);
        }
        //不同星级不同的百分比
        $workerStar = rate_choose($workerStar);


        //把总价加入到第一次的报价和预约金里面
        $auxiliary_price  = $auxiliaryInfo * $actual_square;//辅材包价格
        $total_salary_all = ($data_offer['all_parent_price'] - $data_offer['parent_7']['parent_price']) * $workerStar + $data_offer['parent_7']['parent_price'] + $auxiliary_price;
        //找到该用户,扣除余额补充到保证金里面
        $user_boss = UserDetailModel::where('uid', $data['from_uid'])->first();
        $userInfo  = UserModel::where('id', $data['from_uid'])->where('status', 1)->where('user_type', 1)->first();


        if ($user_boss->balance < ($total_salary_all)) {
            return response()->json(['error' => '账户余额不足', 'difference' => abs($user_boss->balance - ($total_salary_all))], 500);
        } else {
            // 这里是使用余额支付
            $password = UserModel::encryptPassword($data['password'], $userInfo['salt']);
            if ($password != $userInfo['password']) {
                return response()->json(['error' => '您的支付密码不正确'], '403');
            }
        }

        //总价要额外支付20%的工程备用金(暂时不用)

        //加入事务
        $status = DB::transaction(function () use ($work, $total_salary_all, $data, $user_boss, $data_offer, $workerStar, $auxiliary_price) {

            $work->price += $total_salary_all;//配置单总价加上去work表
            WorkOfferModel::where('task_id', $data['task_id'])->where('sn', 1)->update(['price' => $total_salary_all]);//保存提交的总价到
            $work->save();

            $origin_order = OrderModel::where('task_id', $data['task_id'])->first();//找到订单
            $origin_order->cash += $total_salary_all;
            $origin_order->save();//一张订单,订单编号唯一
            $all_pay_second = $total_salary_all;//忘记扣20%的保证金(已不需要扣)
//        $auxiliary_price_res = platformOrderModel::sepbountyOrder($data['from_uid'], $auxiliary_price, $data['task_id'], '辅材包价格');//辅材包写入sub_order表
            platformOrderModel::sepbountyOrder($data['from_uid'], $all_pay_second, $data['task_id'], '配置单价格加辅材包价格(余额->冻结金)', 1);


            $this->payWorkerRespository->bounty($all_pay_second, $data['task_id'], $data['from_uid'], 1, 5);//一次性扣除(扣除余额)

            //平台账号直接收取
            $system_uid = Config::get('task.SYSTEAM_UID');
            platformOrderModel::sepbountyOrder($data['from_uid'], $auxiliary_price, $data['task_id'], '辅材价格(直接扣除到系统账户)', 1);

//            $this->payWorkerRespository->bounty($auxiliary_price, $data['task_id'], $data['from_uid'], 1, 6, true);//扣业主冻结资金

            $financial_decrement_auxiliary = [
                'action' => 6,
                'pay_type' => 1,
                'cash' => $auxiliary_price,
                'uid' => $data['from_uid'],
                'created_at' => date('Y-m-d H:i:s', time()),
                'task_id' => $data['task_id']
            ];

            FinancialModel::create($financial_decrement_auxiliary);

            //默认系统直接获得辅材的收入
            platformOrderModel::sepbountyOrder($system_uid, $auxiliary_price, $data['task_id'], '辅材价格', 2, 1);
            $this->payWorkerRespository->bounty($auxiliary_price, $data['task_id'], $system_uid, 1, 2);//系统的余额增加


            UserDetailModel::where('uid', $data['from_uid'])->update(['frozen_amount' => $user_boss->frozen_amount += $all_pay_second]);//把扣除金额写进冻结资金


            //报价阶段业主确认支付后,完结此阶段
            $res_work_offer_status = WorkOfferModel::where('from_uid', $data['from_uid'])
                ->where('task_id', $data['task_id'])
                ->where('work_id', $work['id'])
                ->where('sn', 1)
                ->update(['status' => 4]);
            if ($res_work_offer_status) {
                // 2表示威客交付(开工啦)

                $sn              = 1;//步骤
                $evaluate_status = 0;
                //生成数据可以插入到work_offer表
                $arr_detail[] = ['type' => 'housekeeper',
                                 'task_id' => $data['task_id'],
                                 'sn' => ++$sn,
                                 'title' => '平台匹配工人',
                                 'percent' => '0',
                                 'price' => 0,
                                 'work_id' => $work['id'],
                                 'from_uid' => $data['from_uid'],
                                 'to_uid' => $work['uid'],
                                 'status' => 1,        //平台分配工人
                                 'project_type' => 0
                ];

                $arr_detail[] = ['type' => 'housekeeper',
                                 'task_id' => $data['task_id'],
                                 'sn' => ++$sn,
                                 'title' => '提交开工时间',
                                 'percent' => '0',
                                 'price' => 0,
                                 'work_id' => $work['id'],
                                 'from_uid' => $data['from_uid'],
                                 'to_uid' => $work['uid'],
                                 'status' => 0,        // 0未开始  1管家提交  2业主确认 3驳回 4进入开工交底
                                 'project_type' => 0
                ];
                foreach ($data_offer as $k => $v) {

                    if ($k !== 'all_parent_price') {
                        $v['can_choose_labor'] = empty($v['can_choose_labor']) ? '0' : $v['can_choose_labor'];
                        $round                 = mt_rand(0, count($v['can_choose_labor']) - 1);
                        $other_stage_price     = $v['parent_price'] * $workerStar;
                        //其他阶段不用乘以星级百分比
                        if ($v['parent_project_type'] == 7) {
                            $other_stage_price = $v['parent_price'];
                            $evaluate_status   = 1;
                        }
                        $arr_detail[] = ['type' => 'labor',
                                         'task_id' => $data['task_id'],
                                         'sn' => ++$sn,
                                         'title' => $v['parent_name'],
                                         'percent' => '0.8',
                                         'price' => $other_stage_price,//每个阶段的价格乘以星级
                                         'work_id' => $work['id'],
                                         'from_uid' => $data['from_uid'],
                                         'to_uid' => $v['can_choose_labor'][$round],
                                         'status' => 0,
                                         'evaluate_status' => $evaluate_status,
                                         'project_type' => $v['parent_project_type']
                        ];
                    }
                }


                foreach ($arr_detail as $key => $value) {
                    WorkOfferModel::create($value);
                }
            }
        });

        if (is_null($status)) {
            UserDetailModel::where('uid', $data['from_uid'])->decrement('frozen_amount', $auxiliary_price);
            //推送给工作者
            $house_uid = $work_off_data->to_uid;
            $application = 20004;
/*            $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_boss_pay_list')->first()->chn_name;
            $woker_info  = UserModel::find($house_uid);
            $woker_info->send_num += 1;
            $woker_info->save();
            //保存发送的消息
            save_push_msg($message, $application, $house_uid);
            PushServiceModel::pushMessageWorker($woker_info->device_token,$message, $woker_info->send_num, $application);*/
            push_accord_by_equip($house_uid,$application,'message_boss_pay_list','',$data['task_id']);
            return response()->json(['message' => '支付成功']);
        } else {
            return response()->json(['error' => '付款失败,有状态错误'], 500);
        }


    }

    /**
     * 业主支付管家的工资(首次提交)
     */
    public function bossPayHouseKeeperSalary(Request $request) {

        $uid_boss = $request->json('uid_boss');
        $task_id  = $request->json('task_id');
        $password = $request->json('password');

        $only_order_code = OrderModel::where('uid', $uid_boss)->where('task_id', $task_id)->first()->code;//找到唯一的订单编号
        $work_detail     = WorkModel::where('task_id', $task_id)->where('status', 1)->first();

        if (empty($work_detail)) return response()->json([['error' => '系统错误'], 500]);
        if ($work_detail->status == 2) return response()->json([['error' => '您已支付过该订单'], 500]);
        $task_info = TaskModel::find($task_id);
        $worker_id= $work_detail->uid;
        //获取第一次报价
        $work_offer_info = WorkOfferModel::where('sn', 0)->where('task_id', $task_id)->where('to_uid', $worker_id)->first();
        $salary          = $work_offer_info->price;
        //支付的是否做个余额判定
        $boss_detail = UserDetailModel::where('uid', $uid_boss)->first();
        $userInfo    = UserModel::where('id', $uid_boss)->where('status', 1)->where('user_type', 1)->first();
        if ($task_info->user_type == 4) {
            $quanlity_service_money = $task_info->quanlity_service_money;
            $salary += $quanlity_service_money;
            if ($salary > $boss_detail->balance) {
                return response()->json(['error' => '余额不足，需使用第三方补缴', 'difference' => abs($boss_detail->balance - $salary)], 500);
            }
        }

        if ($salary > $boss_detail->balance) {
            return response()->json(['error' => '余额不足，需使用第三方补缴', 'difference' => abs($boss_detail->balance - $salary)], 500);
        } else {
            // 这里是使用余额支付
            $password = UserModel::encryptPassword($password, $userInfo['salt']);
            if ($password != $userInfo['password']) {
                return response()->json(['error' => '您的支付密码不正确'], '403');
            }
        }
        if (empty($salary)) {
            return response()->json(['error' => '应缴金额为空'], 500);
        }

        //扣款记录(业主)
        $status = DB::transaction(function () use ($task_info, $uid_boss, $salary, $task_id, $only_order_code, $work_detail) {
            WorkOfferModel::where('sn', 0)->where('task_id', $task_id)->where('to_uid', $work_detail->uid)->update(['status' => 4]);
            $work_detail->status                = 2;
            $task_info->quanlity_service_status = 1;
            $work_detail->save();
            $task_info->save();
            if ($task_info->user_type == 4) {
                //找到质保服务
                $quanlity_service_money = $task_info->quanlity_service_money;
                platformOrderModel::sepbountyOrder($uid_boss, $salary-$quanlity_service_money, $task_id, '监理工资', 1);
                platformOrderModel::sepbountyOrder($uid_boss, $quanlity_service_money, $task_id, '质保服务', 1);
                $total_pay = $salary;
                TaskModel::bounty($total_pay, $task_id, $uid_boss, $only_order_code, 1, 6);//扣用户余额
                UserDetailModel::where('uid', $uid_boss)->increment('frozen_amount', $total_pay);//把扣除金额写进冻结资金
            } else {
                platformOrderModel::sepbountyOrder($uid_boss, $salary, $task_id, '管家工资', 1);
                TaskModel::bounty($salary, $task_id, $uid_boss, $only_order_code, 1, 6);//扣用户余额
                UserDetailModel::where('uid', $uid_boss)->increment('frozen_amount', $salary);//把扣除金额写进冻结资金
            }
        });

        if (is_null($status)) {
            //推送给管家和监理
            switch ($task_info->user_type) {
                case 3:
                    $application = 40008;
                    break;
                case 4:
                    $application = 40009;
                    break;
                default:
                    $application = 40008;
            }
   /*         $work_data = UserModel::find($worker_id);
            $message   = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_boss_pay_salary')->first()->chn_name;
            $work_data->send_num += 1;
            $work_data->save();

            //保存发送的消息
            save_push_msg($message, $application, $work_data->id);
            PushServiceModel::pushMessageWorker($work_data->device_token, $message, $work_data->send_num, $application);*/
            push_accord_by_equip($worker_id,$application,'message_boss_pay_salary','',$task_id);
            return response()->json(['message' => '付款成功']);
        } else {
            return response()->json(['message' => '付款失败']);
        }





    }


    /**
     * 客诉中心的驳回
     */
    public function bossRefuseProject(Request $request) {

        $task_id = $request->json('task_id');
        $sn      = $request->json('sn');

        // 判断任务去到哪一个工程阶段
        $ret = WorkOfferModel::where('task_id', $task_id)
            ->where('status', '>', 0)
            ->where('project_type', '>', 0)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();

        if (empty($ret)) {
            return response()->json(['error' => '未达到验收阶段'], '500');
        }

        $curSnStatus = [];
        foreach ($ret as $key => $value) {
            // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
            if ($value['status'] == 1 || $value['status'] == 1.5 || $value['status'] == 3 || $value['status'] == 3.5) {
                $curSnStatus[] = $value;
                break;
            }
        }
        if (empty($curSnStatus)) return response()->json(['error' => '当前阶段已验收,无法驳回'], 500);
        if ($curSnStatus[0]['sn'] != $sn) return response()->json(['error' => '非法提交'], 500);


        $data_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
        if (empty($data_offer)) return response()->json(['error' => '找不到该阶段'], 500);
        $data['old_labor']    = $data_offer->to_uid;
        $data['project_type'] = $data_offer->project_type;
        $data['status']       = 1;
        $data['sn']           = $sn;
        $data['task_id']      = $task_id;
        $res_change_labor     = ProjectLaborChange::create($data);
        $res_refuse           = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->update(['status' => 3]);
        //推送给工作者
        $data_work = WorkModel::find($data_offer->work_id);
        $users     = empty($data_work) ? 255 : $data_work->uid;

        //根据管家找监理
        $task_id_super    = FindSuperTaskId($task_id);
        $data_work_super  = WorkModel::where('task_id', $task_id_super)->where('status', '>', 0)->first();
        $user_super       = empty($data_work_super) ? 255 : $data_work_super->uid;
//        small_order_to_worker($users, 20010, 'message_customer_complaint', $data_offer->title);
//        small_order_to_worker($user_super, 30002, 'message_customer_complaint', $data_offer->title);
        push_accord_by_equip($users, 20010, 'message_customer_complaint', $data_offer->title, $task_id);
        push_accord_by_equip($user_super, 30002, 'message_customer_complaint', $data_offer->title, $task_id);
        if ($res_refuse && $res_change_labor)
            return response()->json(['message' => '驳回成功'], 200);
        else
            return response()->json(['error' => '驳回失败'], 500);
    }

    /**
     * 普通驳回，仅限于驳回验收申请，不涉及其他（无论是不是进客诉通道，都需要通过这个接口去提交驳回申请）
     * 进程 0未开始 1工作端submit 1.5监理确认 2用户commit 3业主退回 3.5监理退回 4done
     */
    public function normalBossRefuseProject(Request $request) {

        $task_id  = $request->json('task_id');
        $sn       = $request->json('sn');
        $from_uid = $request->json('from_uid');

        $user_type = UserModel::where('id', $from_uid)->first()->user_type;

        if ($user_type == 4) {

            $taskInfo = TaskModel::where('id', $task_id)->first();//监理的单子
            $task_id  = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 3)->first()->id;
            //监理驳回,不需要通知业主
            $data_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
            if (empty($data_offer)) return response()->json(['error' => '找不到该阶段'], 500);
            $title      = $data_offer->titile;
            $data_push['house_uid'] = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->first()->to_uid;//管家
            //推送给管家(业主待定)
            foreach ($data_push as $k => $v) {
                $application = 50003;
                push_accord_by_equip($v,$application,'message_visor_refuse_sub_list',$title,$task_id);
            }
        } else {
            $task_id = $task_id;
            $data_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
            if (empty($data_offer)) return response()->json(['error' => '找不到该阶段'], 500);
            $title      = $data_offer->titile;
            //推送给管家和监理(如果有监理)
            $taskInfo        = TaskModel::where('id', $task_id)->first();//监理的单子
            $task_visor_data = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 4)->first();
            //没有监理
            if (empty($task_visor_data)) {
                $data_push['house_uid'] = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->first()->to_uid;//管家
            } else {
                $task_visor_id          = $task_visor_data->id;
                $data_push['house_uid'] = WorkOfferModel::select('to_uid')->where('task_id', $task_id)->where('sn', 0)->first()->to_uid;//管家
                $data_super_visor       = WorkOfferModel::select('to_uid')->where('task_id', $task_visor_id)->where('sn', 0)->first();//监理
                if (!empty($data_super_visor)) {
                    $data_push['super_visor'] = $data_super_visor->to_uid;
                }
            }

            foreach ($data_push as $k => $v) {
                $application = 50002;
                push_accord_by_equip($v, $application, 'message_boss_refuse_sub_list', $title, $task_id);
            }
        }


        // 判断任务去到哪一个工程阶段
        $ret = WorkOfferModel::where('task_id', $task_id)
            ->where('status', '>', 0)
//            ->where('project_type', '>', 0)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();

        if (empty($ret)) {
            return response()->json(['error' => '未达到验收阶段'], '500');
        }

        $curSnStatus = [];
        foreach ($ret as $key => $value) {
            // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
            //  status = 3 ，  是业主驳回 ，可能处于刚驳回阶段，需要管家去提交延期单，也可能是处于刚驳回管家提交的延期单
            //  status = 3.5 , 是监理驳回 ，可能处于刚驳回阶段，需要管家去提交延期单，也可能是处于刚驳回管家提交的延期单

            if ($value['status'] == 1 || $value['status'] == 1.5 || $value['status'] == 3 || $value['status'] == 3.5) {
                $curSnStatus[] = $value;
                break;
            }
        }
        if (empty($curSnStatus)) return response()->json(['error' => '当前阶段已验收或已处于驳回阶段 , 无法驳回'], 500);
        if ($curSnStatus[0]['sn'] != $sn) return response()->json(['error' => '非法提交'], 500);


        if ($user_type == 1) {
            $status  = 3;//进程 0未开始 1工作端submit 1.5监理确认 2用户commit 3业主退回 3.5监理退回 4done
            $is_sure = 4;//业主是否确认延期单,0:管家没提交, 1:管家已提交,2:监理满意,3:监理驳回,4：业主驳回,5:业主确认
        } elseif ($user_type == 4) {
            $status  = 3.5;
            $is_sure = 3;
        } else {
            return response()->json(['error' => '驳回失败 , 无权驳回'], 500);
        }

        $res_refuse = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->update(['status' => $status]);//off状态改变
        $data_delay = ProjectDelayDate::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();//时间延期

//        if (!empty($data_delay)) {
//            $updateRet = ProjectDelayDate::where('id', $data_delay->id)->update(['is_sure' => $is_sure]);
//        } else {
//            $updateRet = true;
//        }


        if ($res_refuse) {

            return response()->json(['message' => '驳回成功'], 200);
        }
        return response()->json(['error' => '驳回失败'], 500);
    }


    /**
     * 管家提交工程延期单(普通驳回)
     */
    public function HouseKeeperDelayDate(Request $request) {
        $end_date = $request->json('end_date');
        $task_id  = $request->json('task_id');
        $sn       = $request->json('sn');

        $data['sn']            = $sn;
        $data['task_id']       = $task_id;
        $data['original_date'] = TaskModel::find($task_id)['end_at'];
        $data['end_date']      = $end_date;
        $data['is_sure']       = 1;//业主是否确认延期单,0:管家没提交, 1:管家已提交,2:监理满意,3:监理驳回,4：业主驳回,5:业主确认
        if (empty($data['original_date'])) return response()->json(['error' => '原项目结束时间丢失'], 500);
//        $is_created = ProjectDelayDate::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();

//        if (empty($is_created)) {
        $ret = ProjectDelayDate::create($data);
        if ($ret) return response()->json(['message' => '已提交整改时间']);
//        } else {
//            $is_created->end_date = $data['end_date'];
//            $is_created->sn       = $sn;
//            $is_created->is_sure  = $data['is_sure'];
//            $ret = $is_created->save();
//            if ($ret) return response()->json(['message' => '已提交整改时间']);
//        }


        return response()->json(['error' => '提交失败'], 500);
    }

    /**
     * 业主或监理驳回工程延期单
     * 业主是否确认延期单,0:管家没提交, 1:管家已提交,2:监理满意,3:监理驳回,4：业主驳回,5:业主确认
     */
    public function rejectDelayReport(Request $request) {
        $task_id   = $request->json('task_id');
        $sn        = $request->json('sn');
        $from_uid  = $request->json('from_uid');
        $user_type = UserModel::where('id', $from_uid)->first()->user_type;

        if ($user_type == 1) {
            $is_sure = 4;
        } elseif ($user_type == 4) {
            $project_position  = TaskModel::find($task_id)->project_position;
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $is_sure           = 3;
        } else {
            return response()->json(['error' => '无任务权限'], 500);
        }
        if ($user_type == 4) {
            $data_delay = ProjectDelayDate::where('sn', $sn)->where('task_id', $house_keeper_task->id)->orderBy('id', 'desc')->first();
        } else {
            $data_delay = ProjectDelayDate::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        }

        $updateRet = ProjectDelayDate::where('id', $data_delay->id)->delete();
        if ($updateRet) {
            return response()->json(['message' => '驳回工程延期单成功'], 200);
        }
        return response()->json(['error' => '驳回工程延期单失败'], 500);
    }


    /**
     * 业主或监理驳回工程整改单
     * 1:业主提交,2.管家提交整改单,3.业主驳回管家整改单,3.5,监理驳回管家整改单4.业主确认整改单,4.5监理确认整改单,5.平台正在匹配,6.平台匹配完成
     */
    public function rejectListChangeReport(Request $request) {

        $task_id   = $request->json('task_id');
        $sn        = $request->json('sn');
        $from_uid  = $request->json('from_uid');
        $user_type = UserModel::where('id', $from_uid)->first()->user_type;
        if ($user_type == 1) {
            $status_change_labor = 3;
        } elseif ($user_type == 4) {
            $project_position    = TaskModel::find($task_id)->project_position;
            $house_keeper_task   = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $status_change_labor = 3.5;
        } else {
            return response()->json(['error' => '无任务权限'], 500);
        }

        if ($user_type == 4) {
            $data_delay = ProjectLaborChange::where('sn', $sn)->where('task_id', $house_keeper_task->id)->orderBy('id', 'desc')->first();
        } else {
            $data_delay = ProjectLaborChange::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        }

        $updateRet = ProjectLaborChange::where('id', $data_delay->id)->update(['status' => $status_change_labor]);
        if ($updateRet) {
            return response()->json(['message' => '驳回工程整改单成功'], 200);
        }
        return response()->json(['error' => '驳回工程整改单失败'], 500);


        /*        $task_id             = $sn = 999;
                $status_change_labor = 3.5;
                $status_change_labor = 3;//1:业主提交,2.管家提交整改单,3.业主驳回管家整改单,3.5,监理驳回管家整改单4.业主确认整改单,4.5监理确认整改单,5.平台正在匹配,6.平台匹配完成
                $data_change_labor   = ProjectLaborChange::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();//工人更换驳回
                if (!empty($data_change_labor)) {
                    $updateRet_labor = ProjectLaborChange::where('id', $data_change_labor->id)->update(['status' => $status_change_labor]);
                } else {
                    $updateRet_labor = true;
                }*/
    }


    /**
     * 业主或监理确认工程延期单
     * 进程 0未开始 1工作端submit 1.5监理确认 2用户commit 3业主退回 3.5监理退回 4done work_offer
     *
     * 业主是否确认延期单,0:管家没提交, 1:管家已提交,2:监理满意,3:监理驳回,4：业主驳回,5:业主确认
     */
    public function bossSureDelayDate(Request $request) {
        $task_id = $request->json('task_id');
        $sn      = $request->json('sn');

        $from_uid  = $request->json('from_uid');
        $user_type = UserModel::where('id', $from_uid)->first()->user_type;


        if ($user_type == 1) {
            $is_sure = 5;
        } elseif ($user_type == 4) {
            $project_position  = TaskModel::find($task_id)->project_position;
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $is_sure           = 2;
        } else {
            return response()->json(['error' => '无任务权限'], 500);
        }
        if ($user_type == 4) {
            $task_id    = $house_keeper_task->id;
            $data_delay = ProjectDelayDate::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        } else {
            $data_delay = ProjectDelayDate::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        }

        $data_delay->is_sure = $is_sure;

        //业主确认的时候才能改变延期时间
        if ($user_type == 1) {
            $data_task         = TaskModel::find($task_id);
            $data_task->end_at = $data_delay->end_date;
            $res_sure_date     = $data_task->save();
        } else {
            $res_sure_date = true;
        }

        if ($is_sure == 5) {
            $res_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->update(['status' => 0]);//重新让管家提交
        } else {
            $res_offer = true;
        }


        if ($data_delay->save() && $res_sure_date && $res_offer) return response()->json(['message' => '已确认延期时间']);

        return response()->json(['error' => '确认延期时间失败'], 500);
    }


    /**
     * 业主或监理确认工程整改单
     * 业主是否确认整改单,1:业主提交,2.管家提交整改单,3.业主驳回管家整改单,3.5,监理驳回管家整改单4.业主确认整改单,4.5监理确认整改单,5.平台正在匹配,6.平台匹配完成
     */
    public function bossSureChangeList(Request $request) {
        $task_id   = $request->json('task_id');
        $sn        = $request->json('sn');
        $from_uid  = $request->json('from_uid');
        $user_type = UserModel::where('id', $from_uid)->first()->user_type;


        if ($user_type == 1) {
            $is_sure = 5;
        } elseif ($user_type == 4) {
            $project_position  = TaskModel::find($task_id)->project_position;
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $is_sure           = 4.5;
        } else {
            return response()->json(['error' => '无任务权限'], 500);
        }

        if ($user_type == 4) {
            $task_id    = $house_keeper_task->id;
            $data_delay = ProjectLaborChange::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        } else {
            $data_delay = ProjectLaborChange::where('sn', $sn)->where('task_id', $task_id)->orderBy('id', 'desc')->first();
        }

        $updateRet = ProjectLaborChange::where('id', $data_delay->id)->update(['status' => $is_sure]);
        if ($updateRet && $user_type == 1) {
            return response()->json(['message' => '确认成功,平台正在匹配中'], 200);
        } elseif ($updateRet && $user_type == 4) {
            return response()->json(['message' => '确认成功,待业主确认'], 200);
        }

        return response()->json(['error' => '确认工程整改单失败'], 500);

    }


    /**
     * 业主更换工人,管家提交整改工程项目、数量,时间(方案一)
     * 形成工程整改费用清单到业主
     */
    public function houseKeeperReChangeList(Request $request) {

        $sn         = $request->json('sn');
        $task_id    = $request->json('task_id');
        $ids_new    = $request->json('data_lists');//管家提交的该工程需要整改的东西
        $end_date   = $request->json('end_date');
        $data_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->where('status', 3)->first();
        if (empty($data_offer)) return response()->json(['error' => '该阶段未处于驳回状态'], 500);
        $original_date     = TaskModel::find($task_id)['end_at'];
        $configure_data    = create_configure_lists($ids_new);
        $new_total_price   = $configure_data['all_parent_price'];//该整改阶段的总价
        $old_total_price   = $data_offer->price;
        $labor_change_data = ProjectLaborChange::where('task_id', $task_id)->where('project_type', $data_offer->project_type)->where('status', 1)->orWhere('status', 3.5)->orWhere('status', 3)->orderBy('id', 'desc')->first();//驳回重新修改
        if (empty($labor_change_data)) return response()->json(['error' => '找不到此次业主驳回记录'], 500);
        $labor_change_data->list_detail   = serialize($configure_data);//保存管家的整改单
        $labor_change_data->status        = 2;//状态改为管家已提交
        $labor_change_data->change_date   = $end_date;//项目结束时间(提交)
        $labor_change_data->original_date = $original_date;//项目结束时间(原定)

        if ($new_total_price > $old_total_price) {
            return response()->json(['error' => '该整改阶段设置参数计算的总价大于原阶段的总价'], 500);
        }
        if ($labor_change_data->save()) return response()->json(['message' => '提交成功']);

        return response()->json(['error' => '提交失败'], 500);
    }


    /**
     * 业主查看此次整改费用(方案一)
     */
    public function bossGetReChangeList(Request $request) {

        $sn         = $request->get('sn');
        $task_id    = $request->get('task_id');
        $data_labor = ProjectLaborChange::where('task_id', $task_id)->where('sn', $sn)->orderBy('id', 'desc')->first();
        if (empty($data_labor)) return response()->json(['error' => '找不到此次业主驳回记录'], 500);
        $data['data_lists']    = unserialize($data_labor->list_detail);
        $data['end_date']      = $data_labor->change_date;
        $data['original_date'] = $data_labor->original_date;
        return response()->json($data);
    }

    /**
     * 业主驳回整改方案,状态变为3(方案一)
     */
    public function bossRejectLists(Request $request) {
        $sn                 = $request->json('sn');
        $task_id            = $request->json('task_id');
        $data_labor         = ProjectLaborChange::where('task_id', $task_id)->where('sn', $sn)->orderBy('id', 'desc')->first();//找到驳回记录
        $data_labor->status = 3;
        if ($data_labor->save()) return response()->json(['message' => '驳回成功']);
        return response()->json(['error' => '驳回失败'], 500);
    }


    /**
     * 业主确认整改方案(方案一)
     */
    public function bossSureChangeProject(Request $request) {

        $task_id            = $request->json('task_id');//任务id
        $sn                 = $request->json('sn');
        $data_labor         = ProjectLaborChange::where('task_id', $task_id)->where('sn', $sn)->orderBy('id', 'desc')->first();
        $data_labor->status = 5;//5.平台正在匹配中
        //推送给工作者
        $task_id = $data_labor->task_id;//管家订单id
        $data_task = TaskModel::find($task_id);
        $user_house = WorkModel::where('task_id',$task_id)->where('status','>',0)->first()->uid;
        //监理订单
        $task_id_super = FindSuperTaskId($task_id);
        $data_work_super  = WorkModel::where('task_id', $task_id_super)->where('status', '>', 0)->first();
        $user_super       = empty($data_work_super) ? 255 : $data_work_super->uid;
//        small_order_to_worker($user_house, 20010, 'message_customer_complaint');
//        small_order_to_worker($user_super, 30002, 'message_customer_complaint');
        if ($data_labor->save()) return response()->json(['message' => '确认成功,平台正在匹配中'], 200);
        return response()->json(['error' => '确认失败'], 500);
    }



    /**
     * @param $arr
     * @param $otherArr
     * @return mixed
     * 计算两个二维数组的差集
     */
    public function calDifferOfArrs($arr, $otherArr) {
        foreach ($arr as $key => $value) {
            $compare = array_diff_assoc($otherArr[$key], $value);
            if ($compare != null) {
                return $arr;
            }
        }
    }


    /**
     * 业主获取管家提交的工程配置单(首次提交)
     */
    public function bossGetProjectConfList(Request $request) {
        $data['task_id'] = $request->get('task_id');

        $taskInfo = TaskModel::where('id', $data['task_id'])->first();

        if ($taskInfo->user_type == 4) {
            $data['task_id'] = TaskModel::where('project_position', $taskInfo->project_position)->where('user_type', 3)->where('status', '<', 9)->first()->id;
        }

        if ($taskInfo->user_type == 2) {
            $conf = ProjectConfigureTask::where('task_id', $data['task_id'])->orderBy('id', 'desc')->first();//拿到配置单
            if (empty($conf)) return response()->json(['error' => '该任务找不到对应的工程配置单'], 500);
            $confList = unserialize($conf->project_con_list);
            $confList = create_configure_lists($confList);
        } elseif ($taskInfo->user_type == 3) {
            $conf = ProjectConfigureTask::where('task_id', $data['task_id'])->orderBy('id', 'desc')->first();//拿到配置单
            if (empty($conf)) return response()->json(['error' => '该任务找不到对应的工程配置单'], 500);
            $confList = unserialize($conf->project_con_list);
        } else {
            $conf = ProjectConfigureTask::where('task_id', $data['task_id'])->orderBy('id', 'desc')->first();//拿到配置单
            if (empty($conf)) return response()->json(['error' => '该任务找不到对应的工程配置单'], 500);
            $confList = unserialize($conf->project_con_list);
        }

        $work_star      = TaskModel::find($data['task_id'])->workerStar;
        //帮助函数
        $work_star_rate = rate_choose($work_star);

        foreach ($confList as $k => $v) {
            if ($k !== 'all_parent_price') {
                $confList[$k]['parent_price'] = $v['parent_price'] * $work_star_rate;
            }
        }
        $confList['all_parent_price'] = $confList['all_parent_price'] * $work_star_rate;
        if ($work_star == 0) unset($confList['all_parent_price']);

        return response()->json($confList);
    }


    /**
     *
     * 根据业主提交的东西，算出总价(首次提交)
     */
    public function seekConfTotalPrice(Request $request) {

        $worker_star  = $request->get('worker_star');
        $auxiliary_id = $request->get('auxiliary_id');
        $task_id      = $request->get('task_id');


        $task_info = TaskModel::select('user_type', 'project_position as project_position_id', 'id')->where('id', $task_id)->first()->toArray();

        //监理订单类型
        if ($task_info['user_type'] == 4) {
            $project_position  = $task_info['project_position_id'];
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $house_keeper_task->id;
        } else {
            $task_id = $task_info['id'];
        }

        $conf = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();//拿到配置单

        if (empty($conf)) return response()->json(['error' => '找不到该配置单'], 500);


        $confList = unserialize($conf->project_con_list);

        $work_star = rate_choose($worker_star);
        $auxiliaryInfo = DB::table('auxiliary')->where('id', $auxiliary_id)->first()->price;
        $actual_square = WorkModel::where('status', '>=', 1)
            ->where('status', '<=', 4)
            ->where('task_id', $task_id)
            ->first()->actual_square;


//        $ret['conf_list_price'] = $confList['all_parent_price'] * $work_star;                               //选择星级工人之后的配置单价钱
        $ret['conf_list_price'] = ($confList['all_parent_price'] - $confList['parent_7']['parent_price']) * $work_star + $confList['parent_7']['parent_price'];                               //选择星级工人之后的配置单价钱,其他阶段不要乘
        $ret['auxiliary_price'] = $auxiliaryInfo * $actual_square;                                          //辅材包价钱
//        $ret['bond_price']      = (($ret['conf_list_price'] + $ret['auxiliary_price']) * 0.2);                //工程保证金,两者想加乘以0.2
        $ret['bond_price']      = 0;                //工程保证金暂时为0
        $ret['total_price']     = $ret['conf_list_price'] + $ret['auxiliary_price'] + $ret['bond_price'];   //所有钱加起来

        //循环转成字符串
        foreach ($ret as $k => $v) {
            $ret[$k] = (string)$v;
        }
        return response()->json($ret);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 业主确认管家的工程配置单(首次提交)
     */
    public function bossSureLists(Request $request) {

        $task_id      = $request->json('task_id');
        $worker_star  = $request->json('worker_star');
        $auxiliary_id = $request->json('auxiliary_id');
        $project_data = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();

        TaskModel::where('id', $task_id)->update(['workerStar' => $worker_star]);
        $project_data->is_sure      = 1;
        $project_data->auxiliary_id = $auxiliary_id;

        $work_offer_data = WorkOfferModel::where('task_id', $task_id)->where('sn', 1)->where('status',1)->update(['status' => 2]);

        /*        //管家报价阶段,单独拿出来,不需要监理确认
                if (($curSnStatus['status'] == 1 || $curSnStatus['status'] == 3) && $curSnStatus['sn'] == 1) {
                    if ($userInfo != 1) {
                        return response()->json(['error' => '不是业主，无法操作'], '500');
                    } else {
                        $count_submit      = WorkOfferModel::where('id', $curSnStatus['id'])->first()->count_submit;
                        $res_count         = WorkOfferModel::where('id', $value['id'])->update(['count_submit' => ++$count_submit]);//次数加一
                        $res_modify_status = WorkOfferModel::where('id', $curSnStatus['id'])->update(['status' => 2]);
                        if ($res_modify_status && $res_count) return response()->json(['message' => '业主确认验收成功']);
                        return response()->json(['error' => '业主确认验收失败']);
                    }
                }

                //管家报价阶段,单独拿出来,不需要监理确认
                if (($value['status'] == 1 || $value['status'] == 3) && $value['sn'] == 1) {
                    $curSnStatus = $value;
                    break;
                }*/


        $housekeeperProjectPosition = TaskModel::find($task_id)->project_position;
        $designerTaskId             = TaskModel::where('project_position', $housekeeperProjectPosition)->where('user_type', 2)->where('status', '<', 9)->first()->id;
        $designer                   = WorkOfferModel::where('task_id', $designerTaskId)->where('sn', 2)->update(['status' => 4]);
        if (!$project_data) {
            return response()->json(['error' => '找不到该任务或该阶段'], 500);
        }

        if ($work_offer_data && $project_data->save() && $designer) {
            //推送给管家
            $house_uid = WorkOfferModel::select('to_uid')->where('sn', 0)->where('task_id', $task_id)->first()->to_uid;
            $application = 20005;
/*            $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_sub_list_to_boss')->first()->chn_name;
            $woker_info  = UserModel::find($house_uid);
            $woker_info->send_num += 1;
            $woker_info->save();
            //保存发送的消息
            save_push_msg($message, $application, $house_uid);
            PushServiceModel::pushMessageWorker($woker_info->device_token, $message, $woker_info->send_num, $application);*/
            push_accord_by_equip($house_uid,$application,'message_sub_list_to_boss','',$task_id);
            return response()->json(['message' => '确认成功']);
        }
        return response()->json(['error' => '确认失败'], 500);
    }


    /**
     * 管家填写开工日期和竣工日期并提交到业主(首次提交)
     */

    public function houseKeeperProvideDate(Request $request) {

        $start_date = $request->json('start_date');//开工时间
        $end_date   = $request->json('end_date');//竣工日期
        $task_id    = $request->json('task_id');

        if(strtotime($end_date) < strtotime($start_date)){
            return response()->json(['error' => '竣工时间不可小于开工时间'], 500);
        }

        $res_date   = TaskModel::where('id', $task_id)->update(['begin_at' => $start_date, 'end_at' => $end_date]);
        $res_offer  = WorkOfferModel::where('task_id', $task_id)->where('sn', 3)->update(['status' => 1]);

        if ($res_date && $res_offer){
            //推送给业主
            $uid         = TaskModel::find($task_id)->uid;
//            small_order_to_boss($uid,40012,'message_house_sub_date');
            push_accord_by_equip($uid,40012,'message_house_sub_date','',$task_id);

            return response()->json(['message' => '提交时间成功']);
        }
        return response()->json(['error' => '提交时间失败'], 500);
    }


    /**
     * 业主确认开工日期和竣工日期(首次提交)
     */
    public function bossSureDate(Request $request) {
        $task_id   = $request->json('task_id');
        $res_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', 3)->update(['status' => 4]);
        if ($res_offer){
            //推送给管家
            $uid         = WorkOfferModel::select('to_uid')->where('sn', 0)->where('task_id', $task_id)->first()->to_uid;
//            small_order_to_worker($uid,20003,'message_house_sub_date');
            push_accord_by_equip($uid, 20003, 'message_house_sub_date', '', $task_id);
            return response()->json(['message' => '已确认']);
        }
        return response()->json(['error' => '确认失败'], 500);
    }

    /**
     * 业主驳回开工日期和竣工日期
     */
    public function bossRefuseDateOfHouse(Request $request) {
        $task_id   = $request->json('task_id');
        $res_offer = WorkOfferModel::where('task_id', $task_id)->where('sn', 3)->update(['status' => 0]);
        if ($res_offer) {
            //推送给管家
            $uid         = WorkOfferModel::select('to_uid')->where('sn', 0)->where('task_id', $task_id)->first()->to_uid;
//            small_order_to_worker($uid,20003,'message_house_sub_date');
            push_accord_by_equip($uid, 20003, 'message_house_sub_date', '', $task_id);
            return response()->json(['message' => '已驳回']);
        } else {
            return response()->json(['error' => '驳回失败'], 500);
        }
    }


    /**
     * 管家提交工程配置单(首次提交)
     */
    public function houseKeeperOffer(Request $request) {

        $id_arr         = $request->json('configure_list');
        $task_id        = $request->json('task_id');
        $housekeeper_id = $request->json('housekeeper_id');
        $task_info      = TaskModel::find($task_id);
        if (empty($id_arr) || empty($task_id) || empty($housekeeper_id)) {
            return response()->json(['error' => '数据不可为空'], 500);
        }

        foreach ($id_arr as $k => $v) {
            if ($k == 2) {
                foreach ($v as $n => $m) {
                    $work_type[] = ProjectConfigureModel::find($m['child_id'])->work_type;
                }
            }
        }

        $count = count(array_unique($work_type));
        if ($count <= 1) {
            return response()->json(['error' => '您只提交了水电阶段的泥水工或水电工项目,请补全配置单'], 500);
        }

        $data     = create_configure_lists($id_arr);
        $workInfo = WorkModel::select(
            'task.uid as from_id',
            'work.id as work_id',
            'work.uid as to_id',
            'task.id as task_id',
            'work.uid as housekeeper_id'
        )
            ->leftjoin('task', 'task.id', '=', 'work.task_id')
            ->where('work.task_id', $task_id)
            ->where('work.uid', $housekeeper_id)
            ->first();
        if (empty($workInfo)) return response()->json(['error' => '找不到该管家订单'], 500);
        $workInfo           = $workInfo->toArray();




        $designer_task = TaskModel::where('project_position', $task_info->project_position)->where('status', '<', 9)->where('user_type', 2)->first();
        if(empty($designer_task)){
            return response()->json(['error' => '找不到设计师订单'], 500);
        }
        $confList = ProjectConfigureTask::where('task_id', $designer_task->id)->orderBy('id', 'desc')->first();
        if (empty($confList)) return response()->json(['error' => '找不到设计师提交的配置单'], 500);

        $configureInsertRet = ProjectConfigureTask::insert([
            'project_con_list' => serialize($data),
            'task_id' => $task_id,
            'house_keeper_id' => $housekeeper_id,
            'city_id' => $confList->city_id,
            'auxiliary_id' => $confList->auxiliary_id
        ]);
        

//        $configureInsertRet = ProjectConfigureTask::insert([
//            'project_con_list' => serialize($data),
//            'task_id' => $task_id,
//            'house_keeper_id' => $housekeeper_id
//        ]);
        if ($configureInsertRet) {
            //如果是新修改阶段
            $is_have_price = WorkOfferModel::where('task_id', $task_id)->where('sn', 1)->first();
            //推送给业主
            $boss_uid  = $task_info->uid;
//            small_order_to_boss($boss_uid,40010,'message_sub_list_to_boss');
            push_accord_by_equip($boss_uid, 40010, 'message_sub_list_to_boss', '', $task_id);
            if ($is_have_price) {

                $is_have_price->status = 1;
                if ($is_have_price->save()) return response()->json(['message' => '提交成功']);
                return response()->json(['message' => '提交失败'], 500);

            } else {
                $workOfferInsertArr = [
                    'type' => 'housekeeper',
                    'task_id' => $task_id,
                    'sn' => 1,
                    'title' => '管家第二次报价',
                    'percent' => '0',
                    'price' => 0,//业主确认后才会有报价
                    'work_id' => $workInfo['work_id'],
                    'from_uid' => $workInfo['from_id'],
                    'to_uid' => $workInfo['to_id'],
                    'status' => 1,
                    'project_type' => 0
                ];
                if (WorkOfferModel::create($workOfferInsertArr)) return response()->json(['message' => '提交成功']);
                return response()->json(['message' => '提交失败'], 500);
            }

        } else {
            return response()->json(['message' => '操作失败'], 500);
        }

        return response()->json($data);
    }




    /**
     * 根据sn找到对应的阶段详细内容
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getListDetailBysn(Request $request) {

        $task_id      = $request->get('task_id');
        $sn           = $request->get('sn');
        $data_offer   = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
        $project_type = $data_offer->project_type;
        $data_lists   = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();
        if (empty($data_lists)) return response()->json(['error' => '找不到该数据'], 500);
        $data_lists = $data_lists->toArray();
        $new_data   = [];
        foreach (unserialize($data_lists['project_con_list']) as $k => $v) {
            if ($k == 'parent_' . $project_type) {
                unset($v['parent_name'], $v['parent_project_type'], $v['parent_price'], $v['need_work_type']);
                $new_data = $v;
            }
        }
        $new_data['project_type'] = $project_type;
        return response()->json($new_data);
    }


    // 设计师确认提交报价
    public function subOffer(Request $request) {

        $data['to_uid']  = $request->json('designer_id');
        $data['task_id'] = $request->json('task_id');
        $data['square']  = floatval($request->json('actual_square'));     //面积
        $task_info       = TaskModel::find($data['task_id']);
        if (empty($task_info)) return response()->json(['error' => '找不到该项目'], '500');

        $unit_price                       = $task_info->designer_actual_price;//单价
        $unit_price_sub                   = empty($request->json('unit_price')) ? $unit_price : $request->json('unit_price');     //单价
        $task_info->designer_actual_price = $unit_price_sub;
        $task_info->save();
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json(['error' => '必要参数为空'], '500');
            }
        }

        $total = $data['square'] * $unit_price_sub;
        $work  = WorkModel::where('task_id', $data['task_id'])->where('uid', $data['to_uid'])->where('status', 1)->first();
        $task  = TaskModel::where('id', $data['task_id'])->update(['status' => 7]);

        $workOffer = WorkOfferModel::where('sn', 0)->where('task_id', $data['task_id'])
            ->where('to_uid', $data['to_uid'])->where('work_id', $work['id'])
            ->update(['status' => 1, 'price' => $total, 'actual_square' => $data['square']]);

        $work = WorkModel::where('id', $work['id'])->update(['price' => $total, 'actual_square' => $data['square']]);

        //推送
        $application = 50003;
        $boss_uid    = $task_info->uid;
        /*
        $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_sub_offer_first')->first()->chn_name;
        $user_boss   = UserModel::find($boss_uid);
        $user_boss->send_num += 1;
        $user_boss->save();

        //保存发送的消息
        save_push_msg($message, $application, $boss_uid);
        PushServiceModel::pushMessageBoss($user_boss->device_token, $message, $user_boss->send_num, $application);*/
        push_accord_by_equip($boss_uid, $application, 'message_sub_offer_first', '', $data['task_id']);

        if (!$workOffer && !$work && !$task) {
            return response()->json(['error' => '操作失败'], '500');
        }

        return response()->json(array('message' => '设计师提交报价单成功'));
    }


    // 获取设计师报价清单
    public function getDesignerPriceList(Request $request) {
        $data['from_uid'] = $request->json('user_id');
        $data['task_id']  = $request->json('task_id');
        $work             = WorkModel::where('task_id', $data['task_id'])->where('status', 1)->first();

        $workOffer = WorkOfferModel::where('sn', 0)->where('task_id', $data['task_id'])
            ->where('from_uid', $data['from_uid'])->where('work_id', $work['id'])
            ->first();


        $percent = json_decode($workOffer['percent']);
        $list    = [
            '1' => [
                "title" => "初步设计",
                "price" => 0.2 * $percent[0]
            ],
            '2' => [
                "title" => "深化设计",
                "price" => 0.4 * $percent[1]
            ],
            '3' => [
                "title" => "施工指导",
                "price" => 0.4 * $percent[2]
            ]
        ];

        return response()->json($list);
    }


    // 设计师详情
    public function designerDetail(Request $request) {
        $user_id     = intval($request->get('user_id'));
        $designer_id = intval($request->get('designer_id'));

        if (empty($designer_id)) return response()->json(['error' => '非法参数'], '500');

        $detail = $this->userRespository->getDesignerDetailByid($designer_id,$user_id);

        return response()->json(empty($detail) ? [] : $detail);
    }

    /**
     * @param $userName
     * @return mixed
     * 业主端-搜索设计师或作品，按条件筛选接口
     */
    public function searchDesigner(Request $request) {

        $key      = $request->json('key');
        $UserList = UserModel::whereRaw('1=1');
        if ($key) {
            $UserList = $UserList->where('users.name', 'like', "%" . $key . "%");
        }
        $list = $UserList->get()->toArray();

        return response()->json(empty($list) ? [] : $list);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 获取作品详细
     */
    public function getGoodsDetail(Request $request) {
        $goods_id  = $request->get('goods_id');
        $goodsInfo = GoodsModel::select(
            'goods.id',
            'cover',
            'title',
            'goods.uid as designer_id',
            'view_num',
            'desc',
            'square',
            'house_id',
            'goods_address',
            'goods.created_at',
            'comments_num',
            'good_comment',
            'cate.name as cate_name',
            'house.name as house_name',
            'user_detail.avatar',
            'user_detail.nickname'
        )
            ->join('cate', 'cate.id', '=', 'goods.style_id')
            ->join('house', 'house.id', '=', 'goods.house_id')
            ->join('user_detail', 'user_detail.uid', '=', 'goods.uid')
            ->where('goods.id', $goods_id)
            ->where('status', 1)
            ->where('type', 1)
            ->where('is_delete', 0)
            ->first();

        if (!empty($goodsInfo)) {
            $attachment = UnionAttachmentModel::select(
                'a.id',
                'a.title',
                'a.url',
                'a.desc',
                'a.user_id as designer_id'
            )
                ->leftjoin('attachment as a', 'union_attachment.attachment_id', '=', 'a.id')
                ->where('object_id', $goodsInfo->id)
                ->where('a.status', 1)
                ->where('object_type', 4)
                ->get();
            foreach ($attachment as $key => &$value) {
                $value['url'] = url($value['url']);
            }
            $goodsInfo->cover  = url($goodsInfo->cover);
            $goodsInfo->avatar = url($goodsInfo->avatar);
        } else {
            return response()->json(['error' => '获取作品详情失败'], 500);
        }

        return response()->json([
            'main' => $goodsInfo,
            'auxiliary' => $attachment
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 管家获取工程配置单
     */
    public function houseGetProjectConfList(Request $request) {
        $task_id            = $request->get('task_id');
        $taskInfo           = TaskModel::find($task_id);
        $designer_task_info = TaskModel::where('project_position', $taskInfo->project_position)->where('status', '<', 9)->where('user_type', 2)->first();
        if (empty($designer_task_info)) {
            return response()->json(['error' => '获取不到设计师订单'], '404');
        }
        $list_data = ProjectConfigureTask::where('task_id', $designer_task_info->id)->orderBy('id', 'desc')->first();
        if (empty($designer_task_info)) {
            return response()->json(['error' => '系统错误'], '404');
        }
        $city_id                = $list_data->city_id;
        $project_configure_list = ProjectConfigureModel::select('id', 'pid', 'name', 'unit', 'project_type', 'work_type')
            ->where('city_id', $city_id)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();
        $handleArr              = [];
        foreach ($project_configure_list as $key => $value) {
            $value['ele_name'] = '';
            if ($value['pid'] == 0) {
                $handleArr['parent_' . $value['project_type']]['name']      = $value['name'];
                $handleArr['parent_' . $value['project_type']]['parent_id'] = $value['project_type'];
            } else {

                $pid               = 'parent_' . $value['project_type'];
                $value['child_id'] = $value['id'];
                unset($value['id']);
                unset($value['pid']);
                //水电阶段泥水和水电工种分开
                if ($value['work_type'] == 5) {
                    $value['ele_name'] = '泥水';
                }
                if ($value['work_type'] == 7) {
                    $value['ele_name'] = '水电';
                }
                unset($value['work_type']);

                $handleArr[$pid]['childs'][] = $value;

            }
        }
        return response()->json($handleArr);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 设计师获取工程配置单
     */
    public function designerGetProjectConfList(Request $request) {
        $city_id = empty($request->get('city_id')) ? 291 : $request->get('city_id');//city_id默认为1
        if (empty($city_id)) {
            return response()->json(['error' => '获取不到城市id'], '404');
        }
        $project_configure_list = ProjectConfigureModel::select('id', 'pid', 'name', 'unit', 'project_type', 'work_type')
            ->where('city_id', $city_id)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();
        $handleArr              = [];
        foreach ($project_configure_list as $key => $value) {
            $value['ele_name'] = '';
            if ($value['pid'] == 0) {
                $handleArr['parent_' . $value['project_type']]['name']      = $value['name'];
                $handleArr['parent_' . $value['project_type']]['parent_id'] = $value['project_type'];
            } else {

                $pid               = 'parent_' . $value['project_type'];
                $value['child_id'] = $value['id'];
                unset($value['id']);
                unset($value['pid']);
                //水电阶段泥水和水电工种分开
                if ($value['work_type'] == 5) {
                    $value['ele_name'] = '泥水';
                }
                if ($value['work_type'] == 7) {
                    $value['ele_name'] = '水电';
                }
                unset($value['work_type']);

                $handleArr[$pid]['childs'][] = $value;

            }
        }
        return response()->json($handleArr);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 获取设计师图纸
     */
    public function getDesignerImg(Request $request) {

        $task_id = $request->get('task_id');
        $type    = empty($request->get('type')) ? '' : $request->get('type');
        $work_id = WorkModel::where('task_id', $task_id)->where('status', '>', 0)->first();
        if (empty($work_id)) return response()->json(['error' => '无法找到该任务'], 500);

//        $imgList = WorkAttachmentModel::select('attachment.name', 'attachment.url')->where('img_type',1)->where('task_id', $task_id)->where('work_id', $work_id->id)->leftJoin('attachment', 'work_attachment.attachment_id', '=', 'attachment.id')->get();
        $workOffer = WorkOfferModel::where('task_id',$task_id)->where('sn',1)->where('work_id',$work_id->id)->first();

        if ($workOffer->upload_status == 0) return response()->json(['error' => '设计师仍未提交图纸'], 500);

        $imgList = WorkAttachmentModel::select('attachment.name', 'attachment.url')->where('img_type',1)->where('task_id', $task_id)->where('work_id', $work_id->id)->leftJoin('attachment', 'work_attachment.attachment_id', '=', 'attachment.id')->get();

        $handleArr = [];
        foreach ($imgList->toArray() as $key => &$value) {
//            $value['name']             = explode('.', $value['name'])[0];
//            if(empty($type)){
//                $value['url'] = $value['url'];
//            }else{
//                $value['url'] = explode('.', $value['url'])[0] . '_' . $type . '.'.explode('.', $value['url'])[1];
//            }

            $value['url'] = str_replace('.','{@fill}.',$value['url']);
            $handleArr[$value['name']] = url($value['url']);
        }

        return response()->json($handleArr);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 获取设计师图纸
     */
    public function getDesignerDeepImg(Request $request) {

        $task_id = $request->get('task_id');
//        $type = empty($request->get('type'))?'':$request->get('type');
        $work_id = WorkModel::where('task_id', $task_id)->where('status', '>' , 0)->first();
        if (empty($work_id)) return response()->json(['error' => '无法找到该任务'], 500);

        $workOffer = WorkOfferModel::where('task_id',$task_id)->where('sn',2)->where('work_id',$work_id->id)->first();
//        if ($imgList->isEmpty()) return response()->json(['error' => '设计师仍未提交图纸'], 500);
        if ($workOffer->upload_status == 0) return response()->json(['error' => '设计师仍未提交图纸'], 500);
        $imgList = WorkAttachmentModel::select('attachment.name', 'attachment.url','attachment.desc')->where('img_type',2)->where('task_id', $task_id)->where('work_id', $work_id->id)->leftJoin('attachment', 'work_attachment.attachment_id', '=', 'attachment.id')->get();
//        $handleArr = [];
//        for($i=1;$i<=10;$i++){
//            if(!isset($handleArr['deep_'.$i])){
//                $handleArr['deep_'.$i] = '';
//            }
//        }

        foreach ($imgList as $key => &$value) {
            $value['name'] = explode('.', $value['name'])[0];

//            if(empty($type)){
//                $value['url']  = $value['url'];
//            }else{
                if($value['name'] != 'deep_10'){
                    $value['url'] = str_replace('.','{@fill}.',$value['url']);
                }
//            }


            $value['url']  = url($value['url']);
            $value['desc'] = strip_tags($value['desc']);
            if ($value['name'] == 'deep_10') {
                $deep_10 = $value;
                unset($imgList[$key]);
            }
//            if($value['name'] == 'deep_10'){
//                $handleArr[] = ['name'=>$value['']]
//                $handleArr[$value['name']] = $value['desc'];
//            }else{
//                $handleArr[$value['name']] = url($value['url']);
//            }
        }

        if(isset($deep_10)){
            $imgList[] = $deep_10;
        }

        $imgList = array_values($imgList->toArray());


        return response()->json($imgList);
    }








    /******************************************************************************************************************
     *                                                    重构的接口                                                 **
     *****************************************************************************************************************/


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 根据task_id获取设计师的订单详细
     */

    public function getDesignerTaskDetail(Request $request) {
        $task_id = intval($request->get('task_id'));
        $tasks   = $this->taskRespository->getDesignerTaskDetail($task_id);
        return response($tasks);
    }

















}
