<?php
namespace App\Modules\Task\Http\Controllers;

use App\Http\Controllers\IndexController as BasicIndexController;
use App\Http\Requests;
use App\Modules\Manage\Model\AgreementModel;
use App\Modules\Manage\Model\ConfigModel;
use App\Modules\Task\Http\Requests\BountyRequest;
use App\Modules\Task\Http\Requests\TaskRequest;
use App\Modules\Task\Model\ServiceModel;
use App\Modules\Task\Model\TaskAttachmentModel;
use App\Modules\Task\Model\TaskCateModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\TaskServiceModel;
use App\Modules\Task\Model\TaskTemplateModel;
use App\Modules\Task\Model\TaskFocusModel;
use App\Modules\Task\Model\TaskTypeModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\User\Model\BankAuthModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Order\Model\OrderModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Theme;
use QrCode;
use App\Modules\Advertisement\Model\AdTargetModel;
use App\Modules\Advertisement\Model\AdModel;
use App\Modules\Advertisement\Model\RePositionModel;
use App\Modules\Advertisement\Model\RecommendModel;
use App\Modules\User\Model\CommentModel;
use Cache;
use Omnipay;

class IndexController extends BasicIndexController
{
    public function __construct()
    {
        parent::__construct();
        $this->user = Auth::user();
        $this->initTheme('main');
    }

    /**
     * 任务大厅页面
     * @param Request $request
     * @return mixed
     */
    public function tasks(Request $request)
    {
        //seo配置信息
        $seoConfig = ConfigModel::getConfigByType('seo');
        if(!empty($seoConfig['seo_task']) && is_array($seoConfig['seo_task'])){
            $this->theme->setTitle($seoConfig['seo_task']['title']);
            $this->theme->set('keywords',$seoConfig['seo_task']['keywords']);
            $this->theme->set('description',$seoConfig['seo_task']['description']);
        }else{
            $this->theme->setTitle('任务大厅');
        }
        //接收筛选条件
        $data = $request->all();
        //根据任务类型更新任务类型
        if (isset($data['category']) && $data['category']!=0) {
            $category = TaskCateModel::findByPid([intval($data['category'])]);
            $pid = $data['category'];
            if (empty($category)) {
                $category_data = TaskCateModel::findById( intval($data['category']));
                $category = TaskCateModel::findByPid([intval($category_data['pid'])]);
                $pid = $category_data['pid'];
            }
        } else {
            //查询一级的分类,默认的是一级分类
            $category = TaskCateModel::findByPid([0]);
            $pid = 0;
        }

        if (isset($data['province'])) {
            $area_data = DistrictModel::findTree(intval($data['province']));
            $area_pid = $data['province'];
        } elseif (isset($data['city'])) {
            $area_data = DistrictModel::findTree(intval($data['city']));
            $area_pid = $data['city'];
        } elseif (isset($data['area'])) {
            $area = DistrictModel::where('id', '=', intval($data['area']))->first();
            $area_data = DistrictModel::findTree(intval($area['upid']));
            $area_pid = $area['upid'];
        } else {
            $area_data = DistrictModel::findTree(0);
            $area_pid = 0;
        }

        //查询任务大厅的所有任务
        $list = TaskModel::findBy($data);
        $lists = $list->toArray();
        $task_ids = array_pluck($lists['data'],['id']);
        $task_service = TaskServiceModel::select('task_service.*','sc.title')->whereIn('task_id',$task_ids)
            ->join('service as sc','sc.id','=','task_service.service_id')
            ->get()->toArray();
        $task_service = \CommonClass::keyByGroup($task_service,'task_id');

        //判断当前是否登陆
        $my_focus_task_ids = [];
        if(Auth::check())
        {
            //查询当前登录用户收藏的任务
            $my_focus_task_ids = TaskFocusModel::where('uid',Auth::user()['id'])->lists('task_id');
            $my_focus_task_ids = array_flatten($my_focus_task_ids);
        }

        //任务大厅底部广告
        $ad = AdTargetModel::getAdInfo('TASKLIST_BOTTOM');

        //任务大厅右上方广告
        $rightAd = AdTargetModel::getAdInfo('TASKLIST_RIGHT_TOP');

        //任务大厅右侧推荐位
        $reTarget = RePositionModel::where('code','TASKLIST_SIDE')->where('is_open','1')->select('id','name')->first();

        if($reTarget->id){
            $recommend = RecommendModel::getRecommendInfo($reTarget->id)->select('*')->get();
            if(count($recommend)){
                foreach($recommend as $k=>$v){
                    $comment = CommentModel::where('to_uid',$v['recommend_id'])->count();
                    $goodComment = CommentModel::where('to_uid',$v['recommend_id'])->where('type',1)->count();
                    if($comment){
                        $v['percent'] = $goodComment/$comment;
                    }
                    else{
                        $v['percent'] = 0;
                    }
                    $recommend[$k] = $v;
                }
                $hotList = $recommend;
            }
            else{
                $hotList = [];
            }
        }

        $view = [
            'list_array' => $lists,
            'list'=>$list,
            'merge' => $data,
            'category' => $category,
            'pid' => $pid,
            'area' => $area_data,
            'area_pid' => $area_pid,
            'ad' => $ad,
            'rightAd' => $rightAd,
            'hotList' => $hotList,
            'targetName' => $reTarget->name,
            'my_focus_task_ids' => $my_focus_task_ids,
            'task_service' => $task_service,
        ];
        //执行任务调度
        \CommonClass::taskScheduling();

        $this->theme->set('now_menu','/task');
		
        return $this->theme->scope('task.tasks', $view)->render();
    }

    /**
     * 任务发布页面
     * @return mixed
     */
    public function create(Request $request)
    {
        $this->theme->setTitle('发布任务');

        //发布任务协议
        $agree = AgreementModel::where('code_name','task_publish')->first();

        //查询热门任务
        $hotCate = TaskCateModel::hotCate(6);
        //查询所有的末级分类
        $category_all = TaskCateModel::findByPid([0],['id']);
        
        $category_all = array_flatten($category_all);
        
        $category_all = TaskCateModel::findByPid($category_all);
        // var_dump($category_all);exit;
        //查询地区一级数据
        $province = DistrictModel::findTree(0);
        //查询地区二级信息
        $city = DistrictModel::findTree($province[0]['id']);
        //查询三级
        $area = DistrictModel::findTree($city[0]['id']);
        //查询增值服务数据
        $service = ServiceModel::where('status',1)->where('type',1)->get()->toArray();
        //查询任务模板
        $templet_cate = ['设计', '文案', '开发', '装修', '营销', '商务', '生活'];
        $templet = TaskTemplateModel::all();
        $rewardModel = TaskTypeModel::where('alias','xuanshang')->first();
        //获取客服电话
        $phone = \CommonClass::getConfig('phone');
        $qq = \CommonClass::getConfig('qq');
        //右侧广告位信息
        $ad = AdTargetModel::getAdInfo('TASKINFO_RIGHT');
        $view = [
            'hotcate' => $hotCate,
            'category_all' => $category_all,
            'province' => $province,
            'area' => $area,
            'city' => $city,
            'service' => $service,
            'templet_cate' => $templet_cate,
            'templet' => $templet,
            'rewardModel'=>$rewardModel,
            'phone'=>$phone,
            'qq'=>$qq,
            'agree' => $agree,
            'ad' => $ad
        ];

        return $this->theme->scope('task.create', $view)->render();
    }

    /**
     * 任务提交，创建一个新任务
     * @param TaskRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function createTask(TaskRequest $request)
    {
        $data = $request->except(['_token','_url']);
        $data['uid'] = $this->user['id'];                                   //雇主id
        $data['desc'] = \CommonClass::removeXss($data['description']);      //详细描述
        $data['created_at'] = date('Y-m-d H:i:s', time());                  //建立时间
        $data['begin_at'] = preg_replace('/([\x80-\xff]*)/i', '', $data['begin_at']);   //匹配掉汉字数字标点符合
        $data['delivery_deadline'] = preg_replace('/([\x80-\xff]*)/i', '', $data['delivery_deadline']);
        $data['begin_at'] = date('Y-m-d H:i:s', strtotime($data['begin_at']));                      //开始投稿时间
        $data['delivery_deadline'] = date('Y-m-d H:i:s', strtotime($data['delivery_deadline']));    //截稿时间     
        $data['show_cash'] = $data['bounty'];       //悬赏金　托管赏金



        // var_dump($data);exit;

        //联合验证托管赏金和开始时间结束时间


        //发布预览和暂不发布切换
        if ($data['slutype'] == 1) {
            $data['status'] = 1;
            $controller = 'bounty';
        } elseif ($data['slutype'] == 2) {
            return redirect()->to('task/preview')->with($data);
        } elseif ($data['slutype'] == 3) {
            $data['status'] = 0;
            $controller = 'detail';
        }
        //查询当前的任务成功抽成比率
        $task_percentage = \CommonClass::getConfig('task_percentage');
        $task_fail_percentage = \CommonClass::getConfig('task_fail_percentage');
        $data['task_success_draw_ratio'] = $task_percentage;
        $data['task_fail_draw_ratio'] = $task_fail_percentage;

        // var_dump($data['task_success_draw_ratio']);
        // var_dump($data['task_success_draw_ratio']);exit;

        $taskModel = new TaskModel();
        $result = $taskModel->createTask($data);

        if (!$result) {
            return redirect()->back()->with('error', '创建任务失败！');
        }

        if($data['slutype']==3){
            return redirect()->to('user/unreleasedTasks');
        }
        return redirect()->to('task/' . $controller . '/' . $result['id']);
    }

    /**
     * 任务预览
     */
    public function preview(Request $request)
    {
        $this->theme->setTitle('任务预览');

        $data = $request->session()->all();

        if (empty($data['uid'])) {
            return redirect()->back()->with('error', '数据过期，请重新预览！');
        }

        $user_detail = UserDetailModel::where('uid', $data['uid'])->first();
        $task_cate = TaskCateModel::where('id',$data['cate_id'])->first();
        $attatchment = array();
        if (!empty($data['file_id']) && count($data['file_id']) > 0) {
            //查询用户的附件记录，排除掉用户删除的附件记录
            $file_able_ids = AttachmentModel::fileAble($data['file_id']);
            $file_able_ids = array_flatten($file_able_ids);
            $attatchment = AttachmentModel::whereIn('id', $file_able_ids)->get();
        }
        $phone = \CommonClass::getConfig('phone');
        $qq = \CommonClass::getConfig('qq');

        //右侧广告信息
        $ad = AdTargetModel::getAdInfo('TASKINFO_RIGHT');
        $view = [
            'user_detail' => $user_detail,
            'attatchment' => $attatchment,
            'data' => $data,
            'task_cate' => $task_cate,
            'phone'=>$phone,
            'qq'=>$qq,
            'ad' => $ad
        ];
        return $this->theme->scope('task.preview', $view)->render();
    }

    /**
     * ajax获取模板
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getTemplate(Request $request)
    {
        $id = $request->get('id');
        //查询当前任务分类信息
        $cate = TaskCateModel::findById($id);
        //增加任务分类被选次数
        TaskCateModel::where('id',$id)->increment('choose_num',1);
        //查询当前任务父级的id
        $pid = $cate['pid'];

        $template = TaskTemplateModel::where('cate_id',$pid)->where('status',1)->first();
        if (!$template) {
            return response()->json(['errMsg' => '没有模板']);
        }
        $template['content'] = htmlspecialchars_decode($template['content']);
        return response()->json($template);
    }

    /**
     * 暂不发布任务
     * @param TaskRequest $request
     */
    public function ajaxTask(TaskRequest $request)
    {
        $data = $request->except(['_token','_url']);
    }

    /**
     * 赏金托管页面
     * @param $id
     * @return mixed
     */
    public function bounty($id)
    {
        $this->theme->setTitle('赏金托管');
        //查询用户发布的数据
        $task = TaskModel::findById($id);

        //判断用户所要支付的是否是自己的任务和任务是否已经支付
        if ($task['uid'] != $this->user['id'] || $task['status'] >= 2) {
            return redirect()->back()->with(['error' => '非法操作！']);
        }

        //查询用户的余额
        $user_money = UserDetailModel::where(['uid' => $this->user['id']])->first();
        $user_money = $user_money['balance'];

        //查询用户的任务服务费用
        $service = TaskServiceModel::select('task_service.service_id')
            ->where('task_id', '=', $id)->get()->toArray();
        $service = array_flatten($service);//将多维数组变成一维数组
        $serviceModel = new ServiceModel();
        $service_money = $serviceModel->serviceMoney($service);

        //判断用户的余额是否充足
        $balance_pay = false;
        if ($user_money > ($task['bounty'] + $service_money)) {
            $balance_pay = true;
        }

        //查询用户绑定的银行卡信息
        $bank = BankAuthModel::where('uid', '=', $id)->where('status', '=', 4)->get();

        //判断第三方支付是否开启
        $payConfig = ConfigModel::getConfigByType('thirdpay');
        $view = [
            'task' => $task,
            'bank' => $bank,
            'service_money' => $service_money,
            'id' => $id,
            'user_money' => $user_money,
            'balance_pay' => $balance_pay,
            'payConfig' => $payConfig
        ];
        return $this->theme->scope('task.bounty', $view)->render();
    }

    /**
     * 赏金托管提交，托管赏
     * @param Request $request
     * @return $this
     */
    public function bountyUpdate(BountyRequest $request)
    {
        $data = $request->except(['_token','_url']);
        $data['id'] = intval($data['id']);
        //查询用户发布的数据
        $task = TaskModel::findById($data['id']);

        //判断用户所要支付的是否是自己的任务和任务是否已经支付
        if ($task['uid'] != $this->user['id'] || $task['status'] >= 2) {
            return redirect()->to('/task/' . $task['id'])->with('error', '非法操作！');
        }
        //计算用户的任务需要的金额
        $taskModel = new TaskModel();
        $money = $taskModel->taskMoney($data['id']);
        //查询用户的余额
        $balance = UserDetailModel::where(['uid' => $this->user['id']])->first();
        $balance = (float)$balance['balance'];
        //创建订单
        $is_ordered = OrderModel::bountyOrder($this->user['id'], $money, $task['id']);

        if (!$is_ordered) return redirect()->back()->with(['error' => '任务托管失败']);

        //如果余额足够就直接余额付款
        if ($balance >= $money && $data['pay_canel'] == 0)
        {
            //验证用户的密码是否正确
            $password = UserModel::encryptPassword($data['password'], $this->user['salt']);
            if ($password != $this->user['alternate_password']) {
                return redirect()->back()->with(['error' => '您的支付密码不正确']);
            }
            //余额支付产生订单
            $result = TaskModel::bounty($money, $data['id'], $this->user['id'], $is_ordered->code,1,1,false,true);
            if (!$result) return redirect()->back()->with(['error' => '赏金托管失败！']);
            //判断当前任务的状态是否是已经审核通过
            $task = TaskModel::where('id',$data['id'])->first();
            if($task['status']==3){
                $url = 'task/'.$data['id'];
            }elseif($task['status']==2){
                $url = 'task/tasksuccess/'.$data['id'];
            }
            return redirect()->to($url);
        } else if (isset($data['pay_type']) && $data['pay_canel'] == 1) {
            //跳转支付赏金托管流程,返回成功之后就直接执行上面的托管
            if ($data['pay_type'] == 1) {//支付宝支付
                $config = ConfigModel::getPayConfig('alipay');
                $objOminipay = Omnipay::gateway('alipay');
                $objOminipay->setPartner($config['partner']);
                $objOminipay->setKey($config['key']);
                $objOminipay->setSellerEmail($config['sellerEmail']);
                $siteUrl = \CommonClass::getConfig('site_url');
                $objOminipay->setReturnUrl($siteUrl . '/task/result');
                $objOminipay->setNotifyUrl($siteUrl . '/task/notify');

                $response = Omnipay::purchase([
                    'out_trade_no' => $is_ordered->code, //your site trade no, unique
                    'subject' => \CommonClass::getConfig('site_name'), //order title
                    'total_fee' => $money, //order total fee $money
                ])->send();
                $response->redirect();
            } else if ($data['pay_type'] == 2) {//微信支付
                $config = ConfigModel::getPayConfig('wechatpay');
                $wechat = Omnipay::gateway('wechat');
                $wechat->setAppId($config['appId']);
                $wechat->setMchId($config['mchId']);
                $wechat->setAppKey($config['appKey']);
                $out_trade_no = $is_ordered->code;
                $params = array(
                    'out_trade_no' => $is_ordered->code, // billing id in your system
                    'notify_url' => \CommonClass::getDomain() . '/task/weixinNotify?out_trade_no=' . $out_trade_no . '&task_id=' . $data['id'], // URL for asynchronous notify
                    'body' => \CommonClass::getConfig('site_name') . '余额充值', // A simple description
                    'total_fee' => $money, // Amount with less than 2 decimals places
                    'fee_type' => 'CNY', // Currency name from ISO4217, Optional, default as CNY
                );
                $response = $wechat->purchase($params)->send();

                $img = QrCode::size('280')->generate($response->getRedirectUrl());

                $view = array(
                    'cash'=>$money,
                    'img' => $img
                );
                return $this->theme->scope('task.wechatpay', $view)->render();
            } else if ($data['pay_type'] == 3) {
                dd('银联支付！');
            }
        } else if (isset($data['account']) && $data['pay_canel'] == 2) {//银行卡支付
            dd('银行卡支付！');
        } else//如果没有选择其他的支付方式
        {
            return redirect()->back()->with(['error' => '请选择一种支付方式']);
        }

    }

    /**
     * 文件上传控制
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileUpload(Request $request)
    {
        $file = $request->file('file');
        //将文件上传的数据存入到attachment表中
        $attachment = \FileClass::uploadFile($file, 'task');
        $attachment = json_decode($attachment, true);
        //判断文件是否上传
        if($attachment['code']!=200)
        {
            return response()->json(['errCode' => 0, 'errMsg' => $attachment['message']]);
        }
        $attachment_data = array_add($attachment['data'], 'status', 1);
        $attachment_data['created_at'] = date('Y-m-d H:i:s', time());
        //将记录写入到attchement表中
        $result = AttachmentModel::create($attachment_data);
        $result = json_decode($result, true);
        if (!$result) {
            return response()->json(['errCode' => 0, 'errMsg' => '文件上传失败！']);
        }
        //回传附件id
        return response()->json(['id' => $result['id']]);
    }

    /**
     * 附件删除
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function fileDelet(Request $request)
    {
        $id = $request->get('id');
        //查询当前的附件
        $file = AttachmentModel::where('id',$id)->first()->toArray();
        if(!$file)
        {
            return response()->json(['errCode' => 0, 'errMsg' => '附件没有上传成功！']);
        }
        //删除附件
        if(is_file($file['url']))
            unlink($file['url']);
        $result = AttachmentModel::destroy($id);
        if (!$result) {
            return response()->json(['errCode' => 0, 'errMsg' => '删除失败！']);
        }
        return response()->json(['errCode' => 1, 'errMsg' => '删除成功！']);
    }

    /**
     * 微信支付回调
     * @return mixed
     */
    public function weixinNotify()
    {
        //获取微信回调参数
        $arrNotify = \CommonClass::xmlToArray($GLOBALS['HTTP_RAW_POST_DATA']);

        $data = [
            'pay_account' => $arrNotify['buyer_email'],
            'code' => $arrNotify['out_trade_no'],
            'pay_code' => $arrNotify['trade_no'],
            'money' => $arrNotify['total_fee'],
            'task_id' => $arrNotify['task_id']
        ];

        $content = '<xml>
                    <return_code><![CDATA[SUCCESS]]></return_code>
                    <return_msg><![CDATA[OK]]></return_msg>
                    </xml>';

        if ($arrNotify['result_code'] == 'SUCCESS' && $arrNotify['return_code'] = 'SUCCESS') {

            /**
             * 此处处理订单业务逻辑
             */
            //将数据写入到文件中
            //回复微信端请求成功
            return response()->json($content)->header('Content-Type', 'text/xml');
        }
    }

    /**
     * 支付宝同步回调地址
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function result(Request $request)
    {
        $data = $request->all();
        $data = [
            'pay_account' => $data['buyer_email'],
            'code' => $data['out_trade_no'],
            'pay_code' => $data['trade_no'],
            'money' => $data['total_fee'],
        ];
        $gateway = Omnipay::gateway('alipay');

        $options = [
            'request_params' => $_REQUEST,
        ];
        $response = $gateway->completePurchase($options)->send();

        if ($response->isSuccessful() && $response->isTradeStatusOk()) {
            //给用户充值
            $result = UserDetailModel::recharge($this->user['id'], 2, $data);

            if (!$result) {
                echo '支付失败！';
                return redirect()->back()->withErrors(['errMsg' => '支付失败！']);
            }
            //修改订单状态，产生财务记录，修改任务状态
            $task_id = OrderModel::where('code', $data['code'])->first();

            TaskModel::bounty($data['money'], $task_id['task_id'], $this->user['id'], $data['code'], 2,1,false,true);
            echo '支付成功';
            return redirect()->to('task/' . $task_id['task_id']);
        } else {
            //支付失败通知.
            echo '支付失败';
            return redirect()->to('task/bounty')->withErrors(['errMsg' => '支付失败！']);
        }
    }

    /**
     * 支付宝异步回调地址
     * @param Request $request
     * @return $this
     */
    public function notify(Request $request)
    {
        $data = $request->all();
        $data = [
            'pay_account' => $data['buyer_email'],
            'code' => $data['out_trade_no'],
            'pay_code' => $data['trade_no'],
            'money' => $data['total_fee'],
        ];
        $gateway = Omnipay::gateway('alipay');
        $options = [
            'request_params' => $_REQUEST,
        ];
        $response = $gateway->completePurchase($options)->send();

        if ($response->isSuccessful() && $response->isTradeStatusOk()) {
            //给用户充值
            $result = UserDetailModel::recharge($this->user['id'], 2, $data);
            if (!$result) {
                echo '支付失败！';
                return redirect()->back()->withErrors(['errMsg' => '支付失败！']);
            }
            //修改订单状态，产生财务记录，修改任务状态
            $task_id = OrderModel::where('code', $data['code'])->first();

            TaskModel::bounty($data['money'], $task_id['task_id'], $this->user['id'], $data['code'], 2,1,false,ture);
            echo '支付成功';
            return redirect()->to('task/' . $task_id['task_id']);
        } else {
            //支付失败通知
            return redirect()->to('task/bounty')->withErrors(['errMsg' => '支付失败！']);
        }
    }

    /**
     * ajax获取城市、地区数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxcity(Request $request)
    {
        $id = intval($request->get('id'));
        if (!$id) {
            return response()->json(['errMsg' => '参数错误！']);
        }
        $province = DistrictModel::findTree($id);
        //查询第一个市的数据
        $area = DistrictModel::findTree($province[0]['id']);
        $data = [
            'province' => $province,
            'area' => $area
        ];
        return response()->json($data);
    }

    /**
     * ajax获取地区的数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxarea(Request $request)
    {
        $id = intval($request->get('id'));
        if (!$id) {
            return response()->json(['errMsg' => '参数错误！']);
        }
        $area = DistrictModel::findTree($id);
        return response()->json($area);
    }

    /**
     * 用户中心发布任务（暂不发布任务）
     * @param $id
     * @return mixed
     */
    public function release($id)
    {

        $this->theme->setTitle('发布任务');
        //查询任务数据
        $task = TaskModel::where('id', $id)->first();
        if(!$task)
        {
            return redirect()->to('user/unreleasedTasks')->with(['error'=>'非法操作！']);
        }
        //查询任务类型分类
        $category = TaskCateModel::findAll();

        //查询热门任务
        $hotCate = TaskCateModel::hotCate(6);
        //查询所有的末级分类
        $category_all = TaskCateModel::findByPid([0],['id']);
        $category_all = array_flatten($category_all);
        $category_all = TaskCateModel::findByPid($category_all);
        
        //查询增值服务数据
        $service = ServiceModel::all();
        $task_service = TaskServiceModel::where('task_id', $id)->lists('service_id')->toArray();
        $task_service_ids = array_flatten($task_service);
        //计算服务费用
        $task_service_money = ServiceModel::serviceMoney($task_service_ids);


        $province = DistrictModel::findTree(0);
        //查询任务的地区信息
        if ($task['region_limit'] == 1) {
            $city = DistrictModel::findTree($task['province']);
            $area = DistrictModel::findTree($task['city']);
        } else {
            $city = DistrictModel::findTree($province[0]['id']);
            $area = DistrictModel::findTree( $city[0]['id']);
        }

        //任务的附件
        $task_attachment = TaskAttachmentModel::where('task_id', $id)->lists('attachment_id')->toArray();
        $task_attachment_ids = array_flatten($task_attachment);
        $task_attachment_data = AttachmentModel::whereIn('id', $task_attachment_ids)->get();
        $domain = \CommonClass::getDomain();
        $rewardModel = TaskTypeModel::where('alias','xuanshang')->first();
        $view = [
            'hotcate' => $hotCate,
            'category' => $category,
            'category_all' => $category_all,
            'service' => $service,
            'task' => $task,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'task_service_ids' => $task_service_ids,
            'task_service_money' => $task_service_money,
            'task_attachment_data' => $task_attachment_data,
            'domain' => $domain,
            'rewardModel'=>$rewardModel
        ];

        return $this->theme->scope('task.release', $view)->render();
    }

    //赏金验证
    public function checkBounty(Request $request)
    {
        $data = $request->except(['_token','_url']);
        //检测赏金额度是否在后台设置的范围之内
        $task_bounty_max_limit = \CommonClass::getConfig('task_bounty_max_limit');
        $task_bounty_min_limit = \CommonClass::getConfig('task_bounty_min_limit');

        //判断赏金必须大于最小限定
        if ($task_bounty_min_limit > $data['param']) {
            $data['info'] = '赏金应该大于' . $task_bounty_min_limit . '小于' . $task_bounty_max_limit;
            $data['status'] = 'n';
            return json_encode($data);
        }
        //赏金必须小于最大限定
        if ($task_bounty_max_limit < $data['param'] && $task_bounty_max_limit != 0) {
            $data['info'] = '赏金应该大于' . $task_bounty_min_limit . '小于' . $task_bounty_max_limit;
            $data['status'] = 'n';
            return json_encode($data);
        }

        //匹配查询当前的任务交稿截止时间最大规则
        $task_delivery_limit_time = \CommonClass::getConfig('task_delivery_limit_time');
        $task_delivery_limit_time = json_decode($task_delivery_limit_time, true);
        $task_delivery_limit_time_key = array_keys($task_delivery_limit_time);
        $task_delivery_limit_time_key = \CommonClass::get_rand($task_delivery_limit_time_key, $data['param']);
        $task_delivery_limit_time = $task_delivery_limit_time[$task_delivery_limit_time_key];

        $data['status'] = 'y';
        $data['info'] = '您当前的发布的任务金额是' . $data['param'] . ',截稿时间是' . $task_delivery_limit_time . '天';


        return json_encode($data);
    }

    /**
     *
     */
    public function checkDeadline(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $delivery_deadline = preg_replace('/([\x80-\xff]*)/i', '', $data['delivery_deadline']);
        $begin_at = preg_replace('/([\x80-\xff]*)/i', '', $data['begin_at']);
        //验证赏金是否填写
        if (empty($data['param'])) {
            return json_encode(['info' => '请先填写任务赏金', 'status' => 'n']);
        }
        //验证开始时间是否填写
        if (empty($data['begin_at'])) {
            return json_encode(['info' => '请先填写任务开始时间', 'status' => 'n']);
        }
        //验证开始时间大于等于今天
        if (strtotime($data['begin_at'])>=strtotime(date('Y-m-d',time()))) {
            return json_encode(['info' => '开始时间不能在今天之前', 'status' => 'n']);
        }
        //验证结束时间是否填写
        if (empty($data['delivery_deadline'])) {
            return json_encode(['info' => '请填写任务截稿时间', 'status' => 'n']);
        }
        //验证开始时间和结束时间不能在同一天
        if(date('Ymd',strtotime($delivery_deadline))==date('Ymd',strtotime($begin_at)))
        {
            return json_encode(['info' => '投稿时间最少一天', 'status' => 'n','begin_at'=>$data['begin_at'],'delivery_deadline'=>date('Ymd',strtotime($data['delivery_deadline']))]);
        }
        //验证赏金是否合法
        $task_bounty_max_limit = \CommonClass::getConfig('task_bounty_max_limit');
        $task_bounty_min_limit = \CommonClass::getConfig('task_bounty_min_limit');
        //匹配查询当前的任务交稿截止时间最大规则
        $task_delivery_limit_time = \CommonClass::getConfig('task_delivery_limit_time');
        $task_delivery_limit_time = json_decode($task_delivery_limit_time, true);
        $task_delivery_limit_time_key = array_keys($task_delivery_limit_time);
        $task_delivery_limit_time_key = \CommonClass::get_rand($task_delivery_limit_time_key, $data['param']);
        $task_delivery_limit_time = $task_delivery_limit_time[$task_delivery_limit_time_key];
        //判断赏金必须大于最小限定
        if ($task_bounty_min_limit > $data['param']) {
            $info = '赏金应该大于' . $task_bounty_min_limit . '小于' . $task_bounty_max_limit;
            return json_encode(['info' => $info, 'status' => 'n']);
        }
        //赏金必须小于最大限定
        if ($task_bounty_max_limit < $data['param'] && $task_bounty_max_limit != 0) {
            $info = '赏金应该大于' . $task_bounty_min_limit . '小于' . $task_bounty_max_limit;
            return json_encode(['info' => $info, 'status' => 'n']);
        }
        //验证结束时间是否合法
        $delivery_deadline = strtotime($delivery_deadline);
        $task_delivery_limit_time = $task_delivery_limit_time * 24 * 3600;
        $begin_at = strtotime($begin_at);
        //验证截稿时间不能小于开始时间
        if ($begin_at > $delivery_deadline) {
            $info = '截稿时间不能小于开始时间';
            return json_encode(['info' => $info, 'status' => 'n']);
        }
        if (($begin_at + $task_delivery_limit_time) < $delivery_deadline) {
            $info = '当前截稿时间最晚可设置为' . date('Y-m-d', ($begin_at + $task_delivery_limit_time));
            return json_encode(['info' => $info, 'status' => 'n']);
        }
        $info = '当前截稿时间最晚可设置为' . date('Y-m-d', ($begin_at + $task_delivery_limit_time));
        $status = 'y';
        $data = array(
            'info' => $info,
            'status' => $status
        );
        return json_encode($data);

    }

    public function imgupload(Request $request)
    {
        $data = $request->all();
        dd($data);
    }

    /**
     * 收藏任务 方法废除
     * @param $taskId 任务id
     * @return mixed
     */
    public function collectionTask($taskId)
    {
        //获取当前登录用户的id
        $userId = $this->user['id'];
        if ($userId && $taskId) {
            //查询任务是否已经收藏
            $focus = TaskFocusModel::where('uid',$userId)->where('task_id',$taskId)->first();
            if($focus) {
                $route = '/task';
                $msg = '该任务已经收藏过';
            }else{
                $focusArr = array(
                    'uid' => $userId,
                    'task_id' => $taskId,
                    'created_at' => date('Y-m-d H:i:s', time())
                );
                $res = TaskFocusModel::create($focusArr);
                if ($res) {
                    $route = '/task';
                    $msg = '收藏成功';

                } else {
                    $route = '/task';
                    $msg = '收藏失败';
                }
            }
        } else {
            $route = '/task';
            $msg = '没有登录，不能收藏';
        }
        return redirect($route)->with(array('message' => $msg));
    }

    /**
     * 收藏或取消收藏任务
     * @param Request $request
     * @return mixed
     */
    public function postCollectionTask(Request $request)
    {
        //获取当前登录用户的id
        $userId = $this->user['id'];
        if(!empty($userId)){
            $taskId = $request->get('task_id');
            $type = $request->get('type');
            switch($type){
                //收藏
                case 1 :
                    //查询任务是否已经收藏
                    $focus = TaskFocusModel::where('uid',$userId)->where('task_id',$taskId)->first();
                    if($focus) {
                        $data = array(
                            'code' => 2,
                            'msg' => '该任务已经收藏过'
                        );
                    }else{
                        $focusArr = array(
                            'uid' => $userId,
                            'task_id' => $taskId,
                            'created_at' => date('Y-m-d H:i:s', time())
                        );
                        $res = TaskFocusModel::create($focusArr);
                        if ($res) {
                            $data = array(
                                'code' => 1,
                                'msg' => '收藏成功'
                            );

                        } else {
                            $data = array(
                                'code' => 2,
                                'msg' => '收藏失败'
                            );
                        }
                    }
                    break;
                //取消收藏
                case 2 :
                    //查询任务是否已经收藏
                    $focus = TaskFocusModel::where('uid',$userId)->where('task_id',$taskId)->first();
                    if(empty($focus)) {
                        $data = array(
                            'code' => 2,
                            'msg' => '该任务已经取消收藏'
                        );
                    }else{
                        $res = TaskFocusModel::where('uid',$userId)->where('task_id',$taskId)->delete();
                        if ($res) {
                            $data = array(
                                'code' => 1,
                                'msg' => '取消成功'
                            );

                        } else {
                            $data = array(
                                'code' => 2,
                                'msg' => '取消失败'
                            );
                        }
                    }
                    break;
            }
        }else{
            $data = array(
                'code' => 0,
                'msg' => '没有登录，不能收藏'
            );
        }
        return response()->json($data);
    }

    public function checkDesc(Request $request)
    {
        $data = $request->except(['_token','_url']);
        dd($data);
    }

    /**
     * 成功发布任务
     */
    public function taskSuccess($id)
    {
        $id = intval($id);
        //验证任务是否是状态2
        $task = TaskModel::where('id',$id)->first();

        if($task['status']!=2)
        {
            return redirect()->back()->with(['error'=>'数据错误，当前任务不处于等待审核状态！']);
        }
        $qq = \CommonClass::getConfig('qq');
        $view = [
            'id'=>$id,
            'qq'=>$qq,
        ];

        return $this->theme->scope('task.tasksuccess',$view)->render();
    }
}
