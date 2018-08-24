<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Overtrue\EasySms\EasySms;

class apiTest extends TestCase {
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample() {
        $this->assertTrue(true);
    }

    /**
     * 广告 - 列表
     */

    public function testApiAdControlleradList() {

        $con  = new \App\Http\Controllers\Api\ApiAdController();
        $data = $con->adList();
        dd($data);
    }

    /**
     * 广告 - 更新点击数
     */

    public function testApiAdControlleradClick() {
        $con  = new \App\Http\Controllers\Api\ApiAdController();
        $data = $con->adClick(1);
        dd($data);
    }

    /**
     * 测试实名认证状态
     */

    public function testApiAuthControlleradClick() {
        $con  = new \App\Http\Controllers\Api\ApiAuthController();
        $data = $con->judgeAuthState(8);
        dd($data);
    }

    /**
     * 测试发送验证码
     */

    public function testApiCodeControllersendCode() {
        $con  = new \App\Http\Controllers\Api\ApiCodeController();
        $data = $con->sendCode();
        dd($data);
    }

    /**
     * 测试预约金设置
     */

    public function testApiConfigControlleradvanceConfig() {
        $con  = new \App\Http\Controllers\Api\ApiConfigController();
        $data = $con->advanceConfig();
        dd($data);
    }

    /**
     * 测试支付设置
     */

    public function testApiConfigControllerpayConfig() {
        $con  = new \App\Http\Controllers\Api\ApiConfigController();
        $data = $con->payConfig();
        dd($data);
    }

    /**
     * 测试设计费用设置
     */

    public function testApiConfigControllerdesignConfig() {
        $con  = new \App\Http\Controllers\Api\ApiConfigController();
        $data = $con->designConfig();
        dd($data);
    }

    /**
     * 设计师上传作品
     */

    public function testApiDesignerControllerworkRelease() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->workRelease();
        dd($data);
    }

    /**
     * 获取省份
     */
    public function testApiManagerControllergetProvince() {
        $con  = new \App\Http\Controllers\Api\ApiManagerController();
        $data = $con->getProvince();
        dd($data);
    }

    /**
     * 获取地级市
     */
    public function testApiManagerControllergetCity() {
        $con  = new \App\Http\Controllers\Api\ApiManagerController();
        $data = $con->getCity();
        dd($data);
    }

    /**
     * 获取风格列表
     */
    public function testApiManagerControllergetStyle() {
        $con  = new \App\Http\Controllers\Api\ApiManagerController();
        $data = $con->getStyle();
        dd($data);
    }

    /**
     * 获取户型列表
     */
    public function testApiManagerControllergetHouse() {
        $con  = new \App\Http\Controllers\Api\ApiManagerController();
        $data = $con->getHouse();
        dd($data);
    }

    /**
     * 验证规则
     */
    public function testApiUsersControllerrule() {
        $con           = new \App\Http\Controllers\Api\ApiUsersController();
        $validatorData = ['username' => 'sw', 'password' => 111, 'user_type' => 'designer'];
        $data          = $con->rule($validatorData);
        dd($data);
    }

    /**
     * 辅材包套餐列表
     */
    public function testMaterialsControllerrule() {
        $con  = new \App\Http\Controllers\Api\MaterialsController();
        $data = $con->getMaterials();
        dd($data);
    }

    /**
     * 获取设计师作品或者所有设计列表
     */

    public function testApiUsersControllerlists() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->lists('designers');
        dd($data);
    }

    /**
     * 获取余额
     */
    public function testgetUserBalance() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->getUserBalance();
        var_dump($data);
    }

    /**
     * 获取设计师还能接受的任务
     */
    public function testgetTasks() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->getTasks();
        var_dump($data);
    }

    /**
     * 根据uid获取设计师和其他人的订单详细
     */
    public function testdesignerOrder() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->designerOrder();
        var_dump($data);
    }

    /**
     * 查询超时订单并删除
     */
    public function testworkerNotReply() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->workerNotReply();
        var_dump($data);
    }


    /**
     * 获取拒绝原因
     */
    public function testgetRefuseReason() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->getRefuseReason();
        var_dump($data);
    }

    /**
     * 保存拒绝原因
     */
    public function testsaveWorkRefuseReason() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->saveWorkRefuseReason();
        var_dump($data);
    }

    /**
     * 用户已发布任务列表
     */
    public function testuserTasks() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->userTasks();
        var_dump($data);
    }

    /**
     * 根据task_id获取订单详细
     */
    public function testgetUserTaskInfo() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->getUserTaskInfo();
        var_dump($data);
    }

    public function testGetstatusWorkOffer(){
        $project_conf_list = '{
  "project_list_conf" : {
    "7" : [
      {
        "child_id" : 2,
        "child_num" : "10"
      }
    ],
    "3" : [
      {
        "child_id" : 107,
        "child_num" : "10"
      }
    ],
    "4" : [
      {
        "child_id" : 110,
        "child_num" : "10"
      }
    ],
    "5" : [
      {
        "child_id" : 158,
        "child_num" : "10"
      }
    ],
    "1" : [
      {
        "child_id" : 28,
        "child_num" : "10"
      }
    ],
    "6" : [
      {
        "child_id" : 301,
        "child_num" : "10"
      }
    ],
    "2" : [
      {
        "child_id" : 58,
        "child_num" : "10"
      },
      {
        "child_id" : 59,
        "child_num" : "10"
      }
    ]
  },
  "to_uid" : 135,
  "task_id" : 321
}
';
        $id_arr                   = json_decode($project_conf_list, true);
        //5泥水工,6木工,7水电工

        foreach ($id_arr['project_list_conf'] as $k => $v) {
            if ($k==2) {
                foreach ($v as $n => $m) {
                    $work_type[] = \App\Modules\User\Model\ProjectConfigureModel::find($m['child_id'])->work_type;
                }
            }
        }


        if(count(count(array_unique($work_type))==1)){
            dd(2222);
        }else{
            dd(1111);
        }
        dd(array_unique($work_type));


/*        $data    = \App\Modules\Task\Model\WorkOfferModel::select('status', 'sn')->where('task_id', $tack_id)->get()->toArray();
        foreach ($data as $item => $value) {
            if ($value['status'] == 0) {
                unset($data[$item]);
            }
        }*/


//        dd($data[count($data) - 1]);
    }



    /**
     * 根据task_id取消订单
     */
    public function testcancelOrder() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->cancelOrder();
        var_dump($data);
    }

    /**
     * 业主确认()
     */
    public function testownerConfirmation() {
        $config = [
            // HTTP 请求的超时时间（秒）
            'timeout' => 5.0,

            // 默认发送配置
            'default' => [
                // 网关调用策略，默认：顺序调用
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

                // 默认可用的发送网关
                'gateways' => [
                    'alidayu',
                ],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => '/tmp/easy-sms.log',
                ],
                'alidayu' => [
                    'app_key' => '23378626',
                    'app_secret' => '1beca0254e401a0552709136bd1006ba',
                    'sign_name' => 'bg的网站',
                ],
            ],
        ];
        $easySms = new EasySms($config);

        $code = rand(1000,9999);

        $res = $easySms->send('15399900130',
            [
                'content'  => '您的验证码为: '.$code,
                'template' => 'SMS_38320194',
                'data' => [
                    'word' => (string)$code
                ],
            ]);
        dd($res);
    }

    /**
     * 微信支付
     */
    public function testwechatpay() {
        $con  = new \App\Http\Controllers\Api\ApiPayController();
        $data = $con->getWechatpay();
        var_dump($data);
    }

    /**
     * 业主端-搜索设计师或作品，按条件筛选接口
     */
    public function testsearchDesigner() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->searchDesigner();
        var_dump($data);
    }

    /**
     * 支付设计师费用
     */
    public function testpayDesignerPrice() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->payDesignerPrice();
        var_dump($data);
    }

    /**
     * 支付平台手续费20元
     */
    public function testpayPoundage() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->payPoundage();
        var_dump($data);
    }

    /**
     * 约单操作
     */
    public function testmakeAppointment() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->makeAppointment();
        var_dump($data);
    }

    /**
     * 获取已关注的人
     */
    public function testuserFocus() {
        $con  = new \App\Http\Controllers\Api\ApiUsersMoreController();
        $data = $con->userFocus();
        var_dump($data);
    }

    /**
     * 测试登录
     */
    public function testlogin() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->login();
        var_dump($data);
    }

    /**
     * 测试登录
     */
    public function testsubOffer() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->subOffer();
        var_dump($data);
    }


    /**
     * 测试抢单
     */
    public function testrobOrder() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->robOrder();
        var_dump($data);
    }

    public function testinterview() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->interview();
        var_dump($data);
    }

    /**
     * 测试创建约单任务
     * @param Request $request
     */
    public function testemployUpdate() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->employUpdate();
        var_dump($data);
    }

    /**
     * 测试支付约单任务
     * @param Request $request
     */
    public function test() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->employBountyUpdate();
        var_dump($data);
    }

    /**
     * 测试设计师约单查看详细
     * @param Request $request
     */
    public function testworkin() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->workin();
        var_dump($data);
    }

    /**
     * 接受、拒绝或取消这次约单
     * @param Request $request
     */
    public function testexcept() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->except();
        var_dump($data);
    }

    /**
     * 约单提交任务
     * @param Request $request
     */
    public function testworkCreate() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->workCreate();
        var_dump($data);
    }

    /**
     * 约单提交任务接受
     * @param Request $request
     */
    public function testacceptWork() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->acceptWork();
        var_dump($data);
    }


    /**
     * 设计师作品详细
     * @param Request $request
     */
    public function testdesignerDetail() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->designerDetail();
        var_dump($data);
    }



    /**
     * 测试随机给设计师作品
     */

    public function testgetDesignerShow() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->getDesignerShow();
        var_dump($data);
    }

    /**
     * 测试注册
     */
    public function testRegister() {
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->register();
        var_dump($data);
    }

    /**
     * 获取设计师作品详情,通过goods_id
     */

    public function testgetGoodsDetail() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->getGoodsDetail();
        var_dump($data);
    }

    /**
     * 设计师报价
     */

    public function testgetGoodetail() {
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->designer_sub_offer();
        var_dump($data);
    }


    public function testSe(){
        $data = ['效率高','速度快','态度好'];
        dd(serialize($data));
    }

    /**
     * 测试获取用户个人中心数据
     */
    public function testGetUserInfoByid(){
        $con  = new \App\Http\Controllers\Api\ApiUsersController();
        $data = $con->getUserInfoByid(135);
        var_dump($data);
    }

    /**
     * 测试生成报价(约单)
     */
    public function testdesignerOffer(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->designerOffer(135);
        var_dump($data);
    }

    /**
     * 测试业主选择设计师来约谈(约单)
     */

    public function testbossChooseDesigner(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->bossChooseDesigner();
        var_dump($data);
    }

    /**
     * 测试取消关注
     */

    public function testuserFocusDelete(){
        $con  = new \App\Http\Controllers\Api\ApiUsersMoreController();
        $data = $con->userFocusDelete();
        var_dump($data);
    }

    /**
     * 设计师接受约谈报价
     */
    public function testdesignerAccept(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->designerAccept();
        var_dump($data);
    }

    /**
     * 业主确认接单的人(约单)
     */
    public function testnewinterview(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->interview();
        var_dump($data);
    }

    /**
     * (约单)业主付款设计师的订单
     */
    public function testbossPayDesigner(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->bossPayDesigner();
        var_dump($data);
    }

    /**
     * 工作端提交（设计师）(约单)
     */
    public function testdesignerCommit(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->designerCommit();
        var_dump($data);
    }

    /**
     * 业主确认(约单)
     */
    public function testBossCommit(){
        $con  = new \App\Http\Controllers\Api\ApiTaskAppointController();
        $data = $con->BossCommit();
        var_dump($data);
    }

    /**
     * 获取用户消费详细(外层)
     */
    public function testgetUserOrderInfo(){
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->getUserOrderInfo();
        var_dump($data);
    }

    /**
     * 获取用户消费详细(内层)
     */
    public function testgetUserOrderDetail(){
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->getUserOrderDetail();
        var_dump($data);
    }

    /**
     * 测试生成订单编号
     */
    public function testcreateOrderNum() {
        dd(date('YmdHis') . mt_rand(100000, 999999) . 133);
    }

    /**
     * 测试发抢单任务
     */
    public function testuserCreateTask() {
        $con  = new \App\Http\Controllers\Api\ApiTaskController();
        $data = $con->userCreateTask();
        var_dump($data);
    }

    /**
     * 获取管家星级对应的价格
     */
    public function testgetHouserKeeperPrice() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->getHouseKeeperPrice();
        var_dump($data);
    }

    /**
     * 业主支付管家工资
     */
    public function testbossPayHouseKeeper() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossPayHouseKeeper();

        var_dump($data);
    }

    /**
     * 生成管家工程单
     */
    public function testhouseKeeperOffer() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->houseKeeperOffer();
        var_dump($data);
    }

    /**
     * 保存管家工程单
     */
    public function testsaveHouseKeeperOffer() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->saveHouseKeeperOffer();
        var_dump($data);
    }

    /**
     * 业主支付管家配置单,工人星级和辅材包
     */
    public function testbossPayHouseKeeperOffer() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossPayHouseKeeperOffer();
        var_dump($data);
    }

    /**
     * 业主选择工人的星级
     */
    public function testbossChooseWorkerStar() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossChooseWorkerStar();
        var_dump($data);
    }

    /**
     * 管家查看工程配置单
     */
    public function testhouseKeeperViewList() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->houseKeeperViewList();
        var_dump($data);
    }

    /**
     * 管家端业主确认工程完成
     */
    public function testbossConfirmStep() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossConfirmStep();
        var_dump($data);
    }


    /**
     * 业主更换工人,管家提交整改工程项目、数量（即工程变更单）：形成工程整改费用清单到业主
     */
    public function testhouseKeeperReChangeList() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->houseKeeperReChangeList();
        var_dump($data);
    }

    /**
     * 业主确认整改方案,更换工人,结算原工人的钱,存入冻结金
     */

    public function testbossSureChangeProject() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossSureChangeProject();
        var_dump($data);
    }
    /**
     * 管家的第一次报价
     */

    public function testworkerSubFirstOffer() {
        $con  = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $str = '{
            "7":[
                {
                    "child_id":"2",
                    "num":"6"
                },
                {
                   "child_id":"3",
                    "num":"10"
                }
                ],
            "1":[
                {
                    "child_id":"33",
                    "num":"10"
                },
                {
                   "child_id":"34",
                    "num":"6"
                }
                ]

}';
        $data = $con->workerSubFirstOffer($str);
        var_dump($data);
    }

    /**
     * 测试业主支付管家的工资
     */
    public function testbossPayHouseKeeperSalary() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossPayHouseKeeperSalary();
        var_dump($data);
    }

    /**
     * 测试管家获取工程配置单
     */
    public function testgetProjectPriceList() {
        $con  = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $data = $con->getProjectPriceList();
        var_dump($data);
    }

    /**
     * 财务生成
     */
    public function testcreateFinanal() {
        $data_financial = [
            'action' => 3,
            'pay_type' => 3,
            'task_id' => 0,
            'cash' => 50,
            'uid' => 1
        ];
        $res            = \App\Modules\Finance\Model\FinancialModel::create($data_financial);
        dd($res);
    }

    /**
     * 获取token
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function testgetEaseMobToken() {
        //发送的数据
        $params = [
            'client_id' => env('EASEMOB_CLIENT_ID'),
            'client_secret' => env('EASEMOB_CLIENT_SECRET'),
            'grant_type' => config('chat-room.grant_type'),
        ];

        $url = config('chat-room.easemob_token_url');//接收XML地址

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);

        if (curl_errno($ch)) {//出错则显示错误信息
            return response()->json(['error' => '数据获取失败-', curl_error($ch)], 500);
        }

        return $response_data['access_token'];
    }


    /**
     * 注册环信
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function testRegistEaseMob() {

        $levelInfo        = DB::table('level')->where('type', 2)->first()->upgrade;//工人1星级对应的分数
        $upgrade          = json_decode($levelInfo, true);
        $data['workStar'] = 1;
        $data['score']    = $upgrade[1];
        dd($data);


        $users = \App\Modules\User\Model\UserModel::all();
        foreach ($users as $k => $v) {
            //发送的数据
            $params = [
                'username' => $v->name,
                'nickname' => '测试哦',
                'password' => env('EASEMOB_USER_PASSWORD')
            ];

            $header = array();
            $header[] = 'Authorization: Bearer '.$this->testgetEaseMobToken();

            $url = config('chat-room.easemob_users_url');//接收地址

            $ch = curl_init(); //初始化curl

            curl_setopt($ch, CURLOPT_URL, $url);//设置链接

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

            curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

            $response      = curl_exec($ch);//接收返回信息
            $response_data = json_decode($response, true);
        }

        dd(222222);
        //发送的数据
        $params = [
            'username' => '13801138010',
            'password' => env('EASEMOB_USER_PASSWORD')
        ];

        $header = array();
        $header[] = 'Authorization: Bearer '.$this->testgetEaseMobToken();

        $url = config('chat-room.easemob_users_url');//接收地址

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

        $response      = curl_exec($ch);//接收返回信息
        $response_data = json_decode($response, true);
        dd($response_data['entities'][0]['username']);

        if (curl_errno($ch)) {//出错则显示错误信息
            return response()->json(['error' => '数据获取失败-', curl_error($ch)], 500);
        }

        return $response_data['entities'][0]['username'];

    }

    /**
     * 创建聊天室
     */
    public function testCreateChatRooms() {
        //发送的数据
//        $params = [
//            'owner' => '13311111111',
//            'maxusers' => 100,
//            'name' => '测试3',
//            'members' => ["13322222222","13333333333","13344444444"],
//            'roles' => ['admin' => ["13322222222"]]
//        ];
        $ProjectPosition = \App\Modules\Task\Model\ProjectPositionModel::all();
        $new_array = [];
        foreach ($ProjectPosition as $k=>$v){
            $new_array[] = [
                'members'=>\App\Modules\User\Model\UserModel::find($v['uid'])->name,
                'address'=>$v['region'].$v['project_position'],
                'id'=>$v['id'],
            ];
        }

        foreach ($new_array as $n=>$m){
            $params = [
                'owner' => '2907173277',
                'maxusers' => 100,
                'name' => $m['address'],
                'members' => [$m['members']],
                'roles' => ['admin' => [$m['members']]]
            ];


            $header = array();
            $header[] = 'Authorization: Bearer '.$this->testgetEaseMobToken();

            $url = config('chat-room.easemob_chat_room_url');//接收地址

            $ch = curl_init(); //初始化curl

            curl_setopt($ch, CURLOPT_URL, $url);//设置链接

            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

            curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//证书屏蔽

            $response      = curl_exec($ch);//接收返回信息
            $response_data = json_decode($response, true);
            $rooms_id = $response_data['data']['id'];
            \App\Modules\Task\Model\ProjectPositionModel::where('id',$m['id'])->update(['chat_room_id'=>$rooms_id]);
        }

        //聊天室id需要保存到数据库
        ///{org_name}/{app_name}/chatrooms
        dd('success');
        $rooms_id = $response_data['data']['id'];
        curl_close($ch); //关闭curl链接
        dd($rooms_id);
    }

    /**
     * 添加聊天室成员
     */
    public function testaddWorkToChatRoom() {

        $rooms_id = 23905786986497;
        $username = 13322222222;
        $url      = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id . '/users/' . $username;//url
        $params   = '';//参数
        $header   = array();
        $header[] = 'Authorization: Bearer ' . $this->testgetEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_POST, 1);//设置为POST方式

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params, true));//POST数据

        $response      = curl_exec($ch);//接收返回信息

        $response_data = json_decode($response, true);

        curl_close($ch); //关闭curl链接

        dd($response_data['data']['result']);//true或者false

    }

    /**
     * 删除聊天室成员
     */
    public function testdeleteWorkToChatRoom() {

        $rooms_id = 23883681955842;
        $username = 13344444444;
        $url      = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id . '/users/' . $username;//url
        $header   = array();
        $header[] = 'Authorization: Bearer ' . $this->testgetEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");//设置为DELETE方式

        $response      = curl_exec($ch);//接收返回信息

        $response_data = json_decode($response, true);//返回数组

        curl_close($ch); //关闭curl链接

        dd($response_data['data']['result']);//true或者false


    }

    /**
     * 删除聊天室
     */
    public function testdeleteChatRoom() {

        $rooms_id = 23979578425346;
        $url      = config('chat-room.easemob_chat_room_url') . '/' . $rooms_id;//url
        $header   = array();
        $header[] = 'Authorization: Bearer ' . $this->testgetEaseMobToken();

        $ch = curl_init(); //初始化curl

        curl_setopt($ch, CURLOPT_URL, $url);//设置链接

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);//设置头

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置是否返回信息

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");//设置为DELETE方式

        $response      = curl_exec($ch);//接收返回信息

        $response_data = json_decode($response, true);//返回数组

        curl_close($ch); //关闭curl链接

        dd($response_data['data']);//true或者false


    }


    /**
     * 拼接数据
     */
    public function testt() {
        $str = '{
            "7":[
                {
                    "child_id":"2",
                    "num":"6"
                },
                {
                   "child_id":"3",
                    "num":"10"
                }
                ],
            "1":[
                {
                    "child_id":"33",
                    "num":"10"
                },
                {
                   "child_id":"34",
                    "num":"6"
                }
                ]

}';
        $str = json_decode($str, true);

        foreach ($str as $key => $value) {
            $b = 0;
            foreach ($value as $key2 => $value2) {
                $a                = get_object_vars(DB::table('project_configure_list')->select('id', 'name', 'desc', 'price', 'num', 'project_type')->find($value2['child_id']));
                $a['num_2']       = $value2['num'];
                $a['total_price'] = $a['num_2'] * $a['price'];
                $b += $a['total_price'];
                $data[$key]['childs'][] = $a;
            }
            $data[$key]['all_price'] = $b;
        }
        dd($data);
    }

    /**
     *改变数据库一些user为工人
     */
    public function testchangeSomeField(){
       $user =  \App\Modules\User\Model\UserModel::where('user_type',5)->get();
        foreach ($user as $k=>$v){
            $res = \App\Modules\User\Model\UserDetailModel::where('uid',$v->id)->update(['work_type'=>rand(5,10)]);

        }
        dd($res);
    }

    /**
     * 第二种情况更换工人
     */
    public function testbossSureChangeProjectSecond() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $data = $con->bossSureChangeProjectSecond();
        var_dump($data);
    }

    /**
     * 测试序列化
     */
    public function testselize() {
        $data = 'a:3:{s:16:"all_parent_price";i:6070;s:8:"parent_1";a:5:{s:11:"parent_name";s:12:"拆除工程";s:19:"parent_project_type";i:1;s:12:"parent_price";i:4855;s:14:"need_work_type";a:1:{i:0;i:10;}s:6:"childs";a:3:{i:0;a:7:{s:2:"id";i:30;s:4:"name";s:45:"拆除栏杆、扶手（非保护性拆除）';


        $fixed_data = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {
            return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        },$data );
        dd($fixed_data);
    }

    /**
     * 获取辅材包详细信息
     */
    public function testgetAuxiliaryBagDetail() {
        $con  = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getAuxiliaryBagDetail();
    }



    /**
     * 保存工人到work_off表
     */

    public function testsaveLaborsToProject() {
        $con  = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->saveLaborsToProject();
    }

    /**
     * 业主提出更换工人
     */
    public function testbossChangeLabor() {
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->bossChangeLabor();
    }

    /**
     * 获取辅材包详细
     */
    public function testgetAuxiliaryDetail() {
        $con  = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getAuxiliaryDetail();
    }

    /**
     * 排序
     */
    public function testcreate_configure_lists() {
        $str = '{
    "7" : [
      {
        "child_id" : "345",
        "child_num" : "22"
      },
      {
        "child_id" : "348",
        "child_num" : "2"
      },
      {
        "child_id" : "362",
        "child_num" : "4"
      },
      {
        "child_id" : "367",
        "child_num" : "1"
      },
      {
        "child_id" : "368",
        "child_num" : "1"
      },
      {
        "child_id" : "369",
        "child_num" : "1"
      }
    ],
    "3" : [
      {
        "child_id" : "431",
        "child_num" : "39"
      }
    ],
    "4" : [
      {
        "child_id" : "436",
        "child_num" : "45"
      },
      {
        "child_id" : "442",
        "child_num" : "45"
      },
      {
        "child_id" : "451",
        "child_num" : "5"
      },
      {
        "child_id" : "595",
        "child_num" : "51"
      }
    ],
    "5" : [
      {
        "child_id" : "484",
        "child_num" : "53"
      }
    ],
    "1" : [
      {
        "child_id" : "400",
        "child_num" : "11"
      }
    ],
    "6" : [
      {
        "child_id" : "583",
        "child_num" : "156"
      },
      {
        "child_id" : "585",
        "child_num" : "156"
      },
      {
        "child_id" : "591",
        "child_num" : "53"
      }
    ],
    "2" : [
      {
        "child_id" : "402",
        "child_num" : "6"
      },
      {
        "child_id" : "405",
        "child_num" : "3"
      },
      {
        "child_id" : "406",
        "child_num" : "6"
      },
      {
        "child_id" : "408",
        "child_num" : "4"
      },
      {
        "child_id" : "410",
        "child_num" : "6"
      },
      {
        "child_id" : "413",
        "child_num" : "5"
      },
      {
        "child_id" : "594",
        "child_num" : "13"
      },
      {
        "child_id" : "427",
        "child_num" : "25"
      },
      {
        "child_id" : "428",
        "child_num" : "15"
      }
    ]
  }';
//        dd(rate_choose(1));
        dd(create_configure_lists($str));
        $con  = new \App\Http\Controllers\Api\ApiDesignerController();
/*        $str = '{
            "7":[
                {
                    "child_id":"2",
                    "child_num":"6"
                },
                {
                   "child_id":"3",
                    "child_num":"10"
                }
                ],
            "1":[
                {
                    "child_id":"33",
                    "child_num":"10"
                },
                {
                   "child_id":"34",
                    "child_num":"6"
                }
                ],
            "6":[
                {
                    "child_id":"303",
                    "child_num":"10"
                },
                {
                   "child_id":"304",
                    "child_num":"6"
                }
                ],
            "5":[
                {
                    "child_id":"158",
                    "child_num":"10"
                },
                {
                   "child_id":"159",
                    "child_num":"6"
                }
                ],
            "4":[
                {
                    "child_id":"110",
                    "child_num":"10"
                },
                {
                   "child_id":"111",
                    "child_num":"6"
                }
                ],
            "3":[
                {
                    "child_id":"106",
                    "child_num":"10"
                },
                {
                   "child_id":"107",
                    "child_num":"6"
                }
                ],
            "2":[
                {
                    "child_id":"60",
                    "child_num":"10"
                },
                {
                   "child_id":"61",
                    "child_num":"6"
                }
                ]

}';*/

        $str = '{"7":[{"child_id":"2","child_num":"6"}]}';
        $con->create_configure_lists($str);

    }


    /**
     * 小订单
     */
    public function testsmall_order() {
        $data = '';
        dd(collect([1122,1,2]));
    }
    /**
     *根据sn找到对应的阶段详细内容
     */
    public function testgetListDetailBysn() {

        $con = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->getListDetailBysn();

    }

    /**
     *业主查看此次整改费用(方案一)
     */
    public function testbossGetReChangeList() {
        $con = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->bossGetReChangeList();

    }

    /**
     * 测试平台分配工人
     */
    public function testplatformAllotLabor() {
        $con = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->platformAllotLabor();

    }

    /**
     * 平台分配工人(提供可供选择的工人)
     */
    public function testplatformProvideLabor() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->platformProvideLabor();

    }

    /**
     * 管家提交工程延期单(驳回该阶段)
     */
    public function testHouseKeeperDelayDate() {
        $con = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->HouseKeeperDelayDate();

    }

    /**
     * 管家提交工程延期单，业主确认(驳回该阶段)
     */
    public function testbossSureDelayDate() {
        $con = new \App\Http\Controllers\Api\ApiDesignerController();
        $con->bossSureDelayDate();

    }

    /**
     * 管家提交且用户确认的配置单
     */
    public function testprojectConfDetail() {
        $con = new \App\Modules\Manage\Http\Controllers\UserController();
        $con->projectConfDetail('247');

    }

    /**
     * 获取管家列表
     */
    public function testgetHouseKeeperlists() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getHouseKeeperlists();

    }

    /**
     * 管家提交整改单已到需要平台匹配的阶段
     */
    public function testprojectChangeConfList() {

        $con = new \App\Modules\Manage\Http\Controllers\UserController();
        $con->projectChangeConfList();
    }

    /**
     * 整改单进入详细配置页
     */
    public function testprojectChangeConfDetail() {

        $con = new \App\Modules\Manage\Http\Controllers\UserController();
        $con->projectChangeConfDetail(252);
    }

    /**
     * 工程整改单的结算
     */
    public function testsubWorkerConfOfChange() {
        $con = new \App\Modules\Manage\Http\Controllers\UserController();
        $con->subWorkerConfOfChange();
    }


    /**
     * 获取第一次报价(设计师和管家)
     */
    public function testgetFirstOfferPrice() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getFirstOfferPrice();
    }


    /**
     * 查看开工日期和竣工日期
     */
    public function testgerProjectDeadTime() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
//        $con->gerProjectDeadTime();
        $data = [
            "all_parent_price" => 1625,
            "parent_2" => [
                "parent_name" => "水电工程",
                "parent_project_type" => 2,
                "parent_price" => 1625,
                "need_work_type" => [
                    0 => "7"
                ],
                "childs" => [
                    0 => [
                        "id" => 99,
                        "name" => "排水（污）管铺设（PVC：DN7110）铺设",
                        "desc" => "含人工、管材、接头配件（联塑PVC管）单处不足一米按一米计算",
                        "unit_price" => "155",
                        "project_type" => 2,
                        "work_type" => "7",
                        "unit" => "m",
                        "user_need_num" => "10",
                        "child_price" => 1550,
                    ],
                    1 => [
                        "id" => 100,
                        "name" => "排水管开槽及封槽",
                        "desc" => "人工开槽，1:3水泥砂浆封槽，按实际延米计算，不足一米算一米。",
                        "unit_price" => "15",
                        "project_type" => 2,
                        "work_type" => "7",
                        "unit" => "m",
                        "user_need_num" => "5",
                        "child_price" => 75,
                    ]
                ]
            ]
        ];
        dd(serialize($data));
    }

    /**
     * 获取工人
     */
    public function testgetProjectLabors() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getProjectLabors();
    }

    /**
     * 获取账单
     */
    public function testgetBillOfTask() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getBillOfTask();
    }

    /**
     * 业主随时更换配置单
     */

    public function testbossChangeListAnyTime() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->bossChangeListAnyTime();
    }

    /**
     * 查看每一个管家更改的时间
     */

    public function testgetProjectChangeDeadTime() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getProjectChangeDeadTime();
    }

    /**
     * 工人查看自己的订单信息
     */

    public function testlaborGetProjectInfo() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->laborGetProjectInfo();
    }

    /**
     * 查看每一个管家更改的时间
     */

    public function testgetLatestChangeOfProject() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getLatestChangeOfProject();
    }

    /**
     * 该项目的历史更换工人的记录
     */

    public function testgetLogOfChangeLabor() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getLogOfChangeLabor();
    }

    /**
     * 查看每一次的整改单数据(和原数据的对比)
     */

    public function testgetChangeListLogOfProject() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getChangeListLogOfProject();
    }

    /**
     * 对比管家和设计师提交的工程配置单
     */

    public function testcompareDesignerAndHouserKeeperList() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->compareDesignerAndHouserKeeperList();
    }

    /**
     * 工人端可查看订单要做的工程项目和工程量
     */

    public function testlaborWorkOfferInfo() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->laborWorkOfferInfo();
    }


    /**
     * 结束管家的工程
     */

    public function testendProject() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->endProject();
    }

    /**
     * 评价系统(参与人员列表)
     */

    public function testgetProjectPeopleAll() {
        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->getProjectPeopleAll();
    }

    /**
     * 所有用户的我的直播列表
     */

    public function testbroadcastList() {

        $con = new \App\Http\Controllers\Api\ApiHousekeeperController();
        $con->broadcastList();
    }


    /**
     * 所有用户的我的直播列表
     */

    public function testgetDesignerTask() {

        $con = new \App\Http\Controllers\Api\ApiTaskController();
        $con->getDesignerTask();
    }

    /**
     * 获取设计师接过的单和业主选定他的单
     */

    public function testuserChangeAppointWorker() {

        $all_data_other = \App\Modules\User\Model\ProjectConfigureModel::select('pid','name','work_type','project_type','city_id','price','desc','cardnum','unit','provice_id')->where('pid',0)->get()->toArray();
        dd($all_data_other);

        // TODO 手续费，到系统配置里面找
        $poundage_service = \App\Modules\Manage\Model\ServiceModel::where('identify', 'SHOUXUFEI')->first();
        dd($poundage_service->price);

        $con = new \App\Http\Controllers\Api\ApiTaskController();
        $con->userChangeAppointWorker(22,372,'');
    }

    public function testUpdateImage() {
        //        'name', 'mobile', 'ad_slogan', 'brand_name', 'address', 'lat', 'lng', 'brand_logo'
        $data = [
            'name'=>'全国销售电话',
            'mobile'=>'4007995858',
            'ad_slogan'=>'生活因你而时尚',
            'brand_name'=>'百丽橱柜',
//            'brand_logo'=>'东鹏瓷砖',
        ];
        $environment = env('APP_ENV_SEND_MSG');
        dd($environment);
       \App\MerchantDetail::create($data);
    }

    /**
     * 测试发送
     */
    public function testsend() {

//        $message     = 'crossfire!';//消息内容
        $deviceToken = '63f2f3ed865462a540f69418056a945bc2bae67074fb0b55cc23bbe0a4cfb1a9';//应用对应机器码(去掉空格)
        $message             = '您的管家5435435435';
        $application         = \Illuminate\Support\Facades\Config::get('pushMessage.ORDER_ACCEPT');
        $data_send['message'] = $message;
        $data_send['uid'] = 132;
        \App\Modules\User\Model\UsersMessageSendModel::create($data_send);
        $res = \App\PushServiceModel::pushMessageBoss($deviceToken, $message, 1, $application);
         //通知

//        $pushMessage = \Illuminate\Support\Facades\Config::get('pushMessage.ORDER_ACCEPT');
        dd($res);
        $res = \App\PushServiceModel::pushMessageBoss($deviceToken, $message, 1);

    }

    /**
     * 测试发送
     */
    public function testsendtoMsg() {

        $sss = \App\PushServiceModel::pushMessageWorker('19000b55affe11812f7a121e1c8b78a33ab3a2ef51b3fd5e284076d49d20c978', '业主已发预约单,请您处理', 17, 'order_create');
        dd($sss);
////        $message     = 'crossfire!';//消息内容
////
////        $message             = '业主已发预约单,请您处理';
//        $deviceToken = '19000b55affe11812f7a121e1c8b78a33ab3a2ef51b3fd5e284076d49d20c978';//应用对应机器码(去掉空格)
//        $message     = Config::get('pushMessage.MESSAGE_CREATE_ORDER');
//        $num  = 11;
////        $application         = "order_create";
//        $application = Config::get('pushMessage.ORDER_CREATE');
////        $data_send['message'] = $message;
////        $data_send['uid'] = 2;
////        "19000b55affe11812f7a121e1c8b78a33ab3a2ef51b3fd5e284076d49d20c978"
////"业主已发预约单,请您处理"
////11
////"order_create"
//
////        \App\Modules\User\Model\UsersMessageSendModel::create($data_send);
//        $res = \App\PushServiceModel::pushMessageWorker($deviceToken, $message, $num, $application);
//         //通知
//
////        $pushMessage = \Illuminate\Support\Facades\Config::get('pushMessage.ORDER_ACCEPT');
//        dd($res);
////        $res = \App\PushServiceModel::pushMessageBoss($deviceToken, $message, 1);

    }

    public function testotherpush() {
        //手机注册时候返回的设备号，在xcode中输出的，复制过来去掉空格
        $deviceToken = '6b85d2aae7cef2252d1dd8f2c8525680fb7c981cc85bed42e1c0cedc0d092459';

        $pass = 'Huang1234';

        $message = 'this is my push content!' . time();

        $sound = 'default';

        $body        = array();
        $body['aps'] = array('alert' => $message);
        static $badge = 1;
        $body['aps']['badge'] = $badge += 1;
        if ($sound)
            $body['aps']['sound'] = $sound;

        $payload = json_encode($body);

        $ck_worker = public_path() . '/device_testing/ck_worker_testing.pem';

        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $ck_worker);   //刚刚合成的pem文件
        stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
        $fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp) {
            print "Failed to connect $err $errstr\n";
            return;
        } else {
            print "Connection OK\n<br/>";
        }
// send message
        $msg = chr(0) . pack("n", 32) . pack('H*', str_replace(' ', '', $deviceToken)) . pack("n", strlen($payload)) . $payload;
//推送和关闭当前流
        fwrite($fp, $msg);
        fclose($fp);
    }

    public function testio() {
        $a = '  66';
        $b  = '77 ';
        dd(trim($a,$b));
        //推送给业主
        $boss_uid = 50;
        $application = 40011;
        $message     = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_designer_sub_list')->first()->chn_name;
        $woker_info  = \App\Modules\User\Model\UserModel::find($boss_uid);
        $woker_info->send_num += 1;
        $woker_info->save();
        //保存发送的消息
        save_push_msg($message, $application, $boss_uid);
        $RES = \App\PushServiceModel::pushMessageBoss($woker_info->device_token,$message, $woker_info->send_num, $application);

        //推送给工作者
        $house_uid = 52;
        $application = 20002;
        $message     = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_designer_sub_list')->first()->chn_name;
        $woker_info  = \App\Modules\User\Model\UserModel::find($house_uid);
        $woker_info->send_num += 1;
        $woker_info->save();
        //保存发送的消息
        save_push_msg($message, $application, $house_uid);
        $res_other = \App\PushServiceModel::pushMessageWorker($woker_info->device_token,$message, $woker_info->send_num, $application);
        dd($RES,$res_other);
    }


    public function testoooo() {

/*        $pinyin =new \Overtrue\Pinyin\Pinyin();
        $data = \App\Modules\User\Model\DistrictModel::select('id','name')->get();
        foreach ($data as $k=>$v){
            $insert = implode('',$pinyin->convert($v['name']));
            \App\Modules\User\Model\DistrictModel::where('id',$v['id'])->update(['spelling'=>$insert]);
        }
        dd('66');
        dd($pinyin->convert('带着希望去旅行，比到达终点更美好'));*/
        //推送
        switch (4) {
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
        $houseKeeperInfo = \App\Modules\User\Model\UserModel::find(52);
        $message     = get_create_order_msg($application);
        $houseKeeperInfo->send_num += 1;
        $houseKeeperInfo->save();
        $message = '起来了,阿城!';
        //保存发送的消息
        save_push_msg($message,$application,$houseKeeperInfo->id);
        $res =  \App\PushServiceModel::pushMessageWorker($houseKeeperInfo->device_token, $message, $houseKeeperInfo->send_num, $application);
/*        for ($i=0;$i<1000;$i++){

        }*/

        dd($res);
    }




}
