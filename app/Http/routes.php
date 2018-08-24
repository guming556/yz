<?php

/**
 *      ┌─┐       ┌─┐ + +
 *   ┌──┘ ┴───────┘ ┴──┐++
 *   │                 │
 *   │       ───       │++ + + +
 *   ███████───███████ │+
 *   │                 │+
 *   │       ─┴─       │
 *   │                 │
 *   └───┐         ┌───┘
 *       │         │
 *       │         │   + +
 *       │         │
 *       │         └──────────────┐
 *       │                        │
 *       │                        ├─┐
 *       │                        ┌─┘
 *       │                        │
 *       └─┐  ┐  ┌───────┬──┐  ┌──┘  + + + +
 *         │ ─┤ ─┤       │ ─┤ ─┤
 *         └──┴──┘       └──┴──┘  + + + +
 *                神兽保佑
 *               代码无BUG!
 */
/**微信*/
Route::any('/wechat', 'WxController@serve');

/**微信授权获取openid*/
Route::group(['middleware'=>['wechat.oauth']],function(){
Route::get('/getUserInfo', 'WxController@getUserInfo');

});
Route::get('/wxRepairIndex', 'WxController@wxRepairIndex');
Route::get('/renovation', 'WxController@renovation');
Route::get('/userRenovation', 'WxController@userRenovation');
Route::get('/myRenovationRecord', 'WxController@myRenovationRecord');
Route::get('/wxLogin', 'WxController@wxLogin');
Route::get('/wxReg', 'WxController@wxReg');
Route::get('/findHousekeeper', 'WxController@findHousekeeper');
Route::get('/findWorker', 'WxController@findWorker');
Route::get('/housekeeperDetail', 'WxController@housekeeperDetail');
Route::get('/buildingList', 'WxController@buildingList');
Route::post('/LUpload', 'WxController@LUpload');

/**提交注册*/
Route::post('/subRegData', 'WxController@subRegData');

/**API接口*/
Route::get('/', 'HomeController@designerIndex');

/**三方查询登录(视图渲染)*/
Route::get('/selectLogin','SelectInviteController@selectLogin');

/**bug修改*/
Route::post('api/(null)','Api\ApiAdController@bugFix');

/**三方查询登录*/
Route::post('suppliesLogin', 'SelectInviteController@suppliesLogin')->name('suppliesLogin');

$api = app('Dingo\Api\Routing\Router');

$api->version(['v4'], function ($api) {
   

    $api->group(['prefix'=>'v4','namespace' => 'App\Http\Controllers\v4\Api'],function ($api){
        // 用户登录
        $api->post('login', 'ApiUsersController@login');
		//获取当前版本号
		$api->get('getApiVision', 'ApiAdController@getApiVision');
		//广告列表
	    $api->get('adviertisement', 'ApiAdController@adList');
		//更新广告点击数
	    $api->patch('adviertisement/{id}', 'ApiAdController@adClick');
	    //发送验证码
	    $api->post('verificationCode', 'ApiCodeController@sendCode');
		//发送注册验证码
	    $api->post('sendRegCode', 'ApiCodeController@sendRegCode');
		//验证验证码有效性
	    $api->post('checkCode', 'ApiCodeController@checkCode');
        // 用户注册
        $api->post('register', 'ApiUsersController@register');
        //启动广告和新手图片一次性给前端
        $api->get('getAllAdAndHelpImg', 'ApiUsersController@getAllAdAndHelpImg');
        //辅材包查看
        $api->get('auxDetail/{id}','ApiTaskController@auxDetail');
        // 用户忘记密码
        $api->post('forget', 'ApiUsersController@forgetPassword');
        //获取经纬度表经纬度（安卓使用，把区去掉了）
        $api->get('getCoordinateAndroid' , 'ApiUsersController@getCoordinateAndroid');
        //随机给设计师作品
        $api->get('/getDesignerShow', 'ApiTaskController@getDesignerShow');


        //需要验证用户的接口
    	$api->group(['middleware' => ['apiauth']], function ($api) {

	        //根据id获取所有日志评论
	        $api->get('getAllCommentOfLogById', 'ApiHousekeeperController@getAllCommentOfLogById');
	
	        //提交评论
	        $api->post('commentProjectLog', 'ApiHousekeeperController@commentProjectLog');

	        //聊天室不存在就注册一个
	        $api->get('detectChatAccount', 'ApiTaskController@detectChatAccount');

	        //聊天室列表
	        $api->get('getChatRoom', 'ApiUsersController@getChatRoom');
	
	        // 根据电话号码返回名称和姓名(环信获取)
	        $api->get('getAvatarByPhoeNum', 'ApiUsersController@getAvatarByPhoeNum');
	
	        //摄像头url提交
	        $api->post('submitLiveTVUrl', 'ApiUsersController@submitLiveTVUrl');
	
	        // 用户更改头像
	        $api->post('avatar', 'ApiUsersCenterController@avatar');
	
	        // 用户资料完善
	        $api->post('info', 'ApiUsersCenterController@info');
	
	        // 用户关注
	        $api->post('focus', 'ApiServiceController@focus');

	        // 取消关注
	        $api->post('focusDelete', 'ApiUsersMoreController@userFocusDelete');
	
	        // 我的关注
	        $api->get('userFocus', 'ApiUsersMoreController@userFocus');
	
	        // 排除掉取消过订单的设计师
	        $api->get('userFocusRemove', 'ApiUsersMoreController@userFocusRemove');
	
	        // 上传图片
	        $api->post('upload', 'ApiUploadImgController@uploadImg');
	
	        // 身份认证信息提交,暂时用不到
	        $api->post('realname', 'ApiAuthController@postRealnameAuth');
	
	        // 判断是否已经通关身份认证, 暂时用不到
	        $api->post('authState', 'ApiAuthController@realnameAuthState');
	
	        // 用户发布任务
	        $api->post('task', 'ApiTaskController@userCreateTask');
	
	        // 获取任务发布费
	        $api->get('taskPoundage', 'ApiTaskController@getTaskPoundage');
	
	        // 获取发布过的工程地址
	        $api->post('projectList', 'ApiTaskController@getProjectList');
	
	        // 获取风格列表
	        $api->get('styles', 'ApiUsersController@getStyles');
	
	        // 获取空间列表
	        $api->get('spaces', 'ApiUsersController@getSpaces');
	
	        // 获取户型列表
	        $api->get('houses', 'ApiUsersController@getHouses');
	
	        // 获取城市列表
	        // $api->get('getStyle', 'ApiUsersController@getStyle');
	
	        // 设计师发布作品
	        $api->post('workRelease', 'ApiDesignerController@workRelease');
	
	        // 作品附件
	        $api->post('workAttachment', 'ApiDesignerController@workAttachment');
	
	        // 获取所有设计师作品列表
	        $api->get('list/works', 'ApiUsersController@worksLists');
	
	        //获取所有设计师
	        $api->get('list/designers', 'ApiUsersController@designerLists');
	
	        // 支付任务发布费
	        // 2017-04-25 23:00
	        $api->post('payPoundage', 'ApiTaskController@payPoundage');
	
	        // 设计师抢单（等用户确认后在产生订单）
	        $api->post('robOrder', 'ApiTaskController@robOrder');
	
	        //  获取文章信息
	        $api->get('getInfo/{type}', 'ArticleController@getInfo');

	        $api->get('getName', 'ArticleController@getName');//获取文章分类名称

	        //获取辅材包套餐列表
	        $api->get('getMaterials', 'MaterialsController@getMaterials');

	        $api->get('materDetail', 'MaterialsController@materDetail');//辅材包套餐详情
	
	        //全部说明
	        $api->get('getExplain', 'ExplainController@getExplain');
	
	        //说明详情
	        $api->get('detailExplain', 'ExplainController@detailExplain');
	
	        //获取所有工地
	        $api->get('getAll', 'ProjectPositionController@getAll');
	
	        //根据id获取工地信息
	        $api->get('getPosition', 'ProjectPositionController@getPosition');
	
	        //修改
	        $api->post('savePosition', 'ProjectPositionController@savePosition');
	
	        //添加
	        $api->post('createPosition', 'ProjectPositionController@createProjectPosition');
	
	        $api->get('updatePosition', 'ProjectPositionController@updatePosition');

	        //删除工地
	        $api->post('delPosition', 'ProjectPositionController@delPosition');
	
	        // 设计师投稿列表
	        $api->get('worklist', 'ApiTaskController@workList');
	
	        // 工作端获取所有可抢单任务列表
	        $api->get('tasks', 'ApiTaskController@getTasks');
	
	        //根据uid获取管家和监理的订单详细
	        $api->get('designerOrder', 'ApiTaskController@designerOrder');
	
	        // 获取某个设计师作品列表
	        $api->get('designerGoods', 'ApiDesignerController@designerGoods');
	
	        // 确认约谈人选
	        $api->post('interview', 'ApiUsersController@interview');
	
	        // 设计师报价表
	        $api->get('offer', 'ApiDesignerController@designerOffer');
	
	        //管家第一次报价(个人工资)
	        $api->post('getHouseKeeperPrice', 'ApiDesignerController@getHouseKeeperPrice');
	
	        // 设计师提交报价表
	        $api->post('offer', 'ApiDesignerController@subOffer');
	
	        // 业主确认报价单并支付(设计师)
	        $api->post('designerPrice', 'ApiUsersController@payDesignerPrice');
	
	        // 步骤-提交接口
	        $api->post('workConfirmation', 'ApiTaskController@workConfirmation');
	
	        //最终确认,返钱给工人和设计师
	        $api->post('ownerFinalConfirmation', 'ApiTaskController@ownerFinalConfirmation');
	
	        // 步骤-提交时的接口(判断设计师有没有上传过图纸)
	        $api->get('detectUploadImg', 'ApiTaskController@detectUploadImg');
	
	        // 步骤-确认接口(监理确认,业主确认)
	        $api->post('ownerConfirmation', 'ApiTaskController@ownerConfirmation');
	
	        // 获取当前任务状态
	        $api->post('currentOrderStatus', 'ApiTaskController@getCurrentOrderStatus');
	
	        // 用户已发布任务列表
	        $api->get('userTasks', 'ApiUsersController@userTasks');
	
	        // 设计师详情
	        $api->get('designerDetail', 'ApiDesignerController@designerDetail');
	
	        // 管家详情
	        $api->get('houseKeeperDetail', 'ApiHousekeeperController@houseKeeperDetail');
	
	        // 监理详情
	        $api->get('supervisorDetail', 'ApiSupervisorController@supervisorDetail');
	
	        //设计师或者业主余额
	        $api->get('getUserBalance', 'ApiUsersController@getUserBalance');
	
	        //业主拒绝理由列表
	        $api->get('getRefuseReason', 'ApiUsersController@getRefuseReason');
	
	        //业主拒绝理由提交
	        $api->post('saveWorkRefuseReason', 'ApiUsersController@saveWorkRefuseReason');
	
	        //根据task_id取消订单(可以选择换人,已扣预约金)
	        $api->post('cancelOrder', 'ApiTaskController@cancelOrder');
	
	        //业主端-搜索设计师或作品，按条件筛选接口
	        $api->post('/searchDesigner', 'ApiDesignerController@searchDesigner');
	
	        //获取作品详细
	        $api->get('goodsDetail', 'ApiDesignerController@getGoodsDetail');

	        //获取用户个人中心数据
	        $api->get('/getUserInfoByid', 'ApiUsersController@getUserInfoByid');
	
	        //根据管家id获取其星级单价
	        $api->get('/housekeeperUnitPrice', 'ApiHousekeeperController@getHousekeeperStar');
	
	        //管家获取工程配置单列表(拿到设计师提交的配置单)
	        $api->get('/projectConfigureList', 'ApiHousekeeperController@getProjectPriceList');
	
	        //评价工人
	        $api->post('/evaluateWorker', 'ApiUsersController@evaluateWorker');
	
	        //评价管家,监理
	        $api->post('/evaluateHouser', 'ApiUsersController@evaluateHouser');
	
	        //根据work_offer_id拿到工人详细
	        $api->get('/getLaborEvaluate', 'ApiUsersController@getLaborEvaluate');
	        //根据task_id拿到管家和监理详细
	        $api->get('/getHouserKpperEvaluate', 'ApiUsersController@getHouserKpperEvaluate');
	
	        //是否已评价过
	        $api->get('evaluateStatus', 'ApiUsersController@evaluateStatus');
	
	        //是否已评价过(管家,监理)
	        $api->get('evaluateStatusOfHouser', 'ApiUsersController@evaluateStatusOfHouser');
	
	        //获取工作者评价
	        $api->get('getEvaluateOfWorker', 'ApiUsersController@getEvaluateOfWorker');
	
	        //监理提交第一次报价
	        $api->post('/workerSubFirstOffer', 'ApiHousekeeperController@workerSubFirstOffer');
	
	        //业主付款管家提交的第一次报价
	        $api->post('bossPayHouseKeeperSalary', 'ApiDesignerController@bossPayHouseKeeperSalary');
	
	        //管家提交工程配置单
	        $api->post('houseKeeperOffer', 'ApiDesignerController@houseKeeperOffer');
	
	        //管家获取辅材包列表信息
	        $api->get('auxiliaryBagList', 'ApiHousekeeperController@getAuxiliaryBag');
	
	        //管家获取辅材包详细信息
	        $api->get('auxiliaryBagDetail', 'ApiHousekeeperController@getAuxiliaryBagDetail');
	
	        //用户获取管家提交的工程配置单
	        $api->get('bossGetProjectConfList', 'ApiDesignerController@bossGetProjectConfList');
	
	        //用于帮助前端计算出该工程配置单一共需要支付多少钱
	        $api->get('seekConfTotalPrice', 'ApiDesignerController@seekConfTotalPrice');
	
	        //业主确认管家的工程配置单
	        $api->post('bossSureLists', 'ApiDesignerController@bossSureLists');
	
	        //业主支付管家的工程配置单(业主付款管家提交的第二次报价)
	        $api->post('bossPayHouseKeeperOffer', 'ApiDesignerController@bossPayHouseKeeperOffer');
	
	        //根据任务id获取辅材包详细和工人星级
	        $api->get('getAuxiliaryDetail', 'ApiHousekeeperController@getAuxiliaryDetail');
	
	        //管家填写开工日期和竣工日期并提交到业主(首次提交)
	        $api->post('houseKeeperProvideDate', 'ApiDesignerController@houseKeeperProvideDate');
	
	        //业主确认开工日期和竣工日期
	        $api->post('bossSureDate', 'ApiDesignerController@bossSureDate');
	
	        //业主驳回开工日期和竣工日期
	        $api->post('bossRefuseDateOfHouse', 'ApiDesignerController@bossRefuseDateOfHouse');
	
	        //业主在客诉中心驳回
	        $api->post('bossRefuseProject', 'ApiDesignerController@bossRefuseProject');
	
	        //业主确认管提交的更换工人,提交的整改单
	        $api->post('bossSureChangeProject', 'ApiDesignerController@bossSureChangeProject');
	
	        //根据sn找到对应的阶段详细内容
	        $api->get('getListDetailBysn', 'ApiDesignerController@getListDetailBysn');
	
	        //获取工程阶段（水电或泥水等单一阶段的工人和管家）结算清单
	        $api->get('getSettlementList', 'ApiTaskController@getSettlementList');
	
	        //获取整改历史记录
	        $api->get('getChangeListHistory' , 'ApiTaskController@getChangeListHistory');
	
	        //业主更换工人,管家提交整改工程项目,数量,时间,阶段 (方案1)
	        $api->post('houseKeeperReChangeList' , 'ApiDesignerController@houseKeeperReChangeList');
	
	        //监理或业主普通驳回验收请求
	        $api->post('normalBossRefuseProject' , 'ApiDesignerController@normalBossRefuseProject');
	
	        //管家提交工程延期单
	        $api->post('HouseKeeperDelayDate' , 'ApiDesignerController@HouseKeeperDelayDate');
	
	        //业主或监理驳回延期单
	        $api->post('rejectDelayReport' , 'ApiDesignerController@rejectDelayReport');
	
	        //业主或监理确认工程延期单
	        $api->post('bossSureDelayDate' , 'ApiDesignerController@bossSureDelayDate');
	
	        //管家或监理列表
	        $api->get('getHouseKeeperlists' , 'ApiHousekeeperController@getHouseKeeperlists');
	
	        //获取第一次报价(设计师和管家)
	        $api->get('getFirstOfferPrice' , 'ApiHousekeeperController@getFirstOfferPrice');
	
	        //查看开工日期和竣工日期
	        $api->get('gerProjectDeadTime' , 'ApiHousekeeperController@gerProjectDeadTime');
	
	        //查看每一次变更时间的记录
	        $api->get('getDelayDates' , 'ApiHousekeeperController@getDelayDates');
	
	        //查看工程单的大概价钱
	        $api->get('getConfPriceList' , 'ApiHousekeeperController@getConfPriceList');
	
	        //查看工程单,业主获取工人列表
	        $api->get('getProjectLabors' , 'ApiHousekeeperController@getProjectLabors');
	
	        //查看每一个管家更改的时间
	        $api->get('getProjectChangeDeadTime' , 'ApiHousekeeperController@getProjectChangeDeadTime');
	
	        //工人查看自己的订单信息
	        $api->get('laborGetProjectInfo' , 'ApiHousekeeperController@laborGetProjectInfo');
	
	        //设计师获取工程配置单
	        $api->get('designerGetProjectConfList' , 'ApiDesignerController@designerGetProjectConfList');
	
	        //管家获取工程配置单
	        $api->get('houseGetProjectConfList' , 'ApiDesignerController@houseGetProjectConfList');
	
	        //该项目的历史更换工人的记录
	        $api->get('getLogOfChangeLabor' , 'ApiHousekeeperController@getLogOfChangeLabor');
	        //该项目的历史更换工人的记录，安卓端使用
	        $api->get('getLogOfChangeLaborAndroid' , 'ApiHousekeeperController@getLogOfChangeLaborAndroid');
	
	        //查看每一次的整改单数据(和原数据的对比)(已确认的)
	        $api->get('getChangeListLogOfProject' , 'ApiHousekeeperController@getChangeListLogOfProject');
	
	        //获取管家刚提交的整改单
	        $api->get('getLatestChangeListOfProject' , 'ApiHousekeeperController@getLatestChangeListOfProject');
	
	        //对比管家和设计师提交的工程配置单
	        $api->get('compareDesignerAndHouserKeeperList' , 'ApiHousekeeperController@compareDesignerAndHouserKeeperList');
	
	        //获取账单
	        $api->get('getBillOfTask' , 'ApiHousekeeperController@getBillOfTask');
	
	        //获取账单(可以申请提现)
	        $api->get('getBillOfTaskWithdraw' , 'ApiHousekeeperController@getBillOfTaskWithdraw');
	
	        //获取账单(不能申请提现)
	        $api->get('getBillOfTaskWithdrawDeny' , 'ApiHousekeeperController@getBillOfTaskWithdrawDeny');
	
	        //获取账单(给最终结果给工作者)
	        $api->get('getFinalCashOutResult' , 'ApiHousekeeperController@getFinalCashOutResult');
	
	        //业主或监理驳回工程整改单
	        $api->post('rejectListChangeReport' , 'ApiDesignerController@rejectListChangeReport');
	
	        //业主或监理确认工程整改单
	        $api->post('bossSureChangeList' , 'ApiDesignerController@bossSureChangeList');
	
	        //工人端可查看订单要做的工程项目和工程量
	        $api->get('laborWorkOfferInfo' , 'ApiHousekeeperController@laborWorkOfferInfo');
	
	        //查看每一次的整改单数据(和原数据的对比)(未确认的)
	        $api->get('getChangeListNowOfProject' , 'ApiHousekeeperController@getChangeListNowOfProject');
	
	        //微信充值
	        $api->post('postCashUser', 'ApiHousekeeperController@postCashUser');
	
	        //微信回调
	        $api->post('checkSign', 'ApiHousekeeperController@checkSign');
	
	        //支付宝充值
	        $api->post('postCashUserAliPay', 'ApiHousekeeperController@postCashUserAliPay');
	
	        //支付宝回调
	        $api->post('checkSignAliPay', 'ApiHousekeeperController@checkSignAliPay');
	
	        //正常结单
	        $api->post('endProject', 'ApiHousekeeperController@endProject');
	
	        //获取已开工的任务列表
	        $api->get('underConstructionTask', 'ApiHousekeeperController@getUnderConstructionTask');
	
	        //开工后管家提交工程日志
	        $api->post('housekeeperSubProjectLog', 'ApiHousekeeperController@housekeeperSubProjectLog');
	
	        //TODO 未完善（监理或设计师未考虑好怎样获取日志） 开工后管家或业主查看工程日志
	        $api->get('constructionLog', 'ApiHousekeeperController@getConstructionLog');
	
	        //所有用户的我的直播列表
	        $api->get('broadcastList', 'ApiHousekeeperController@broadcastList');
	
	        //除去自己的直播外的其他人的工地直播
	        $api->get('broadcastListExclude', 'ApiHousekeeperController@broadcastListExclude');
	
	        //管家客诉通道(管家发起请求)
	        $api->post('broadcastListExclude', 'ApiHousekeeperController@broadcastListExclude');
	
	        //管家客诉通道(发起请求)
	        $api->post('houseKeeperApply', 'ApiHousekeeperController@houseKeeperApply');
	
	        //提现接口
	        $api->post('withdrawApplication', 'ApiHousekeeperController@withdrawApplication');
	
	        //获取设计师初步图纸接口
	        $api->get('getDesignerImg', 'ApiDesignerController@getDesignerImg');
	
	        //获取设计师深化图纸接口
	        $api->get('getDesignerDeepImg', 'ApiDesignerController@getDesignerDeepImg');
	
	        //业主在没确认之前更换约单设计师
	        $api->post('userChangeAppointWorker', 'ApiTaskController@userChangeAppointWorker');
	
	        //业主在没确认之前更换约单管家
	        $api->post('userChangeAppointHouse', 'ApiHousekeeperController@userChangeAppointHouse');
	
	        //创建约单任务(设计师)
	        $api->post('userCreateAppointTask', 'ApiTaskController@userCreateAppointTask');
	
	        //创建约单任务(设计师,方案2)
	        $api->post('userCreateAppointTaskPlanB', 'ApiTaskController@userCreateAppointTaskPlanB');
	
	        //创建约单任务(管家)
	        $api->post('userCreateHouseKeeperAppointTask', 'ApiHousekeeperController@userCreateHouseKeeperAppointTask');
	
	        //业主发起换人请求(新约单)
	        $api->post('bossApplyOrderPlanB', 'ApiTaskController@bossApplyOrderPlanB');
	
	        //1.管家提交更改项(新约单) 即小订单
	        $api->post('houseKeeperReChangeListPlanB', 'ApiTaskController@houseKeeperReChangeListPlanB');
	
	        //2.获取管家刚提交的整改单(新约单)
	        $api->get('getLatestChangeListOfProjectPlanB', 'ApiTaskController@getLatestChangeListOfProjectPlanB');
	
	        //3.获取管家刚提交的整改单里层(新约单)
	        $api->get('getChangeListDataPlanB', 'ApiTaskController@getChangeListDataPlanB');
	
	        //4.业主支付管家的小订单(新约单)
	        $api->post('bossPaySmallOrderPlanB', 'ApiTaskController@bossPaySmallOrderPlanB');
	
	        //5.管家首次提交验收(新约单)
	        $api->post('houseKeeperSubmitSmallOrder', 'ApiTaskController@houseKeeperSubmitSmallOrder');
	
	        //6.业主驳回管家提交更改项(新约单)
	        $api->post('bossRefuseSmallOrder', 'ApiTaskController@bossRefuseSmallOrder');
	
	        //6.1业主驳回后,管家修改参数再提交
	        $api->post('houseKeeperReSubmitList', 'ApiTaskController@houseKeeperReSubmitList');
	
	        //7.业主确认管家的验收请求(新约单)
	        $api->post('bossSureSmallOrder', 'ApiTaskController@bossSureSmallOrder');
	
	        //8.业主取消管家的小订单(新约单)
	        $api->post('cancelSmallOrder', 'ApiTaskController@cancelSmallOrder');
	
	        //接单中心
	        $api->get('getTasksAppoint', 'ApiTaskController@getTasksAppoint');
	
	        //约单中心(管家)
	        $api->get('getTasksAppointHouse', 'ApiHousekeeperController@getTasksAppointHouse');
	
	        //管家接受或拒绝约单任务
	        $api->post('houseKeeperReplyBoss', 'ApiHousekeeperController@houseKeeperReplyBoss');
	
	        //设计师接受或拒绝约单任务
	        $api->post('designerReplyBoss', 'ApiTaskController@designerReplyBoss');
	
	        //设计师不接单,废弃订单处理
	        $api->get('workerNotReply', 'ApiTaskController@workerNotReply');
	
	        //筛选掉用户关注列表里面,该任务已选的设计师
	        $api->get('filterDesignerOfAppoint', 'ApiTaskController@filterDesignerOfAppoint');
	
	        //清除数据
	        //$api->get('truncate', 'ApiTaskController@truncate');
	
	        //工人列表
	        $api->get('workerList', 'ApiWorkerController@getWorkerList');
	
	        //根据工种找工人
	        $api->get('workerListByWorkType', 'ApiWorkerController@workerListByWorkType');
	
	        //保存机器码
	        $api->post('saveDeviceToken', 'ApiUsersController@saveDeviceToken');
	
	        //退出登录
	        $api->post('logout', 'ApiUsersController@logout');
	
	        //获取设计师接过的单和业主选定他的单(我的订单)
	        $api->get('getDesignerTask', 'ApiTaskController@getDesignerTask');
	
	        //获取商家信息
	        $api->get('getMerchantDetails', 'ApiTaskController@getMerchantDetails');
	
	        //生成缩略图的方法
	        $api->get('createThumb', 'ApiUploadImgController@createThumb');
	
	        //消息清零
	        $api->post('messageClear', 'ApiTaskController@messageClear');
	
	        //获取用户待发送的消息
	        $api->get('getUsersWaitMessage', 'ApiTaskController@getUsersWaitMessage');
	
	        //业主发起整改单请求
	        $api->post('bossApplyOrder', 'ApiTaskController@bossApplyOrder');
	
	        //批量注册聊天室用户
	        $api->get('UserRegistEaseMob', 'ApiTaskController@UserRegistEaseMob');
	
	        //批量创建聊天室
	        $api->get('UserCreateChatRooms', 'ApiTaskController@UserCreateChatRooms');
	
	        //聊天室加人
	        $api->post('UseraddWorkToChatRoom', 'ApiTaskController@UseraddWorkToChatRoom');
	
	        //获取聊天室成员
	        $api->get('UsergetChatRoomAllMembers', 'ApiTaskController@UsergetChatRoomAllMembers');
	
	        //根据uid获取银行卡
	        $api->get('getUserBankInfoByid', 'ApiUsersController@getUserBankInfoByid');
	
	        //生成邀请码
	        $api->post('GenInvitationCode', 'ApiUsersController@GenInvitationCode');
	
	        //邀请码渲染试图
	        $api->get('registerPlatform/{code}', 'ApiUsersController@registerPlatform');
	
	        //邀请码注册
	        $api->post('postRegisterByCode', 'ApiUsersController@postRegisterByCode');
	
	        //获取线下转账的公司信息
	        $api->get('getCompanyBankInfo', 'ApiUsersController@getCompanyBankInfo');
	
	//    //启动广告单独给前端
	//    $api->get('getStartAd', 'ApiUsersController@getStartAd');
	
	        //根据管家输入的信息显示对应的价格,不能超过管家本身的星级
	        $api->get('getHousePriceByStar','ApiUsersController@getHousePriceByStar');
	
	        //一键删除所有测试数据
	        $api->get('deleteAllDataByTaskid','ApiTaskController@deleteAllDataByTaskid');
	
	        //恢复状态
	        //$api->get('changeStatusByTest','ApiTaskController@changeStatusByTest');
	
	        //根据配置单获取城市和拼音
	        $api->get('getCityByLists','ApiTaskController@getCityByLists');
	
	        //自助传图  TODO
	        $api->get('detectUploadImgByTest', 'ApiTaskController@detectUploadImgByTest');
	
	        //自助改状态  TODO
	        $api->get('changeStatusByTest', 'ApiTaskController@changeStatusByTest');
	
	        //自助改匹配工人完成
	        $api->get('LaborMatchStatusByTest', 'ApiTaskController@LaborMatchStatusByTest');


	        //根据城市id获取对应的辅材包信息
	        $api->get('getAuxDetailByCityid','ApiTaskController@getAuxDetailByCityid');
	
	        //辅材包城市列表
	        $api->get('getAuxAllCity','ApiTaskController@getAuxAllCity');
	
	        //判断有没有管家的订单
	        $api->get('judgeHouseExist','ApiTaskController@judgeHouseExist');
	
	        //拉取用户所有消息
	        $api->get('getALLPushMsg','ApiPushMsgController@getALLPushMsg');
	
	        //用户已读某一条消息
	        $api->post('userReadMsg','ApiPushMsgController@userReadMsg');
	
	        //用户已读所有消息
	        $api->post('userReadMsgAll','ApiPushMsgController@userReadMsgAll');
	
	        //删除所有消息
	        $api->post('delAllMessage','ApiPushMsgController@delAllMessage');
	
	        //删除某条消息
	        $api->post('delMessageByid','ApiPushMsgController@delMessageByid');
	
	        //获取日志(供安卓端)
	        $api->get('getConstructionLogAndroid', 'ApiHousekeeperController@getConstructionLogAndroid');
	
	
	        /******************************************************************************************************************
	         *                                                重构的接口
	         *******************************************************************************************************************/
	        // 根据订单id获取图纸
	        $api->get('getTaskImgStatus', 'ApiTaskController@getTaskImgStatus');
	
	
	
	        //  设计师 根据 task_id 获取订单详细
	        $api->get('getDesignerTaskDetail', 'ApiDesignerController@getDesignerTaskDetail');
	
	        //  管家 根据 task_id 获取订单详细
	        $api->get('getHousekeeperTaskDetail', 'ApiHousekeeperController@getHousekeeperTaskDetail');
	
	        //  监理 根据 task_id 获取订单详细
	        $api->get('getSupervisorTaskDetail', 'ApiSupervisorController@getSupervisorTaskDetail');
	
	        // 步骤-确认接口(监理确认)
	        $api->post('supervisorConfirmation', 'ApiSupervisorController@supervisorConfirmation');
	
	        //业主取消管家订单
	        $api->post('bossCancelHouseKeeperOrder', 'ApiHousekeeperController@bossCancelHouseKeeperOrder');
	
	        //生成经纬度
	        $api->get('getlat' , 'ApiHousekeeperController@getlat');
	
	
	        //获取经纬度表经纬度
	        $api->get('getCoordinate' , 'ApiUsersController@getCoordinate');

	        //获取可选的城市
	        $api->get('getScreenCity' , 'ApiUsersController@getScreenCity');
	
	        //获取主材单订单列表
	        $api->get('getPrincipalOrderList' , 'MaterialsController@getPrincipalOrderList');
	
	        //获取主材单选材列表
	        $api->get('getPrimaryPrincipal' , 'MaterialsController@getPrimaryPrincipal');
	        //获取主材单列表（被驳回时获取原提交信息，通过主材选购单详细获取）
	        $api->get('getPrincipalList' , 'MaterialsController@getPrincipalList');
	
	        //获取已确认的主材选购单
	        $api->get('getPrincipalDetail' , 'MaterialsController@getPrincipalDetail');
	
	        //获取可选择区域
	        $api->get('getUseArea' , 'MaterialsController@getUseArea');
	
	        //确认收货某项主材
	        $api->post('confirmReceive' , 'MaterialsController@confirmReceive');
	
	        //业主驳回主材选购单
	        $api->post('bossRejectMaterialList' , 'MaterialsController@bossRejectMaterialList');
	
	        //业主确认主材选购单
	        $api->post('bossSureMaterialList' , 'MaterialsController@bossSureMaterialList');
	
	        //管家提交主材选购单
	        $api->post('subMaterialList' , 'MaterialsController@subMaterialList');
	
	        //获取主材单列表头部菜单
	        $api->get('getTopMenu' , 'MaterialsController@getTopMenu');
	
	        //获取主材单历史修改记录
	        $api->get('updateHistoryList' , 'MaterialsController@updateHistoryList');
	
	        //业主驳回某个材料项的修改请求
	        $api->post('bossRejectMaterial' , 'MaterialsController@bossRejectMaterial');
	
	        //业主确认某个材料项的修改请求
	        $api->post('bossSureMaterial' , 'MaterialsController@bossSureMaterial');
	
	        //管家提出修改某一材料项申请
	        $api->post('updateMaterials' , 'MaterialsController@updateMaterials');
	
	
	        //获取报建报修地区
	        $api->get('getManageArea' , 'ApiManagerController@getManageArea');
	
	
	        //生成缩略图并覆盖原图
	        $api->post('createOriginalThumb' , 'ApiUploadImgController@createOriginalThumb');
	
	        //提交报修资料
	        $api->post('uploadRenovationData' , 'ApiManagerController@uploadRenovationData');
	
	        //获取工种列表
	        $api->get('getWorkType' , 'ApiManagerController@getWorkType');
	
	        //获取楼盘列表
	        $api->get('getBuilding' , 'ApiManagerController@getBuilding');
	
	        //删除图片
	        $api->post('deletedImage' , 'ApiManagerController@deletedImage');
	
	        //报建列表
	        $api->get('repairList' , 'ApiManagerController@repairList');
	
	        //报建详细
	        $api->get('repairDetail' , 'ApiManagerController@repairDetail');
        });

    });
    
});








