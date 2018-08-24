<?php

namespace App\Http\Controllers\v4\Api;

use App\AlipayTradeAppPayRequest;
use App\AopClient;
use App\Modules\Finance\Model\CashoutModel;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Manage\Model\ConfigModel;
use App\Modules\Manage\Model\SubOrderModel;
use App\Modules\Project\ProjectConfigureChangeModel;
use App\Modules\Project\ProjectLog;
use App\Modules\Project\ProjectLogComment;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\User\Model\BankAuthModel;
use App\Modules\User\Model\HouseKeeperComplaintModel;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\User\Model\UserFocusModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectDelayDate;
use App\Modules\Project\ProjectLaborChange;
//use EasyWeChat\Support\Log;
use App\PushSentenceList;
use App\PushServiceModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use DB;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\CommentModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\Task\Model\TaskCateModel;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\Manage\Model\SpaceModel;
use App\Modules\Manage\Model\HouseModel;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\Task\Model\WorkAttachmentModel;

use App\Modules\User\Model\AttachmentModel;
use App\Modules\Order\Model\OrderModel;
use App\Modules\Manage\Model\ServiceModel;
use Log;

use App\Respositories\UserRespository;
use App\Respositories\TaskRespository;
use App\Respositories\TaskAppointRespository;

class ApiHousekeeperController extends BaseController
{

    //注入
    protected $userRespository;
    protected $taskRespository;
    protected $taskAppointRespository;


    public function __construct(UserRespository $userRespository, TaskRespository $taskRespository, TaskAppointRespository $taskAppointRepository) {
        $this->userRespository       = $userRespository;
        $this->taskRespository       = $taskRespository;
        $this->taskAppointRepository = $taskAppointRepository;
    }



    /**
     * 管家约单
     */
    public function userCreateHouseKeeperAppointTask(Request $request) {
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
        $data['show_cash']        = $request->json('show_cash');       //即预算
        $data['housekeeperStar']  = !empty($request->json('housekeeperStar')) ? $request->json('housekeeperStar') : '1';//用户要求的星级（管家端和监理端需要传的字段）
        $data_designer['uid']     = $request->json('designer_id');//可能为多个  //约单人数限制

//        $data['workerStar']       = !empty($request->json('workerStar')) ? $request->json('workerStar') : '1';//管家端需要传的字段

//   任务状态:0暂不发布 1已经发布,未付发布费 2已经付发布费 3审核通过 4威客交稿 5雇主选稿 6任务公示 7交付验收 8双方互评 9已结束 10失败 11维权

//     2.设计师订单设计师流程中增加上传图片（PC），用户增加查看设计图片。
        foreach ($data as $key => $value) {
            if (empty($value) && $value != 0) {
                return $this->error('工程信息不可为空或工地不存在');
            }
        }

        //判断下是否传的是管家
        foreach ($data_designer['uid'] as $k => $v) {
            $houseKeeperInfo = UserModel::find($v);
            if (empty($houseKeeperInfo)) {
                return $this->error('找不到该管家');
            }
            if ($houseKeeperInfo->user_type != 3) {
                return $this->error('您选择了非管家人员');
            }
        }


        $is_work_able = $this->taskAppointRepository->createAppoint($data,$data_designer['uid']);

        //返回原因
        if (!($is_work_able['able'])) {
            return $this->error($is_work_able['errMsg']);
        } else {
            return $this->success(['task_id' => $is_work_able['successMsg']]);
        }
    }




    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 约单中心(管家)
     */
    public function getTasksAppointHouse(Request $request) {

        $uid       = intval($request->get('user_id'));
        $user_info = UserModel::find($uid);
        if (empty($user_info)) return $this->error('获取不到用户信息');
        $user_type              = $user_info->user_type;//看类型
        $list                   = TaskModel::appFindBy(['user_type' => $user_type, 'type_id' => 2, 'user_designer' => $uid]);//返回列表
        $count_house_keep_robot = WorkModel::where('uid', $uid)->where('status', '>', 0)->where('status', '<', 3)->count();//查找管家已接单的任务

        //抢单管家当前进行中的任务数量是否超过6个，超出不可抢（接）
/*        if ($user_type == 3 && $count_house_keep_robot > 6) {
            return $this->error('当前进行中的任务超过6个');
        }*/

        //抢单监理当前进行中的任务数量是否超过15个，超出不可抢（接）
/*        if ($user_type == 3 && $count_house_keep_robot > 15) {
            return $this->error('当前进行中的任务超过15个');
        }*/
        //如果管家是1星的,不能看到业主要要求的2星及以上的订单
/*        if ($user_type == 3) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取管家星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return $this->error([]);
                }
            }
        }*/

        //如果监理是1星的,不能看到业主要要求的2星及以上的订单
/*        if ($user_type == 4) {
            $workerStar = DB::table('user_detail')->where('uid', $uid)->first()->star;//获取监理星级
            foreach ($list->toArray() as $k => $v) {
                $boss_expect_house_keep_star = TaskModel::find($v['task_id'])->housekeeperStar;//业主期望的星级
                if ($boss_expect_house_keep_star > $workerStar) {
                    return $this->error([]);
                }
            }
        }*/

        foreach ($list->toArray() as $key => $value) {
            $value['avatar'] = url($value['avatar']);
            if ($value['status'] == 7) {
                $work_offer_status = WorkOfferModel::where('task_id', $value['task_id'])->orderBy('sn', 'ASC')->get()->toArray();

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
     * 管家确认或者拒绝此次的约单任务
     */
    public function houseKeeperReplyBoss(Request $request) {
        $task_id   = $request->json('task_id');
        $user_id   = $request->json('user_id');//设计师id
        $refuse_id = $request->json('refuse_id');//0接单(抢单),1接受,2拒绝,3超时

        $is_work_able = $this->taskAppointRepository->ReplyBoss($task_id,$user_id,$refuse_id);
        //返回原因
        if (!$is_work_able['able']) {
            return $this->error($is_work_able['errMsg']);
        }else {
            return $this->error('操作成功',0);
        }
    }


    /**
     * @param $chang_uid
     * @param $task_id
     * @param $origin_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 业主在没确认之前更换约单管家
     */
    public function userChangeAppointHouse(Request $request) {
        $chang_uid        = $request->json('chang_uid');
        $task_id          = $request->json('task_id');
        $origin_id        = $request->json('origin_id');
        $chang_uid_deatil = UserModel::find($chang_uid);
        if (empty($chang_uid_deatil) || $chang_uid_deatil->user_type !== 3)
            return $this->error('找不到该用户或更改的用户不是管家');
        //先去work表业主第一次选了几个人
        $count_work = WorkModel::where('task_id', $task_id)->count();
        if (empty($count_work)) return $this->error('非法操作');

        //log表加记录
        $data_log['new_uid'] = $chang_uid;
        $data_log['task_id'] = $task_id;
        //新加入管家
        if (empty($origin_id) && $count_work < 3) {
            $data_designer['desc']       = '';
            $data_designer['uid']        = $chang_uid;
            $data_designer['task_id']    = $task_id;
            $data_designer['created_at'] = date('Y-m-d H:i:s', time());

            //work表插入数据
            $result_designer     = WorkModel::create($data_designer);
            $result_designer_log = workDesignerLog::create($data_log);
            if ($result_designer && $result_designer_log) return $this->error('更改成功',0);
        } else {

            if ($chang_uid == $origin_id) return $this->error('您选了相同的设计师,请核对');
            //修改原来的人work_log
            $chang_work_log = workDesignerLog::where('task_id', $task_id)->where('new_uid', $origin_id)->where('is_refuse', 2)->first();
            //修改原来的人work
            $chang_work = WorkModel::where('uid', $origin_id)->where('task_id', $task_id)->where('status', 0)->first();
            if (empty($chang_work)) return $this->error('该项目原设计师未找到');
            //这里要判断下更换的人是否拒绝过这个订单

            //如果原设计师已接受这个订单,不可更改
            $seek_designer_status = workDesignerLog::where('new_uid', $origin_id)->where('is_refuse', 1)->first();
            if ($seek_designer_status) return $this->error('该设计师已确认接单,不可更改');

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
            if ($res_change_work && $res_change_work_log) return $this->error('更改成功',0);
        }

        return $this->error('更改失败');
    }

    /**
     * 监理提交第一次报价
     * @return \Illuminate\Http\Response
     */
    public function workerSubFirstOffer( Request $request )
    {
        $data['square']     = $request->json('square'); //实测面积
        $data['to_uid']     = $request->json('housekeeper_id');//工作人id
        $data['agree_star'] = empty($request->json('agree_star')) ? 1 : $request->json('agree_star');//商定的星级
        $data['task_id']    = $request->json('task_id');//任务id

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('必要参数为空');
            }
        }
        $task_info  = TaskModel::find($data['task_id']);
        if (empty($task_info)) return $this->error('找不到该项目');
        $user_type = UserModel::find($data['to_uid'])->user_type;//获取中标管家或者监理1：雇主 2：设计师 3：管家 4：监理 5工人
        if ($user_type == 3) {
            $config1 = LevelModel::getConfigByType(1)->toArray();
        } elseif ($user_type == 4) {
            $config1 = LevelModel::getConfigByType(2)->toArray();

//            if ($data['square'] < 50) {
//                $data['square'] = 50;
//            }
            //找到质保服务单价
            $quanlity_price       = ServiceModel::select('price')->where('identify', 'QUANLITYSERVICE')->first();
            $quanlity_price_total = $quanlity_price->price * $data['square'];//质保服务
            $task_info->quanlity_service_money = $quanlity_price_total;
            $task_info->save();
        }

        $workerStarPrice      = LevelModel::getConfig($config1, 'price');


        if ($task_info->type_id == 2) {
            //约单以要约的人的星级为准
//            $workerStar = UserDetailModel::select('star')->where('uid', $data['to_uid'])->first()->star;
            //约单以商量的星级为准
            $workerStar = $data['agree_star'];
        } else {
            //这里的星级以业主的所选星级为准
            $workerStar = $task_info->housekeeperStar;
        }

        $total               = $workerStarPrice[$workerStar - 1]->price * $data['square'];//星级5 乘以面积120
        $work                = WorkModel::where('task_id', $data['task_id'])->where('uid', $data['to_uid'])->where('status', 1)->first();
        $work->price         = $total;//修改work的价格
        $work->actual_square = $data['square'];//修改work的实测面积
        $task_status         = TaskModel::where('id', $data['task_id'])->update(['status' => 7,'boss_agree_star'=>$data['agree_star']]);
        $workOffer           = WorkOfferModel::where('sn', 0)->where('task_id', $data['task_id'])
                                                            ->update(['status' => 1, 'price' => $total, 'actual_square' => $data['square']]);

        if ($work->save() && $workOffer && $task_status) {
            switch ($user_type) {
                case 3:
                    $application = 50004;
                    break;
                case 4:
                    $application = 50005;
                    break;
                default:
                    $application = 50004;
            }
            //推送
            $boss_uid  = $task_info->uid;
/*            $message   = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_sub_offer_first')->first()->chn_name;
            $user_boss = UserModel::find($boss_uid);
            $user_boss->send_num += 1;
            $user_boss->save();

            //保存发送的消息
            save_push_msg($message, $application, $boss_uid);
            PushServiceModel::pushMessageBoss($user_boss->device_token, $message, $user_boss->send_num, $application);*/
            push_accord_by_equip($boss_uid, $application, 'message_sub_offer_first', '', $data['task_id']);
            return $this->error('管家提交报价成功',0);
        } else {
            return $this->error('操作失败');
        }

    }

    /**
     * 获取辅材包列表信息
     */
    public function getAuxiliaryBag(Request $request) {
        $task_id = $request->get('task_id');
        if (empty($task_id)) {
            $city_id = 291;
        } else {
            $city_id = ProjectConfigureTask::where('task_id', $task_id)->first()->city_id;
        }
        $auxiliary = DB::table('auxiliary')->select('name', 'price', 'id', 'detail_url')->where('city_id', $city_id)->get();
        foreach ($auxiliary as $key => &$value) {
            if (!empty($value->detail_url)) {
                $value->detail_url = url($value->detail_url);
            }
        }
        return $this->success($auxiliary);
    }

    /**
     * 获取辅材包详细信息
     */
    public function getAuxiliaryBagDetail(Request $request) {
        $pid       = $request->get('auxiliary_id') ? $request->get('auxiliary_id') : '1';
        $auxiliary = DB::table('auxiliary_detail')->select('id', 'brand', 'model', 'spec', 'unit_price', 'num', 'total')->where('pid', $pid)->get();
        return $this->success($auxiliary);
    }

    /**
     *
     * 获取配置单每项价格
     */
    public function getConfPriceList(Request $request) {
        $task_id       = $request->get('task_id');
        $conf          = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();//拿到配置单
        if (empty($conf)) return $this->error('找不到配置单');
        $confList      = unserialize($conf->project_con_list);
        $auxiliaryInfo = DB::table('auxiliary')->where('id', $conf->auxiliary_id)->first();
        if (empty($auxiliaryInfo)) return $this->error('辅材包丢失');

        $work_star = TaskModel::where('id', $task_id)->first();
        $work_star = $work_star->workerStar;

        switch ($work_star) {
            case 1:
                $work_star_rate = 1;
                break;
            case 2:
                $work_star_rate = 1.1;
                break;
            case 3:
                $work_star_rate = 1.2;
                break;
            case 4:
                $work_star_rate = 1.3;
                break;
            case 5:
                $work_star_rate = 1.4;
                break;
            default:
                $work_star_rate = 1;
        }

        $actual_square = WorkModel::where('status', '>=', 1)
            ->where('status', '<=', 4)
            ->where('task_id', $task_id)
            ->first()->actual_square;


        $ret['conf_list_price']      = ($confList['all_parent_price'] - $confList['parent_7']['parent_price']) * $work_star_rate + $confList['parent_7']['parent_price'];                               //选择星级工人之后的配置单价钱,其他阶段不要乘
        $ret['auxiliary_price']      = $auxiliaryInfo->price * $actual_square;                                   //辅材包价钱详情
        $ret['auxiliary_detail_url'] = !empty($auxiliaryInfo->detail_url) ? url($auxiliaryInfo->detail_url) : '';
        $ret['auxiliary_name']       = $auxiliaryInfo->name;
        $ret['need_worker_star']     = $work_star;
        $ret['bond_price']           = 0;                //工程保证金,两者想加乘以0.2
        $ret['total_price']          = $ret['conf_list_price'] + $ret['auxiliary_price'] + $ret['bond_price'];   //所有钱加起来
//        $ret['bond_price']           = ($ret['conf_list_price'] + $ret['auxiliary_price']) * 0.2;                //工程保证金,两者想加乘以0.2
        foreach ($ret as $key => &$value) {
            $value = (string)$value;
        }

        return $this->success($ret);
    }

    /**
     * 管家获取工程配置单
     */
    public function getProjectPriceList(Request $request) {

        //拿到设计师提交的配置单
        $task_id        = $request->get('task_id');
        $uid            = $request->get('user_id');


        //2：设计师 3：管家 4：监理

// 同时进行时，突然间结束一个，唯一编号还是一样，再找另一个，唯一编号还是一样，但要找是同一个订单的，可以根据正常接单字段来判断是否同属一订但
        //但如果设计师突然取消，但深化后就无法取消了，所以不存在设计师的订单会被取消，管家的取消则不需要考虑，只是重新根据设计师的的丹丹在发一个工程配置单。
        //而且管家进入了拆除后就无法在取消订单了，所以不用担心监理那部分逻辑会引起错误
        //就算唯一编号多个都是一样的，都不用担心，只需要根据工地查找回来他们呢的关系就可以
        //以上是个人的逻辑理解，只是备忘一下，不需理会


        $supervisor_task_data = TaskModel::where('status', '<=', 9)->where('id', $task_id)->first();
        //判断管家有没有提交过配置单
        $houseKeeper_list = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();

        if (empty($supervisor_task_data)) return $this->error('找不到订单');

        $project_ppsition = $supervisor_task_data->project_position;
        $user_type_user   = UserModel::find($uid)['user_type'];
        //如果要看管家的话
        if ($user_type_user == 3 && $houseKeeper_list) {
            $task_id = $task_id;
        } elseif ($user_type_user == 4 && empty($houseKeeper_list)) {
            $houseKeeper_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<=', 9)->where('user_type', 3)->first();
            $task_id          = $houseKeeper_task->id;
        } else {
            // TODO   管家提交后，设计师获取的应该是最终的。没提交前，要看自己的那一条记录。
            $designer_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<=', 9)->where('user_type', 2)->first();
//            var_dump($designer_task);exit;
            if (empty($designer_task)) {
                return $this->error('找不到设计师订单');
            }
            $task_id = $designer_task->id;
        }

        $confList = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();
        if (empty($confList)) return $this->error('找不到配置单');
        if ($confList->house_keeper_id) {
            $ret_new = unserialize($confList->project_con_list);
        } else {
            $ret     = unserialize($confList->project_con_list);
            $ret_new = create_configure_lists($ret);
        }
        return $this->success($ret_new);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 管家提交工程日志
     */
    public function housekeeperSubProjectLog(Request $request) {
        $data['task_id']         = $request->input('task_id');
        $data['img_serialize']   = $request->file('img_list');
        $data['sn']              = $request->input('sn');
        $data['desc']            = $request->input('desc');
//        var_dump($data['img_serialize']);exit();
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error($key . '参数不可为空');
            }
        }

        $taskInfo = DB::table('task')->where('id', $data['task_id'])->first();
        if (empty($taskInfo)) {
            return $this->error('不存在的订单');
        }

        $newArr         = [];
        $newBrr         = [];
        $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        for ($i = 0; $i <= 2; $i++) {

            if (isset($data['img_serialize'][$i]) && !empty($data['img_serialize'][$i])) {
                $file   = $data['img_serialize'][$i];
                $type   = 'user';
//                $result = \FileClass::uploadFile($file, $path = $type, $allowExtension);

                $result = \FileClass::thumbnailUploadImage($file,$type);
                $result = json_decode($result, true);

                if ($result['code'] != 200) {
                    return $this->error(array('error' => $result['message']), '503');
                }
                $img_name = explode('.', $result['data']['name']);

                $newArr[$img_name[0]] = $result['data']['url'];

            }

            if (isset($data['desc'][$i])) {
                $newBrr[$i] = $data['desc'][$i];
            }
        }


        $data['img_serialize']       = serialize($newArr);
        $data['desc']                = serialize($newBrr);
        $data['project_position_id'] = $taskInfo->project_position;
        $data['housekeeper_task_id']        = $data['house_keeper_id'];

        $work = DB::table('work')->where('status', '>', 1)->where('task_id', $data['task_id'])->first();
        if (empty($taskInfo) || empty($work)) return $this->error('找不到该订单');

        $original_task_id = $data['task_id'];
        //如果是监理提交的，应该找回管家的任务
        if($taskInfo->user_type == 4){
            $taskInfo = DB::table('task')->where('project_position', $taskInfo->project_position)->where('user_type',3)->where('status','<',9)->first();
            if (empty($taskInfo)) {
                return $this->error('未有管家参与');
            }
            $data['task_id'] = $taskInfo->id;
            $data['housekeeper_task_id'] = $taskInfo->id;
            $work = DB::table('work')->where('status', '>', 1)->where('task_id', $data['task_id'])->first();
            if (empty($taskInfo) || empty($work)) return $this->error('找不到该订单');
        }

        $offer_data_by_front = WorkOfferModel::where('task_id', $data['task_id'])->where('sn', $data['sn'])->first();
//var_dump($offer_data_by_front);exit();
        if (!empty($offer_data_by_front)) {
            $project_type_by_front = $offer_data_by_front->status;
        } else {
            return $this->error('不存在的工程阶段');
        }
        if ($project_type_by_front == 4){
			$data['sn']+=1;
			//return response()->json(['error' => '非法提交'], 500);
		}


//        判断提交的日志处于哪个工程阶段
        $project_stage = DB::table('work_offer')->where('work_id', $work->id)
            ->where('task_id', $data['task_id'])
            ->where('to_uid', $work->uid)
            ->where('project_type', '>', 0)
            ->where('type', 'housekeeper')
            ->orderBy('project_type', 'DESC')->get();
        $count         = count($project_stage) - 1;
        foreach ($project_stage as $key => $value) {
//          project_type 处于哪个阶段

            if ($value['status'] > 0) {
                $data['project_type'] = $value['project_type'];
                $data['stage']        = 0;
                break;
            }
            if ($key == $count) {
                $data['project_type'] = 1;
                $data['stage']        = 0;
            }
        }

        unset($data['img_serialize_1']);
        unset($data['img_serialize_2']);
        unset($data['img_serialize_3']);

        $data['created_at'] = date('Y-m-d H:i:s', time());
        $data['updated_at'] = date('Y-m-d H:i:s', time());


        $data['task_id'] = $original_task_id;

//        return $this->error(['message' => $data['task_id']]);
        $insertRet = DB::table('project_log')->insert($data);

        if ($insertRet) {
            return $this->error('提交成功',0);
        }

        return $this->error('提交失败');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获取开工日志
     */
    public function getConstructionLog(Request $request) {

        $task_id = $request->get('task_id');

        if (empty($task_id)) {
            return $this->error('非法提交');
        }

        $taskInfo = TaskModel::find($task_id);

        if (!empty($taskInfo)) {
            $project_position = $taskInfo->project_position;
        } else {
            return $this->error('不存在的任务');
        }

        if ($taskInfo->user_type != 3 && $taskInfo->user_type != 1) {
            $housekeeper_task_info = TaskModel::where('user_type', 3)->where('project_position', $project_position)->first();
            if (empty($housekeeper_task_info)) {
                return $this->error('未有管家');
            } else {
                $task_id = $housekeeper_task_info->id;
            }
        }

        //判断是否有拆除
        $is_have_dismantle     = 0;
        $res_is_have_dismantle = WorkOfferModel::where('task_id', $task_id)->where('project_type', 1)->first();
        if ($res_is_have_dismantle) {
            $is_have_dismantle = 1;
        }
//var_dump($task_id,$project_position);exit;
        $log = DB::table('project_log')->where('task_id', $task_id)->where('project_position_id', $project_position)->orWhere('housekeeper_task_id',$task_id)->get();
//        $log = DB::table('project_log')->where('task_id', $task_id)->orWhere('housekeeper_task_id',$task_id)->where('project_position_id', $project_position)->get();
//        $log = ProjectLog::where('task_id', $task_id)->where('project_position_id', $project_position)->get();
        $logIdArr = array_column($log,'id');
        $housekeeperIdArr = array_unique(array_column($log,'house_keeper_id'));
        $housekeeper_info = UserDetailModel::select('user_detail.uid','user_detail.nickname','users.user_type')->leftJoin('users','users.id','=','user_detail.uid')->whereIn('uid',$housekeeperIdArr)->get()->toArray();
        $temp_key = array_column($housekeeper_info,'uid');  //键值
        $housekeeper_info = array_combine($temp_key,$housekeeper_info) ;

        foreach ($log as $key => $value) {
            //把集合转成数组
            $value = collect($value)->toArray();

            if (!empty($value['desc'])) {
                $value['desc'] = unserialize($value['desc']);
            }
            if (!empty($value['img_serialize'])) {

                $img_serialize = unserialize($value['img_serialize']);
                foreach ($img_serialize as $key2 => &$value2) {
                    $value2 = url($value2);
                }
                $value['img_serialize'] = $img_serialize;
            }
            //加管家昵称
            $value['user_type'] = $housekeeper_info[$value['house_keeper_id']]['user_type'];
            $value['nikename']  =  !empty($housekeeper_info[$value['house_keeper_id']]['nickname']) ? $housekeeper_info[$value['house_keeper_id']]['nickname'] : '匿名用户';;
            $logHandle[$value['sn']][] = $value;

        }

        $b = [];
        if (empty($logHandle)) {
            $b['ProjectContent'] = [];
        } else {
            $m_o_evaluate = ProjectLogComment::select('project_log_comments.comments', 'user_detail.realname','project_log_comments.project_log_id')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'project_log_comments.uid')
                ->whereIn('project_log_id', $logIdArr)
                ->orderBy('project_log_comments.created_at', 'desc')
                ->get()->toArray();

            $m_o_evaluate_arr = [];
            foreach($m_o_evaluate as $key => $value){
                $arr['realname'] = empty($value['realname']) ? '匿名' : $value['realname'];
                $arr['user_type'] = $value['realname'];
                $arr['comments'] = empty($value['comments']) ? '' :  unserialize($value['comments']);
                $m_o_evaluate_arr[$value['project_log_id']][] = $arr;
            }


            foreach ($logHandle as $n => $m) {
                foreach ($m as $o => $p) {
                    if(isset($m_o_evaluate_arr[$p['id']]) && !empty($m_o_evaluate_arr[$p['id']]) ){
                        $m[$o]['evaluate'] = $m_o_evaluate_arr[$p['id']];
                    }else{
                        $m[$o]['evaluate'] = [];
                    }
                }
                $last_log['sn']           = $n;
                $last_log['DailyContent'] = $m;
                $b['ProjectContent'][]    = $last_log;
            }
        }

        $b['is_have_dismantle']        = $is_have_dismantle;
        $project_position_info         = ProjectPositionModel::select('live_tv_url', 'region', 'project_position')->where('id', $project_position)->first();
        $project_position_address      = $project_position_info->region . $project_position_info->project_position;
        $live_tv_url                   = $project_position_info->live_tv_url;
        $boss_info                     = UserDetailModel::select('nickname', 'avatar')->where('user_detail.uid', $taskInfo->uid)->first();
        $boss_info->avatar             = url($boss_info->avatar);
        $b['boss_info']                = $boss_info;
        $b['project_position_address'] = $project_position_address;
        $b['live_tv_url']              = $live_tv_url;
        return $this->success($b);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 获取开工日志(安卓端)
     */
    public function getConstructionLogAndroid(Request $request) {

        $task_id = $request->get('task_id');
        $logHandle = [];
        if (empty($task_id)) { return $this->error('非法提交'); }

        $taskInfo = TaskModel::find($task_id);

        if (!empty($taskInfo)) {
            $project_position = $taskInfo->project_position;
        } else {
            return $this->error('不存在的任务');
        }

        if ($taskInfo->user_type != 3 && $taskInfo->user_type != 1) {

            $housekeeper_task_info = TaskModel::where('user_type', 3)->where('project_position', $project_position)->first();

            if (empty($housekeeper_task_info)) {
                return $this->error('未有管家');
            } else {
                $task_id = $housekeeper_task_info->id;
            }
        }

        $c_sn = 0;
        $c_status = 0;
        $work_offer_info_detail = WorkOfferModel::select('status','sn','id')->where('task_id',$task_id)->orderBy('sn','desc')->get()->toArray();
        foreach($work_offer_info_detail as $key => $value){
            if( intval($value['status']) > 0 ){
                $c_status = $value['status'];
                $c_sn = $value['sn'];
                break;
            }
        }


        //判断是否有拆除
        $is_have_dismantle     = 0;
        $res_is_have_dismantle = WorkOfferModel::where('task_id', $task_id)->where('project_type', 1)->first();
        if ($res_is_have_dismantle) {
            $is_have_dismantle = 1;
        }
        $log = ProjectLog::where('task_id', $task_id)->where('project_position_id', $project_position)->orWhere('housekeeper_task_id',$task_id)->get();

        $housekeeperIdArr = array_unique(array_column($log->toArray(),'house_keeper_id'));
        $housekeeper_info = UserDetailModel::select('user_detail.uid','user_detail.nickname','users.user_type')->leftJoin('users','users.id','=','user_detail.uid')->whereIn('uid',$housekeeperIdArr)->get()->toArray();
        $temp_key = array_column($housekeeper_info,'uid');  //键值
        $housekeeper_info = array_combine($temp_key,$housekeeper_info) ;

        $logIdArr = array_column($log->toArray(),'id');

        foreach ($log->toArray() as $key => $value) {
            //把集合转成数组
            $value = collect($value)->toArray();
            if (!empty($value['desc'])) { $value['desc'] = unserialize($value['desc']); }
            if (!empty($value['img_serialize'])) {
                $img_serialize = unserialize($value['img_serialize']);
                foreach ($img_serialize as $key2 => &$value2) {
                    $value2 = url($value2);
                }
                $value['img_serialize'] = $img_serialize;
            }
            //加管家昵称
            $value['user_type'] = $housekeeper_info[$value['house_keeper_id']]['user_type'];
            $value['nikename']  =  !empty($housekeeper_info[$value['house_keeper_id']]['nickname']) ? $housekeeper_info[$value['house_keeper_id']]['nickname'] : '匿名用户';;
            $logHandle[$value['sn']][] = $value;

        }
        $b = [];
        if (empty($logHandle)) {
            $b['ProjectContent'] = [];
        } else {
            $other_sn = WorkOfferModel::select('sn')->where('task_id',$task_id)->where('project_type','>',0)->get();
            $other_sn_data = [];

            foreach ($other_sn as $k => $v) {
                $other_sn_data[] = $v['sn'];
            }
            foreach ($logHandle as $k => $v) {
                $have_data_sn[] = $k;
            }
            $diff_sn = array_diff($other_sn_data, $have_data_sn);

            foreach ($diff_sn as $n => $m) {
                $logHandle[$m] = [];
            }

            ksort($logHandle);

            $m_o_evaluate = ProjectLogComment::select('project_log_comments.comments', 'user_detail.realname','project_log_comments.project_log_id')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'project_log_comments.uid')
                ->whereIn('project_log_id', $logIdArr)
                ->orderBy('project_log_comments.created_at', 'desc')
                ->get()->toArray();
            $m_o_evaluate_arr = [];
            foreach($m_o_evaluate as $key => $value){
                $arr['realname'] = empty($value['realname']) ? '匿名' : $value['realname'];
                $arr['comments'] = empty($value['comments']) ? '' :  unserialize($value['comments']);
                $m_o_evaluate_arr[$value['project_log_id']][] = $arr;
            }
            foreach ($logHandle as $n => $m) {
                if (!empty($m)) {
                    foreach ($m as $o => $p) {
                        if(isset($m_o_evaluate_arr[$p['id']]) && !empty($m_o_evaluate_arr[$p['id']]) ){
                            $m[$o]['evaluate'] = $m_o_evaluate_arr[$p['id']];
                        }else{
                            $m[$o]['evaluate'] = [];
                        }
                    }
                }
                $last_log['sn']           = $n;
                $last_log['DailyContent'] = $m;
                $b['ProjectContent'][]    = $last_log;

            }
        }
        if(empty($b['ProjectContent'])){
            $b['ProjectContent'] = [
                ['sn'=>4,'DailyContent'=>[]],
                ['sn'=>5,'DailyContent'=>[]],
                ['sn'=>6,'DailyContent'=>[]],
                ['sn'=>7,'DailyContent'=>[]],
                ['sn'=>8,'DailyContent'=>[]],
                ['sn'=>9,'DailyContent'=>[]],
                ['sn'=>10,'DailyContent'=>[]],
            ];
        }
//TODO 垃圾写法，为了补全有10个
        $drr = array_column($b['ProjectContent'],'sn');

        for($sn_i = 4;$sn_i<=10;$sn_i++){
            if(!in_array($sn_i , $drr)){
                $b['ProjectContent'][] = ['sn'=>$sn_i , 'DailyContent'=>[]];
            }
        }


        if($taskInfo->status >= 9){
            $designerTaskDetail = TaskModel::where('unique_code',$taskInfo->unique_code)->where('user_type',2)->where('end_order_status',1)->first();
        }else{
            $designerTaskDetail = TaskModel::where('unique_code',$taskInfo->unique_code)->where('user_type',2)->where('status','<',9)->first();
        }
        $first_image = '';
        if(!empty($designerTaskDetail)){
            $images = WorkAttachmentModel::select('work_attachment.id', 'attachment.url', 'work_attachment.task_id')
                ->join('attachment', 'attachment.id', '=', 'work_attachment.attachment_id')
                ->where('work_attachment.img_type', 1)
                ->where('work_attachment.task_id', $designerTaskDetail->id)
                ->offset(1)->limit(1)->get()->first();

            if(!empty($images)){
                $imageArrExplode = explode('.',$images->url);
                $first_image = url($imageArrExplode[0].'_medium.'.$imageArrExplode[1]);
            }
        }

        $b['is_have_dismantle']        = $is_have_dismantle;
        $project_position_info         = ProjectPositionModel::select('live_tv_url', 'region', 'project_position')->where('id', $project_position)->first();
        $project_position_address      = $project_position_info->region . $project_position_info->project_position;
        $live_tv_url                   = $project_position_info->live_tv_url;
        $boss_info                     = UserDetailModel::select('nickname', 'avatar')->where('user_detail.uid', $taskInfo->uid)->first();
        $boss_info->avatar             = url($boss_info->avatar);
        $b['boss_info']                = $boss_info;
        $b['project_position_address'] = $project_position_address;
        $b['live_tv_url']              = $live_tv_url;
        $b['first_image']              = $first_image;
        $b['status']                   = $c_status;
        $b['sn']                       = $c_sn;
        return $this->success($b);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * 提交日志评论
     */
    public function commentProjectLog(Request $request) {
        $data['uid']            = $request->json('uid');
        $data['comments']       = empty($request->json('comments')) ? '' : serialize(trim($request->json('comments')));
        $data['project_log_id'] = $request->json('project_log_id');
        if (ProjectLogComment::create($data))
            return $this->success();
        else
            return $this->error();
    }


    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response\Factory3
     * 根据id获取所有日志评论
     */
    public function getAllCommentOfLogById(Request $request) {
        $project_log_id = $request->get('project_log_id');
        $data           = ProjectLogComment::select('project_log_comments.comments', 'user_detail.realname', 'project_log_comments.created_at')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'project_log_comments.uid')
            ->where('project_log_id', $project_log_id)
            ->orderBy('project_log_comments.created_at', 'desc')
            ->get();
        foreach ($data as $k => $v) {
            $data[$k]['realname'] = empty($data[$k]['realname']) ? '匿名' : $data[$k]['realname'];
            $data[$k]['comments'] = empty($data[$k]['comments']) ? '' : unserialize($data[$k]['comments']);
        }
        return $this->success($data);
    }


    /**
     * 交易评论
     */
    public function evaluateCreate(Request $request)
    {
        $data = $request->input('data');

        //判断当前评价的人是否具有评价资格
        $is_checked = WorkModel::where('task_id',$data['task_id'])
            ->where('uid',$this->user['id'])
            ->where('status',3)->first();
        //查询雇主信息
        $task = TaskModel::where('id',$data['task_id'])->first();

        if(!$is_checked && $task['uid']!=$this->user['id'])
        {
            return $this->error('你不具备评价资格!',500);
        }
        //保存评论数据
        $data['from_uid'] = $this->user['id'];
        $data['comment'] = e($data['comment']);
        $data['created_at'] = date('Y-m-d H:i:s',time());
        //评论雇主
        if($is_checked)
        {
            $data['to_uid'] = $task['uid'];
            $data['comment_by'] = 0;
        }else if($task['uid']==$this->user['id'])
        {
            $work = WorkModel::where('id',$data['work_id'])->first();
            $data['to_uid'] = $work['uid'];
            $data['comment_by'] = 1;
        }

        $is_evaluate =  CommentModel::where('from_uid',$this->user['id'])
            ->where('task_id',$data['task_id'])->where('to_uid',$data['to_uid'])
            ->first();

        if($is_evaluate)
        {
            return $this->error('你已经评论过了!');
        }


        $result = CommentModel::commentCreate($data);

        if(!$result)
        {
            return $this->error('评论失败');
        }
        return $this->error('评论成功',0);
    }



    /**
     * 根据任务id查辅材包详细和工人星级
     */
    public function getAuxiliaryDetail(Request $request) {

        $task_id     = $request->get('task_id');
        $list_detail = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();
        if (empty($list_detail)) {
            return $this->error('找不到辅材包');
        }
        $data['auxiliary_detail'] = DB::table('auxiliary')->select('id', 'price', 'name', 'detail_url')->find($list_detail->auxiliary_id);
        $data['work_star']        = TaskModel::find($task_id)->workerStar;
        return $this->success($data);
    }

    /**
     * 管家和监理列表
     */
    public function getHouseKeeperlists(Request $request) {

        $user_type             = $request->get('user_type') ? $request->get('user_type') : '3';
        $userCityId = $request->get('user_city_id') ? $request->get('user_city_id') : '0';

        $data['serve_area']    = RealnameAuthModel::select('serve_area')->distinct()->get();
        $data['star']          = [1, 2, 3, 4, 5];
        $data['need_star']     = intval($request->get('need_star'));
        $data['num_of_employ'] = [100, 500, 1000];//设置成交量
        $data['districts']     = DistrictModel::getDistrictProvinceFiles();
//var_dump(100000*(microtime()-$time1));exit;



        if ($user_type == 3) {
            $config1 = LevelModel::getConfigByType(1)->toArray();
        } elseif ($user_type == 4) {
            $config1 = LevelModel::getConfigByType(2)->toArray();
        }

        $workerStarPrice = LevelModel::getConfig($config1, 'price');
        $one_star_price  = $workerStarPrice[0]->price;

        foreach ($workerStarPrice as $k => $v) {
            $data['cost_of_design'][] = $v->price;
        }

        $items = UserModel::select('user_detail.nickname', 'user_detail.realname', 'user_detail.city', 'user_detail.star', 'user_detail.avatar', 'user_detail.uid', 'user_detail.experience', 'user_detail.star', 'realname_auth.serve_area', 'user_detail.tag','user_detail.lat','user_detail.lng')
            ->where('users.status', 1)
            ->where('users.user_type', $user_type);

//        var_dump($data['need_star']);exit;
        if(!empty($data['need_star'])){

            $items = $items->where('user_detail.star',$data['need_star']);
        }


//此处的分页页数由前端在url上拼接，laravel的分页类已经根据路由上的page参数处理好返回的数据了
        if(!empty($userCityId)){

            $data['items'] = $items->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')
                ->leftJoin('realname_auth', 'realname_auth.uid', '=', 'users.id')
                ->where('user_detail.serve_area_id',$userCityId)
                ->orderBy('users.sort_id')
                ->simplePaginate(30)->toArray()['data'];
        }else{
            $data['items'] = $items
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')
                ->leftJoin('realname_auth', 'realname_auth.uid', '=', 'users.id')
                ->orderBy('users.sort_id')
                ->simplePaginate(30)->toArray()['data'];
        }

        $itemIdArr = array_column($data['items'],'uid');
        $num_of_employ_arr = UserDetailModel::whereIn('uid', $itemIdArr)->lists('employee_num','uid')->toArray();
        $focus_num = UserFocusModel::select(DB::raw('focus_uid,count(focus_uid) num'))->whereIn('focus_uid', $itemIdArr)->groupBy('focus_uid')->get()->toArray();
        $new_foucs_num = [];
        foreach($focus_num as $key => $value){
            $new_focus_num[$value['focus_uid']] = $value['num'];
        }
//var_dump($itemIdArr,$focus_num);exit;
        foreach ($data['items'] as $key => $value) {
            $data['items'][$key]['experience'] = floatval($value['experience']);
            $data['items'][$key]['avatar']     = !empty($value['avatar']) ? url($value['avatar']) : '';
            $data['items'][$key]['nickname']   = !empty($value['nickname']) ? $value['nickname'] : $value['realname'];
            $data['items'][$key]['star']       = $value['star'];
            $data['items'][$key]['tag']        = empty($value['tag']) ? [] : unserialize($value['tag']);
            $value['star']                     = empty($value['star']) ? 1 : $value['star'];
            if ($value['star'] == 1) {
                $data['items'][$key]['unit_price'] = $workerStarPrice[$value['star'] - 1]->price;
            } else {
                $data['items'][$key]['unit_price'] = $one_star_price . '-' . $workerStarPrice[$value['star'] - 1]->price;
            }

            unset($data['items'][$key]['realname']);
            //实际成交量
            /*            $num_of_employ                        = WorkModel::where('uid', $value['uid'])->where('status', '>', '1')->count();
                        $data['items'][$key]['num_of_employ'] = !empty($num_of_employ) ? $num_of_employ : 0;     */

//            $focus_num                            = UserFocusModel::select('uid')->where('focus_uid', $value['uid'])->count();//关注量
            $data['items'][$key]['num_of_employ'] = $num_of_employ_arr[$value['uid']];

            $data['items'][$key]['focus_num']     = isset($new_focus_num[$value['uid']])?$new_focus_num[$value['uid']]:0;
        }




        return $this->success($data);
    }



    /**
     * 获取第一次报价的每个费用(管家和设计师)
     */
    public function getFirstOfferPrice(Request $request) {

        $task_id   = $request->get('task_id');
        $task_data = TaskModel::where('id', $task_id)->first();
        $work_data = WorkModel::where('task_id', $task_id)->where('status', '>', 0)->where('status', '<', 4)->first();
        if (empty($task_data) || empty($work_data)) return $this->error('找不到该任务的数据');
        $data_offer = WorkOfferModel::where('task_id', $task_id)->where('work_id', $work_data->id)->where('sn', 0)->first();
        if (empty($data_offer)) return $this->error('找不到该任务的数据');
        $all_data_offer = WorkOfferModel::where('task_id', $task_id)->where('work_id', $work_data->id)->where('sn', '>', 0)->get();//
        // 接单人的角色，管家还是设计师还是监理
        $user_data = UserModel::select('user_detail.star', 'user_detail.cost_of_design', 'users.user_type')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')
            ->where('users.id', $work_data->uid)
            ->first();

        $first_offer_price = $data_offer->price;//第一次报价
        $actual_square     = $work_data->actual_square;//实测面积

        // 设计师单价
        $res_designer_price = [];
        if ($user_data->user_type == 2) {
            $unit_price     = $task_data->designer_actual_price;
            if (!empty($all_data_offer->toArray())) {
                foreach ($all_data_offer as $n => $m) {
                    $res_designer_price['sn_' . $m['sn']] = $m['price'];
                }
            } else {
                $res_designer_price = ['sn_1' => $first_offer_price * 0.2, 'sn_2' => 0.4 * $first_offer_price, 'sn_3' => 0.4 * $first_offer_price];
            }
        }

        //找到预约金
        $poundage_service = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;


        if ($task_data->type_id == 2) {
            //约单以要约的人的星级为准
//            $star_house_keeper = UserDetailModel::select('star')->where('uid', $work_data->uid)->first()->star;

            //约单以商量的为准
            $star_house_keeper = TaskModel::find($task_id)->boss_agree_star;
        } else {
            //这里的星级以业主的所选星级为准
            $star_house_keeper = $task_data->housekeeperStar;
        }

        // 管家星级单价
        if ($user_data->user_type == 3) {
            $config1           = LevelModel::getConfigByType(1)->toArray();
            $workerStarPrice   = LevelModel::getConfig($config1, 'price');
            $unit_price        = $workerStarPrice[$star_house_keeper - 1]->price;
        }

        // 监理星级单价
        if ($user_data->user_type == 4) {
            $config1           = LevelModel::getConfigByType(2)->toArray();
            $workerStarPrice   = LevelModel::getConfig($config1, 'price');
            $unit_price        = $workerStarPrice[$star_house_keeper - 1]->price;
        }
        $data = [
            'first_offer_price' => (float)$first_offer_price,
            'unit_price' => (float)$unit_price,
            'poundage_service' => (float)$poundage_service,//预约金
            'worker_star' => 0,
            'actual_square' => (float)$actual_square,
            'actually_pay' => (float)($first_offer_price - $poundage_service),
            'quanlity_service_money' => (float)$task_data->quanlity_service_money //找到质保费用
        ];

        if ($user_data->user_type == 2) {
            foreach ($res_designer_price as $key => $value) {
                $data[$key] = $value;
            }

        }

        return $this->success($data);
    }


    /**
     * 查看开工日期和竣工日期
     */
    public function gerProjectDeadTime(Request $request) {

        $task_id   = $request->get('task_id');
        $task_info = TaskModel::find($task_id);
        if (empty($task_info)) return $this->error('找不到该任务的数据');
        $start_time = empty($task_info->begin_at) ? '' : date('Y-m-d', strtotime($task_info->begin_at));
        $end_time   = empty($task_info->end_at) ? '' : date('Y-m-d', strtotime($task_info->end_at));
        $data       = ['start_time' => $start_time, 'end_time' => $end_time];
        return $this->success($data);
    }

    /**
     * 查看每一次管家更改的时间
     */
    public function getProjectChangeDeadTime(Request $request) {

        $task_id   = $request->get('task_id');
        $sn        = $request->get('sn');
        $task_info = TaskModel::find($task_id);

        if (empty($task_info)) return $this->error('找不到该任务的数据');
        if ($task_info->user_type == 4) {
            //根据监理工地找到管家的订单
            $project_ppsition  = $task_info->project_position;
            $house_keeper_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<', 9)->where('user_type', 3)->first();
            if (empty($house_keeper_task)) return $this->error('找不到对应的管家订单');
            $task_id = $house_keeper_task->id;
            $task_info = TaskModel::find($task_id);
        }
        $change_date = ProjectDelayDate::select('original_date as project_original_stage_date', 'end_date as project_change_end_time')->where('task_id', $task_id)->where('sn', $sn)->orderBy('id', 'desc')->first();
        //var_dump($change_date);exit();
        $project_start_time = empty($task_info->begin_at) ? '' : date('Y-m-d', strtotime($task_info->begin_at));
        if (empty($change_date)) return $this->error();

        $change_date->project_change_end_time     = date('Y-m-d', strtotime($change_date->project_change_end_time));
        $change_date->project_original_stage_date = date('Y-m-d', strtotime($change_date->project_original_stage_date));
        $change_date['change_days']               = (strtotime($change_date->project_change_end_time) - strtotime($change_date->project_original_stage_date)) / (24 * 3600);
        $change_date['project_start_time']        = $project_start_time;

        return $this->success($change_date);

    }

    /**
     * 工人查看自己的订单信息
     */

    public function laborGetProjectInfo(Request $request) {

        $labor_id          = $request->get('labor_id');
        $data_offer        = WorkOfferModel::select('task_id')->where('type', 'labor')->where('to_uid', $labor_id)->orWhere('to_uid', 'like', '%' . $labor_id . '-' . '%')->orWhere('to_uid', 'like', '%' . '-' . $labor_id . '%')->distinct('task_id')->get();
        $data_labor_change = ProjectLaborChange::select('task_id')->where('old_labor', $labor_id)->orWhere('new_labor', $labor_id)->distinct('task_id')->get();
        $data_labor_change = empty($data_labor_change) ? [] : $data_labor_change->toArray();
        $all_task_id       = (array_merge($data_offer->toArray(), $data_labor_change));//去重
        $data_guanjia      = [];

        foreach ($all_task_id as $k => $v) {
            $data_guanjia[] = WorkModel::select('work.uid as houseKeeper_id', 'user_detail.nickname', 'user_detail.mobile', 'user_detail.avatar', 'task.project_position', 'project_position.project_position', 'project_position.region', 'task.room_config', 'task.status as node', 'task.id as task_id')
                ->leftJoin('users', 'users.id', '=', 'work.uid')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')
                ->leftJoin('task', 'task.id', '=', 'work.task_id')
                ->leftJoin('project_position', 'project_position.id', '=', 'task.project_position')
                ->leftJoin('cate', 'cate.id', '=', 'task.favourite_style')
                ->where('work.task_id', $v['task_id'])
                ->first()->toArray();
        }

        $labor_rule        = json_decode(ConfigModel::where('type', 'worker')->first()->rule, true);
        $labor_work_type   = UserDetailModel::where('uid', $labor_id)->first()->work_type;//工种名
        $is_have_dismantle = 0;//是否有拆除
        //给前端进行到哪一步了
        foreach ($data_guanjia as $item => $value) {
            if ($value['node'] == 7) {

                //查的是工程去到了哪一步(work_offer表的数据是唯一的)
                $project_status = WorkOfferModel::select('status', 'sn', 'project_type')->where('task_id', $value['task_id'])->where('project_type', '>', 0)->get()->toArray();
                foreach ($project_status as $o => $p) {
                    if ($p['status'] == 0) {
                        unset($project_status[$o]);
                    }
                    if ($p['status'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }

                if (empty($project_status)) {
                    $project_status = [['status' => 0, 'sn' => 0]];
                } else {
                    $project_status[count($project_status) - 1];
                }
                $data_guanjia[$item]['current_status']    = $project_status[count($project_status) - 1];
                $data_guanjia[$item]['is_have_dismantle'] = $is_have_dismantle;
                $data_guanjia[$item]['work_type_name']    = $labor_rule[$labor_work_type];
                $data_guanjia[$item]['mobile']            = empty($data_guanjia[$item]['mobile']) ? UserModel::find($value['houseKeeper_id'])->name : $data_guanjia[$item]['mobile'];

            }
            $data_guanjia[$item]['avatar'] = url($value['avatar']);
        }

        return $this->success($data_guanjia);
    }


    
    /**
     * 查看每一次变更时间的记录
     */
    public function getDelayDates(Request $request) {

        $task_id   = $request->get('task_id');
        $task_info = TaskModel::find($task_id);
        if (empty($task_info)) return $this->error('找不到该任务');
        $user_type        = $task_info->user_type;
        $project_position = $task_info->project_position;

        //监理查看管家
        if ($user_type == 4) {

            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $house_keeper_task->id;

        }


        $data_delay                = ProjectDelayDate::where('task_id', $task_id)->where('is_sure', 5)->get();
        $data_delay_of_change_list = ProjectLaborChange::where('task_id', $task_id)->where('is_confirm', 1)->get();
        $new_data                  = [];

        foreach ($data_delay as $k => $v) {

            $delay_days = (int)(strtotime($v->end_date) - strtotime($v->original_date)) / (3600 * 24);//延期了好多天

            $new_data[] = [
                'operate_time' => $v->updated_at->format('Y-m-d H:i:s'),//操作时间
                'original_date' => date('Y-m-d', strtotime($v->original_date)),//原项目结束时间
                'end_date' => date('Y-m-d', strtotime($v->end_date)),//修改后,项目结束时间
                'sn' => $v->sn,//哪个步骤延期的
                'delay_days' => $delay_days,//延期了好多天
            ];
        }


        foreach ($data_delay_of_change_list as $k => $v) {

            $delay_days = (int)(strtotime($v->change_date) - strtotime($v->original_date)) / (3600 * 24);//延期了好多天
            $new_data[] = [
                'operate_time' => $v->updated_at->format('Y-m-d H:i:s'),//操作时间
                'original_date' => date('Y-m-d', strtotime($v->original_date)),//原项目结束时间
                'end_date' => date('Y-m-d', strtotime($v->change_date)),//修改后,项目结束时间
                'sn' => $v->sn,//哪个步骤延期的
                'delay_days' => $delay_days,//延期了好多天
            ];
        }

        //拼一下格式,给前端
        $format_date = [];
        foreach ($new_data as $n => $m) {
            $format_date[$m['sn']]['sn']            = $m['sn'];
            $format_date[$m['sn']]['DateContent'][] = $m;
        }

        //项目最终确定的日期
        $last_date = TaskModel::where('id', $task_id)->first();

        $lastArr['history']         = array_values($format_date);
        $lastArr['last_start_date'] = date('Y-m-d', strtotime($last_date->begin_at));
        $lastArr['last_end_date']   = date('Y-m-d', strtotime($last_date->end_at));

        return $this->success($lastArr);

    }

    /**
     * 查看工程单,业主获取工人列表
     */
    public function getProjectLabors(Request $request) {
        $task_id     = $request->get('task_id');
        $data_labors = WorkOfferModel::where('task_id', $task_id)->where('project_type', '>', 0)->get();
        if (empty($data_labors)) return $this->error('系统还未匹配工人');
        $new_data      = [];
        $labor_rule    = json_decode(ConfigModel::where('type', 'worker')->first()->rule, true);
        $labor_rule[0] = '管家';

        foreach ($data_labors as $k => $v) {
            $workArr                              = explode('-', $v['to_uid']);
            $new_data[$v['project_type']]['name'] = $v['title'];
            foreach ($workArr as $n => $m) {
                $user_info = UserDetailModel::select('realname_auth.serve_area','user_detail.native_place', 'user_detail.avatar', 'user_detail.nickname', 'user_detail.star', 'user_detail.work_type', 'user_detail.user_age', 'realname_auth.serve_area', 'user_detail.experience')
                    ->leftJoin('realname_auth', 'realname_auth.uid', '=', 'user_detail.uid')
                    ->where('user_detail.uid', $m)
//                    ->where('realname_auth.status', 1)
                    ->first();

                if (empty($user_info)) return $this->error('找不到工人信息');
                $user_info                               = $user_info->toArray();
                if ($user_info['work_type'] == 1) {
                    $user_info['work_type'] = 0;
                }
                $user_info['avatar']                     = empty($user_info['avatar']) ? '' : url($user_info['avatar']);
                $user_info['nickname']                   = empty($user_info['nickname']) ? '' : $user_info['nickname'];
                $user_info['serve_area']                 = empty($user_info['serve_area']) ? '' : $user_info['serve_area'];
                $user_info['work_type_name']             = $labor_rule[$user_info['work_type']];

                // 这里可以上传到外网
                $user_info['evaluate_good']   = CommentModel::where('to_uid', $m)->where('total_score', 5)->count();
                $user_info['evaluate_normal'] = CommentModel::where('to_uid', $m)->where('total_score', 3)->count();
                $user_info['evaluate_bad']    = CommentModel::where('to_uid', $m)->where('total_score', 1)->count();


                $new_data[$v['project_type']]['labor'][] = $user_info;
            }

        }

        return $this->success(array_values($new_data));

    }


    /**
     * 获取账单
     */
    public function getBillOfTask(Request $request) {

        $uid_boss = $request->get('uid_boss');
        $data_all = SubOrderModel::select('id as sub_order_id','order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'fund_state','withdraw_status')
            ->where('cash', '>', 0)
            ->where('uid', $uid_boss)
            ->orderBy('created_at', 'desc')->get();


        //查找用户的充值
        $detail_recharge = OrderModel::select('cash', 'code as order_code', 'title', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)
            ->where('uid', $uid_boss)->orderBy('created_at', 'desc')->get();

        if ($data_all->isEmpty()) return $this->error('找不到该订单数据');
        $new_data = [];
        foreach ($data_all->toArray() as $k => $v) {
            if ($v['fund_state'] == 1) {
                $sign = '-';
            } else {
                $sign = '+';
            }
            $cash_info                             = CashoutModel::select('status')->where('task_id', $v['task_id'])->where('sn', $v['project_type'])->first();
            $status_cash_info = empty($cash_info) ? 0 : $cash_info->status;
            $v['cash_info_status']                 = cash_out_status($status_cash_info);
            $v['cash']                             = doubleval($sign . $v['cash']);
            $wai_time                              = date('Y-m-d', strtotime($v['created_at']));
            $new_data[$wai_time]['time']           = $wai_time;
            $v['created_at']                       = date('H:i:s', strtotime($v['created_at']));
            $new_data[$wai_time]['Bill_Content'][] = $v;
        }
        $new_data_recharge = [];

        //充值
        foreach ($detail_recharge->toArray() as $k => $v) {
            $v['cash']                                      = '+' . $v['cash'];
            $wai_time                                       = date('Y-m-d', strtotime($v['created_at']));
            $new_data_recharge[$wai_time]['time']           = $wai_time;
            $v['created_at']                                = date('H:i:s', strtotime($v['created_at']));
            $new_data_recharge[$wai_time]['Bill_Content'][] = $v;
        }

        $all_data = array_merge(array_values($new_data), array_values($new_data_recharge));
        return $this->success(($all_data));
        //计算每天的支出
        /*        foreach ($new_data as $k => $v) {
                    foreach ($v as $n => $m) {
                        $cash = '';
                        for ($i = 0; $i < count($m); $i++) {
                            $cash += $new_data[$k][$n][$i]['cash'];
                            $new_data[$k][$n]['total_cash'] = $cash;
                        }
                    }
                }*/
    }


    /**
     * 数组转xml
     */
    public function arrayToXml($arr) {
        $xml = "<xml\>";
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $xml .= "<" . $key . ">" . arrayToXml($val) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    /**
     * alipay
     */
    public function postCashUserAliPay(Request $request) {
        $total_amount = $request->json('total_amount');
        $user_id      = $request->json('user_id');
        $user         = UserDetailModel::where('uid', $user_id)->first();

        if (empty($user) || empty(UserModel::find($user_id))) return $this->error('找不到该用户,无法充值');

        $config       = ConfigModel::getConfigByAlias('cash');
        $config->rule = json_decode($config->rule, true);

        /*        if ($cash < $config->rule['recharge_min']) {
                    return \CommonClass::formatResponse('充值金额不得小于' . $config->rule['recharge_min'] . '元', 201);
                }*/
        $data             = array(
            'code' => OrderModel::randomCode($user_id),
            'title' => '余额充值',
            'cash' => $total_amount,
            'uid' => $user_id,
            'task_id' => 0,
            'created_at' => date('Y-m-d H:i:s', time()),
            'order_action'=>3
        );
        $res_create_order = OrderModel::create($data);//创建订单
        if ($res_create_order) {

            $aop = new AopClient;
            $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
            $aop->appId = env('ALIPAYAPPID');
            $aop->rsaPrivateKey = env('ALIPAYPRIVATEKEY');
            $aop->format = "json";
            $aop->charset = "UTF-8";
            $aop->signType = "RSA2";
            $aop->alipayrsaPublicKey = env('ALIPAYPUBLICKEY');
            //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
            $request_alipay = new AlipayTradeAppPayRequest();
            //SDK已经封装掉了公共参数，这里只需要传入业务参数


            $bizcontent = [
                'body' => '易装平台支付-账户充值' . $data['code'],//签名类型
                "subject" => '易装平台支付-账户充值',
                "out_trade_no" => $data['code'],
                "timeout_express" => "30m",
                "seller_id" => "",
                "total_amount" => $total_amount,
                "product_code" => "QUICK_MSECURITY_PAY",
            ];
            $bizcontent = json_encode($bizcontent, true);
            $request_alipay->setNotifyUrl("https://yizhuang.grandway020.com/api/checkSignAliPay");
            $request_alipay->setBizContent($bizcontent);

            //这里和普通的接口调用不同，使用的是sdkExecute
            $response = $aop->sdkExecute($request_alipay);
            //htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
            return $this->success(['orderString'=>($response)]);//就是orderString 可以直接给客户端请求，无需再做处理。
        } else {
            return $this->error('创建订单失败');
        }

    }


    /**
     * 用户充值
     */
    public function postCashUser(Request $request) {

        $user_id = $request->json('user_id');
        $cash    = $request->json('cash');

        $user = UserDetailModel::where('uid', $user_id)->first();

        if (empty($user) || empty(UserModel::find($user_id))) return $this->error('找不到该用户,无法充值');

        $config       = ConfigModel::getConfigByAlias('cash');
        $config->rule = json_decode($config->rule, true);

        /*        if ($cash < $config->rule['recharge_min']) {
                    return \CommonClass::formatResponse('充值金额不得小于' . $config->rule['recharge_min'] . '元', 201);
                }*/
        $data = array(
            'code' => OrderModel::randomCode($user_id),
            'title' => '余额充值',
            'cash' => $cash,
            'uid' => $user_id,
            'task_id' => 0,
            'created_at' => date('Y-m-d H:i:s', time()),
            'order_action'=>3
        );

        $params = [
            'appid' => env('APPID'),//应用APPID
            'mch_id' => env('MCH_ID'),//商户号
            'device_info' => 'WEB',//终端设备号(门店号或收银设备ID)，默认请传"WEB"
            'nonce_str' => $this->generatePassword(20),//随机字符串
            'body' => '易装平台支付-账户充值' . $data['code'],//签名类型
            'out_trade_no' => $data['code'],//商户订单号
            'total_fee' => $cash * 100,//金额
            'spbill_create_ip' => $request->getClientIp(),//终端IP
            'notify_url' => 'https://yizhuang.grandway020.com/api/checkSign',//通知地址
            'trade_type' => 'APP',//交易类型
            'attach' => $user_id . '_' . $data['code'],//附加数据(uid和账单id)
        ];
        foreach ($params as $k => $v) {
            $params[$k] = (string)$v;
        }
        $params['total_fee'] = (int)$params['total_fee'];

        $sign           = $this->createSign($params);//签名
        $params['sign'] = $sign;

        $params           = $this->arrayToXml($params);
        $res_create_order = OrderModel::create($data);//创建订单

        if ($res_create_order) {

            $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';//接收XML地址
            $ch  = curl_init(); //初始化curl

            curl_setopt($ch, CURLOPT_URL, $url);//设置链接

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

            curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);//POST数据

            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

            $response = curl_exec($ch);//接收返回信息
            if (curl_errno($ch)) {//出错则显示错误信息
                return $this->error('微信数据获取失败-', curl_error($ch));
            }
            curl_close($ch); //关闭curl链接
            $data_response = $this->xmlToArray($response);
            $params_other = [
                'appid' => env('APPID'),
                'noncestr' => $this->generatePassword(20),//随机字符串
                'package' => 'Sign=WXPay',
                'partnerid' => env('MCH_ID'),//商户号
                'timestamp' => time(),
                'prepayid' => $data_response['prepay_id'],//预支付订单编号
            ];

            $sign_two             = $this->createSignOther($params_other);
            $params_other['sign'] = $sign_two;
            return $this->success($params_other);//显示返回信息

        } else {
            return $this->error('创建订单失败');
        }


    }




    /**
     * 生成签名
     */
    public function createSign($params) {

        ksort($params);
        // 分隔符
        $signSrc = "";
        // md5密钥（KEY）
        $md5key = env('PAY_PRIKEY');
        //按照签名规则组织签名，按顺序排列
        foreach ($params as $k => $v)
        {
            if($k != "sign"){
                $signSrc .= $k . "=" . $v . "&";
            }
        }
        $singInfo = $signSrc . 'key=' . $md5key;
        $sign     = strtoupper(MD5($singInfo));

        return $sign;
    }

    /**
     * 生成二次签名
     */
    public function createSignOther($params) {

        ksort($params);
        // 分隔符
        $signSrc = "";
        // md5密钥（KEY）
        $md5key = env('PAY_PRIKEY');
        //按照签名规则组织签名，按顺序排列
        foreach ($params as $k => $v)
        {
            if($k != "sign"){
                $signSrc .= $k . "=" . $v . "&";
            }
        }
        $singInfo = $signSrc . 'key=' . $md5key;
        $sign     = strtoupper(MD5($singInfo));

        return $sign;
    }

    /**
     * @param $xml
     * @return mixed
     * 将XML转为array
     */
    public function xmlToArray($xml) {
        libxml_disable_entity_loader(true);
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }

    /**
     * @param $xml
     * @return mixed
     * 将XML转为array
     */
    public function xmlToArrayOther($xml) {
        libxml_disable_entity_loader(true);
        $arra_str   = json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA));
        $array_data = json_decode(htmlspecialchars_decode($arra_str), true);
        return $array_data;
    }

    /**
     * 校验三方签名数据,订单入库
     * @param $params
     */
    public function checkSign() {
        $data         = file_get_contents("php://input");
        $new_data     = $this->xmlToArrayOther($data);
        $return_code  = $new_data['return_code'];//返回状态码
        $out_trade_no = $new_data['out_trade_no'];//商户订单号
        $total_fee    = $new_data['total_fee'] / 100;//订单总金额
        $attach       = $new_data['attach'];//附加数据
        if (!$return_code && !$out_trade_no && !$total_fee && !$attach) return 'FAIL';
        $uid = explode('_', $attach)[0];//用户id

        //创建财务订单
        $data_financial = [
            'action' => 3,
            'pay_type' => 3,
            'task_id' => 0,
            'cash' => $total_fee,
            'pay_code' => $out_trade_no,
            'created_at' => date('Y-m-d H:i:s', time()),
            'uid' => $uid
        ];

        //校验之后.用户余额增加,订单状态改变
        $order_no = OrderModel::where('uid', $uid)->where('code', $out_trade_no)->first();
        if ($return_code == 'SUCCESS' && !empty($order_no)) {
            $user = UserDetailModel::where('uid', $uid)->first();
            $user->balance += $total_fee;//余额增加
            $res_user          = $user->save();
            $order_no->status  = 1;
            $order_no->task_id = 0;
            $res_order_status  = $order_no->save();
            FinancialModel::create($data_financial);
        }else{
            if(!empty($order_no)){
                $order_no->status  = 2;
                $order_no->task_id = 0;
                $order_no->save();
            }
        }

        if ($res_user && isset($res_order_status) && $res_order_status) {
            $result =  ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
            echo $this->array2xml($result);
            exit;
        } else {
            $result = ['return_code' => 'FAIL', 'return_msg' => 'FAIL'];
            echo $this->array2xml($result);
            exit;
        }
    }


    /**
     * 校验三方签名数据,订单入库(支付宝)
     * @param $params
     */
    public function checkSignAliPay(Request $request) {

        $return_code           = $request->trade_status;//返回状态码
        $out_trade_no          = $request->out_trade_no;//商户订单号
        $total_fee             = $request->total_amount;//订单总金额
        $app_id_alipay_request = $request->auth_app_id;//订单总金额
        $app_id_alipay         = env('ALIPAYAPPID');
        //校验之后.用户余额增加,订单状态改变
        $order_no = OrderModel::where('code', $out_trade_no)->where('cash', $total_fee)->first();

        //创建财务订单
        $data_financial = [
            'action' => 3,
            'pay_type' => 3,
            'task_id' => 0,
            'cash' => $total_fee,
            'pay_code' => $out_trade_no,
            'created_at' => date('Y-m-d H:i:s', time()),
            'uid' => $order_no->uid
        ];

        if ($return_code == 'TRADE_SUCCESS' && !empty($order_no) && $app_id_alipay_request == $app_id_alipay) {
            $user = UserDetailModel::where('uid', $order_no->uid)->first();
            $user->balance += $total_fee;//余额增加
            $res_user          = $user->save();
            $order_no->status  = 1;
            $order_no->task_id = 0;
            $res_order_status  = $order_no->save();
            FinancialModel::create($data_financial);
            return 'success';
        } else {
            if(!empty($order_no)){
                $order_no->status  = 2;
                $order_no->task_id = 0;
                $res_order_status  = $order_no->save();
            }
            return 'error';
        }
    }



    function array2xml($arr, $level = 1) {
        $s = $level == 1 ? "<xml\>" : '';
        foreach ($arr as $tagname => $value) {
            if (is_numeric($tagname)) {
                $tagname = $value['TagName'];
                unset($value['TagName']);
            }
            if (!is_array($value)) {
                $s .= "<{$tagname}>" . (!is_numeric($value) ? '<![CDATA[' : '') . $value . (!is_numeric($value) ? ']]>' : '') . "</{$tagname}>";
            } else {
                $s .= "<{$tagname}>" . array2xml($value, $level + 1) . "</{$tagname}>";
            }
        }
        $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
        return $level == 1 ? $s . "</xml>" : $s;
    }

    /**
     * 生成随机字符串
     * @param int $limit
     * @return string
     */
    public function generatePassword($limit = 7) {
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        srand((double)microtime() * 1000000);
        $rand = "";
        for ($i = 0; $i < $limit; $i++) {
            $rand .= $c[rand() % strlen($c)];
        }
        return $rand;
    }

    /**
     * 获取管家刚提交的整改单
     */
    public function getLatestChangeListOfProject(Request $request) {

        $task_id = $request->get('task_id');
        $sn      = $request->get('sn');

        $user_type        = TaskModel::find($task_id)->user_type;
        $project_position = TaskModel::find($task_id)->project_position;

        //监理查看管家
        if ($user_type == 4) {
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $house_keeper_task->id;
        }

        $latest_submit_info = ProjectLaborChange::where('task_id', $task_id)->where('status', 2)->where('is_confirm', 0)->where('sn', $sn)->first();//管家刚提交的整改单


        if (empty($latest_submit_info)) return $this->error('无法找到该管家刚提交的整改单');
        $list_detail = unserialize($latest_submit_info->list_detail);
        return $this->success($list_detail);
    }




    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 该项目的历史更换工人的记录
     */
    public function getLogOfChangeLabor(Request $request) {

        $task_id    = $request->get('task_id');
        $labors_arr = ProjectConfigureChangeModel::select('old_labor', 'new_labor', 'project_type_id as project_type', 'updated_at', 'id')
            ->where('task_id', $task_id)
            ->where('is_sure', 1)
            ->distinct('id')->get();

        if ($labors_arr->isEmpty()) return $this->error('该任务暂时未更换过工人');

        $new_data = [];
        foreach ($labors_arr->toArray() as $k => $v) {
            if ($v['project_type'] == 2) {//水电阶段
                $v['old_labor'] = array_values(explode('-', $v['old_labor']));
                $v['new_labor'] = array_values(explode('-', $v['new_labor']));

                $bing = array_intersect($v['old_labor'], $v['new_labor']);//并集

                if (empty($bing)) {

                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'][0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'][1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'][0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'][1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];

                } else {
                    $he = array_merge($v['old_labor'], $v['new_labor']);//合并两个数组
                    foreach ($he as $n => $m) {
                        if ($m == implode($bing)) {
                            unset($he[$n]);
                        }
                    }
                    $he = array_values($he);

                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $he[0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $he[1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];
                }

            } else {
                $new_data['parent_' . $v['project_type']][$v['id']]['old_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'])->first()->toArray();
                $new_data['parent_' . $v['project_type']][$v['id']]['new_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'])->first()->toArray();
                $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];
            }

        }
        foreach ($new_data as $o => $p) {
            $new_data[$o] = array_values($p);
        }
        return $this->success($new_data);
    }



    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 该项目的历史更换工人的记录（安卓端使用）
     */
    public function getLogOfChangeLaborAndroid(Request $request) {

        $task_id    = $request->get('task_id');
        $labors_arr = ProjectConfigureChangeModel::select('old_labor', 'new_labor', 'project_type_id as project_type', 'updated_at', 'id')
            ->where('task_id', $task_id)
            ->where('is_sure', 1)
            ->distinct('id')->get();

        if ($labors_arr->isEmpty()) return $this->error('该任务暂时未更换过工人');

        $new_data = [];
        foreach ($labors_arr->toArray() as $k => $v) {
            if ($v['project_type'] == 2) {//水电阶段
                $v['old_labor'] = array_values(explode('-', $v['old_labor']));
                $v['new_labor'] = array_values(explode('-', $v['new_labor']));

                $bing = array_intersect($v['old_labor'], $v['new_labor']);//并集

                if (empty($bing)) {

                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'][0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'][1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'][0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor'][] = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'][1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];

                } else {
                    $he = array_merge($v['old_labor'], $v['new_labor']);//合并两个数组
                    foreach ($he as $n => $m) {
                        if ($m == implode($bing)) {
                            unset($he[$n]);
                        }
                    }
                    $he = array_values($he);

                    $new_data['parent_' . $v['project_type']][$v['id']]['old_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $he[0])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['new_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $he[1])->first()->toArray();
                    $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];
                }

            } else {
                $new_data['parent_' . $v['project_type']][$v['id']]['old_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['old_labor'])->first()->toArray();
                $new_data['parent_' . $v['project_type']][$v['id']]['new_labor']   = UserDetailModel::select('star', 'realname', 'uid')->where('uid', $v['new_labor'])->first()->toArray();
                $new_data['parent_' . $v['project_type']][$v['id']]['modify_time'] = $v['updated_at'];
            }

        }
        foreach ($new_data as $o => $p) {
            $new_data[$o] = array_values($p);
        }

        for($i=1;$i<=7;$i++){
            $parentIndex = 'parent_'.$i;
            if(!isset($new_data[$parentIndex])){
                $new_data[$parentIndex] = [];
            }
        }
        ksort($new_data);

        return $this->success($new_data);
    }



    /**
     * 查看每一次的整改单数据(和原数据的对比)(已确认的)
     */
    public function getChangeListLogOfProject(Request $request) {

        $task_id     = $request->get('task_id');
        $change_task = ProjectConfigureChangeModel::where('task_id', $task_id)->where('is_sure', 1)->get();//拿到整改单(会有多个,需要循环判断)
        $origin_task = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();//配置单

        if ($change_task->isEmpty()) return $this->error('无法找到整改单');
        if (empty($origin_task)) return $this->error('无法找到配置单');

        $origin_list = unserialize($origin_task->project_con_list);//原始单
        $change_list = [];
        foreach ($change_task as $k => $v) {

            $change_list = unserialize($v->list_changes);

        }

        $compare_data = [];
        foreach ($origin_list as $n => $m) {
            foreach ($change_list as $o => $p) {
                if ($o == $n && $n != 'all_parent_price' && $o != 'all_parent_price') {
                    $compare_data['origin_' . $n] = $m;
                    $compare_data['change_' . $o] = $p;
                }
            }
        }

        return $this->success($compare_data);
    }



    /**
     * 查看每一次的整改单数据(和原数据的对比)(未确认的)
     */

    public function getChangeListNowOfProject(Request $request) {

        $task_id          = $request->get('task_id');
        $sn               = $request->get('sn');
        $user_type        = TaskModel::find($task_id)->user_type;
        $project_position = TaskModel::find($task_id)->project_position;
        //监理查看管家
        if ($user_type == 4) {
            $house_keeper_task = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
            $task_id           = $house_keeper_task->id;
        }

        $change_task = ProjectLaborChange::where('task_id', $task_id)->where('is_confirm', 0)->where('sn', $sn)->first();//拿到整改单
        $origin_task = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();//配置单

        if (empty($change_task)) return $this->error('无法找到整改单');
        if (empty($origin_task)) return $this->error('无法找到配置单');

        $origin_list = unserialize($origin_task->project_con_list);//原始单

        $change_list = unserialize($change_task->list_detail);
 
        $compare_data = [];
        foreach ($origin_list as $n => $m) {

            foreach ($change_list as $o => $p) {
                if ($o == $n && $n != 'all_parent_price' && $o != 'all_parent_price') {
                    $compare_data['origin_' . $n] = $m;
                    $compare_data['change_' . $o] = $p;
                }
            }
        }

        return $this->success($compare_data);
    }

    /**
     * 对比管家和设计师提交的工程配置单
     */
    public function compareDesignerAndHouserKeeperList(Request $request) {
        $task_id          = $request->get('task_id');
        $project_position = TaskModel::find($task_id)->project_position;
        $designer_task    = TaskModel::where('project_position', $project_position)->where('user_type', 2)->where('status', '>=', 3)->where('status','<',9)->first();
        if (empty($designer_task)) return $this->error('无法找到设计师的任务');
        $designer_list     = ProjectConfigureTask::where('task_id', $designer_task->id)->first();
        $house_keeper_list = ProjectConfigureTask::where('task_id', $task_id)->orderBy('id', 'desc')->first();

        if (empty($designer_list) || empty($house_keeper_list)) return $this->error('无法找到设计师或管家的配置单');

        $designer_con_list     = unserialize($designer_list->project_con_list);
        $designer_con_list     = create_configure_lists($designer_con_list);
        $house_keeper_con_list = unserialize($house_keeper_list->project_con_list);
        $data = [
            'designer_con_list' => $designer_con_list,
            'house_keeper_con_list' => $house_keeper_con_list,
        ];
        return $this->success($data);

    }
    
    /**
     * 工人端可查看订单要做的工程项目和工程量
     */
    public function laborWorkOfferInfo(Request $request) {

        $task_id   = $request->get('task_id');
        $to_uid    = $request->get('to_uid');
        $work_list = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();//找出原始配置单
        if (empty($work_list)) return $this->error('无法找到该任务');
        //拿到所有工人
        $to_uids = WorkOfferModel::where('task_id', $task_id)->where('project_type', '>', 0)->lists('to_uid');
        if ($to_uids->isEmpty()) return $this->error('该任务还未分配工人');
        $to_uids_str        = implode(',', $to_uids->toArray());
        $task_require_labor = strpos($to_uids_str, (string)$to_uid);

        if (empty($task_require_labor)) return $this->error('您不在该项目中');

        $work_offer_labor = WorkOfferModel::where('task_id', $task_id)->where('to_uid', $to_uid)->orWhere('to_uid', 'like', '%' . $to_uid . '-' . '%')->orWhere('to_uid', 'like', '%' . '-' . $to_uid . '%')->first();//找出该阶段

        $project_type = $work_offer_labor->project_type;
        //先找整改单有没有这个工人
        $list_change = ProjectConfigureChangeModel::where('task_id', $task_id)->where('new_labor', $to_uid)->orWhere('new_labor', 'like', '%' . $to_uid . '-' . '%')->orWhere('new_labor', 'like', '%' . '-' . $to_uid . '%')->where('project_type_id', $project_type)->where('is_sure', 1)->first();

        $task_should_do = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();

        $project_con_list_task   = unserialize($task_should_do->project_con_list);
        $project_con_list_change = unserialize($list_change->list_changes);


        $should_do_data = [];
        if (!empty($project_con_list_change)) {
            foreach ($project_con_list_change as $k => $v) {
                if ($k == 'parent_' . $project_type) {
                    $should_do_data = $v;
                }
            }
        } else {
            foreach ($project_con_list_task as $k => $v) {
                if ($k == 'parent_' . $project_type) {
                    $should_do_data = $v;
                }
            }
        }
        return $this->success($should_do_data);


    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 结束管家的工程
     */

    public function endProject(Request $request) {
        $task_id   = $request->post('task_id');
        $task_info = TaskModel::find($task_id);
        if (empty($task_info)) return $this->error('无法找到该订单');
        $task_info->status = 9;
        if ($task_info->save()) {
            $user = UserDetailModel::where('uid', $task_info->uid)->first();
            $user->balance += $task_info->pretty_cash;
            $task_info->pretty_cash = 0;
            if ($user->save() && $task_info->save()) return $this->error('工程保证金余额已付,订单完结',0);
        } else {
            return $this->error('结束订单失败');
        }
    }



    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 获取施工中的任务列表
     */
    public function getUnderConstructionTask(Request $request) {
        $housekeeper_id = $request->get('housekeeper_id');

        $choseWorkList = TaskModel::select(
            'work.task_id', 'work_offer.status', 'work.uid', 'task.created_at', 'task.room_config', 'task.square',
            'task.show_cash', 'task.status as node', 'project_position.lat', 'cate.name as favourite_style',
            'project_position.lng', 'work_offer.sn', 'user_detail.avatar', 'user_detail.nickname', 'users.name', 'work_offer.project_type'
        )
            ->leftjoin('work', 'work.task_id', '=', 'task.id')
            ->where('work.uid', $housekeeper_id)
            ->where('task.status', 7)
            ->leftjoin('work_offer', 'work_offer.task_id', '=', 'task.id')
//                                  ->where('work_offer.sn',3)
//                                  ->where('work_offer.status',4)
            ->leftjoin('project_position', 'project_position.id', '=', 'task.project_position')
            ->leftjoin('users', 'users.id', '=', 'task.uid')
            ->leftjoin('user_detail', 'user_detail.uid', '=', 'task.id')
            ->leftjoin('cate', 'cate.id', '=', 'task.favourite_style')
            ->get()->toArray();

        $is_have_dismantle = 0;
        foreach ($choseWorkList as $n => $m) {
            if ($m['project_type'] == 1) {
                $is_have_dismantle = 1;
            }
        }
        foreach ($choseWorkList as $key => $value) {

            $choseWorkList[$key]['avatar'] = !empty($value['avatar']) ? url($value['avatar']) : '';
            $value['is_owner']             = 1;
            if (empty($value['nickname'])) {
                $choseWorkList[$key]['nickname'] = $value['name'];
            }
            unset($choseWorkList[$key]['name']);
            if ($value['status'] == 0) {
                unset($choseWorkList[$key]);
            }
        }
        if (empty($choseWorkList)) {
            $last_work_offer = [];
        } else {
            $last_work_offer                      = array_values($choseWorkList)[count($choseWorkList) - 1];
            $last_work_offer['is_have_dismantle'] = $is_have_dismantle;
        }

        return $this->success($last_work_offer);
    }


    /**
     * 所有用户的我的直播列表
     */
    public function broadcastList(Request $request) {
        $uid           = $request->get('user_id');
        $broadcastList = $this->userRespository->broadcastList($uid);
        if ($broadcastList['status']) {
            $task_info_boss = $broadcastList['successMsg'];
            return $this->success($task_info_boss);
        } else {
            return $this->error($broadcastList['errMsg']);
        }
    }

    /**
     * 所有用户的我的直播列表
     */
    public function broadcastListExclude(Request $request) {
        $uid           = $request->get('user_id');
        $broadcastList = $this->userRespository->broadcastListExclude($uid);
        if ($broadcastList['status']) {
            $task_info_boss = $broadcastList['successMsg'];
            return $this->success($task_info_boss);
        } else {
            return $this->error($broadcastList['errMsg']);
        }
    }

    /**
     * 获取账单(可申请提现)
     */
    public function getBillOfTaskWithdraw(Request $request) {

        $uid_boss = $request->get('uid_boss');

        $data_all = SubOrderModel::select('order_code','id as sub_order_id', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'fund_state','withdraw_status')
            ->where('cash', '>', 0)
            ->where('withdraw_status',0)
            ->where('status',1)
            ->where('uid', $uid_boss)
            ->orderBy('created_at', 'desc')->get();


        //查找用户的充值
        $detail_recharge = OrderModel::select('cash', 'code as order_code', 'title', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)
            ->where('uid', $uid_boss)->orderBy('created_at', 'desc')->get();

        if ($data_all->isEmpty()){
            return $this->error();
        }
        $new_data = [];
        foreach ($data_all->toArray() as $k => $v) {
            $cash_info                             = CashoutModel::select('status')->where('task_id', $v['task_id'])->where('sn', $v['project_type'])->first();
            $status_cash_info = empty($cash_info) ? 0 : $cash_info->status;
            $v['cash_info_status']                 = cash_out_status($status_cash_info);
            $v['cash']                             = $v['cash'];
            $wai_time                              = date('Y-m-d', strtotime($v['created_at']));
            $new_data[$wai_time]['time']           = $wai_time;
            $v['created_at']                       = date('H:i:s', strtotime($v['created_at']));
            $new_data[$wai_time]['Bill_Content'][] = $v;
        }
        $all_data = array_values($new_data);
        return $this->success(($all_data));
    }

    /**
     * 获取账单(不可申请提现)
     */
    public function getBillOfTaskWithdrawDeny(Request $request) {

        $uid_boss = $request->get('uid_boss');
        $data_all = SubOrderModel::select('id as sub_order_id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'fund_state', 'withdraw_status')
            ->where('cash', '>', 0)
            ->where('withdraw_status', 1)
            ->where('uid', $uid_boss)
            ->orderBy('created_at', 'desc')->get();


        //查找用户的充值
        $detail_recharge = OrderModel::select('cash', 'code as order_code', 'title', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)
            ->where('uid', $uid_boss)->orderBy('created_at', 'desc')->get();

        if ($data_all->isEmpty()) return $this->error('找不到该订单数据');
        $new_data = [];

        foreach ($data_all->toArray() as $k => $v) {
            $cash_info                             = CashoutModel::select('status')->where('sub_order_index_id', $v['sub_order_id'])->first();
            $status_cash_info = empty($cash_info) ? 0 : $cash_info->status;
            $v['cash_info_status']                 = cash_out_status($status_cash_info);
            $v['cash']                             = $v['cash'];
            $wai_time                              = date('Y-m-d', strtotime($v['created_at']));
            $new_data[$wai_time]['time']           = $wai_time;
            $v['created_at']                       = date('H:i:s', strtotime($v['created_at']));
            $new_data[$wai_time]['Bill_Content'][] = $v;
        }

        $all_data = array_values($new_data);
        return $this->success($all_data);
    }


    /**
     * 提现申请
     * 0 待审核 1 待验证 2 认证通过 3 认证失败 4 禁用
     */

    public function withdrawApplication(Request $request) {
        $uid          = $request->json('user_id');//拿到uid
        $sub_order_id = $request->json('sub_order_id');
        //多个主id
        $user_work_info= UserModel::find($uid);
        if(empty($user_work_info)) return $this->error('找不到您的信息');
        foreach ($sub_order_id as $o => $p) {
            $sub_order_data = SubOrderModel::find($p);
            if (empty($sub_order_data)) return $this->error('找不到记录,非法提交');
            if ($sub_order_data->withdraw_status == 1) return $this->error('您已申请过提现');
            if ($uid != $sub_order_data->uid) return $this->error('提现人和数据库中不一样,非法提交');

            $apply_balance = $sub_order_data->cash;//拿到提现金额
            $sn            = $sub_order_data->project_type;//哪个阶段
            $task_id       = $sub_order_data->task_id;//哪个任务
            $cashout_type  = 2;

            $user_info = UserDetailModel::where('uid', $uid)->first();
            if (empty($user_info)) return $this->error('找不到用户');

            $balance   = $user_info->balance;//找到余额
            $work_info = WorkModel::select('price','status')->where('task_id', $task_id)->where('status', '>', 0)->first();

            $work_offer_data = WorkOfferModel::select('price')->where('task_id', $task_id)->where('sn',0)->where('status',4)->first();
            $work_offer_labor_data = WorkOfferModel::select('price')->where('task_id', $task_id)->where('sn',1)->where('status',4)->first();
            if (empty($work_offer_data)) return $this->error('找不到总价');


            if (in_array($user_work_info->user_type, [2, 3, 4])) {
                $total_pay_task = $work_offer_data->price;//总价(监理,设计师,管家对应第一阶段的报价,工人对应第二阶段的报价)
            } else {
                $total_pay_task = $work_offer_labor_data->price;
            }

            $pay_code       = $sub_order_data->order_code;
            //提现账号
            $bank_info = BankAuthModel::where('uid', $uid)->first();
            if (empty($bank_info)) return $this->error('找不到提现账户');

            $cashout_account = $bank_info->bank_account;
            if (empty($cashout_account)) return $this->error('找不到提现银行卡');

            $taskDetail = TaskModel::find($task_id);
            if (empty($taskDetail)) return $this->error('找不到任务');

            //工地
            $project_position_id   = $taskDetail->project_position;
            $project_position_info = ProjectPositionModel::find($project_position_id);
            if (empty($project_position_info)) return $this->error('找不到工地信息');

            $users_info = UserDetailModel::select('user_detail.nickname', 'user_detail.realname', 'users.name', 'users.user_type')
                ->where('user_detail.uid', $uid)
                ->leftJoin('users', 'users.id', '=', 'user_detail.uid')->first();
            if (empty($users_info)) return $this->error('找不到您的个人资料,无法保存该申请');


            // TODO 这里订单结束后不可以提现？
            if($users_info->user_type == 4){
                $supervisor_task_data = TaskModel::where('id', $task_id)->first();
//                $supervisor_task_data = TaskModel::where('status', '<', 9)->where('id', $task_id)->first();

                if (empty($supervisor_task_data)) return $this->error('找不到对应的订单');
                $project_ppsition  = $supervisor_task_data->project_position;
//                $house_keeper_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<', 9)->where('user_type', 3)->first();
                if($supervisor_task_data->status < 9){
                    $house_keeper_task = TaskModel::where('unique_code', $supervisor_task_data->unique_code)->where('user_type', 3)->where('status', '<', 9)->first();
                }else{
                    $house_keeper_task = TaskModel::where('unique_code', $supervisor_task_data->unique_code)->where('user_type', 3)->where('end_order_status', 1)->first();
                }

                if (empty($house_keeper_task)) return $this->error('找不到对应的管家订单');

                $work_offer_detail = WorkOfferModel::where('task_id', $house_keeper_task->id)->where('sn', $sn)->first();

            }else{
                $work_offer_detail = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
            }

//            $work_offer_detail = WorkOfferModel::where('task_id', $task_id)->where('sn', $sn)->first();
            if (empty($work_offer_detail)) return $this->error('找不到申请提现的工地阶段');

            $boss_uid  = $taskDetail->uid;
            $boss_info = UserModel::find($boss_uid);
            if (empty($boss_info)) return $this->error('找不到您服务的业主资料');

            $boss_phone_num = $boss_info->name;

            //找到进行到哪一步了
            $work_offer_status = [];
            if ($taskDetail['status'] == 7) {

                $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'title', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $task_id)
                    ->orderBy('sn', 'ASC')
                    ->get()->toArray();

                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                }
                $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
            }

            //没有work_offer状态,取work的状态
            if (empty($work_offer_status)) {
                $work_status = work_status($work_info['status']);
            } else {
                $work_status = $last_work_offer_status['title'] . work_offer_status($last_work_offer_status['status']);
            }

            //工地地址
            $position_address = $project_position_info->region . $project_position_info->project_position;
            $title            = $work_offer_detail->title;//拿到阶段名称

            if($users_info->user_type == 4){
                $title = $sub_order_data->title;
            }

            if($users_info->user_type == 3){

                $title = str_replace('工作者报价流程', '', $sub_order_data->title);
                $title = str_replace('手续费', '款', $title);

                if($sub_order_data['is_auxiliary'] == 1){
                    $title = $title;
                }else{
                    $title = get_project_type($sub_order_data->project_type).'通过'.$title;
                }

            }

            if($users_info->user_type == 5){
                $title = $sub_order_data->title;
            }

//            TODO by HUI 挖坑了，直接判断是否包含某个字符串来判断用那个阶段名称，因为提现的费用说明不想大改动
            if(strpos($sub_order_data->title,'变更单收入')){
                $title = $sub_order_data->title;
            }

            if($sub_order_data->is_auxiliary === 0){
                if ($apply_balance > $balance){
                    return $this->error('您申请提现的金额大于账户余额');
                }
            }else{
                $auxiliaryOrder = SubOrderModel::where('task_id',$task_id)->where('uid',255)->where('title','辅材价格')->first();
                if ($apply_balance > $auxiliaryOrder->cash){
                    return $this->error('你申请的提现金额大于辅材包总价');
                }
            }


            $data = [
                'uid' => $uid,
                'task_id' => $task_id,
                'pay_code' => $pay_code,
                'total_pay_task' => $total_pay_task,
                'cashout_type' => $cashout_type,
                'cashout_account' => $cashout_account,
                'sn' => $sn,
                'new_order' => createGoodsNo(),
                'cash' => $apply_balance,
                'status' => 1,
                'work_offer_status_name' => $work_status,
                'position_address' => $position_address,
                'sn_title' => $title,
                'boss_phone_num' => $boss_phone_num,
                'worker_phone_num' => $users_info['name'],
                'worker_name' => $users_info['realname'],
                'user_type_name' => get_user_type_name($users_info['user_type']),
                'bank_name' => empty($bank_info->deposit_name) ? '尚未填写' : $bank_info->deposit_name,
                'sub_order_index_id' => $p
            ];

            $status = DB::transaction(function () use ($data, $apply_balance,$p,$sub_order_data) {
                SubOrderModel::where('id', $p)->update(['withdraw_status' => 1]);
                CashoutModel::create($data);
                if($sub_order_data['is_auxiliary'] === 1){
                    UserDetailModel::where('uid', 255)->decrement('balance', $apply_balance);
                }else{
                    UserDetailModel::where('uid', $data['uid'])->decrement('balance', $apply_balance);
                }
            });
        }


        if (is_null($status))
            return $this->error('提现申请已提交',0);//创建提现申请
        else
            return $this->error('提现申请失败');
    }

    /**
     * 给最终结果给工作者
     */
    public function getFinalCashOutResult(Request $request) {
        $sub_order_id = $request->get('sub_order_id');
        $cash_info    = CashoutModel::where('sub_order_index_id', $sub_order_id)->first();
        if (empty($cash_info)) return $this->error();
        if (!empty($cash_info->fees * 100)) {
            $real_fees = $cash_info->fees / 100;
        } else {
            $real_fees = 0;
        }
        $status_cash_info = empty($cash_info) ? 0 : $cash_info->status;
        $data             = [
            'cash_out_id' => $cash_info->new_order,
            'pay_code' => $cash_info->pay_code,
            'cash' => $cash_info->cash,
            'privilege_amount_sn' => $cash_info->privilege_amount_sn,
            'status' => cash_out_status($status_cash_info),
            'real_cash' => empty($cash_info->real_cash) ? '' : $cash_info->real_cash,//实付
            'bank_fee' => ($cash_info->cash-$cash_info->privilege_amount_sn) * $real_fees,//银行手续费
            'current_status' => $status_cash_info,
        ];
        return $this->success($data);
    }



    /**
     * 审核通过与不通过
     */
    public function withdrawAudit(Request $request) {
        $id         = $request->json('id');
        $status     = empty($request->json('status')) ? '3' : $request->json('status');//默认为不通过
        $audit_info = BankAuthModel::find($id);
        if (empty($audit_info)) return $this->error('找不到该提现申请');
        $audit_info->status = $status;
        if ($audit_info->save()) return $this->error('操作成功',0);//创建提现申请
        return $this->error('操作失败');
    }

    /**
     * 已确认打款接口
     * ($uid, $money, $task_id, $title = '发布任务', $fund_state = 1, $status = 1, $project_type = 0
     */

    public function confirmWithdraw(Request $request) {
        $id         = $request->json('id');
        $audit_info = BankAuthModel::find($id);
        if (empty($audit_info)) return $this->error('找不到该提现申请');
        $audit_info->status = 5;//5代表确认打款
        $user_info          = UserDetailModel::where('uid', $audit_info->uid)->first();
        $decrement          = $audit_info->pay_to_user_cash;//减少的金额
        $user_info->balance -= $decrement;

        $res_create_order = OrderModel::sepbountyOrder($audit_info->uid, $decrement, 0, '用户提现', 1, 1, 0);
        if ($res_create_order && $user_info->save()) return $this->error('操作成功',0);
        return $this->error('操作失败');
    }





    /************************************************************************************************************
     * 重构的接口
     *********************************************************************************************************/

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据task_id获取管家的订单详细
     */
    public function getHousekeeperTaskDetail(Request $request) {
        $task_id                     = intval($request->get('task_id'));

        $tasks = $this->taskRespository->getHousekeeperTaskDetail($task_id);
        return $this->success($tasks);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 管家客诉通道(管家发起请求)
     */
    public function houseKeeperApply(Request $request) {
        $data['worker'] = $request->json('housekeeper_id');
        $work_offer_id  = $request->json('work_offer_id');
        if (HouseKeeperComplaintModel::where('work_offer_id', $work_offer_id)->first())
            return $this->error('您已申请过,无需再申请',500);
        $work_offer_data = WorkOfferModel::find($work_offer_id);

        $task_id   = $work_offer_data->task_id;
        $task_data = TaskModel::find($task_id);

        $position_data = ProjectPositionModel::find($task_data->project_position);
        if(empty($work_offer_data)||empty($task_data)||empty($position_data))
            return $this->error('无法找到该项目',500);

        $position_name     = $position_data->region . $position_data->project_position;
        $work_offer_title  = $work_offer_data->title;
        $work_offer_status = work_offer_status($work_offer_data->status);
        //根据管家的单找监理的单
        //根据管家的task_id找监理的task_id
        $supervisor_task_info = TaskModel::select('id')->where('project_position', $task_data->project_position)->where('status', '<', 9)->where('user_type', 4)->first();
        $supervisor_uid       = WorkModel::select('uid')->where('task_id', $supervisor_task_info->id)->where('status', '>', 0)->first();
        $data_uid             = ['boss' => $task_data->uid, 'houseKeeper' => $data['worker'], 'supervisor_id' => $supervisor_uid->uid];

        foreach ($data_uid as $k => $v) {
            $all_user[] = UserDetailModel::select('user_detail.realname', 'user_detail.nickname', 'users.name', 'users.user_type')->leftJoin('users', 'users.id', '=', 'user_detail.uid')->where('user_detail.uid', $v)->first()->toArray();
        }

        $data_insert_other = [
            'sn_title' => $work_offer_title . $work_offer_status,
            'task_id' => $task_id,
            'work_offer_id' => $work_offer_id,
            'worker' => $data['worker'],
            'sn' => $work_offer_data->sn,
            'position_name' => $position_name,
        ];
        foreach ($all_user as $k => $v) {
            if ($v['user_type'] == 1) {
                $data_insert_1 = [
                    'boss_name' => empty($v['realname']) ? $v['nickname'] : $v['realname'],
                    'boss_phone_num' => $v['name']
                ];
            }
            if ($v['user_type'] == 3) {
                $data_insert_3 = [
                    'house_name' => empty($v['realname']) ? $v['nickname'] : $v['realname'],
                    'house_phone_num' => $v['name']
                ];
            }

            if ($v['user_type'] == 4) {
                $data_insert_4 = [
                    'visor_name' => empty($v['realname']) ? $v['nickname'] : $v['realname'],
                    'visor_phone_num' => $v['name']
                ];
            }

        }
        $data_insert = array_merge($data_insert_1, $data_insert_3, $data_insert_4, $data_insert_other);

        if (HouseKeeperComplaintModel::create($data_insert))
            return $this->error('发起客诉成功',0);
        else
            return $this->error('发起客诉失败');
    }


    /**
     * @param Request $request
     * 管家详细
     */
    public function houseKeeperDetail(Request $request) {

        $housekeeper_id     = $request->get('housekeeper_id');
        $user_id            = $request->get('user_id');

        $houseKeeper_detail = $this->userRespository->getHouseKeeperAndSuperVisorDetailByid($housekeeper_id, $user_id);

        return $this->success($houseKeeper_detail->toArray());
    }

    /**
     * 保存前端的聊天室数据
     */
    public function saveChatRoomData(Request $request) {

    }


//    public function getlat(Request $request){
//
////        $b=file_get_contents('https://api.map.baidu.com/geocoder/v2/?location=22.557906119228,114.02326865911&output=json&pois=0&ak=ZQEAQICL6vg3MLfqP9yEYz3X');
////        var_dump(\GuzzleHttp\json_decode($b));exit;
//
////        header("Content-Type: text/html; charset=utf-8");
//        $list = UserDetailModel::where('address','!=','')->where('lat',0)->orWhere('lat',1)->limit(500)->get()->toArray();
//        foreach($list as $key => $value){
//
//            if(empty($value['address']))continue;
//            $url = 'https://api.map.baidu.com/geocoder/v2/?address='.$value['city'].$value['address'].'&output=json&ak=ZQEAQICL6vg3MLfqP9yEYz3X';
//            $get = json_decode(file_get_contents($url),true);
//            if($get['status'] == 0){
//                UserDetailModel::where('id',$value['id'])->update(['lat'=>$get['result']['location']['lat'] , 'lng'=>$get['result']['location']['lng']]);
//            }else{
//                echo 1;exit;
//            }
//        }
//    }


}
