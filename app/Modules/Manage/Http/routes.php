<?php

/*
|--------------------------------------------------------------------------
| Module Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for the module.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
Route::get('/manage/login', 'Auth\AuthController@getLogin')->name('loginCreatePage');
//物管注册链接

//Route::get('/manage/reg_property', 'RepairController@reg_property')->name('reg_property');

Route::group(['middleware' => 'systemlog'], function () {
    Route::post('/manage/login', 'Auth\AuthController@postLogin')->name('loginCreate');
});
Route::get('/manage/logout', 'Auth\AuthController@getLogout')->name('logout');

Route::group(['prefix' => 'manage', 'middleware' => ['manageauth', 'RolePermission', 'systemlog']], function () {

    Route::get('/', 'IndexController@getManage')->name('backstagePage');//后台首页
    //RBAC路由
    Route::get('/addRole', 'IndexController@addRole')->name('roleCreate');
    Route::get('/addPermission', 'IndexController@addPermission')->name('permissionCreate');
    Route::get('/attachRole', 'IndexController@attachRole')->name('attachRoleCreate');
    Route::get('/attachPermission', 'IndexController@attachPermission')->name('attachPermissionCreate');
    //实名认证管理路由
    Route::get('/realnameAuthList', 'AuthController@realnameAuthList')->name('realnameAuthList');//实名认证列表
    Route::get('/realnameAuthHandle/{id}/{action}', 'AuthController@realnameAuthHandle')->name('realnameAuthHandle');//实名认证处理
    Route::get('/realnameAuth/{id}', 'AuthController@realnameAuth')->name('realnameAuth');//实名认证详情

    //城市站路由
    Route::get('/cityStationUser', 'UserController@cityStationUser')->name('cityStationUser');//城市站用户管理



    //支付宝认证管理路由
    Route::get('/alipayAuthList', 'AuthController@alipayAuthList')->name('alipayAuthList');//支付宝认证列表
    Route::get('/alipayAuthHandle/{id}/{action}', 'AuthController@alipayAuthHandle')->name('alipayAuthHandle');//支付宝认证处理
    Route::post('/alipayAuthMultiHandle', 'AuthController@alipayAuthMultiHandle')->name('alipayAuthMultiHandle');//支付宝认证批量处理
    Route::get('alipayAuth/{id}', 'AuthController@getAlipayAuth')->name('alipayAuth');//支付宝认证详情
    Route::post('alipayAuthPay', 'AuthController@alipayAuthPay')->name('alipayAuthPayCreate');//支付宝后台打款

    //银行认证管理路由
    Route::get('/bankAuthList', 'AuthController@bankAuthList')->name('bankAuthList');//银行认证列表
    Route::get('/bankAuthHandle/{id}/{action}', 'AuthController@bankAuthHandle')->name('bankAuthHandle');//银行认证处理
    Route::post('/bankAuthMultiHandle', 'AuthController@bankAuthMultiHandle')->name('bankAuthMultiHandle');//银行认证批量审核
    Route::get('/bankAuth/{id}', 'AuthController@getBankAuth')->name('bankAuth');//银行认证列表
    Route::post('bankAuthPay', 'AuthController@bankAuthPay')->name('bankAuthPayCreate');//银行后台支付

    //任务管理路由
    Route::get('/taskList', 'TaskController@taskList')->name('taskList');//任务列表

    Route::get('/cityStationTask', 'TaskController@cityStationTask')->name('cityStationTask');//任务列表

    Route::get('/taskHandle/{id}/{action}', 'TaskController@taskHandle')->name('taskUpdate');//任务处理
    Route::post('/taskMultiHandle', 'TaskController@taskMultiHandle')->name('taskMultiUpdate');//任务批量处理
    Route::get('/taskDetail/{id}', 'TaskController@taskDetail')->name('taskDetail');//任务详情
    Route::post('/taskDetailUpdate', 'TaskController@taskDetailUpdate')->name('taskDetailUpdate');//任务详情提交
    Route::get('/taskMassageDelete/{id}', 'TaskController@taskMassageDelete')->name('taskMassageDelete');//删除任务留言

    //财务管理路由
    Route::get('/financeList', 'FinanceController@financeList')->name('financeList');//网站流水列表
    Route::get('/financeListExport', 'FinanceController@financeListExport')->name('financeListExportCreate');//导出网站流水记录
    Route::get('/userFinanceListExport/{param}', 'FinanceController@userFinanceListExport')->name('userFinanceListExportCreate');//用户流水导出
    Route::get('/financeStatement', 'FinanceController@financeStatement')->name('financeStatementList');//财务报表
    Route::get('/financeRecharge', 'FinanceController@financeRecharge')->name('financeRechargeList');//财务报表-充值记录
    Route::get('/financeRechargeExport/{param}', 'FinanceController@financeRechargeExport')->name('financeRechargeExportCreate');//充值记录导出
    Route::get('/financeWithdraw', 'FinanceController@financeWithdraw')->name('financeWithdrawList');//财务报表-提现记录
    Route::get('/financeWithdrawExport/{param}', 'FinanceController@financeWithdrawExport')->name('financeWithdrawExportCreate');//提现记录导出
    Route::get('/financeProfit', 'FinanceController@financeProfit')->name('financeProfitList');//财务报表-利润统计

    //地区管理路由
    Route::get('/area', 'AreaController@areaList')->name('areaList');//地区管理列表
    Route::post('/areaCreate', 'AreaController@areaCreate')->name('areaCreate');//地区管理添加
    Route::get('/areaDelete/{id}', 'AreaController@areaDelete')->name('areaDelete');//地区管理删除
    Route::get('/ajaxcity', 'AreaController@ajaxCity')->name('ajaxCity');//地区管理筛选（城市）
    Route::get('/ajaxarea', 'AreaController@ajaxArea')->name('ajaxArea');//地区管理筛选（地区）


    //行业管理路由
    Route::get('/industry', 'IndustryController@industryList')->name('industryList');//行业管理列表
    Route::post('/industryCreate', 'IndustryController@industryCreate')->name('industryCreate');//行业管理提交
    Route::get('/industryDelete/{id}', 'IndustryController@industryDelete')->name('industryDelete');//行业管理删除
    Route::get('/ajaxSecond', 'IndustryController@ajaxSecond')->name('ajaxSecond');//行业管理筛选（城市）
    Route::get('/ajaxThird', 'IndustryController@ajaxThird')->name('ajaxThird');//行业管理筛选（地区）
    Route::get('/tasktemplate/{id}', 'IndustryController@taskTemplates')->name('taskTemplates');//行业实例页面
    Route::post('/templateCreate', 'IndustryController@templateCreate')->name('templateCreate');//行业实例添加控制器
    Route::get('/industryInfo/{id}', 'IndustryController@industryInfo')->name('industryDetail');//编辑行业分类图标
    Route::post('/industryInfo', 'IndustryController@postIndustryInfo')->name('postIndustryDetail');//编辑行业分类图标

    Route::get('/userFinance', 'FinanceController@userFinance')->name('userFinanceCreate');//用户流水记录
    Route::get('/cashoutList', 'FinanceController@cashoutList')->name('cashoutList');//提现审核列表
    Route::get('/cashoutHandle/{id}/{action}', 'FinanceController@cashoutHandle')->name('cashoutUpdate');//提现审核处理
    Route::get('cashoutInfo/{id}', 'FinanceController@cashoutInfo')->name('cashoutDetail');//提现记录详情

    Route::get('userRecharge', 'FinanceController@getUserRecharge')->name('userRechargePage');//后台充值视图
    Route::post('userRecharge', 'FinanceController@postUserRecharge')->name('userRechargeUpdate');//后台用户充值
    Route::get('rechargeList', 'FinanceController@rechargeList')->name('rechargeList');// 用户充值订单列表
    Route::get('confirmRechargeOrder/{order}', 'FinanceController@confirmRechargeOrder')->name('confirmRechargeOrder');//后台确认订单充值

    //全局配置
    Route::get('/config', 'ConfigController@getConfigBasic')->name('configDetail');//
    Route::get('/config/basic', 'ConfigController@getConfigBasic')->name('basicConfigDetail');//基本配置
    Route::post('/config/basic', 'ConfigController@saveConfigBasic')->name('configBasicUpdate');//保存基本配置
    Route::get('/config/seo', 'ConfigController@getConfigSEO')->name('seoConfigDetail');//seo配置
    Route::post('/config/seo', 'ConfigController@saveConfigSEO')->name('configSeoUpdate');//保存seo配置
    Route::get('/config/nav', 'ConfigController@getConfigNav')->name('navConfigDetail');//获取导航配置
    Route::post('/config/nav', 'ConfigController@postConfigNav')->name('configNavCreate');//新增导航
    Route::get('/config/nav/{id}/delete', 'ConfigController@deleteConfigNav')->name('configNavDelete');//删除导航
    Route::get('/config/attachment', 'ConfigController@getAttachmentConfig')->name('attachmentConfigDetail');//附件配置
    Route::post('/config/attachment', 'ConfigController@postAttachmentConfig')->name('attachmentConfigCreate');//保存附件配置信息

    Route::get('/config/site', 'ConfigController@getConfigSite')->name('siteConfigDetail');//站点配置视图
    Route::post('/config/site', 'ConfigController@saveConfigSite')->name('configSiteUpdate');//保存站点配置
    Route::get('/config/email', 'ConfigController@getConfigEmail')->name('emailConfigDetail');//邮箱配置视图
    Route::post('/config/email', 'ConfigController@saveConfigEmail')->name('configEmailUpdate');//保存邮箱配置

    Route::post('/config/sendEmail', 'ConfigController@sendEmail')->name('sendEmail');//发送测试邮件


    //任务配置
    Route::get('/taskConfig/{id}', 'TaskConfigController@index')->name('taskConfigPage');//任务配置页面
    Route::post('/taskConfigUpdate', 'TaskConfigController@update')->name('taskConfigUpdate');//任务配置提交
    Route::get('/ajaxUpdateSys', 'TaskConfigController@ajaxUpdateSys')->name('ajaxUpdateSys');//任务配置系统辅助流程开关
    Route::post('/baseConfig', 'TaskConfigController@baseConfig')->name('baseConfigCreate');//任务配置基本配置


    //接口管理
    Route::get('payConfig', 'InterfaceController@getPayConfig')->name('payConfigDetail');//支付配置
    Route::post('payConfig', 'InterfaceController@postPayConfig')->name('payConfigUpdate');//保存支付配置
    Route::get('thirdPay', 'InterfaceController@getThirdPay')->name('thirdPayDetail');//第三方支付配置列表
    Route::get('thirdPayHandle/{id}/{action}', 'InterfaceController@thirdPayHandle')->name('thirdPayStatusUpdate');//启用/禁用支付接口
    Route::get('thirdPayEdit/{id}', 'InterfaceController@getThirdPayEdit')->name('thirdPayUpdatePage');//配置支付接口视图
    Route::post('thirdPayEdit', 'InterfaceController@postThirdPayEdit')->name('thirdPayUpdate');//保存支付配置
    Route::get('advanceConfig', 'InterfaceController@getAdvanceConfig')->name('advanceConfigDetail');//预约金设置列表
    Route::get('advanceConfigUpdate/{id}', 'InterfaceController@advanceConfigUpdate')->name('advanceConfigUpdatePage');//预约金设置视图
    Route::post('advanceConfigUpdate', 'InterfaceController@advanceConfigEdit')->name('advanceConfigUpdate');//预约金设置视图
    Route::get('designConfig', 'InterfaceController@getDesignConfig')->name('designConfigDetail');//设计费用设置
    Route::post('designConfig', 'InterfaceController@designConfig')->name('designConfigUpdate');//设计费用设置
    Route::get('orderConfig', 'InterfaceController@getOrderConfig')->name('orderConfigDetail');//接单设置列表
    Route::post('orderConfig', 'InterfaceController@orderConfig')->name('orderConfigUpdate');//接单设置更新

    //第三方登陆
    Route::get('thirdLogin', 'InterfaceController@getThirdLogin')->name('thirdLoginPage');//第三方登录授权配置
    Route::post('thirdLogin', 'InterfaceController@postThirdLogin')->name('thirdLoginCreate');//保存第三方登录配置

    //资讯中心路由
    Route::get('/article/{upID}', 'ArticleController@articleList')->name('articleList'); //资讯中心文章列表
    Route::get('/articleFooter/{upID}', 'ArticleController@articleList')->name('articleFooterList'); //页脚配置文章列表
    Route::get('/addArticle/{upID}', 'ArticleController@addArticle')->name('articleCreatePage'); //添加资讯文章视图
    Route::get('/addArticleFooter/{upID}', 'ArticleController@addArticle')->name('articleFooterCreatePage'); //添加页脚文章视图
    Route::post('/addArticle', 'ArticleController@postArticle')->name('articleCreate'); //添加文章
    Route::get('/articleDelete/{id}/{upID}', 'ArticleController@articleDelete')->name('articleDelete'); //删除文章
    Route::get('/editArticle/{id}/{upID}', 'ArticleController@editArticle')->name('articleUpdatePage'); //编辑资讯文章视图
    Route::get('/editArticleFooter/{id}/{upID}', 'ArticleController@editArticle')->name('articleFooterUpdatePage'); //编辑页脚文章视图
    Route::post('/editArticle', 'ArticleController@postEditArticle')->name('articleUpdate'); //编辑文章
    Route::post('/allDelete', 'ArticleController@allDelete')->name('allDelete'); //批量删除文章

    //资讯中心分类路由
    Route::get('/categoryList/{upID}', 'ArticleCategoryController@categoryList')->name('categoryList'); //资讯文章分类列表
    Route::get('/categoryFooterList/{upID}', 'ArticleCategoryController@categoryList')->name('categoryFooterList'); //页脚文章分类列表
    Route::get('/categoryDelete/{id}/{upID}', 'ArticleCategoryController@categoryDelete')->name('categoryDelete'); //删除文章分类
    Route::get('/categoryAdd/{upID}', 'ArticleCategoryController@categoryAdd')->name('categoryCreatePage'); //添加资讯文章分类视图
    Route::post('/categoryAdd', 'ArticleCategoryController@postCategory')->name('categoryCreate');//添加文章分类
    Route::get('/categoryEdit/{id}/{upID}', 'ArticleCategoryController@categoryEdit')->name('categoryUpdatePage');//编辑资讯文章分类视图
    Route::post('/categoryEdit', 'ArticleCategoryController@postEditCategory')->name('categoryUpdate');//编辑文章分类
    Route::post('/categoryAllDelete', 'ArticleCategoryController@cateAllDelete')->name('categoryAllDelete');//批量删除文章分类
    Route::get('/getChildCateList/{id}', 'ArticleCategoryController@getChildCateList')->name('getChildCateList'); //页脚文章分类列表
    Route::get('/categoryFooterAdd/{upID}', 'ArticleCategoryController@categoryAdd')->name('categoryFooterCreatePage'); //添加页脚文章分类视图
    Route::get('/categoryFooterEdit/{id}/{upID}', 'ArticleCategoryController@categoryEdit')->name('categoryFooterUpdatePage');//编辑页脚文章分类视图
    Route::get('/add/{upID}', 'ArticleCategoryController@add')->name('addCategory');//进入新建视图 判断资讯或页脚
    Route::get('/edit/{id}/{upID}', 'ArticleCategoryController@edit')->name('editCategory');//进入编辑视图 判断资讯或页脚


    //后台成功案例
    Route::get('/successCaseList', 'SuccessCaseController@successCaseList')->name('successCaseList');//成功案例列表
    Route::get('/successCaseAdd', 'SuccessCaseController@create')->name('successCaseCreatePage');//成功案例添加页面
    Route::post('/successCaseUpdate', 'SuccessCaseController@update')->name('successCaseCreate');//成功案例提交页面
    Route::get('/successCaseDel/{id}', 'SuccessCaseController@successCaseDel')->name('successCaseDel');//成功案例删除
    Route::post('/ajaxGetSecondCate', 'SuccessCaseController@ajaxGetSecondCate')->name('ajaxGetSecondCate');//成功案例提交页面

    //自定义导航
    Route::get('/navList', 'NavController@navList')->name('navList'); //自定义导航列表
    Route::get('/addNav', 'NavController@addNav')->name('navCreatePage');  //添加自定义导航视图
    Route::post('/addNav', 'NavController@postAddNav')->name('navCreate'); //添加自定义导航
    Route::get('/editNav/{id}', 'NavController@editNav')->name('navUpdatePage'); //编辑自定义导航视图
    Route::post('/editNav', 'NavController@postEditNav')->name('navUpdate'); //编辑自定义导航
    Route::get('/deleteNav/{id}', 'NavController@deleteNav')->name('navDelete');//删除自定义导航
    Route::get('/isFirst/{id}', 'NavController@isFirst')->name('isFirst'); //设为首页

    //用户管理
    Route::get('/userList', 'UserController@getUserList')->name('userList');//普通用户列表
    Route::get('userListExport', 'UserController@userListExport')->name('userListExport');//导出用户
//    Route::get('/generalList', 'UserController@getUserList')->name('generalList');//普通用户列表
    Route::get('/handleUser/{uid}/{action}', 'UserController@handleUser')->name('userStatusUpdate');//用户处理
    Route::get('/userAdd', 'UserController@getUserAdd')->name('userCreatePage');//添加用户视图
    Route::post('/userAdd', 'UserController@postUserAdd')->name('userCreate');//添加用户
    Route::post('checkUserName', 'UserController@checkUserName')->name('checkUserName');//检测用户名是否存在
    Route::post('checkEmail', 'UserController@checkEmail')->name('checkEmail');//检测邮箱
    Route::get('/userEdit/{uid}', 'UserController@getUserEdit')->name('userUpdatePage');//用户详情
//    Route::get('/generalEdit/{uid}', 'UserController@getUserEdit')->name('generalEdit');//用户详情
//    Route::post('savegeneral', 'UserController@savegeneral')->name('savegeneral');//保存或者修改
    Route::post('/userEdit', 'UserController@postUserEdit')->name('userUpdate');//用户详情更新
    Route::get('/managerList', 'UserController@getManagerList')->name('managerList');//系统用户列表
    Route::get('/handleManage/{uid}/{action}', 'UserController@handleManage')->name('userStatusUpdate');//系统用户处理
    Route::get('/managerAdd', 'UserController@managerAdd')->name('managerCreatePage');//系统用户添加视图
    Route::post('/managerAdd', 'UserController@postManagerAdd')->name('managerCreate');//系统用户添加
    Route::post('checkManageName', 'UserController@checkManageName')->name('checkManageName');//检测系统用户名
    Route::post('checkManageEmail', 'UserController@checkManageEmail')->name('checkManageEmail');//检测系统用户邮箱
    Route::get('/managerDetail/{id}', 'UserController@managerDetail')->name('managerDetail');//系统用户详情
    Route::post('/managerDetail', 'UserController@postManagerDetail')->name('managerDetailUpdate');//更新系统用户
    Route::get('/managerDel/{id}', 'UserController@managerDel')->name('managerDelete');//系统用户删除
    Route::post('/managerDeleteAll', 'UserController@postManagerDeleteAll')->name('managerAllDelete');//系统用户批量删除

    Route::get('/rolesList', 'UserController@getRolesList')->name('rolesList');//用户组列表
    Route::get('/rolesAdd', 'UserController@getRolesAdd')->name('rolesCreatePage');//用户组添加视图
    Route::post('/rolesAdd', 'UserController@postRolesAdd')->name('rolesCreate');//用户组添加
    Route::get('/rolesDel/{id}', 'UserController@getRolesDel')->name('rolesDelete');//用户组删除
    Route::get('/rolesDetail/{id}', 'UserController@getRolesDetail')->name('rolesDetail');//用户组详情
    Route::post('/rolesDetail', 'UserController@postRolesDetail')->name('rolesDetailUpdate');//用户组更新

    Route::get('/permissionsList', 'UserController@getPermissionsList')->name('permissionsList');//权限列表
    Route::get('/permissionsAdd', 'UserController@getPermissionsAdd')->name('permissionsCreatePage');//权限添加视图
    Route::post('/permissionsAdd', 'UserController@postPermissionsAdd')->name('permissionsCreate');//权限添加
    Route::get('/permissionsDel/{id}', 'UserController@getPermissionsDel')->name('permissionsDelete');//删除权限
    Route::get('/permissionsDetail/{id}', 'UserController@getPermissionsDetail')->name('permissionsDetail');//权限详情
    Route::post('/permissionsDetail', 'UserController@postPermissionsDetail')->name('postPermissionsDetailUpdate');//权限更新

    //后台权限添加
    Route::get('/menuList/{id}/{level}', 'MenuController@getMenuList')->name('getMenuList');
    Route::get('/addMenu/{id?}', 'MenuController@addMenu')->name('addMenu');
    Route::post('/menuCreate', 'MenuController@menuCreate')->name('menuCreate');
    Route::get('/menuDelete/{id}', 'MenuController@menuDelete')->name('menuDelete');
    Route::get('/menuUpdate/{id}', 'MenuController@menuUpdate')->name('menuUpdate');
    Route::post('/updateMenu', 'MenuController@updateMenu')->name('updateMenu');
    //用户举报

    Route::get('/reportList', 'TaskReportController@reportList')->name('reportList');//用户举报列表
    Route::get('/reportDelet/{id}', 'TaskReportController@reportDelet')->name('reportDelete');//用户举报单个删除
    Route::post('/reportDeletGroup', 'TaskReportController@reportDeletGroup')->name('reportGroupDelete');//用户举报批量删除
    Route::get('/reportDetail/{id}', 'TaskReportController@reportDetail')->name('reportDetail');//用户举报详情
    Route::post('/handleReport', 'TaskReportController@handleReport')->name('reportUpdate');//用户举报处理

    //交易维权
    Route::get('/rightsList', 'TaskRightsController@rightsList')->name('rightsList');//交易维权列表
    Route::get('/rightsDelet/{id}', 'TaskRightsController@rightsDelet')->name('rightsDelete');//交易维权单个删除
    Route::post('/rightsDeletGroup', 'TaskRightsController@rightsDeletGroup')->name('rightsGroupDelete');//交易维权批量删除
    Route::get('/rightsDetail/{id}', 'TaskRightsController@rightsDetail')->name('rightsDetail');//交易维权详情
    Route::post('/handleRights', 'TaskRightsController@handleRights')->name('handleRightsCreate');//交易维权处理

    //增值工具
    Route::get('/serviceList', 'ServiceController@serviceList')->name('adServiceList'); //增值工具列表
    Route::get('/addService', 'ServiceController@addService')->name('addServiceCreatePage'); //添加增值工具视图
    Route::post('/addService', 'ServiceController@postAddService')->name('addServiceCreate');//添加增值工具
    Route::get('/editService/{id}', 'ServiceController@editService')->name('addServiceUpdatePage');//编辑增值工具视图
    Route::post('/postEditService', 'ServiceController@postEditService')->name('addServiceUpdate');//编辑增值工具
    Route::get('/deleteService/{id}', 'ServiceController@deleteService')->name('addServiceDelete');//删除增值工具
    Route::get('/serviceBuy', 'ServiceController@serviceBuy')->name('serviceBuyList'); //增值工具购买列表

    //友情链接
    Route::get('/link', 'LinkController@linkList')->name('linkList');//友情链接列表
    Route::post('/addlink', 'LinkController@postAdd')->name('linkCreate');//友情链接添加
    Route::get('/editlink/{id}', 'LinkController@getEdit')->name('linkUpdatePage');//友情链接详情
    Route::get('/deletelink/{id}', 'LinkController@getDeleteLink')->name('linkDelete');//友情链接删除
    Route::post('/allDeleteLink', 'LinkController@allDeleteLink')->name('allLinkDelete');//友情链接批量删除
    Route::get('/handleLink/{id}/{action}', 'LinkController@handleLink')->name('linkStatusUpdate');//友情链接处理
    Route::post('/updatelink/{id}', 'LinkController@postUpdateLink')->name('linkUpdate');//友情链接更新


    //投诉建议
    Route::get('/feedbackList', 'FeedbackController@listInfo')->name('feedbackList');//查看投诉建议列表信息
    Route::get('/feedbackDetail/{id}', 'FeedbackController@feedbackDetail')->name('feedbackDetail');//查看投诉建议详情
    Route::get('/feedbackReplay/{id}', 'FeedbackController@feedbackReplay')->name('feedbackReplayUpdate');//回复某个投诉建议
    Route::get('/deleteFeedback/{id}', 'FeedbackController@deletefeedback')->name('feedbackDelete');//删除某个投诉建议
    Route::get('/feedbackUpdate', 'FeedbackController@feedbackUpdate')->name('feedbackUpdate');//修改某个投诉建议

    //热词管理
    Route::get('/hotwordsList', 'HotwordsController@hotwordsInfo')->name('hotwordsList');//热词列表
    Route::post('/hotwordsCreate', 'HotwordsController@hotwordsCreate')->name('hotwordsCreate');//添加热词
    Route::get('/listorderUpdate', 'HotwordsController@listorderUpdate')->name('listorderUpdate');//热词排序修改
    Route::get('/hotwordsDelete/{id}', 'HotwordsController@hotwordsDelete')->name('hotwordsDelete');//删除热词信息
    Route::get('/hotwordsMulDelte', 'HotwordsController@hotwordsMulDelte')->name('hotwordsMulDelete');//批量删除热词信息

    //站长工具
    Route::get('attachmentList', 'ToolController@getAttachmentList')->name('attachmentList');//附件管理列表
    Route::get('attachmentDel/{id}', 'ToolController@attachmentDel')->name('attachmentDelete');//附件删除处理


    //短信模板
    Route::get('/messageList', 'MessageController@messageList')->name('messageList');//模板列表
    Route::get('/editMessage/{id}', 'MessageController@editMessage')->name('messageUpdatePage'); //编辑模版视图
    Route::post('/editMessage', 'MessageController@postEditMessage')->name('messageUpdate'); //编辑模版
    Route::get('/changeStatus/{id}/{isName}/{status}', 'MessageController@changeStatus')->name('messageStatusUpdate'); //改变模版状态

    //系统日志
    Route::get('/systemLogList', 'SystemLogController@systemLogList')->name('systemLogList');//系统日志列表
    Route::get('/systemLogDelete/{id}', 'SystemLogController@systemLogDelete')->name('systemLogDelete');//删除某个系统日志信息
    Route::get('/systemLogDeleteAll', 'SystemLogController@systemLogDeleteAll')->name('systemLogDeleteAll');//清空日志
    Route::post('/systemLogMulDelete', 'SystemLogController@systemLogMulDelete')->name('systemLogMulDelete');//批量删除

    //用户互评
    Route::get('/getCommentList', 'TaskCommentController@getCommentList')->name('commentList');//用户互评列表页面
    Route::get('/commentDel/{id}', 'TaskCommentController@commentDel')->name('commentDelete');//用户互评删除按钮

    //协议管理
    Route::get('/agreementList', 'AgreementController@agreementList')->name('agreementList'); //协议列表
    Route::get('/addAgreement', 'AgreementController@addAgreement')->name('agreementCreatePage');//添加协议视图
    Route::post('/addAgreement', 'AgreementController@postAddAgreement')->name('agreementCreate');//添加协议
    Route::get('/editAgreement/{id}', 'AgreementController@editAgreement')->name('agreementUpdatePage');//编辑协议视图
    Route::post('/editAgreement', 'AgreementController@postEditAgreement')->name('agreementUpdate');//编辑协议
    Route::get('/deleteAgreement/{id}', 'AgreementController@deleteAgreement')->name('agreementDelete');//删除协议

    //模板管理
    Route::get('/skin', 'AgreementController@skin')->name('manageSkin');//删除协议
    Route::get('/skinSet/{number}', 'AgreementController@skinSet')->name('skinSet');//删除协议
    //关于我们
    Route::get('/aboutUs', 'ConfigController@aboutUs')->name('aboutUs');

    //雇佣管理
    Route::get('/employConfig', 'EmployController@employConfig')->name('employConfig');//雇佣配置
    Route::get('/employList', 'EmployController@employList')->name('employList');//雇佣列表
    Route::get('/employEdit/{id}', 'EmployController@employEdit')->name('employEdit');//雇佣编辑页面
    Route::post('/employUpdate', 'EmployController@employUpdate')->name('employUpdate');//雇佣修改控制器
    Route::get('/employDelete/{id}', 'EmployController@employDelete')->name('employDelete');//删除雇佣数据
    Route::get('/download/{id}', 'EmployController@download')->name('download');//下载附件
    Route::get('/employConfig', 'EmployController@employConfig')->name('employConfig');//配置雇佣
    Route::post('/configUpdate', 'EmployController@configUpdate')->name('configUpdate');//雇佣配置提交

    //企业认证管理路由
    Route::get('/enterpriseAuthList', 'AuthController@enterpriseAuthList')->name('enterpriseAuthList');//企业认证列表
    Route::get('/enterpriseAuthHandle/{id}/{action}', 'AuthController@enterpriseAuthHandle')->name('enterpriseAuthHandle');//企业认证处理
    Route::get('/enterpriseAuth/{id}', 'AuthController@enterpriseAuth')->name('enterpriseAuth');//企业认证详情
    Route::post('/allEnterprisePass', 'AuthController@allEnterprisePass')->name('allEnterprisePass');//企业认证批量通过
    Route::post('/allEnterpriseDeny', 'AuthController@allEnterpriseDeny')->name('allEnterpriseDeny');//企业认证批量失败

    //店铺管理路由
    Route::get('/shopList', 'ShopController@shopList')->name('shopList');//店铺列表
    Route::get('/shopInfo/{id}', 'ShopController@shopInfo')->name('shopInfo');//店铺详情
    Route::post('/updateShopInfo', 'ShopController@updateShopInfo')->name('updateShopInfo');//后台修改店铺详情
    Route::get('/openShop/{id}', 'ShopController@openShop')->name('openShop');//开启店铺
    Route::get('/closeShop/{id}', 'ShopController@closeShop')->name('closeShop');//关闭店铺
    Route::get('/recommendShop/{id}', 'ShopController@recommendShop')->name('recommendShop');//推荐店铺
    Route::get('/removeRecommendShop/{id}', 'ShopController@removeRecommendShop')->name('removeRecommendShop');//取消推荐店铺
    Route::post('/allOpenShop', 'ShopController@allOpenShop')->name('allOpenShop');//批量开启店铺
    Route::post('/allCloseShop', 'ShopController@allCloseShop')->name('allCloseShop');//批量关闭店铺

    Route::get('/shopConfig', 'ShopController@shopConfig')->name('shopConfig');//店铺配置视图
    Route::post('/postShopConfig', 'ShopController@postShopConfig')->name('postShopConfig');//保存店铺配置

    Route::get('/goodsList', 'GoodsController@goodsList')->name('goodsList');//商品列表
    Route::get('/goodsInfo/{id}', 'GoodsController@goodsInfo')->name('goodsInfo');//商品详情
    Route::get('/goodsComment/{id}', 'GoodsController@goodsComment')->name('goodsComment');//商品详情
    Route::post('/saveGoodsInfo', 'GoodsController@saveGoodsInfo')->name('saveGoodsInfo');//保存商品信息
    Route::post('/changeGoodsStatus', 'GoodsController@changeGoodsStatus')->name('changeGoodsStatus');//修改商品状态
    Route::post('/checkGoodsDeny', 'GoodsController@checkGoodsDeny')->name('checkGoodsDeny');//商品审核失败
    Route::post('/ajaxGetSecondCate', 'GoodsController@ajaxGetSecondCate')->name('ajaxGetSecondCate');//获取二级行业分类

    Route::get('/goodsConfig', 'GoodsController@goodsConfig')->name('goodsConfig');//商品流程配置视图
    Route::post('/postGoodsConfig', 'GoodsController@postGoodsConfig')->name('postGoodsConfig');//保存商品流程配置

    Route::get('/ShopRightsList', 'ShopController@rightsList')->name('ShopRightsList');//店铺维权列表
    Route::get('/shopRightsInfo/{id}', 'ShopController@shopRightsInfo')->name('shopRightsInfo');//店铺维权详情
    Route::post('/download', 'ShopController@download')->name('download');//下载附件
    Route::get('/ShopRightsSuccess/{id}', 'ShopController@ShopRightsSuccess')->name('ShopRightsSuccess');//处理维权成功
    Route::post('/serviceRightsSuccess', 'ShopController@serviceRightsSuccess')->name('serviceRightsSuccess');//处理雇佣维权成功
    Route::get('/ShopRightsFailure/{id}', 'ShopController@ShopRightsFailure')->name('ShopRightsFailure');//处理维权失败
    Route::get('/serviceRightsFailure/{id}', 'ShopController@serviceRightsFailure')->name('serviceRightsFailure');//处理维权失败
    Route::get('/deleteShopRights/{id}', 'ShopController@deleteShopRights')->name('deleteShopRights');//删除已经处理的维权

    Route::get('/shopOrderList', 'ShopOrderController@orderList')->name('shopOrderList');//店铺商品订单列表
    Route::get('/shopOrderInfo/{id}', 'ShopOrderController@shopOrderInfo')->name('shopOrderInfo');//店铺商品订单详情

    Route::get('/goodsServiceList', 'GoodsServiceController@goodsServiceList')->name('goodsServiceList');//店铺服务列表
    Route::get('/serviceOrderList', 'GoodsServiceController@serviceOrderList')->name('serviceOrderList');//店铺订单列表
    Route::get('/serviceOrderInfo/{id}', 'GoodsServiceController@serviceOrderInfo')->name('serviceOrderInfo');//店铺订单详情
    Route::get('/serviceConfig', 'GoodsServiceController@serviceConfig')->name('serviceConfig');//店铺流程配置
    Route::get('/serviceInfo/{id}', 'GoodsServiceController@serviceInfo')->name('serviceInfo');//店铺流程配置
    Route::post('/serviceConfigUpdate', 'GoodsServiceController@serviceConfigUpdate')->name('serviceConfigUpdate');//店铺流程配置提交
    Route::get('/serviceComments/{id}', 'GoodsServiceController@serviceComments')->name('serviceComments');//店铺流程配置
    Route::post('/saveServiceInfo', 'GoodsServiceController@saveServiceInfo')->name('saveServiceInfo');//店铺流程配置
    Route::get('/checkServiceDeny', 'GoodsServiceController@checkServiceDeny')->name('checkServiceDeny');//店铺服务审核失败
    Route::get('/changeServiceStatus', 'GoodsServiceController@changeServiceStatus')->name('changeServiceStatus');//店铺服务状态修改
    Route::get('/serviceOrderEdit/{id}', 'GoodsServiceController@serviceOrderEdit')->name('serviceOrderEdit');//服务订单修改
    Route::post('/serviceOrderUpdate', 'GoodsServiceController@serviceOrderUpdate')->name('serviceOrderUpdate');//服务订单修改提交


    Route::get('/questionList', 'QuestionController@getList')->name('questionList');//问答列表
    Route::get('/verify/{id}/{status}', 'QuestionController@verify')->name('verify');//问答验证
    Route::get('/getDetail/{id}', 'QuestionController@getDetail')->name('getDetail');//问答详情
    Route::post('/postDetail', 'QuestionController@postDetail')->name('postDetail');//问答详情修改
    Route::get('/getDetailAnswer/{id}', 'QuestionController@getDetailAnswer')->name('getDetailAnswer');//问答回答
    Route::get('/questionConfig', 'QuestionController@getConfig')->name('questionConfig');//问答配置
    Route::get('/postConfig', 'QuestionController@postConfig')->name('postConfig');//问答配置修改
    Route::get('/ajaxCategory', 'QuestionController@ajaxCategory')->name('ajaxCategory');//问答类别切换
    Route::get('/questionDelete/{id}', 'QuestionController@questionDelete')->name('questionDelete');//问答类别切换

    //收费设置
    Route::get('charge', 'SeetingController@getCharge')->name('getCharge'); //收费单设置
    Route::get('addCharge/{type}/{pid?}', 'SeetingController@addCharge')->name('addCharge'); //添加收费单
    Route::post('saveCharge', 'SeetingController@saveCharge')->name('saveCharge'); //保存收费单
    Route::get('editCharge/{id}', 'SeetingController@editCharge')->name('editCharge'); //编辑收费单
    Route::post('updateCharge', 'SeetingController@updateCharge')->name('updateCharge'); //更新收费单
    Route::get('deleteCharge/{id}', 'SeetingController@deleteCharge')->name('deleteCharge'); //删除收费单

    Route::get('project', 'SeetingController@getProject')->name('getProject'); //工程设置
    Route::get('addProject/{id?}', 'SeetingController@addProject')->name('addProject'); //添加工程
    Route::post('saveProject', 'SeetingController@saveProject')->name('saveProject'); //保存工程
    Route::get('editProject/{id}', 'SeetingController@editProject')->name('editProject'); //编辑工程
    Route::post('updateProject', 'SeetingController@updateProject')->name('updateProject'); //更新工程
    Route::get('deleteProject/{id}', 'SeetingController@deleteProject')->name('deleteProject'); //删除工程

    //人员设置
    Route::get('service', 'LevelController@getService')->name('getService');
    Route::get('worker/{id?}', 'LevelController@getWorker')->name('getWorker');
    Route::get('addWorker', 'LevelController@addWorker')->name('addWorker');
    Route::post('saveWorker', 'LevelController@saveWorker')->name('saveWorker');
    Route::get('deleteWorker/{id}', 'LevelController@deleteWorker')->name('deleteWorker');
    Route::get('staffing/{id}', 'LevelController@getStaffing')->name('staffing');
    Route::post('staffing', 'LevelController@saveStaffing')->name('staffing');

    Route::get('explain', 'ExplainController@getExplain')->name('getExplain'); //说明设置
    Route::get('addExplain', 'ExplainController@addExplain')->name('addExplain'); //添加说明设置
    Route::post('saveExplain', 'ExplainController@saveExplain')->name('saveExplain'); //添加说明设置
    Route::get('explainDetail/{id}', 'ExplainController@explainDetail')->name('explainDetail'); //添加说明设置
    Route::get('editExplain/{id}', 'ExplainController@editExplain')->name('editExplain'); //编辑说明设置
    Route::get('deleteExplain/{id}', 'ExplainController@deleteExplain')->name('deleteExplain'); //删除说明设置


    //订单上传图纸路由
    Route::get('/upload', 'UploadController@uploadList')->name('uploadList');//上传图纸列表

    Route::get('space', 'SpaceController@getSpace')->name('space'); //空间管理
    Route::get('spaceDelete', 'SpaceController@spaceDelete')->name('spaceDelete'); //空间管理
    Route::post('spaceCreate', 'SpaceController@spaceCreate')->name('spaceCreate'); //空间管理

    Route::get('/reply', 'replyController@replyList')->name('replyList'); //回复管理
    Route::get('/delreply/{id}', 'replyController@delreply')->name('delreply'); //删除回复
    Route::get('upreply/{id}', 'replyController@upreply')->name('upreply'); //编辑回复内容
    Route::post('savereply', 'replyController@savereply')->name('savereply'); //添加回复内容



    Route::get('/management', 'managementController@managementList')->name('managementList'); //管理员账号管理
    Route::get('editmanagement/{id}', 'managementController@editmanagement')->name('editmanagement'); //管理员账号编辑
    Route::post('savemanagement', 'managementController@savemanagement')->name('savemanagement'); //管理员账号添加
    Route::get('delmanagement/{id}', 'managementController@delmanagement')->name('delmanagement'); //管理员账号删除
    Route::get('enmanagement/{id}', 'managementController@enmanagement')->name('enmanagement'); //管理员账号禁用

    Route::get('overview','OverviewController@overview')->name('overview'); //概述


    Route::get('editmaterials/{id}', 'materialsController@editmaterials')->name('editmaterials'); //辅材包编辑
    Route::get('materiaList', 'materialsController@materiaList')->name('materiaList'); //辅材包列表
    Route::get('delMaterial/{id}', 'materialsController@delMaterial')->name('delMaterial'); //辅材包删除
    Route::get('down/{id}', 'materialsController@dowm')->name('down'); //辅材包下架
    Route::post('savematerials', 'materialsController@savematerials')->name('savematerials'); //辅材包保存

    Route::get('house', 'HouseController@getHouse')->name('house'); //户型管理
    Route::post('/houseCreate', 'HouseController@houseCreate')->name('houseCreate'); //户型地区添加
    Route::get('/houseDelete/{id}', 'HouseController@houseDelete')->name('houseDelete'); //户型地区删除

    Route::get('projectpositionList','ProjectPositionController@projectpositionList')->name('projectpositionList');//工地管理
    Route::get('projectpositionDetail/{id}','ProjectPositionController@projectpositionDetail')->name('projectpositionDetail');//工地详情
    Route::get('/projectpositionListExport/{param}', 'ProjectPositionController@projectpositionListExport')->name('projectpositionListListExportCreate');//导出工地统计记录
    Route::get('projectpositonOverview','ProjectPositionController@projectpositonOverview')->name('projectpositonOverview'); //工地概述

    Route::get('/designerAdd', 'UserController@designerAdd')->name('designerAdd');//添加设计师页面
    Route::post('/designerAdd', 'UserController@designerInfoInsert')->name('designerInfoInsert');//添加设计师

    Route::get('/laborAdd', 'UserController@laborAdd')->name('laborAdd');//添加工人
    Route::post('/laborDataInsert', 'UserController@laborDataInsert')->name('laborDataInsert');//添加工人

    Route::get('/housekeeperAdd', 'UserController@housekeeperAdd')->name('housekeeperAdd');//添加管家
    Route::post('/housekeeperDataInsert', 'UserController@housekeeperDataInsert')->name('housekeeperDataInsert');//添加管家

    Route::get('/supervisorAdd', 'UserController@supervisorAdd')->name('supervisorAdd');//添加监理
    Route::post('/supervisorInfoInsert', 'UserController@supervisorInfoInsert')->name('supervisorInfoInsert');//添加监理

    Route::get('/workGoodsList/{worker_id}', 'UserController@workGoodsList')->name('workGoodsList');//设计师作品列表
    Route::get('/delWorkGoods/{id}/{worker_id}', 'UserController@delWorkGoods')->name('delWorkGoods');//删除设计师作品
    Route::get('/editWorkGoods/{goods_id}', 'UserController@editWorkGoods')->name('editWorkGoods');//修改设计师作品视图
    Route::get('/addWorkGoods/{worker_id}', 'UserController@addWorkGoods')->name('addWorkGoods');//修改设计师作品视图
    Route::post('/handleWorkGoodsSub', 'UserController@handleWorkGoodsSub')->name('handleWorkGoodsSub');//提交作品添加或修改信息


    // 平台为管家匹配配置单里需要的工人（非整改单）
    Route::get('/projectConfList', 'UserController@projectConfList')->name('projectConfList');//获取需要配置工人的工程任务
    Route::get('/projectConfDetail/{task_id}', 'UserController@projectConfDetail')->name('projectConfDetail');//获取需要配置工人的工程任务详细

    //平台为整改单匹配新工人（整改单）
    Route::get('/projectChangeConfList', 'UserController@projectChangeConfList')->name('projectChangeConfList');//获取需要配置工人的工程任务
    Route::get('/projectChangeConfDetail/{task_id}', 'UserController@projectChangeConfDetail')->name('projectChangeConfDetail');//获取需要配置工人的工程任务详细

    //提交配置单的内容（整改单与非整改单公用）
    Route::post('/subWorkerConf', 'UserController@subWorkerConf')->name('subWorkerConf');//获取需要配置工人的工程任务

    //图片裁切
    Route::post('/crop', 'CropController@cropRet')->name('crop');//获取需要配置工人的工程任务

    //非业主修改
    Route::get('/workerEdit/{uid}', 'UserController@getWorkerEdit')->name('workerEdit');

    //充值
    Route::get('/userRecharge/{uid}', 'UserController@userRecharge')->name('userRecharge');
    Route::post('/postUserRecharge', 'UserController@postUserRecharge')->name('postUserRecharge');

    //提现
    Route::post('cashConfirm', 'FinanceController@cashConfirm')->name('cashConfirm');

    //提现(最终确认)
    Route::post('cashConfirmEnd', 'FinanceController@cashConfirmEnd')->name('cashConfirmEnd');

    //提现(最终确认)
    Route::post('withdrawRemit', 'FinanceController@withdrawRemit')->name('withdrawRemit');

    //提现(最终确认)
    Route::post('withdrawAllPriceDetail', 'FinanceController@withdrawAllPriceDetail')->name('withdrawAllPriceDetail');

    // 管家客诉通道列表客诉
    Route::get('/houseKeeperApply', 'UserController@houseKeeperApply')->name('houseKeeperApply');

    //管家客诉通道(后台代替业主确认功能)
    Route::post('houseKeeperApplyConfirm', 'UserController@houseKeeperApplyConfirm');

    // 工程配置单增减
    Route::get('/projectListManage', 'UserController@projectListManage')->name('projectListManage');

    //根据不同类型加载不同的配置单
    Route::get('/projectListManage/{id}/cityId/{cityId}', 'UserController@projectListManageById')->name('projectListManage');

    //根据不同id修改配置单
    Route::get('/projectConfigureEdit/{id}', 'UserController@projectConfigureEdit')->name('projectConfigureEdit');

    //添加商家信息
    Route::get('/projectConfigureEdit', 'UserController@projectConfigureAdd')->name('projectConfigureEdit');

    //根据不同删除配置单
    Route::get('/projectConfigureDel/{id}', 'UserController@projectConfigureDel')->name('projectConfigureDel');

    //提交配置单
    Route::post('/projectConfigureSubmit', 'UserController@projectConfigureSubmit')->name('projectConfigureSubmit');

    // 商家信息列表
    Route::get('/businessInfo', 'UserController@businessInfo')->name('businessInfo');

    //编辑商家信息列表
    Route::get('/businessInfoEdit/{id}', 'UserController@businessInfoEdit')->name('businessInfoEdit');

    //添加商家信息
    Route::get('/businessInfoEdit', 'UserController@businessInfoAdd')->name('businessInfoEdit');

    //保存商家信息
    Route::post('/saveBusinessInfo', 'UserController@saveBusinessInfo')->name('saveBusinessInfo');

    //删除商家信息
    Route::post('/businessInfoDelete', 'UserController@businessInfoDelete')->name('saveBusinessInfo');

    //测试账户一键操作
    Route::get('/systemAccountUpOrDown', 'UserController@systemAccountUpOrDown')->name('systemAccountUpOrDown');

    //发送充值邮件
    Route::get('/sendRechargeEmail', 'UserController@sendRechargeEmail')->name('sendRechargeEmail');

    //访问邀请码
    Route::get('registerPlatform/{invita_code}', 'UserController@registerPlatform')->name('registerPlatform');

    //编辑版块信息
    Route::get('/editSection/{goods_id}/{worker_id}', 'UserController@editSection')->name('editSection');

    //版块信息提交
    Route::post('/section_edit_submit', 'UserController@section_edit_submit')->name('section_edit_submit');

    //启动广告和新手图片查看
    Route::get('adImgList','UserController@adImgList')->name('adImgList');

    //启动广告和新手图片编辑
    Route::get('adImgEdit/{img_id}','UserController@adImgEdit')->name('adImgEdit');

    //新手图片渲染
    Route::get('adHelpImgEdit/{img_id}','UserController@adHelpImgEdit')->name('adHelpImgEdit');

    //广告和新手图片删除
    Route::post('adImgDelete','UserController@adImgDelete')->name('adImgDelete');

    //广告和新手图片提交
    Route::post('adHelpImgSubmit','UserController@adHelpImgSubmit')->name('adHelpImgSubmit');

    //工程配置单excel上传
    Route::post('ProjectListExcelUpload','UserController@ProjectListExcelUpload')->name('ProjectListExcelUpload');

    //工程配置单管理
    Route::get('auxManage','TaskController@auxManage')->name('auxManage');

    //辅材包管理上传
    Route::post('auxUpload','TaskController@auxUpload')->name('auxUpload');

    //辅材包编辑(页面渲染)
    Route::get('auxEdit/{id?}','TaskController@auxEdit')->name('auxEdit');

    //辅材包添加和编辑
    Route::post('auxAdd','TaskController@auxAdd')->name('auxAdd');

    //辅材包删除
    Route::get('auxDelete/{id}','TaskController@auxDelete')->name('auxDelete');

    //辅材包恢复
    Route::get('auxRestore/{id}','TaskController@auxRestore')->name('auxRestore');

    //用户排序
    Route::post('userSortByAdmin','UserController@userSortByAdmin')->name('userSortByAdmin');

    //直播排序
    Route::post('broadcastSort','UserController@broadcastSort')->name('broadcastSort');

    //直播隐藏
    Route::post('broadcastHidden','UserController@broadcastHidden')->name('broadcastHidden');

    //查看下线
    Route::get('getUserDownline/{id}','UserController@getUserDownline')->name('getUserDownline');

    //配置可筛选区域
    Route::get('region','UserController@region')->name('region');
//更改可筛选区域状态
    Route::get('changeCurrentStatus/{id}/{work_select}','UserController@changeCurrentStatus')->name('changeCurrentStatus');

    //主材分类
    Route::get('principalCategoryList','PrincipalMaterialController@PrincipalCategoryList')->name('PrincipalCategoryList');
    //主材分类编辑
//    Route::get('changeCurrentStatus/{id}/{work_select}','UserController@changeCurrentStatus')->name('changeCurrentStatus');
    //主材列表
    Route::get('principalGoodsList','PrincipalMaterialController@principalGoodsList')->name('principalGoodsList');

    //主材编辑
    Route::get('goodsEdit','PrincipalMaterialController@goodsEdit')->name('goodsEdit');
    //主材编辑提交
    Route::post('subGoodsEdit','PrincipalMaterialController@subGoodsEdit')->name('subGoodsEdit');

    //获取已提交的主材单的材料列表
    Route::get('getMaterialList/{id}','TaskController@getMaterialList')->name('getMaterialList');

    //确认发货主材
    Route::post('sureMaterial','TaskController@sureMaterial')->name('sureMaterial');

    //主材分类编辑页面
    Route::get('categoryEdit','PrincipalMaterialController@categoryEdit')->name('categoryEdit');

    //确认发货主材
    Route::post('subCategoryEdit','PrincipalMaterialController@subCategoryEdit')->name('subCategoryEdit');

    //物管列表
    Route::get('property','RepairController@property')->name('property');

    //楼盘列表
    Route::get('building','RepairController@building')->name('building');

    //报建列表
    Route::get('repairOrder','RepairController@repairOrder')->name('repairOrder');

    //楼盘编辑
    Route::get('buildingEdit','RepairController@buildingEdit')->name('buildingEdit');

    //提交楼盘编辑
    Route::post('subBuildingEdit','RepairController@subBuildingEdit')->name('subBuildingEdit');

    //改变报键单状态
    Route::post('updateOrderStatus','RepairController@updateOrderStatus')->name('updateOrderStatus');

    //报键单详细
    Route::post('orderDetail','RepairController@orderDetail')->name('orderDetail');

    //下载图片
    Route::get('zipUpload','RepairController@zipUpload')->name('zipUpload');

    //后台自己添加报健视图
    Route::get('createRepairOrderView','RepairController@createRepairOrderView')->name('createRepairOrderView');

/*    Route::get('/auxManage', 'AuxiliaryController@auxiliary')->name('auxiliary');//辅材包列表
    Route::get('/auxiliaryDetail/{id}', 'AuxiliaryController@auxiliaryDetail')->name('auxiliaryDetail');//辅材包详细
    Route::post('/editAuxiliary', 'AuxiliaryController@editAuxiliary')->name('editAuxiliary');//添加或修改辅材包
    Route::get('/deleteAuxiliary/{id}', 'AuxiliaryController@deleteAuxiliary')->name('deleteAuxiliary');//删除辅材包*/

});

//Route::group(['prefix' => 'manage', 'middleware' => ['manageauth', 'RolePermission', 'systemlog']], function () {});

//Route::any('/wechat', 'WxController@serve');