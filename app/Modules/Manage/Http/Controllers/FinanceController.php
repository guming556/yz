<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\BasicController;
use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Modules\Finance\Model\CashoutModel;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Order\Model\OrderModel;
use App\Modules\Order\Model\SubOrderModel;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\BankAuthModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use Guzzle\Http\Message\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use App\Modules\Manage\Http\Requests\CashOutRequest;

class FinanceController extends ManageController
{
    public function __construct()
    {
        parent::__construct();
        $this->initTheme('manage');
        $this->theme->set('manageType', 'finance');
    }


    /**
     * 二次确认(提现)
     * @param Request $request
     */
    public function cashConfirm(Request $request) {

        $data['privilege_amount_task'] = empty($request->privilege_amount_task) ? 0 : $request->privilege_amount_task;
        $data['privilege_amount_sn']   = empty($request->privilege_amount_sn) ? 0 : $request->privilege_amount_sn;
        $data['fees']                  = $request->fees;
        $data['status']                = 2;
        $cash_out_id                   = $request->cash_out_id;
        $cash_out_info                 = CashoutModel::where('id', $cash_out_id)->first();
        if (empty($data['fees'])) {
            return back()->withErrors(['messageOfStatus' => '充值平台费率为空']);
        }
        if (!is_numeric($data['privilege_amount_task']) || !is_numeric($data['privilege_amount_sn'])) {
            return back()->withErrors(['messageOfStatus' => '平台赠送金额或抵用金额填写的不是数字']);
        }
        if ($data['fees'] > 1 || $data['fees'] < 0) {
            return back()->withErrors(['messageOfStatus' => '充值费率必须在0-1之间']);
        }

        if ($data['privilege_amount_task'] > $cash_out_info['total_pay_task']) {
            return back()->withErrors(['messageOfStatus' => '平台赠送金额大于项目金额']);
        }
        if ($data['privilege_amount_sn'] > $cash_out_info['cash']) {
            return back()->withErrors(['messageOfStatus' => '抵用金额大于该阶段总额']);
        }


        $rate_actual                   = 1 - ($data['fees'] / 100);
        $cha_task                      = ($cash_out_info['total_pay_task'] - $data['privilege_amount_task']) * $rate_actual;//平台实际到账
        $cha_sn_price                  = ($cash_out_info['cash'] - $data['privilege_amount_sn']) * $rate_actual;//实付给设计师的钱
        $data['total_pay_task_actual'] = $cha_task;
        $data['real_cash']             = $cha_sn_price;
        CashoutModel::where('id', $cash_out_id)->update($data);

        if(!empty($request->search)){
            $search = $request->search;
            $url = '';
            foreach($search as $key => $value){
                $url .= $key.'='.$value.'&';
            }
            return redirect()->to('manage/cashoutList?'.$url)->with(['message' => '操作成功']);
        }

        return redirect()->to('manage/cashoutList')->with(['message' => '操作成功']);

    }

    /**
     * 已打款
     * @param Request $request
     */
    public function withdrawRemit(Request $request) {

        $cash_out_id = $request->id;
        $sub_order_index_id     = $request->sub_order_index_id;
        $cash_out_info = CashoutModel::where('id', $cash_out_id)->update(['status' => 5]);
        $res_sub_order = SubOrderModel::where('id', $sub_order_index_id)->update(['withdraw_status' => 1]);
        if ($cash_out_info && $res_sub_order) {
            $data = [
                'message' => '操作成功'
            ];
            return response()->json($data);
        } else {
            $data = [
                'message' => '操作失败'
            ];
            return response()->json($data);
        }
    }

    /**
     * @param Request $request
     * 最终确认(提现)
     */
    public function cashConfirmEnd(Request $request) {
        $cash_out_id        = $request->id;
        $cash_out_info      = CashoutModel::find($cash_out_id);
        $cash_out_rate      = $request->altrate;
        $privilege_amount_task = $request->privilege_amount_task;

        $task_id        = $cash_out_info->task_id;
        $this_task = CashoutModel::select('fees','privilege_amount_task')->where('task_id', $task_id)->get();


        foreach ($this_task as $k => $v) {

            if (!empty((int)($v['fees'] * 100)) && $cash_out_rate != $v['fees']) {
                $data = [
                    'message' => '充值平台费率与之前填写的不同,请修改'
                ];
                return response()->json($data);
            }

//            if (!empty($v['privilege_amount_task'] * 100) && $privilege_amount_task != $v['privilege_amount_task']) {
//                $data = [
//                    'message' => '平台赠送充值金额与之前填写的不同,请修改'
//                ];
//                return response()->json($data);
//            }
        }

        $cash_out_info->status = 3;
        $cash_out_info->save();
        $data = [
            'message' => '确认成功'
        ];
        return response()->json($data);


    }


    /**
     * 后台网站流水列表
     */
    public function financeList(Request $request) {

        $this->theme->setTitle('平台流水');
        $SubOrderInfo = SubOrderModel::whereRaw('1 = 1');
        $OrderInfo    = OrderModel::whereRaw('1 = 1');

        if ($request->get('phone_num')) {
            $users_info   = UserModel::select('id')->where('name', $request->get('phone_num'))->first();
            if (empty($users_info)) {
                return redirect()->back()->with(['error' => '找不到该用户']);
            }
            $SubOrderInfo = $SubOrderInfo->where('sub_order.uid', $users_info->id);
            $OrderInfo    = $OrderInfo->where('order.uid', $users_info->id);
        }

        if ($request->get('order_num')) {
            $SubOrderInfo = $SubOrderInfo->where('sub_order.order_code', $request->get('order_num'));
            $OrderInfo    = $OrderInfo->where('order.code', $request->get('order_num'));
        }

        if ($request->get('start')) {
            $start        = date('Y-m-d H:i:s', strtotime($request->get('start')));
            $SubOrderInfo = $SubOrderInfo->where('sub_order.created_at', '>', $start);
            $OrderInfo    = $OrderInfo->where('order.created_at', '>', $start);
        }
        if ($request->get('end')) {
            $end          = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $SubOrderInfo = $SubOrderInfo->where('sub_order.created_at', '<', $end);
            $OrderInfo    = $OrderInfo->where('order.created_at', '<', $end);
        }

        if ($request->get('fund_state')) {
            if ($request->get('fund_state') == 1) {
                $data_all        = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                    ->where('cash', '>', 0)
                    ->where('fund_state',2)
                    ->orderBy('created_at', 'desc')->get();

                $detail_recharge = $OrderInfo->select('id', 'cash', 'code as order_code', 'title', 'uid', 'task_id', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)->orderBy('created_at', 'desc')->get();

            } else {
                $data_all        = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                    ->where('cash', '>', 0)
                    ->where('fund_state',1)
                    ->orderBy('created_at', 'desc')->get();
                $detail_recharge = collect([]);
            }
        } else {
            $data_all = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                ->where('cash', '>', 0)
                ->orderBy('created_at', 'desc')->get();

            $detail_recharge = $OrderInfo->select('id', 'cash', 'code as order_code', 'title', 'uid', 'task_id', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)->orderBy('created_at', 'desc')->get();
        }

        $all_data = array_merge(array_values($data_all->toArray()), array_values($detail_recharge->toArray()));
        if (empty($all_data)) {
            return redirect()->back()->with(['error' => '找不到任何数据']);
        }

        $uidArr = array_unique(array_column($all_data,'uid'));//取出数组里的所有uid，去重
        $taskIdArr = array_filter(array_unique(array_column($all_data,'task_id')));//取出数组里的所有任务id，去重，去空值

        $userArr = UserModel::select('users.id','users.name','user_detail.realname','user_detail.nickname','users.user_type')->join('user_detail','user_detail.uid','=','users.id')->whereIn('users.id',$uidArr)->orderBy('users.id','asc')->get()->toArray();
        $taskInfoArr = TaskModel::select('p.region','task.id','p.project_position')->join('project_position as p','task.project_position','=','p.id')->whereIn('task.id',$taskIdArr)->get()->toArray();

        $handleUser = $handleTask = [];
        foreach($userArr as $key => $value){
            $handleUser[$value['id']] = $value;
        }
        foreach($taskInfoArr as $key => $value){
            $handleTask[$value['id']] = $value;
        }

//var_dump($taskInfoArr);exit;
        foreach ($all_data as $k => $v) {
//            $user_data                   = UserModel::find($v['uid']);
            $all_data[$k]['user_mobile'] = $handleUser[$v['uid']]['name']??"未知";
//            $user_info                   = UserDetailModel::where('uid', $v['uid'])->first();
            if (empty($v['task_id'])) {
                $all_data[$k]['project_position'] = '无法找到';
            } else {
//                $task_info = TaskModel::find($v['task_id']);
                if(isset($handleTask[$v['task_id']]) && !empty($handleTask[$v['task_id']])){
                    $all_data[$k]['project_position'] = $handleTask[$v['task_id']]['region'] . $handleTask[$v['task_id']]['project_position'];
                }else{
                    $all_data[$k]['project_position'] = '无法找到';
                }
//                if (empty($task_info)) {
//                    $all_data[$k]['project_position'] = '无法找到';
//                } else {
//                    $ProjectPositionData = ProjectPositionModel::find($task_info->project_position);
//                    if (empty($ProjectPositionData)) {
//                        $all_data[$k]['project_position'] = '无法找到';
//                    } else {
//                        $all_data[$k]['project_position'] = $ProjectPositionData->region . $ProjectPositionData->project_position;
//                    }
//
//                }
            }
            $all_data[$k]['user_name'] = '未填写';
            if(isset($handleUser[$v['uid']])){
                if(!empty($handleUser[$v['uid']]['realname'])){
                    $all_data[$k]['user_name'] = $handleUser[$v['uid']]['realname'];
                    continue;
                }
                if(!empty($handleUser[$v['uid']]['nickname'])){
                    $all_data[$k]['user_name'] = $handleUser[$v['uid']]['nickname'];
                }
//                $all_data[$k]['user_name'] = $handleUser[$v['uid']]['nickname'];
            }

//                $all_data[$k]['user_name'] = empty($user_info) ? "未知" : empty($user_info->realname) ? empty($user_info->nickname)?'未知' : $user_info->nickname:$user_info->realname;
        }
//        $collection = collect($all_data);
        $collection = collect($all_data)->sortByDesc(function ($product, $key) {
            return $product['created_at'];
        });
        $collection = $collection->values()->all();

        //Get current page form url e.g. &page=6

        $currentPage = LengthAwarePaginator::resolveCurrentPage();


        $perPage = 15;

        //Slice the collection to get the items to display in current page
        if (count($collection) > 15) {

            $currentPageSearchResults = array_slice($collection, ($currentPage - 1) * $perPage, $perPage); //注释1
            $paginatedSearchResults   = new LengthAwarePaginator($currentPageSearchResults, count($collection), $perPage);
        } else {
            $paginatedSearchResults = new LengthAwarePaginator($collection, count($collection), count($collection));
        }

        //Create our paginator and pass it to the view

        $paginatedSearchResults->setPath("/manage/financeList/");

        $search          = [
            'phone_num' => $request->get('phone_num'),
            'order_num' => $request->get('order_num'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'fund_state' => $request->get('fund_state'),
        ];
        $data['search']  = $search;
        $data['finance'] = $paginatedSearchResults;
        $data['export_url'] = "/manage/financeListExport/?fund_state=".$search['fund_state']."&phone_num=".$search['phone_num']."&order_num=".$search['order_num']."&start=".$search['start']."&end=".$search['end'];
        return $this->theme->scope('manage.financelist', $data)->render();

    }


    public function financeListStatus(Request $request) {
        $data = [];
        return $this->theme->scope('manage.financelist', $data)->render();
    }

    /**
     * 导出网站流水记录
     */
    public function financeListExport(Request $request) {

        $phone_num  = $request->phone_num;
        $fund_state = $request->fund_state;
        $order_num  = $request->order_num;
        $start      = $request->start;
        $end        = $request->end;


        $SubOrderInfo = SubOrderModel::whereRaw('1 = 1');
        $OrderInfo    = OrderModel::whereRaw('1 = 1');
        if ($phone_num) {
            $users_info   = UserModel::select('id')->where('name', $phone_num)->first();
            $SubOrderInfo = $SubOrderInfo->where('sub_order.uid', $users_info->id);
            $OrderInfo    = $OrderInfo->where('order.uid', $users_info->id);
        }

        if ($order_num) {
            $SubOrderInfo = $SubOrderInfo->where('sub_order.order_code', $order_num);
            $OrderInfo    = $OrderInfo->where('order.code', $request->get('order_num'));
        }

        if ($start) {
            $start        = date('Y-m-d H:i:s', strtotime($start));
            $SubOrderInfo = $SubOrderInfo->where('sub_order.created_at', '>', $start);
            $OrderInfo    = $OrderInfo->where('order.created_at', '>', $start);
        }
        if ($end) {
            $end          = date('Y-m-d H:i:s', strtotime($end));
            $SubOrderInfo = $SubOrderInfo->where('sub_order.created_at', '<', $end);
            $OrderInfo    = $OrderInfo->where('order.created_at', '<', $end);
        }


        if ($fund_state) {
            if ($fund_state == 1) {
                $data_all = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                    ->where('cash', '>', 0)
                    ->where('fund_state', 2)
                    ->orderBy('created_at', 'desc')->get();

                $detail_recharge = $OrderInfo->select('id', 'cash', 'code as order_code', 'title', 'uid', 'task_id', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)->orderBy('created_at', 'desc')->get();

            } else {
                $data_all        = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                    ->where('cash', '>', 0)
                    ->where('fund_state', 1)
                    ->orderBy('created_at', 'desc')->get();
                $detail_recharge = collect([]);
            }
        } else {
            $data_all = $SubOrderInfo->select('id', 'order_code', 'cash', 'created_at', 'project_type', 'title', 'task_id', 'uid', 'fund_state')
                ->where('cash', '>', 0)
                ->orderBy('created_at', 'desc')->get();

            $detail_recharge = $OrderInfo->select('id', 'cash', 'code as order_code', 'title', 'uid', 'task_id', 'created_at')->where('cash', '>', 0)->where('status', 1)->where('task_id', 0)->orderBy('created_at', 'desc')->get();
        }

        $all_data = array_merge(array_values($data_all->toArray()), array_values($detail_recharge->toArray()));

        foreach ($all_data as $k => $v) {
            $user_data                   = UserModel::find($v['uid']);
            $all_data[$k]['user_mobile'] = empty($user_data) ? "未知" : $user_data->name;
            $user_info                   = UserDetailModel::where('uid', $v['uid'])->first();
            if (empty($v['task_id'])) {
                $all_data[$k]['project_position'] = '无法找到';
            } else {
                $task_info = TaskModel::find($v['task_id']);
                if (empty($task_info)) {
                    $all_data[$k]['project_position'] = '无法找到';
                } else {
                    $ProjectPositionData = ProjectPositionModel::find($task_info->project_position);
                    if (empty($ProjectPositionData)) {
                        $all_data[$k]['project_position'] = '无法找到';
                    } else {
                        $all_data[$k]['project_position'] = $ProjectPositionData->region . $ProjectPositionData->project_position;
                    }

                }
            }
            $all_data[$k]['user_name'] = empty($user_info) ? "未知" : empty($user_info->realname) ? empty($user_info->nickname) ? '未知' : $user_info->nickname : $user_info->realname;
        }

        foreach ($all_data as $k => $v) {
            if (empty($v['fund_state'])) {
                $data[$k] = [
                    '编号' => $v['id'], '姓名' => $v['user_name'], '手机号' => $v['user_mobile'], '明细' => $v['title'], '金额<¥>' => '+' . $v['cash'] , '工地' => $v['project_position'], '时间' => $v['created_at']
                ];
            } else {
                if ($v['fund_state'] == 1) {
                    $data[$k] = [
                        '编号' => $v['id'], '姓名' => $v['user_name'], '手机号' => $v['user_mobile'], '明细' => $v['title'], '金额<¥>' => '-' . $v['cash'] , '工地' => $v['project_position'], '时间' => $v['created_at']
                    ];
                } else {
                    $data[$k] = [
                        '编号' => $v['id'], '姓名' => $v['user_name'], '手机号' => $v['user_mobile'], '明细' => $v['title'], '金额<¥>' => '+' . $v['cash'] , '工地' => $v['project_position'], '时间' => $v['created_at']
                    ];
                }
            }
        }

        Excel::create('website_fund_record' . uniqid(), function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        })->export('xls');


    }

    /**
     * 用户流水导出
     *
     * @param Request $request
     *
     */
    public function userFinanceListExport($param)
    {
        $param = \CommonClass::getParamByQueryString($param);
        $userFinance = FinancialModel::whereRaw('1 = 1');
        if (!empty($param['uid'][0])) {
            $userFinance = $userFinance->where('financial.uid', $param['uid'][0]);
        }
        if (!empty($param['username'][0])) {
            $userFinance = $userFinance->where('users.name', $param['username'][0]);
        }
        if (!empty($param['action'][0])) {
            $userFinance = $userFinance->where('financial.action', $param['action'][0]);
        }
        if (!empty($param['start'][0])) {
            $start = date('Y-m-d H:i:s', substr($param['start'][0], 0, -3));
            $userFinance = $userFinance->where('financial.created_at', '>', $start);
        }
        if (!empty($param['end'][0])) {
            $end = date('Y-m-d H:i:s', substr($param['end'][0], 0, -3));
            $userFinance = $userFinance->where('financial.created_at', '<', $end);
        }
        $by = !empty($param['by'][0]) ? $param['by'][0] : 'id';
        $order = !empty($param['order'][0]) ? $param['order'][0] : 'desc';
        $result = $userFinance->leftJoin('user_detail', 'financial.uid', '=', 'user_detail.uid')
            ->leftJoin('users', 'financial.uid', '=', 'users.id')
            ->select('financial.*', 'user_detail.balance', 'users.name')
            ->orderBy($by, $order)->get()->chunk(100);

        $data = [
            ['编号', '财务类型', '用户', '金额', '用户余额', '时间']
        ];
        $i = 0;
        foreach ($result as $chunk) {
            foreach ($chunk as $k => $v) {
                switch ($v->action) {
                    case 1:
                        $v->action = '收入';
                        break;
                    case 2:
                        $v->action = '支出';
                        break;
                    case 3:
                        $v->action = '充值';
                        break;
                    case 4:
                        $v->action = '提现';
                        break;
                    case 5:
                        $v->action = '购买增值服务';
                        break;
                    case 6:
                        $v->action = '购买作品';
                        break;
                    case 7:
                        $v->action = '任务失败退款';
                        break;
                    case 8:
                        $v->action = '提现失败退款';
                        break;
                    case 9:
                        $v->action = '出售作品';
                        break;
                    case 10:
                        $v->action = '维权退款';
                        break;
                    case 11:
                        $v->action = '推荐到威客商城失败退款';
                        break;
                }

                $data[$i + 1] = [
                    $v->id, $v->action, $v->name, '￥' . $v->cash . '元', $v->balance, $v->created_at
                ];
                $i++;
            }
        }
        $url_excel = Excel::create('用户流水记录', function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->rows($data);
            });
        })->export('csv');


    }


    /**
     * 用户流水记录
     *
     * @param Request $request
     * @return mixed
     */
    public function userFinance(Request $request)
    {
        $this->theme->setTitle('用户流水');

        $userFinance = FinancialModel::whereRaw('1 = 1');

        if ($request->get('uid')) {
            $userFinance = $userFinance->where('financial.uid', $request->get('uid'));
        }
        if ($request->get('username')) {
            $userFinance = $userFinance->where('users.name', $request->get('username'));
        }
        if ($request->get('action')) {
            $userFinance = $userFinance->where('financial.action', $request->get('action'));
        }
        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));
            $userFinance = $userFinance->where('financial.created_at', '>', $start);
        }
        if ($request->get('end')) {
            $end = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $userFinance = $userFinance->where('financial.created_at', '<', $end);
        }
        $by = $request->get('by') ? $request->get('by') : 'id';
        $order = $request->get('order') ? $request->get('order') : 'desc';
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;

        $list = $userFinance->leftJoin('user_detail', 'financial.uid', '=', 'user_detail.uid')
            ->leftJoin('users', 'financial.uid', '=', 'users.id')
            ->select('financial.*', 'user_detail.balance','user_detail.frozen_amount', 'users.name')
            ->orderBy($by, $order)->paginate($paginate);

        $data = array(
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'action' => $request->get('action'),
            'paginate' => $request->get('paginate'),
            'order' => $request->get('order'),
            'by' => $request->get('by'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'list' => $list
        );
        $search = [
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'action' => $request->get('action'),
            'paginate' => $request->get('paginate'),
            'order' => $request->get('order'),
            'by' => $request->get('by'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ];
        $data['search'] = $search;

        return $this->theme->scope('manage.userfinance', $data)->render();
    }

    /**
     * 提现审核列表
     *
     * @param Request $request
     * @return mixed
     */
    public function cashoutList(Request $request)
    {
        $this->theme->setTitle('提现审核');
        $search['position_address']         = $request->get('position_address');
        $search['worker_phone_num'] = $request->get('worker_phone_num');
        $search['cashout_status']   = $request->get('cashout_status');
        $search['new_order']        = $request->get('new_order');
        $search['pay_code']         = $request->get('pay_code');
        $paginate                   = $request->get('paginate') ? $request->get('paginate') : 5;
        $cashout                    = CashoutModel::whereRaw('1 = 1');
        if ($search['position_address']) {
            $cashout = $cashout->where('position_address', 'like', '%' . $search['position_address'] . '%');
//            var_dump($position);exit;
        }
        if ($search['worker_phone_num']) {
            $cashout = $cashout->where('worker_phone_num', $search['worker_phone_num']);
        }
        if ($search['cashout_status']) {
            $cashout = $cashout->where('status', $search['cashout_status']);
        }
        if ($search['new_order']) {
            $cashout = $cashout->where('new_order', $search['new_order']);
        }
        if ($search['pay_code']) {
            $cashout = $cashout->where('status', $search['pay_code']);
        }


        $all_list = $cashout->orderBy('id','desc')->paginate($paginate);

        $data = array(
            'all_list'=>$all_list,
            'search'=>$search
        );

        return $this->theme->scope('manage.cashoutlist', $data)->render();
    }


    /**
     * 提现审核处理
     *
     * @param $id
     * @param $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cashoutHandle(Request $request,$id, $action)
    {
        dd($request);
        $info = CashoutModel::where('id', $id)->first();

        dd($info->toArray());
//        if (!empty($info)) {
//            switch ($action) {
//                case 'pass':
//                    $status = $info->update(array('status' => 1));
//                    break;
//                case 'deny':
//                    $status = CashoutModel::cashoutRefund($id);
//                    break;
//            }
//            if ($status)
//                return redirect('manage/cashoutList')->with(array('message' => '操作成功'));
//        }
    }

    /**
     * 提现记录详情
     *
     * @param $id
     * @return mixed
     */
    public function cashoutInfo($id)
    {
        $info = CashoutModel::where('cashout.id', $id)
            ->leftJoin('user_detail', 'cashout.uid', '=', 'user_detail.uid')
            ->select('cashout.*', 'user_detail.realname')
            ->first();

        if (!empty($info)) {
            $data = array(
                'info' => $info
            );
            return $this->theme->scope('manage.cashoutinfo', $data)->render();
        }
    }

    /**
     * 后台充值视图
     *
     * @return mixed
     */
    public function getUserRecharge()
    {
        return $this->theme->scope('manage.recharge')->render();
    }


    /**
     * 后台用户充值
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUserRecharge(Request $request)
    {
        $account = UserModel::where('id', $request->get('uid'))->orWhere('name', $request->get('username'))->first();
        if (!empty($account)) {
            $action = $request->get('action');
            switch ($action) {
                case 'increment':
                    //TODO:增加余额
                    $status = '';
                    break;
                case 'decrement':
                    //TODO:扣除余额
                    $status = '';
                    break;
            }
            if ($status)
                return redirect('manage/recharge')->with(array('message' => '操作成功'));
        }
    }

    /**
     * 验证用户信息
     *
     * @param $param
     * @return string
     */
    public function verifyUser($param)
    {
        $user = UserModel::where('id', $param)->orWhere('name', $param)->first();
        $data = null;
        if (!empty($user)) {
            $userInfo = UserDetailModel::select('balance')->where('uid', $user->id)->first();
            $data = array(
                'username' => $user->name,
                'balance' => $userInfo->balance
            );
        }
        return \CommonClass::formatResponse('验证完成', 200, $data);
    }

    /**
     * 用户充值订单列表
     *
     * @param Request $request
     * @return mixed
     */
    public function rechargeList(Request $request)
    {
        $this->theme->setTitle('充值审核');

        $recharge = OrderModel::whereNull('order.task_id')->where('order.status', 0);
        if ($request->get('code')) {
            $recharge = $recharge->where('order.code', $request->get('code'));
        }
        if ($request->get('username')) {
            $recharge = $recharge->where('users.name', $request->get('username'));
        }
        if ($request->get('start')) {
            $recharge = $recharge->where('order.created_at', '>', date('Y-m-d H:i:s', strtotime($request->get('start'))));
        }
        if ($request->get('end')) {
            $recharge = $recharge->where('order.created_at', '<', date('Y-m-d H:i:s', strtotime($request->get('end'))));
        }

        $by = $request->get('by') ? $request->get('by') : 'code';
        $order = $request->get('order') ? $request->get('order') : 'desc';
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;

        $list = $recharge->leftJoin('users', 'order.uid', '=', 'users.id')
            ->select('order.*', 'users.name')
            ->orderBy($by, $order)->paginate($paginate);

        $data = array(
            'code' => $request->get('code'),
            'username' => $request->get('username'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'order' => $request->get('order'),
            'by' => $request->get('by'),
            'paginate' => $request->get('paginate'),
            'list' => $list
        );
        $search = [
            'code' => $request->get('code'),
            'username' => $request->get('username'),
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'order' => $request->get('order'),
            'by' => $request->get('by'),
            'paginate' => $request->get('paginate'),
        ];
        $data['search'] = $search;

        return $this->theme->scope('manage.rechargelist', $data)->render();
    }

    /**
     * 后台确认订单充值
     *
     * @param $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function confirmRechargeOrder($order)
    {
        $order = OrderModel::where('code', $order)->first();
        if (!empty($order)) {
            $status = OrderModel::adminRecharge($order);
            if ($status) {
                return redirect('manage/rechargeList')->with(array('message' => '操作成功'));
            }
        }
    }

    /**
     * 财务报表
     * @return mixed
     */
    public function financeStatement()
    {
        $this->theme->setTitle('网站收支');
        $now = strtotime(date('Y-m-d', time()));
        $oneDay = 24 * 60 * 60;
        //定义最大天数
        $maxDay = 7;
        for ($i = 0; $i < $maxDay; $i++) {
            $timeArr[$i]['min'] = date('Y-m-d H:i:s', ($now - $oneDay * ($i + 1)));
            $timeArr[$i]['max'] = date('Y-m-d H:i:s', ($now - $oneDay * $i));
        }
        //反向排序
        $timeArr = array_reverse($timeArr);

        foreach ($timeArr as $k => $v) {
            $dateArr[] = date('m', strtotime($timeArr[$k]['min'])) . '月' . date('d', strtotime($timeArr[$k]['min'])) . '日';
        }
        //充值提现取值
        $arrFinance = FinancialModel::select('action', 'cash', 'created_at')
            ->where('created_at', '<', $timeArr[6]['max'])
            ->where('created_at', '>', $timeArr[1]['min'])->get();
        //发布任务订单取值
        $arrTask = OrderModel::select('created_at', 'cash')->whereNotNull('task_id')
            ->where('created_at', '<', $timeArr[6]['max'])
            ->where('created_at', '>', $timeArr[1]['min'])->get();
        //增值服务订单取值
        $arrService = SubOrderModel::select('created_at', 'cash')->where('product_type', 3)
            ->where('created_at', '<', $timeArr[6]['max'])
            ->where('created_at', '>', $timeArr[1]['min'])->get();

        $arr = array();
        //收支数组赋值
        if (!empty($arrFinance)) {
            foreach ($arrFinance as $item) {
                switch ($item->action) {
                    case 3:
                        for ($i = 0; $i < $maxDay; $i++) {
                            if ($item->created_at > $timeArr[$i]['min'] && $item->created_at < $timeArr[$i]['max']) {
                                $arr['in'][$i][] = $item->cash;
                            }
                        }
                        break;
                    case 4:
                        for ($i = 0; $i < $maxDay; $i++) {
                            if ($item->created_at > $timeArr[$i]['min'] && $item->created_at < $timeArr[$i]['max']) {
                                $arr['out'][$i][] = $item->cash;
                            }
                        }
                        break;
                }
            }
        }
        if (!empty($arrTask)) {
            foreach ($arrTask as $item) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if ($item->created_at > $timeArr[$i]['min'] && $item->created_at < $timeArr[$i]['max']) {
                        $arr['task'][$i][] = $item->cash;
                    }
                }
            }
        }
        if (!empty($arrService)) {
            foreach ($arrService as $item) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if ($item->created_at > $timeArr[$i]['min'] && $item->created_at < $timeArr[$i]['max']) {
                        $arr['tool'][$i][] = $item->cash;
                    }
                }
            }
        }
        //拼接收支明细
        if (!empty($arr)) {
            if (!empty($arr['in'])) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if (isset($arr['in'][$i])) {
                        $arr['in'][$i] = array_sum($arr['in'][$i]);
                    } else {
                        $arr['in'][$i] = 0;
                    }
                }
            } else {
                for ($i = 0; $i < $maxDay; $i++) {
                    $arr['in'][$i] = 0;
                }
            }
            if (!empty($arr['out'])) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if (isset($arr['out'][$i])) {
                        $arr['out'][$i] = array_sum($arr['out'][$i]);
                    } else {
                        $arr['out'][$i] = 0;
                    }
                }
            } else {
                for ($i = 0; $i < $maxDay; $i++) {
                    $arr['out'][$i] = 0;
                }
            }
            if (!empty($arr['task'])) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if (isset($arr['task'][$i])) {
                        $arr['task'][$i] = array_sum($arr['task'][$i]);
                    } else {
                        $arr['task'][$i] = 0;
                    }
                }
            } else {
                for ($i = 0; $i < $maxDay; $i++) {
                    $arr['task'][$i] = 0;
                }
            }
            if (!empty($arr['tool'])) {
                for ($i = 0; $i < $maxDay; $i++) {
                    if (isset($arr['tool'][$i])) {
                        $arr['tool'][$i] = array_sum($arr['tool'][$i]);
                    } else {
                        $arr['tool'][$i] = 0;
                    }
                }
            } else {
                for ($i = 0; $i < $maxDay; $i++) {
                    $arr['tool'][$i] = 0;
                }
            }
        } else {
            for ($i = 0; $i < $maxDay; $i++) {
                $arr['in'][$i] = 0;
                $arr['out'][$i] = 0;
                $arr['task'][$i] = 0;
                $arr['tool'][$i] = 0;
            }
        }
        /*$leftK = 1; $rightK = 2; $incre = 0;
        foreach ($arr as $k => $v){
            if ($k == 'in' || $k == 'task'){
                foreach ($v as $item){
                    $finance['in'][] = [$leftK, $item];
                    $leftK += 3;
                }
            }
            if ($k == 'out' || $k == 'tool'){
                foreach ($v as $item){
                    $finance['out'][] = [$rightK, $item];
                    $rightK += 3;
                }
            }
            $broken[$k] = [$incre, $arr[$k][$incre]];
        }*/
        //收支\利润数组
        $finance = [
            'in' => [
                [1, $arr['in'][0]],
                [4, $arr['in'][1]],
                [7, $arr['in'][2]],
                [10, $arr['in'][3]],
                [13, $arr['in'][4]],
                [16, $arr['in'][5]],
                [19, $arr['in'][6]]
            ],
            'out' => [
                [2, $arr['out'][0]],
                [5, $arr['out'][1]],
                [8, $arr['out'][2]],
                [11, $arr['out'][3]],
                [14, $arr['out'][4]],
                [17, $arr['out'][5]],
                [20, $arr['out'][6]]
            ],
            'task' => [
                [1, $arr['task'][0]],
                [4, $arr['task'][1]],
                [7, $arr['task'][2]],
                [10, $arr['task'][3]],
                [13, $arr['task'][4]],
                [16, $arr['task'][5]],
                [19, $arr['task'][6]]
            ],
            'tool' => [
                [2, $arr['tool'][0]],
                [5, $arr['tool'][1]],
                [8, $arr['tool'][2]],
                [11, $arr['tool'][3]],
                [14, $arr['tool'][4]],
                [17, $arr['tool'][5]],
                [20, $arr['tool'][6]]
            ]
        ];
        //折线图数组
        $broken = [
            'cash' => [
                [0, $arr['in'][0]],
                [1, $arr['in'][1]],
                [2, $arr['in'][2]],
                [3, $arr['in'][3]],
                [4, $arr['in'][4]],
                [5, $arr['in'][5]],
                [6, $arr['in'][6]],
            ],
            'out' => [
                [0, $arr['out'][0]],
                [1, $arr['out'][1]],
                [2, $arr['out'][2]],
                [3, $arr['out'][3]],
                [4, $arr['out'][4]],
                [5, $arr['out'][5]],
                [6, $arr['out'][6]],
            ],
            'task' => [
                [0, $arr['task'][0]],
                [1, $arr['task'][1]],
                [2, $arr['task'][2]],
                [3, $arr['task'][3]],
                [4, $arr['task'][4]],
                [5, $arr['task'][5]],
                [6, $arr['task'][6]],
            ],
            'tool' => [
                [0, $arr['tool'][0]],
                [1, $arr['tool'][1]],
                [2, $arr['tool'][2]],
                [3, $arr['tool'][3]],
                [4, $arr['tool'][4]],
                [5, $arr['tool'][5]],
                [6, $arr['tool'][6]],
            ]
        ];
        $data = [
            'finance' => json_encode($finance),
            'broken' => json_encode($broken),
            'dateArr' => json_encode($dateArr)
        ];
        return $this->theme->scope('manage.financeStatement', $data)->render();
    }

    /**
     * 财务报表-充值记录
     * @return mixed
     */
    public function financeRecharge(Request $request)
    {
        $this->theme->setTitle('充值记录');
        $list = FinancialModel::select('financial.id', 'users.name', 'financial.pay_type', 'financial.pay_account', 'financial.pay_code', 'financial.cash', 'financial.created_at')
            ->leftJoin('users', 'users.id', '=', 'financial.uid')->where('financial.action', 3);
        if ($request->get('type')) {
            switch ($request->get('type')) {
                case 'alipay':
                    $list = $list->where('financial.pay_type', 2);
                    break;
                case 'wechat':
                    $list = $list->where('financial.pay_type', 3);
                    break;
                case 'bankunion':
                    $list = $list->where('financial.pay_type', 4);
                    break;
            }
        }
        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));
            $list = $list->where('financial.created_at', '>', $start);
        }
        if ($request->get('end')) {
            $end = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $list = $list->where('financial.created_at', '<', $end);
        }
        if ($request->get('uid')) {
            $uid = $request->get('uid');
            $list = $list->where('financial.uid', $uid);
        }

        if ($request->get('username')) {
            $username = $request->get('username');
            $uid = UserModel::where('name',$username)->first();
            if(!empty($uid)){
                $list = $list->where('financial.uid', $uid->id);
            }
        }

        $count = $list->count();
        $sum = $list->sum('financial.cash');

        $list = $list->orderBy('financial.id', 'DESC')->paginate(10);
        $data = [
            'list' => $list,
            'count' => $count,
            'sum' => $sum,
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'type' => $request->get('type')
        ];
        $search = [
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'type' => $request->get('type')
        ];
        $data['search'] = $search;
        return $this->theme->scope('manage.financeRecharge', $data)->render();
    }

    /**
     * 充值记录导出excel
     *
     * @param $param
     */
    public function financeRechargeExport($param)
    {
        $param = \CommonClass::getParamByQueryString($param);

        $list = FinancialModel::select('financial.id', 'users.name', 'financial.pay_type', 'financial.pay_account', 'financial.cash', 'financial.created_at')
            ->leftJoin('users', 'users.id', '=', 'financial.uid')->where('financial.action', 3);
        if ($param['type'][0]) {
            switch ($param['type'][0]) {
                case 'alipay':
                    $list = $list->where('financial.pay_type', 2);
                    break;
                case 'wechat':
                    $list = $list->where('financial.pay_type', 3);
                    break;
                case 'bankunion':
                    $list = $list->where('financial.pay_type', 4);
                    break;
            }
        }
        if ($param['start'][0]) {
            $start = date('Y-m-d H:i:s', strtotime($param['start'][0]));
            $list = $list->where('financial.created_at', '>', $start);
        }
        if ($param['end'][0]) {
            $end = date('Y-m-d H:i:s', strtotime($param['end'][0]));
            $list = $list->where('financial.created_at', '<', $end);
        }

        $count = $list->count();
        $sum = $list->sum('financial.cash');
        $list = $list->get()->chunk(100);
        $data = [
            ['编号', '用户名', '充值方式', '充值账号', '金额', '充值时间']
        ];
        $i = 0;
        foreach ($list as $chunk) {
            foreach ($chunk as $k => $v) {
                switch ($v->pay_type) {
                    case 2:
                        $v->action = '支付宝';
                        break;
                    case 3:
                        $v->action = '微信';
                        break;
                    case 4:
                        $v->action = '银联';
                        break;
                }
                $data[$i + 1] = [
                    $v->id, $v->name, $v->action, $v->pay_account, '￥' . $v->cash . '元', $v->created_at
                ];
                $i++;
            }
        }
        $data[$i + 1] = [
            '总计', '', $count, '', $sum, ''
        ];
        Excel::create('充值记录', function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->rows($data);
            });
        })->export('csv');
    }

    /**
     * 财务报表-提现记录
     * @return mixed
     */
    public function financeWithdraw(Request $request)
    {
        $this->theme->setTitle('提现记录');
        $list = CashoutModel::select('cashout.id', 'users.name', 'cashout.cashout_type', 'cashout.cashout_account', 'cashout.cash',
            'cashout.real_cash', 'cashout.fees', 'cashout.created_at', 'cashout.updated_at')
            ->leftJoin('users', 'cashout.uid', '=', 'users.id')->where('cashout.status', 1);

        if ($request->get('type')) {
            switch ($request->get('type')) {
                case 'alipay':
                    $list = $list->where('cashout.cashout_type', 1);
                    break;
                case 'bank':
                    $list = $list->where('cashout.cashout_type', 2);
                    break;
            }
        }
        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));
            $list = $list->where('cashout.updated_at', '>', $start);
        }
        if ($request->get('end')) {
            $end = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $list = $list->where('cashout.updated_at', '<', $end);
        }
        //提现次数
        $count = $list->count();
        //提现金额总计
        $cashSum = $list->sum('cashout.cash');
        //到账金额总计
        $realCashSum = $list->sum('cashout.real_cash');
        //手续费总计
        $feesSum = $list->sum('cashout.fees');
        $list = $list->orderBy('cashout.id', 'DESC')->paginate(10);
        $data = [
            'list' => $list,
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'type' => $request->get('type'),
            'count' => $count,
            'cashSum' => $cashSum,
            'realCashSum' => $realCashSum,
            'feesSum' => $feesSum
        ];
        $search = [
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'type' => $request->get('type'),
        ];
        $data['search'] = $search;

        return $this->theme->scope('manage.financeWithdraw', $data)->render();
    }


    public function financeWithdrawExport($param)
    {
        $param = \CommonClass::getParamByQueryString($param);

        $list = CashoutModel::select('cashout.id', 'users.name', 'cashout.cashout_type', 'cashout.cashout_account', 'cashout.cash',
            'cashout.real_cash', 'cashout.fees', 'cashout.created_at', 'cashout.updated_at')
            ->leftJoin('users', 'cashout.uid', '=', 'users.id')->where('cashout.status', 1);

        if ($param['type'][0]) {
            switch ($param['type'][0]) {
                case 'alipay':
                    $list = $list->where('cashout.cashout_type', 1);
                    break;
                case 'bank':
                    $list = $list->where('cashout.cashout_type', 2);
                    break;
            }
        }
        if ($param['start'][0]) {
            $start = date('Y-m-d H:i:s', strtotime($param['start'][0]));
            $list = $list->where('cashout.updated_at', '>', $start);
        }
        if ($param['end'][0]) {
            $end = date('Y-m-d H:i:s', strtotime($param['end'][0]));
            $list = $list->where('cashout.updated_at', '<', $end);
        }
        //提现次数
        $count = $list->count();
        //提现金额总计
        $cashSum = $list->sum('cashout.cash');
        //到账金额总计
        $realCashSum = $list->sum('cashout.real_cash');
        //手续费总计
        $feesSum = $list->sum('cashout.fees');

        $list = $list->get()->chunk(100);
        $data = [
            ['编号', '用户名', '提现方式', '提现账号', '提现金额', '到账金额', '手续费', '提现时间']
        ];
        $i = 0;
        foreach ($list as $chunk) {
            foreach ($chunk as $k => $v) {
                switch ($v->cashout_type) {
                    case 1:
                        $v->action = '支付宝';
                        break;
                    case 2:
                        $v->action = '银行卡';
                        break;
                }
                $data[$i + 1] = [
                    $v->id, $v->name, $v->action . '次', $v->cashout_account, $v->cash, $v->real_cash, $v->fees, $v->created_at
                ];
                $i++;
            }
        }
        $data[$i + 1] = [
            '总计', '', $count, '', $cashSum, $realCashSum, $feesSum, ''
        ];
        Excel::create('提现记录', function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->rows($data);
            });
        })->export('csv');
    }

    /**
     * 财务报表-利润统计
     * @return mixed
     */
    public function financeProfit(Request $request)
    {
        $this->theme->setTitle('利润统计');

        $from = $request->get('from') ? $request->get('from') : 'task';
        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));
        }
        if ($request->get('end')) {
            $end = date('Y-m-d H:i:s', strtotime($request->get('end')));
        }

        switch ($from) {
            case 'task':
                $list = OrderModel::select('order.task_id', 'users.name', 'order.cash', 'order.created_at')
                    ->whereNotNull('order.task_id')->leftJoin('users', 'order.uid', '=', 'users.id')->where('order.status', 1)
                    ->orderBy('order.created_at', 'DESC');
                if (isset($start)) {
                    $list = $list->where('order.created_at', '>', $start);
                }
                if (isset($end)) {
                    $list = $list->where('order.created_at', '<', $end);
                }
                $sum = $list->sum('order.cash');
                break;
            case 'tool':
                $list = SubOrderModel::select('users.name', 'sub_order.cash', 'sub_order.created_at')
                    ->where('sub_order.product_type', 3)->leftJoin('users', 'sub_order.uid', '=', 'users.id')
                    ->where('sub_order.status', 1)->orderBy('sub_order.created_at', 'DESC');
                if (isset($start)) {
                    $list = $list->where('sub_order.created_at', '>', $start);
                }
                if (isset($end)) {
                    $list = $list->where('sub_order.created_at', '<', $end);
                }
                $sum = $list->sum('sub_order.cash');
                break;
            case 'cashout':
                $list = CashoutModel::select('cashout.cash', 'cashout.real_cash', 'cashout.fees', 'cashout.created_at', 'users.name')
                    ->where('cashout.status', 1)->leftJoin('users', 'users.id', '=', 'cashout.uid')
                    ->orderBy('cashout.created_at', 'DESC');
                if (isset($start)) {
                    $list = $list->where('cashout.created_at', '>', $start);
                }
                if (isset($end)) {
                    $list = $list->where('cashout.created_at', '<', $end);
                }
                $sum = $list->sum('cashout.fees');
                break;
        }

        $list = $list->paginate(10);
        $data = [
            'list' => $list,
            'from' => $from,
            'start' => $request->get('start'),
            'end' => $request->get('end'),
            'sum' => $sum
        ];
        $search = [
            'from' => $from,
            'start' => $request->get('start'),
            'end' => $request->get('end'),
        ];
        $data['search'] = $search;

        return $this->theme->scope('manage.financeProfit', $data)->render();
    }
}
