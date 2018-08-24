<?php

use Illuminate\Database\Seeder;

class MenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('menu')->delete();

        \DB::table('menu')->insert(array (
            0 =>
            array (
                'id' => 1,
                'name' => '后台首页',
                'route' => 'manage/',
                'pid' => 0,
                'level' => 1,
                'note' => '后台首页',
                'sort' => 2,
                'created_at' => '2016-07-20 10:36:54',
                'updated_at' => '2016-07-20 10:36:54',
            ),
            1 =>
            array (
                'id' => 36,
                'name' => '系统配置',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '系统配置',
                'sort' => 3,
                'created_at' => '2016-07-20 10:37:20',
                'updated_at' => '2016-07-20 10:37:20',
            ),
            2 =>
            array (
                'id' => 37,
                'name' => '全局配置',
                'route' => '',
                'pid' => 36,
                'level' => 2,
                'note' => '全局配置',
                'sort' => 1,
                'created_at' => '2016-07-10 10:34:56',
                'updated_at' => '2016-07-10 10:34:56',
            ),
            3 =>
            array (
                'id' => 38,
                'name' => '站点配置',
                'route' => 'manage/config/site',
                'pid' => 37,
                'level' => 3,
                'note' => '站点配置',
                'sort' => 1,
                'created_at' => '2016-07-16 18:40:30',
                'updated_at' => '2016-07-16 18:40:30',
            ),
            4 =>
            array (
                'id' => 39,
                'name' => '附件配置',
                'route' => 'manage/config/attachment',
                'pid' => 37,
                'level' => 3,
                'note' => '附件配置',
                'sort' => 2,
                'created_at' => '2016-07-14 09:12:31',
                'updated_at' => '2016-07-14 09:12:31',
            ),
            5 =>
            array (
                'id' => 40,
                'name' => '自定义导航列表',
                'route' => 'manage/navList',
                'pid' => 36,
                'level' => 2,
                'note' => '自定义导航列表',
                'sort' => 2,
                'created_at' => '2016-07-14 09:12:35',
                'updated_at' => '2016-07-14 09:12:35',
            ),
            6 =>
            array (
                'id' => 42,
                'name' => '接口管理',
                'route' => '',
                'pid' => 36,
                'level' => 2,
                'note' => '接口管理',
                'sort' => 3,
                'created_at' => '2016-07-10 10:38:09',
                'updated_at' => '2016-07-10 10:38:09',
            ),
            7 =>
            array (
                'id' => 43,
                'name' => '支付管理',
                'route' => 'manage/payConfig',
                'pid' => 42,
                'level' => 3,
                'note' => '支付管理',
                'sort' => 1,
                'created_at' => '2016-07-14 09:12:38',
                'updated_at' => '2016-07-14 09:12:38',
            ),
            8 =>
            array (
                'id' => 44,
                'name' => '第三方登录',
                'route' => 'manage/thirdLogin',
                'pid' => 42,
                'level' => 3,
                'note' => '第三方登录',
                'sort' => 2,
                'created_at' => '2016-07-14 09:12:40',
                'updated_at' => '2016-07-14 09:12:40',
            ),
            9 =>
            array (
                'id' => 45,
                'name' => '热词搜索管理',
                'route' => 'manage/hotwordsList',
                'pid' => 36,
                'level' => 2,
                'note' => '热词搜索管理',
                'sort' => 4,
                'created_at' => '2016-07-14 09:12:42',
                'updated_at' => '2016-07-14 09:12:42',
            ),
            10 =>
            array (
                'id' => 46,
                'name' => '协议管理',
                'route' => 'manage/agreementList',
                'pid' => 36,
                'level' => 2,
                'note' => '协议管理',
                'sort' => 5,
                'created_at' => '2016-07-14 09:12:45',
                'updated_at' => '2016-07-14 09:12:45',
            ),

            12 =>
            array (
                'id' => 48,
                'name' => '用户管理',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '用户管理',
                'sort' => 3,
                'created_at' => '2016-07-10 10:42:49',
                'updated_at' => '2016-07-10 10:42:49',
            ),
            13 =>
            array (
                'id' => 49,
                'name' => '用户体系',
                'route' => '',
                'pid' => 48,
                'level' => 2,
                'note' => '用户体系',
                'sort' => 1,
                'created_at' => '2016-07-10 10:43:13',
                'updated_at' => '2016-07-10 10:43:13',
            ),
            14 =>
            array (
                'id' => 50,
                'name' => '互评记录',
                'route' => 'manage/getCommentList',
                'pid' => 49,
                'level' => 3,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-07-14 09:12:49',
                'updated_at' => '2016-07-14 09:12:49',
            ),
            15 =>
            array (
                'id' => 51,
                'name' => '认证管理',
                'route' => '',
                'pid' => 48,
                'level' => 2,
                'note' => '认证管理',
                'sort' => 2,
                'created_at' => '2016-07-10 10:44:49',
                'updated_at' => '2016-07-10 10:44:49',
            ),
            16 =>
            array (
                'id' => 52,
                'name' => '实名认证',
                'route' => 'manage/realnameAuthList',
                'pid' => 51,
                'level' => 3,
                'note' => '实名认证',
                'sort' => 1,
                'created_at' => '2016-07-14 09:12:53',
                'updated_at' => '2016-07-14 09:12:53',
            ),
            17 =>
            array (
                'id' => 53,
                'name' => '银行卡绑定',
                'route' => 'manage/bankAuthList',
                'pid' => 51,
                'level' => 3,
                'note' => '银行卡绑定',
                'sort' => 2,
                'created_at' => '2016-07-14 09:12:55',
                'updated_at' => '2016-07-14 09:12:55',
            ),
            18 =>
            array (
                'id' => 54,
                'name' => '支付宝绑定',
                'route' => 'manage/alipayAuthList',
                'pid' => 51,
                'level' => 3,
                'note' => '支付宝绑定',
                'sort' => 3,
                'created_at' => '2016-07-14 09:12:57',
                'updated_at' => '2016-07-14 09:12:57',
            ),
            19 =>
            array (
                'id' => 55,
                'name' => '用户反馈',
                'route' => '',
                'pid' => 48,
                'level' => 2,
                'note' => '用户反馈',
                'sort' => 3,
                'created_at' => '2016-07-10 10:47:59',
                'updated_at' => '2016-07-10 10:47:59',
            ),
            20 =>
            array (
                'id' => 56,
                'name' => '交易举报列表',
                'route' => 'manage/reportList',
                'pid' => 55,
                'level' => 3,
                'note' => '交易举报列表',
                'sort' => 1,
                'created_at' => '2016-07-14 09:13:01',
                'updated_at' => '2016-07-14 09:13:01',
            ),
            21 =>
            array (
                'id' => 57,
                'name' => '交易维权列表',
                'route' => 'manage/rightsList',
                'pid' => 55,
                'level' => 3,
                'note' => '交易维权列表',
                'sort' => 2,
                'created_at' => '2016-07-14 09:13:03',
                'updated_at' => '2016-07-14 09:13:03',
            ),
            22 =>
            array (
                'id' => 58,
                'name' => '投诉建议',
                'route' => 'manage/feedbackList',
                'pid' => 55,
                'level' => 3,
                'note' => '投诉建议',
                'sort' => 3,
                'created_at' => '2016-07-14 09:13:05',
                'updated_at' => '2016-07-14 09:13:05',
            ),
            23 =>
            array (
                'id' => 59,
                'name' => '用户管理',
                'route' => '',
                'pid' => 48,
                'level' => 2,
                'note' => '用户管理',
                'sort' => 4,
                'created_at' => '2016-07-10 10:50:58',
                'updated_at' => '2016-07-10 10:50:58',
            ),
            24 =>
            array (
                'id' => 60,
                'name' => '系统用户',
                'route' => 'manage/managerList',
                'pid' => 59,
                'level' => 3,
                'note' => '系统用户',
                'sort' => 1,
                'created_at' => '2016-07-14 09:13:10',
                'updated_at' => '2016-07-14 09:13:10',
            ),
            25 =>
            array (
                'id' => 61,
                'name' => '普通用户',
                'route' => 'manage/userList',
                'pid' => 59,
                'level' => 3,
                'note' => '普通用户',
                'sort' => 2,
                'created_at' => '2016-07-14 09:13:13',
                'updated_at' => '2016-07-14 09:13:13',
            ),
            26 =>
            array (
                'id' => 62,
                'name' => '用户组列表',
                'route' => 'manage/rolesList',
                'pid' => 59,
                'level' => 3,
                'note' => '用户组列表',
                'sort' => 3,
                'created_at' => '2016-07-14 09:13:16',
                'updated_at' => '2016-07-14 09:13:16',
            ),
            27 =>
            array (
                'id' => 63,
                'name' => '权限管理',
                'route' => 'manage/permissionsList',
                'pid' => 59,
                'level' => 3,
                'note' => '权限管理',
                'sort' => 4,
                'created_at' => '2016-07-14 09:13:19',
                'updated_at' => '2016-07-14 09:13:19',
            ),
            28 =>
            array (
                'id' => 64,
                'name' => '菜单管理',
                'route' => 'manage/menuList/1/1',
                'pid' => 59,
                'level' => 3,
                'note' => '菜单管理',
                'sort' => 5,
                'created_at' => '2016-07-14 09:13:21',
                'updated_at' => '2016-07-14 09:13:21',
            ),
            29 =>
            array (
                'id' => 65,
                'name' => '任务控制台',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '任务控制台',
                'sort' => 4,
                'created_at' => '2016-07-10 14:22:51',
                'updated_at' => '2016-07-10 14:22:51',
            ),
            30 =>
            array (
                'id' => 66,
                'name' => '任务管理',
                'route' => '',
                'pid' => 65,
                'level' => 2,
                'note' => '任务管理',
                'sort' => 1,
                'created_at' => '2016-07-10 11:05:55',
                'updated_at' => '2016-07-10 11:05:55',
            ),
            31 =>
            array (
                'id' => 67,
                'name' => '任务列表',
                'route' => 'manage/taskList',
                'pid' => 66,
                'level' => 3,
                'note' => '任务列表',
                'sort' => 1,
                'created_at' => '2016-07-14 09:13:28',
                'updated_at' => '2016-07-14 09:13:28',
            ),
            32 =>
            array (
                'id' => 68,
                'name' => '行业分类',
                'route' => 'manage/industry',
                'pid' => 36,
                'level' => 2,
                'note' => '行业分类',
                'sort' => 2,
                'created_at' => '2016-08-16 16:05:29',
                'updated_at' => '2016-08-16 16:05:29',
            ),
            33 =>
            array (
                'id' => 70,
                'name' => '任务配置',
                'route' => 'manage/taskConfig/1',
                'pid' => 65,
                'level' => 2,
                'note' => '任务配置',
                'sort' => 2,
                'created_at' => '2016-08-05 11:12:09',
                'updated_at' => '2016-08-05 11:12:09',
            ),
            34 =>
            array (
                'id' => 74,
                'name' => '增值工具',
                'route' => '',
                'pid' => 65,
                'level' => 2,
                'note' => '增值工具',
                'sort' => 3,
                'created_at' => '2016-07-10 11:11:18',
                'updated_at' => '2016-07-10 11:11:18',
            ),
            35 =>
            array (
                'id' => 75,
                'name' => '工具列表',
                'route' => 'manage/serviceList',
                'pid' => 74,
                'level' => 3,
                'note' => '工具列表',
                'sort' => 1,
                'created_at' => '2016-07-14 09:13:50',
                'updated_at' => '2016-07-14 09:13:50',
            ),
            36 =>
            array (
                'id' => 76,
                'name' => '推荐管理',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '推荐管理',
                'sort' => 5,
                'created_at' => '2016-07-10 11:12:57',
                'updated_at' => '2016-07-10 11:12:57',
            ),
            37 =>
            array (
                'id' => 77,
                'name' => '推荐位管理',
                'route' => 'advertisement/recommendList',
                'pid' => 76,
                'level' => 2,
                'note' => '推荐位管理',
                'sort' => 1,
                'created_at' => '2016-07-16 15:01:12',
                'updated_at' => '2016-07-16 15:01:12',
            ),
            38 =>
            array (
                'id' => 78,
                'name' => '广告管理',
                'route' => 'advertisement/adTarget',
                'pid' => 76,
                'level' => 2,
                'note' => '广告管理',
                'sort' => 0,
                'created_at' => '2016-08-05 11:28:06',
                'updated_at' => '2016-08-05 11:28:06',
            ),
            39 =>
            array (
                'id' => 79,
                'name' => '友情链接',
                'route' => 'manage/link',
                'pid' => 76,
                'level' => 2,
                'note' => '友情链接',
                'sort' => 3,
                'created_at' => '2016-07-14 09:13:57',
                'updated_at' => '2016-07-14 09:13:57',
            ),
            40 =>
            array (
                'id' => 80,
                'name' => '站长工具',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '站长工具',
                'sort' => 6,
                'created_at' => '2016-07-10 11:15:55',
                'updated_at' => '2016-07-10 11:15:55',
            ),
            41 =>
            array (
                'id' => 81,
                'name' => '系统日志',
                'route' => 'manage/systemLogList',
                'pid' => 80,
                'level' => 2,
                'note' => '系统日志',
                'sort' => 1,
                'created_at' => '2016-07-14 09:14:14',
                'updated_at' => '2016-07-14 09:14:14',
            ),
            42 =>
            array (
                'id' => 83,
                'name' => '附件管理',
                'route' => 'manage/attachmentList',
                'pid' => 80,
                'level' => 2,
                'note' => '附件管理',
                'sort' => 0,
                'created_at' => '2016-07-14 09:14:20',
                'updated_at' => '2016-07-14 09:14:20',
            ),
            43 =>
            array (
                'id' => 84,
                'name' => '资讯管理',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '资讯管理',
                'sort' => 7,
                'created_at' => '2016-07-10 11:18:29',
                'updated_at' => '2016-07-10 11:18:29',
            ),
            44 =>
            array (
                'id' => 85,
                'name' => '页脚配置',
                'route' => '',
                'pid' => 84,
                'level' => 2,
                'note' => '页脚配置',
                'sort' => 1,
                'created_at' => '2016-07-10 11:19:05',
                'updated_at' => '2016-07-10 11:19:05',
            ),
            45 =>
            array (
                'id' => 86,
                'name' => '文章模块',
                'route' => '',
                'pid' => 84,
                'level' => 2,
                'note' => '文章模块',
                'sort' => 2,
                'created_at' => '2016-07-10 11:19:34',
                'updated_at' => '2016-07-10 11:19:34',
            ),
            46 =>
            array (
                'id' => 87,
                'name' => '成功案例',
                'route' => '',
                'pid' => 84,
                'level' => 2,
                'note' => '成功案例',
                'sort' => 3,
                'created_at' => '2016-07-10 11:20:10',
                'updated_at' => '2016-07-10 11:20:10',
            ),
            47 =>
            array (
                'id' => 88,
                'name' => '页脚管理',
                'route' => 'manage/articleFooter/3',
                'pid' => 85,
                'level' => 3,
                'note' => '页脚管理',
                'sort' => 1,
                'created_at' => '2016-08-04 16:50:09',
                'updated_at' => '2016-08-04 16:50:09',
            ),
            48 =>
            array (
                'id' => 89,
                'name' => '页脚分类',
                'route' => 'manage/categoryFooterList/3',
                'pid' => 85,
                'level' => 3,
                'note' => '页脚分类',
                'sort' => 2,
                'created_at' => '2016-08-04 17:48:07',
                'updated_at' => '2016-08-04 17:48:07',
            ),
            49 =>
            array (
                'id' => 90,
                'name' => '文章管理',
                'route' => 'manage/article/1',
                'pid' => 86,
                'level' => 3,
                'note' => '文章管理',
                'sort' => 1,
                'created_at' => '2016-07-14 09:14:28',
                'updated_at' => '2016-07-14 09:14:28',
            ),
            50 =>
            array (
                'id' => 91,
                'name' => '文章分类',
                'route' => 'manage/categoryList/1',
                'pid' => 86,
                'level' => 3,
                'note' => '文章分类',
                'sort' => 2,
                'created_at' => '2016-08-04 15:46:02',
                'updated_at' => '2016-08-04 15:46:02',
            ),
            51 =>
            array (
                'id' => 92,
                'name' => '案例列表',
                'route' => 'manage/successCaseList',
                'pid' => 87,
                'level' => 3,
                'note' => '案例列表',
                'sort' => 1,
                'created_at' => '2016-07-14 09:14:34',
                'updated_at' => '2016-07-14 09:14:34',
            ),
            52 =>
            array (
                'id' => 93,
                'name' => '案例添加',
                'route' => 'manage/successCaseAdd',
                'pid' => 87,
                'level' => 3,
                'note' => '案例添加',
                'sort' => 2,
                'created_at' => '2016-07-14 09:14:38',
                'updated_at' => '2016-07-14 09:14:38',
            ),
            53 =>
            array (
                'id' => 94,
                'name' => '财务管理',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '财务管理',
                'sort' => 8,
                'created_at' => '2016-07-10 11:25:10',
                'updated_at' => '2016-07-10 11:25:10',
            ),
            54 =>
            array (
                'id' => 95,
                'name' => '财务概况',
                'route' => '',
                'pid' => 94,
                'level' => 2,
                'note' => '财务概况',
                'sort' => 1,
                'created_at' => '2016-07-10 11:25:41',
                'updated_at' => '2016-07-10 11:25:41',
            ),
            55 =>
            array (
                'id' => 96,
                'name' => '网站收支',
                'route' => 'manage/financeStatement',
                'pid' => 95,
                'level' => 3,
                'note' => '网站收支',
                'sort' => 1,
                'created_at' => '2016-07-14 09:15:10',
                'updated_at' => '2016-07-14 09:15:10',
            ),
            56 =>
            array (
                'id' => 97,
                'name' => '充值记录',
                'route' => 'manage/financeRecharge',
                'pid' => 95,
                'level' => 3,
                'note' => '充值记录',
                'sort' => 2,
                'created_at' => '2016-07-14 09:15:13',
                'updated_at' => '2016-07-14 09:15:13',
            ),
            57 =>
            array (
                'id' => 98,
                'name' => '提现记录',
                'route' => 'manage/financeWithdraw',
                'pid' => 95,
                'level' => 3,
                'note' => '提现记录',
                'sort' => 3,
                'created_at' => '2016-07-14 09:15:15',
                'updated_at' => '2016-07-14 09:15:15',
            ),
            58 =>
            array (
                'id' => 99,
                'name' => '利润统计',
                'route' => 'manage/financeProfit',
                'pid' => 95,
                'level' => 3,
                'note' => '利润统计',
                'sort' => 4,
                'created_at' => '2016-07-14 09:15:17',
                'updated_at' => '2016-07-14 09:15:17',
            ),
            59 =>
            array (
                'id' => 100,
                'name' => '网站流水',
                'route' => 'manage/financeList',
                'pid' => 94,
                'level' => 2,
                'note' => '网站流水',
                'sort' => 2,
                'created_at' => '2016-07-14 09:15:20',
                'updated_at' => '2016-07-14 09:15:20',
            ),
            60 =>
            array (
                'id' => 101,
                'name' => '用户流水',
                'route' => 'manage/userFinance',
                'pid' => 94,
                'level' => 2,
                'note' => '用户流水',
                'sort' => 3,
                'created_at' => '2016-07-14 09:15:33',
                'updated_at' => '2016-07-14 09:15:33',
            ),
            61 =>
            array (
                'id' => 102,
                'name' => '充值审核',
                'route' => 'manage/rechargeList',
                'pid' => 94,
                'level' => 2,
                'note' => '充值审核',
                'sort' => 4,
                'created_at' => '2016-07-14 09:15:37',
                'updated_at' => '2016-07-14 09:15:37',
            ),
            62 =>
            array (
                'id' => 103,
                'name' => '提现审核',
                'route' => 'manage/cashoutList',
                'pid' => 94,
                'level' => 2,
                'note' => '提现审核',
                'sort' => 5,
                'created_at' => '2016-07-14 09:15:39',
                'updated_at' => '2016-07-14 09:15:39',
            ),
            63 =>
            array (
                'id' => 104,
                'name' => '短信模板',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '短信模板',
                'sort' => 9,
                'created_at' => '2016-07-10 11:32:32',
                'updated_at' => '2016-07-10 11:32:32',
            ),
            64 =>
            array (
                'id' => 105,
                'name' => '短信模板列表',
                'route' => 'manage/messageList',
                'pid' => 104,
                'level' => 2,
                'note' => '短信模板列表',
                'sort' => 1,
                'created_at' => '2016-07-14 09:15:42',
                'updated_at' => '2016-07-14 09:15:42',
            ),
            65 =>
            array (
                'id' => 106,
                'name' => '地区管理',
                'route' => 'manage/area',
                'pid' => 37,
                'level' => 3,
                'note' => NULL,
                'sort' => 3,
                'created_at' => '2016-07-14 09:15:44',
                'updated_at' => '2016-07-14 09:15:44',
            ),

            67 =>
            array (
                'id' => 108,
                'name' => '店铺管理',
                'route' => '',
                'pid' => 0,
                'level' => 1,
                'note' => '店铺管理',
                'sort' => 4,
                'created_at' => '2016-08-26 14:31:27',
                'updated_at' => '2016-08-26 14:31:27',
            ),
            68 =>
            array (
                'id' => 109,
                'name' => '雇佣管理',
                'route' => '',
                'pid' => 108,
                'level' => 2,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-08-25 16:26:33',
                'updated_at' => NULL,
            ),
            69 =>
            array (
                'id' => 110,
                'name' => '  雇佣配置',
                'route' => 'manage/employConfig',
                'pid' => 109,
                'level' => 3,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-08-29 10:10:46',
                'updated_at' => '2016-08-29 10:10:46',
            ),
            70 =>
            array (
                'id' => 112,
                'name' => '雇佣列表',
                'route' => 'manage/employList',
                'pid' => 109,
                'level' => 3,
                'note' => '雇佣列表',
                'sort' => 0,
                'created_at' => '2016-08-29 10:10:30',
                'updated_at' => '2016-08-29 10:10:30',
            ),
            71 =>
            array (
                'id' => 113,
                'name' => '企业认证',
                'route' => '/manage/enterpriseAuthList',
                'pid' => 51,
                'level' => 3,
                'note' => '企业认证列表',
                'sort' => 2,
                'created_at' => '2016-08-29 18:45:11',
                'updated_at' => NULL,
            ),
            72 =>
            array (
                'id' => 114,
                'name' => '店铺列表',
                'route' => '/manage/shopList',
                'pid' => 108,
                'level' => 2,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-08-31 11:42:46',
                'updated_at' => NULL,
            ),
            73 =>
            array (
                'id' => 115,
                'name' => '店铺配置',
                'route' => '/manage/shopConfig',
                'pid' => 108,
                'level' => 2,
                'note' => '店铺配置',
                'sort' => 0,
                'created_at' => '2016-09-06 11:04:47',
                'updated_at' => NULL,
            ),
            74 =>
            array (
                'id' => 118,
                'name' => '交易维权',
                'route' => '/manage/ShopRightsList',
                'pid' => 108,
                'level' => 2,
                'note' => '',
                'sort' => 6,
                'created_at' => '2016-09-14 18:43:26',
                'updated_at' => '2016-09-14 18:43:26',
            ),
            75 =>
            array (
                'id' => 119,
                'name' => '威客商品',
                'route' => '',
                'pid' => 108,
                'level' => 2,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-09-18 18:41:48',
                'updated_at' => NULL,
            ),
            76 =>
            array (
                'id' => 120,
                'name' => '商品管理',
                'route' => '/manage/goodsList ',
                'pid' => 119,
                'level' => 3,
                'note' => '',
                'sort' => 1,
                'created_at' => '2016-09-18 18:43:01',
                'updated_at' => NULL,
            ),
            77 =>
            array (
                'id' => 121,
                'name' => '订单管理',
                'route' => '/manage/shopOrderList',
                'pid' => 119,
                'level' => 3,
                'note' => '',
                'sort' => 0,
                'created_at' => '2016-09-18 18:44:03',
                'updated_at' => NULL,
            ),
            78 =>
            array (
                'id' => 122,
                'name' => '商品配置',
                'route' => '/manage/goodsConfig ',
                'pid' => 119,
                'level' => 3,
                'note' => '',
                'sort' => 2,
                'created_at' => '2016-09-18 18:45:09',
                'updated_at' => NULL,
            ),
            79 =>
            array (
                'id' => 123,
                'name' => '威客服务',
                'route' => '',
                'pid' => 108,
                'level' => 2,
                'note' => '威客服务',
                'sort' => 4,
                'created_at' => '2016-09-21 18:13:11',
                'updated_at' => NULL,
            ),
            80 =>
            array (
                'id' => 125,
                'name' => '订单管理',
                'route' => '/manage/serviceOrderList',
                'pid' => 123,
                'level' => 3,
                'note' => '订单管理',
                'sort' => 1,
                'created_at' => '2016-09-21 18:15:42',
                'updated_at' => NULL,
            ),
            81 =>
            array (
                'id' => 126,
                'name' => '服务管理',
                'route' => '/manage/goodsServiceList',
                'pid' => 123,
                'level' => 3,
                'note' => '服务管理',
                'sort' => 2,
                'created_at' => '2016-09-22 16:45:08',
                'updated_at' => '2016-09-22 16:45:08',
            ),
            82 =>
            array (
                'id' => 127,
                'name' => '流程配置',
                'route' => '/manage/serviceConfig',
                'pid' => 123,
                'level' => 3,
                'note' => '流程配置',
                'sort' => 3,
                'created_at' => '2016-09-21 18:20:30',
                'updated_at' => NULL,
            ),
        ));


    }
}
