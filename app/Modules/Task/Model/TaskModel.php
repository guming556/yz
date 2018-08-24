<?php

namespace App\Modules\Task\Model;

use App\Modules\Employ\Models\EmployUserModel;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Manage\Model\MessageTemplateModel;
use App\Modules\Order\Model\OrderModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\User\Model\MessageReceiveModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Task\Model\TaskCateModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Modules\Task\Model\workDesignerLog;
use Illuminate\Support\Facades\Session;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Order\Model\SubOrderModel;
//use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
//use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
//use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class TaskModel extends Model
{
    protected $table = 'task';
    protected $fillable = [
        'title', 'desc', 'type_id', 'cate_id', 'phone', 'region_limit', 'status', 'bounty', 'bounty_status', 'created_at', 'updated_at',
        'verified_at', 'begin_at', 'end_at', 'delivery_deadline', 'show_cash', 'real_cash', 'deposit_cash', 'province', 'city', 'area',
        'view_count', 'delivery_count', 'uid', 'username', 'worker_num', 'selected_work_at', 'publicity_at', 'checked_at', 'comment_at',
        'top_status','task_success_draw_ratio','task_fail_draw_ratio','engine_status','work_status','poundage','room_config','project_position','square','user_type','favourite_style','task_type','boss_refuse_reason_id','housekeeperStar','workerStar','cancel_order','end_order_status','boss_agree_star','hidden_status','broadcastOrderBy','unique_code'
    ];

    static public function myTasks($data)
    {
        $query = self::select('task.*', 'tt.name as type_name', 'us.name as nickname', 'ud.avatar', 'tc.name as cate_name','province.name as province_name','city.name as city_name')
            ->where('task.status', '>', 1)
            ->where('task.status', '<=', 11)->where('task.uid', $data['uid'])->where('bounty_status',1);
        //状态筛选
        if (isset($data['status']) && $data['status'] != 0) {
            switch ($data['status']) {
                case 1:
                    $status = [3,4,6];
                    break;
                case 2:
                    $status = [5];
                    break;
                case 3:
                    $status = [7];
                    break;
                case 4:
                    $status = [8,9,10];
                    break;
                case 5:
                    $status = [2, 11];
                    break;
            }
            $query->whereIn('task.status', $status);
        }
        //时间段筛选
        if (isset($data['time'])) {
            switch ($data['time']) {
                case 1:
                    $query->whereBetween('task.created_at', [date('Y-m-d H:i:s', strtotime('-1 month')), date('Y-m-d H:i:s', time())]);
                    break;
                case 2:
                    $query->whereBetween('task.created_at', [date('Y-m-d H:i:s', strtotime('-3 month')), date('Y-m-d H:i:s', time())]);
                    break;
                case 3:
                    $query->whereBetween('task.created_at', [date('Y-m-d H:i:s', strtotime('-6 month')), date('Y-m-d H:i:s', time())]);
                    break;
            }

        }

        $data = $query->join('task_type as tt', 'task.type_id', '=', 'tt.id')
            ->leftjoin('district as province','province.id','=','task.province')
            ->leftjoin('district as city','city.id','=','task.city')
            ->leftjoin('users as us', 'us.id', '=', 'task.uid')
            ->leftjoin('user_detail as ud', 'ud.uid', '=', 'task.uid')
            ->leftjoin('cate as tc', 'tc.id', '=', 'task.cate_id')
            ->orderBy('task.created_at','desc')
            ->paginate(5);

        return $data;
    }
    /**
     * 任务筛选
     * @param $data
     * @return mixed
     * author: muker（qq:372980503）
     */
    static function findBy($data)
    {
        // $query = self::select('task.*', 'b.name as type_name', 'us.name as user_name')->where('task.status', '>', 2)
        //     ->where('task.bounty_status', 1)->where('task.status', '<=', 9)->where('begin_at', "<=", date('Y-m-d H:i:s', time()))
        //     ->orderBy('top_status', 'desc');
        $query = self::select('task.*', 'b.name as type_name', 'us.name as user_name')->where('task.status', '>', 2)
            ->where('task.bounty_status', 1)->where('task.status', '<=', 9)
            ->orderBy('top_status', 'desc');
        //关键词筛选
        if (isset($data['keywords'])) {
            $query = $query->where('task.title', 'like', '%' . e($data['keywords']) . '%');
        }
        //类别筛选
        if (isset($data['category']) && $data['category']!=0) {
            //查询所有的底层id
            $category_ids = TaskCateModel::findCateIds($data['category']);
            $query->whereIn('cate_id', $category_ids);
        }
        //地区筛选
        if (isset($data['province'])) {
            $query->where('task.province', intval($data['province']));
        }
        if (isset($data['city'])) {
            $query->where('task.city', intval($data['city']));
        }
        if (isset($data['area'])) {
            $query->where('task.area', intval($data['area']));
        }
        //任务状态
        if (isset($data['status'])) {
            switch ($data['status']) {
                case 1:
                    $status = [4];
                    break;
                case 2:
                    $status = [5];
                    break;
                case 3:
                    $status = [6, 7];
                    break;
                case 4:
                    $status = [8,9];
                    break;
            }
            $query->whereIn('task.status', $status);
        }
        //排序
        if (isset($data['desc']) && $data['desc']!='created_at') {
            $query->orderBy($data['desc'], 'desc');
        }elseif(isset($data['desc']) && $data['desc']=='created_at'){
            $query->orderBy('created_at');
        }else{
            $query->orderBy('created_at','desc');
        }

        $data = $query->join('task_type as b', 'task.type_id', '=', 'b.id')
            ->leftjoin('users as us', 'us.id', '=', 'task.uid')
            ->paginate(10);

        return $data;
    }


// TODO 不确定改了finBy后对后台是否有影响
    static function appFindBy($data) {

        $query = self::select('task.status', 'task.created_at', 'task.type_id', 'ud.avatar', 'ud.nickname', 'task.id as task_id', 'task.square', 'task.room_config', 'c.name as favourite_style', 'task.show_cash', 'p.lat', 'p.lng', 'p.project_position')->where('task.status', '>', 2)
            ->where('task.bounty_status', 1)->where('task.status', '<', 9)
            ->orderBy('top_status', 'desc');

        //关键词筛选
        if (isset($data['keywords'])) {
            $query = $query->where('task.title', 'like', '%' . e($data['keywords']) . '%');
        }
        //类别筛选
        if (isset($data['category']) && $data['category'] != 0) {
            //查询所有的底层id
            $category_ids = TaskCateModel::findCateIds($data['category']);
            $query->whereIn('cate_id', $category_ids);
        }
        //地区筛选
        if (isset($data['province'])) {
            $query->where('task.province', intval($data['province']));
        }
        if (isset($data['city'])) {
            $query->where('task.city', intval($data['city']));
        }
        if (isset($data['area'])) {
            $query->where('task.area', intval($data['area']));
        }
        if (isset($data['user_type'])) {
            $query->where('task.user_type', intval($data['user_type']));
        }
        //任务类型,1抢单,2约单
        if (isset($data['type_id'])) {
            $query->where('task.type_id', intval($data['type_id']));
        }
        //任务状态
        if (isset($data['status'])) {
            switch ($data['status']) {
                case 1:
                    $status = [4];
                    break;
                case 2:
                    $status = [5];
                    break;
                case 3:
                    $status = [6, 7];
                    break;
                case 4:
                    $status = [8, 9];
                    break;
            }
            $query->whereIn('task.status', $status);
        }
        //排序
        if (isset($data['desc']) && $data['desc'] != 'created_at') {
            $query->orderBy($data['desc'], 'desc');
        } elseif (isset($data['desc']) && $data['desc'] == 'created_at') {
            $query->orderBy('created_at');
        } else {
            $query->orderBy('task.created_at', 'desc');
        }
        $data_new = $query->join('task_type as b', 'task.type_id', '=', 'b.id')
            ->leftjoin('users as us', 'us.id', '=', 'task.uid')
            ->leftjoin('user_detail as ud', 'us.id', '=', 'ud.uid')
            ->leftjoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftjoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->get();
        $data_filt = [];
        //看这个设计师有没有接受或拒绝这个任务
        if (isset($data['user_designer'])) {
            //没有选过的人也看不到这个项目(第一次筛选!)
            $first_condition = WorkModel::select('task_id')->where('uid', $data['user_designer'])->get();
            if ($first_condition->isEmpty()) {
                return $first_condition;
            } else {
                foreach ($data_new as $k => $v) {
                    foreach ($first_condition as $n => $m) {
                        if ($v['task_id'] == $m['task_id']) {
                            $data_filt[] = $data_new[$k];
                        }
                    }
                }
            }

            //接受或者拒绝过此订单就不能看见了
            foreach ($data_filt as $k => $v) {
                $operate_task = workDesignerLog::select('task_id', 'new_uid')->where('new_uid', $data['user_designer'])->where('task_id', $v['task_id'])->where('is_refuse', 2)->orWhere('is_refuse', 1)->get();
                //用户已经操作过这个单了(并且操作者是他自己),看不见
                if (!$operate_task->isEmpty()) {
                    foreach ($operate_task as $n => $m) {
                        if (($data['user_designer'] == $m->new_uid) && ($m->task_id == $v['task_id'])) {
                            unset($data_filt[$k]);
                        }
                    }
                }
            }
            //在判断这个单是否业主已选人
            foreach (collect($data_filt) as $k => $v) {
                //找到业主确定的单
                $boss_sure_designer = WorkModel::select('uid')->where('task_id', $v['task_id'])->where('status', '>=', 1)->first();
                //业主确认了并且确认的人是传过来的设计师
                if ($boss_sure_designer && ($data['user_designer'] !== $boss_sure_designer->uid)) {//确认了,确认的人不是该设计师
                    unset($data_filt[$k]);
                }
            }
        }else{
            $data_filt = $data_new;
        }
        return collect($data_filt);
    }


    /**
     * 创建一个任务
     * @param $data
     * @return mixed
     */
    static public function createTask($data) {
        $status = DB::transaction(function () use ($data) {

            $is_have_project = ProjectPositionModel::findLocalId($data);

            if (empty($is_have_project)) {
                return false;
            }
            // TODO
            $data['type_id'] = empty($data['type_id']) ? '1' : $data['type_id'];
            $result          = self::create($data);
            if (!empty($data['product'])) {
                foreach ($data['product'] as $k => $v) {

                    $server = ServiceModel::find($v);
                    if ($server['identify'] == 'ZHIDING') {
                        self::where('id', $result['id'])->update(['top_status' => 1]);
                    }
                    if ($server['identify'] == 'SOUSUOYINGQINGPINGBI') {
                        self::where('id', $result['id'])->update(['engine_status' => 1]);
                    }
                    if ($server['identify'] == 'GAOJIANPINGBI') {
                        self::where('id', $result['id'])->update(['work_status' => 1]);
                    }
                    $service_data = [
                        'task_id' => $result['id'],
                        'service_id' => $v,
                        'created_at' => date('Y-m-d H:i:s', time()),
                    ];
                    TaskServiceModel::create($service_data);
                }
            }

            return $result;
        });
        return $status;
    }

    /**
     * 根据id查询任务
     * @param $id
     */
    static function findById($id)
    {
        $data = self::select('task.*', 'b.name as cate_name', 'c.name as type_name')
            ->where('task.id', '=', $id)
            ->join('cate as b', 'task.cate_id', '=', 'b.id')
            ->leftjoin('task_type as c', 'task.type_id', '=', 'c.id')
            ->first();

        return $data;
    }

    // static function appFindById($id)
    // {
    //     $data = self::select('task.*', 'b.name as cate_name', 'c.name as type_name')
    //         ->where('task.id', '=', $id)
    //         ->join('cate as b', 'task.cate_id', '=', 'b.id')
    //         ->leftjoin('task_type as c', 'task.type_id', '=', 'c.id')
    //         ->first();

    //     return $data;
    // }

    /**
     * 计算用户的任务金额
     */
    public function taskMoney($id)
    {
        $bounty = self::select('task.bounty')->where('id', '=', $id)->first();
        $bounty = $bounty['bounty'];
        $service = TaskServiceModel::select('task_service.service_id')
            ->where('task_id', '=', $id)->get()->toArray();
        $service = array_flatten($service);
        $serviceModel = new ServiceModel();
        $service_money = $serviceModel->serviceMoney($service);
        $money = $bounty + $service_money;

        return $money;
    }
    static function employbounty($money,$task_id,$uid,$code,$type = 2)
    {
        $status = DB::transaction(function () use ($money, $task_id, $uid, $code, $type) {
            //扣除用户的余额
            $query = DB::table('user_detail')->where('uid', '=', $uid);
            $query->where(function ($query) {
                $query->where('balance_status', '!=', 1);
            })->decrement('balance', $money);
            //修改任务的赏金托管状态
            $data = self::where('id', $task_id)->update(['bounty_status' => 1]);
            //生成财务记录，action 1表示发布任务
            $financial = [
                'action' => 1,
                'pay_type' => $type,
                'cash' => $money,
                'uid' => $uid,
                'created_at' => date('Y-m-d H:i:s', time())
            ];
            FinancialModel::create($financial);
            //修改订单状态
            OrderModel::where('code', $code)->update(['status' => 1]);

            //修改用户的托管状态
            self::where('id', '=', $task_id)->update(['status' => 0]);

            //增加用户的发布任务数量
            UserDetailModel::where('uid',$uid)->increment('publish_task_num',1);
        });

        return is_null($status)?true:false;
    }

    /**
     * @param $money 金额
     * @param $task_id 任务id
     * @param $uid 用户id
     * @param $code 订单编号
     * @param int $type 收支行为(1:发布任务 2:接受任务 3:用户充值 4:用户提现 5:购买增值服务 6:购买用户商品 7:任务失败退款)
     * @param int $action 收支行为(1:发布任务 2:接受任务 3:用户充值 4:用户提现 5:购买增值服务 6:购买用户商品 7:任务失败退款)
     * @param bool $use_frozen_money 是否使用冻结金扣款
     * @param bool $changeStatus 是否需要改变task表的status的状态
     * @param bool $is_worker 是否是工人
     * @return bool
     * 生成资金记录,自动扣除金额和入金
     */
    static function bounty($money, $task_id, $uid, $code, $type = 1, $action = 1, $use_frozen_money = false, $changeStatus = false,$is_worker = false) {
        $status = DB::transaction(function () use ($money, $task_id, $uid, $code, $type, $action, $use_frozen_money,$changeStatus,$is_worker) {
            //扣除用户的余额   TODO 用户可用余额
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

            self::where('id', $task_id)->update(['bounty_status' => 1, 'poundage' => $money]);
            //TODO 生成财务记录，action 1表示发布任务
            $financial = [
                'action' => $action,
                'pay_type' => $type,
                'cash' => $money,
                'uid' => $uid,
                'created_at' => date('Y-m-d H:i:s', time()),
                'task_id'=>$task_id
            ];

            FinancialModel::create($financial);
            //修改子订单状态的支付状态
            OrderModel::where('code', $code)->update(['status' => 1]);

            // SubOrderModel::where('order_code', $order_code)->update(['status' => 1]);
            // OrderModel::where('code', $code)->update(['status' => 1]);
            if ($changeStatus) {
                self::where('id', '=', $task_id)->update(['status' => 3]);
            }

            //修改用户的托管状态
            //判断用户的赏金是否大于系统的任务审核金额  TODO 这里不需要了
            // $bounty_limit = \CommonClass::getConfig('task_bounty_limit');
            // if ($bounty_limit < $money) {
            //     self::where('id', '=', $task_id)->update(['status' => 3]);
            // } else {
            //     self::where('id', '=', $task_id)->update(['status' => 2]);
            // }
            //增加用户的发布任务数量
            UserDetailModel::where('uid', $uid)->increment('publish_task_num', 1);
        });
        // TODO
        if (is_null($status)) {
            //判断当前的任务发布成功之后是否需要发送系统消息
            $task_publish_success = MessageTemplateModel::where('code_name', 'task_publish_success')->where('is_open', 1)->where('is_on_site', 1)->first();
            if ($task_publish_success) {
                $task        = self::where('id', $task_id)->first()->toArray();
                $task_status = [
                    'status' => [
                        0 => '暂不发布',
                        1 => '已经发布',
                        2 => '赏金托管',
                        3 => '审核通过',
                        4 => '威客交稿',
                        5 => '雇主选稿',
                        6 => '任务公示',
                        7 => '交付验收',
                        8 => '双方互评'
                    ]
                ];
                $task        = \CommonClass::intToString([$task], $task_status);
                $task        = $task[0];
                $user        = UserModel::where('id', $uid)->first();//必要条件
                $site_name   = \CommonClass::getConfig('site_name');//必要条件
                $domain      = \CommonClass::getDomain();
                //组织好系统消息的信息
                //发送系统消息
                $messageVariableArr = [
                    'username' => $user['name'],
                    'task_number' => $task['id'],
                    'task_title' => $task['title'],
                    'task_status' => $task['status_text'],
                    'website' => $site_name,
                    'task_number' => $task['id'],
                    'task_link' => $task['title'],
                    'start_time' => $task['begin_at'],
                    'manuscript_end_time' => $task['delivery_deadline'],
                ];
                $message            = MessageTemplateModel::sendMessage('task_publish_success', $messageVariableArr);
                $data               = [
                    'message_title' => $task_publish_success['name'],
                    'code' => 'task_publish_success',
                    'message_content' => $message,
                    'js_id' => $user['id'],
                    'message_type' => 2,
                    'receive_time' => date('Y-m-d H:i:s', time()),
                    'status' => 0,
                ];
                MessageReceiveModel::create($data);
            }
        }
        return is_null($status) ? true : false;
    }

    /**
     * 查询任务详情
     * @param $id
     */
    static function detail($id)
    {
        $query = self::select('task.*', 'a.name as user_name', 'b.name as type_name', 'c.name as cate_name')
            ->where('task.id', '=', $id);
        //赏金已经托管
        $query = $query->where(function ($query) {
            $query->where('task.status', '>=', 2);
        });
        $data = $query->join('users as a', 'a.id', '=', 'task.uid')
            ->leftjoin('task_type as b', 'b.id', '=', 'task.type_id')
            ->leftjoin('cate as c', 'c.id', '=', 'task.cate_id')
            ->first();
        return $data;
    }


    /**
     * 查找相似的任务
     * @param $cate_id
     */
    static function findByCate($cate_id, $id)
    {
        $query = self::where('cate_id', '=', $cate_id);
        $query = $query->where(function ($query) use ($id) {
            $query->where('id', '!=', $id);
        });
        //赏金已经托管的任务
        $query = $query->where(function ($query) {
            $query->where('status', '>', 2);
        });
        //没有到截稿时间
        $query = $query->where(function ($query) {
            $query->where('delivery_deadline', '>', date('Y-m-d H:i:s', time()));
        });
        $data = $query->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        return $data;
    }

    /**
     * 判断是不是雇主
     */
    static function isEmployer($task_id, $uid)
    {
        $data = self::where('id', $task_id)->first();
        if ($data['uid'] == $uid)
            return true;
        return false;
    }

    /**
     * 赏金分配
     * @param $id
     * @param $uid
     */
    static public function distributeBounty($id, $uid)
    {
        //计算赏金
        $bounty = self::where('id', $id)->first();
        $bounty = ($bounty['bounty'] / $bounty['worker_num']) * (1 - sprintf("%.2f", $bounty['task_success_draw_ratio']/100));

        $status = DB::transaction(function () use ($bounty, $uid) {
            //增加用户余额
            UserDetailModel::where('uid', $uid)->increment('balance', $bounty);
            //产生一笔财务流水 表示接受任务产生的钱
            $finance_data = [
                'action' => 2,
                'pay_type' => 1,
                'cash' => $bounty,
                'uid' => $uid,
                'create_at'=>date('Y-m-d H:i:s',time())
            ];
            FinancialModel::create($finance_data);
        });

        return is_null($status) ? true : false;
    }



    /**
     * 任务验收通过和任务验收失败
     * @param $task 相关任务数据
     * @param $type 操作类型1表示验收通过2表示验收失败
     */
    static function employAccept($task,$type)
    {
        $status = DB::transeaction(function() use($task,$type)
        {
            //验收通过
            if($type==1)
            {
                //将任务状态修改成3验收通过
                TaskModel::where('id',$task['id'])->update(['status'=>3]);
                //将任务的稿件修改成验收通过
                $employee_user = EmployUserModel::where('task_id',$task['id'])->first();
                //将任务的托管金打给威客，并生成记录
                self::distributeBounty($task['id'],$employee_user['uid']);
                $bounty = self::where('id', $task['id'])->first();
                $bounty = ($bounty['bounty'] / $bounty['worker_num']) * (1 - $bounty['task_success_draw_ratio']);
                //增加用户余额
                UserDetailModel::where('uid', $employee_user['uid'])->increment('balance', $bounty);
                //产生一笔财务流水 表示接受任务产生的钱
                $finance_data = [
                    'action' => 2,
                    'pay_type' => 1,
                    'cash' => $bounty,
                    'uid' => $employee_user['uid'],
                    'create_at'=>date('Y-m-d H:i:s',time())
                ];
                FinancialModel::create($finance_data);

            }else if($type==2)
            {

            }
        });
    }
}
