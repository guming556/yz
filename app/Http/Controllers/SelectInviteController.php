<?php

namespace App\Http\Controllers;

use App\Modules\Manage\Model\ConfigModel;
use App\Modules\Task\Model\WorkOfferModel;
use Illuminate\Http\Request;
use Teepluss\Theme\Theme;
use Cache;


use App\Modules\Advertisement\Model\RecommendModel;
use App\Modules\Advertisement\Model\RePositionModel;
use App\Modules\Finance\Model\CashoutModel;
use App\Modules\Manage\Model\LinkModel;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\Task\Model\SuccessCaseModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\User\Model\CommentModel;
use App\Modules\User\Model\TaskModel;
use App\Modules\User\Model\AuthRecordModel;
use App\Modules\User\Model\UserModel;
use Illuminate\Routing\Controller;
use App\Modules\Advertisement\Model\AdTargetModel;


class SelectInviteController extends IndexController {

    public function __construct() {
        parent::__construct();
//        $this->initTheme('common');
        $this->initTheme('auth');
        $this->theme->setTitle('威客|系统—客客出品,专业威客建站系统开源平台');
    }

    /**
     * 视图渲染
     */
    public function selectLogin() {
        $code        = \CommonClass::getCodes();
        $oauthConfig = ConfigModel::getConfigByType('oauth');
        $view        = array(
            'code' => $code,
            'oauth' => $oauthConfig,
        );
        $this->theme->set('authAction', '欢迎登录');
        $this->theme->setTitle('欢迎登录');
        return $this->theme->scope('sekectInvite.selectLogin', $view)->render();
    }

    /**
     * @param Request $request
     * post数据
     */
    public function suppliesLogin(Request $request) {
        $this->theme->set('authAction', '欢迎登录');
        $this->theme->setTitle('欢迎登录');
        $this->validate($request,
            [
                'username' => 'required',
                'password' => 'required'
            ],
            [
                'username.required' => '必须填写用户名',
                'password.required' => '必须填写密码'
            ]
        );
        $username = $request->get('username');//用户名
        $password = $request->get('password');//密码

        if (!UserModel::checkPassword($username, $password)) {
            $error['password'] = '请输入正确的帐号或密码';
        } else {
            $user = UserModel::where('name', $username)->first();
            if (!empty($user) && $user->status == 2) {
                $error['username'] = '该账户已禁用';
            }
        }
        if (!empty($error)) {
            return back()->withErrors($error);
        }
        $user_login = UserModel::where('name', $username)->first();

        $data = UserModel::select('name', 'id', 'created_at')->where('invite_uid', $user_login->id)->get();
        foreach ($data as $k => $v) {
            $data[$k]['name'] = hideStar($v['name']);
        }
        $data_task = [];
        foreach ($data as $k => $v) {
            $data_task[] = TaskModel::select('task.id', 'task.title', 'us.name', 'p.project_position', 'us.created_at', 'task.uid', 'task.status')
                ->where('task.uid', $v['id'])
                ->where('task.user_type', 3)
                ->leftJoin('users as us', 'us.id', '=', 'task.uid')
                ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                ->get();
        }


        foreach ($data_task as $k => $v) {
            if (!$v->isEmpty()) {
                $taskDetail = $v;
            }
        }
        if (!empty($taskDetail)) {
            foreach ($taskDetail as $k => $v) {
                $taskDetail[$k]['name'] = hideStar($v['name']);
                if ($v['status'] == 7) {
                    $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'title', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                        ->where('task_id', $v['id'])
                        ->orderBy('sn', 'ASC')
                        ->get()->toArray();

                    //返回work_offer中status为0的前一条数据
                    foreach ($work_offer_status as $n => $m) {
                        if ($m['status'] == 0) {
                            unset($work_offer_status[$n]);
                        }
                    }
                    $last_work_offer_status        = array_values($work_offer_status)[count($work_offer_status) - 1];
                    $taskDetail[$k]['status_work'] = $last_work_offer_status['title'] . work_offer_status_title($last_work_offer_status['status']);
                } else {
                    $taskDetail[$k]['status_work'] = '暂无';
                }
            }
        } else {
            $taskDetail = collect([]);
        }


        $data = [
            'taskDetail' => $taskDetail,
            'users' => $data,
        ];
        return $this->theme->scope('sekectInvite.selectIndex', $data)->render();

    }

}
