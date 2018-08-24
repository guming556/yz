<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Modules\Advertisement\Model\AdImg;
use App\Modules\Project\MerchantDetail;
use App\Modules\Finance\Model\FinancialModel;
use App\Modules\Manage\Model\ManagerModel;
use App\Modules\Manage\Model\MenuPermissionModel;
use App\Modules\Manage\Model\ModuleTypeModel;
use App\Modules\Manage\Model\Permission;
use App\Modules\Manage\Model\PermissionRoleModel;
use App\Modules\Manage\Model\Role;
use App\Modules\Manage\Model\RoleUserModel;
use App\Modules\Order\Model\OrderModel;
use App\Modules\Shop\Models\GoodsModel;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\User\Model\BankAuthModel;
use App\Modules\User\Model\CoordinateModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\User\Model\HouseKeeperComplaintModel;
use App\Modules\User\Model\ProjectConfigureModel;
use App\Modules\User\Model\TaskModel;
use App\Modules\Task\Model\TaskModel as TaskOtherModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserModel;
use App\Modules\Project\ProjectLaborChange;
use App\Modules\Verificationcode\Models\VerificationModel;
use App\PushSentenceList;
use App\PushServiceModel;
use Gregwar\Image\Image;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\Manage\Model\HouseModel;
use App\Modules\Task\Model\TaskCateModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\Manage\Model\ConfigModel;
use App\Modules\Project\ProjectConfigureTask;
use App\Modules\Project\ProjectConfigureChangeModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Order\Model\OrderModel as platformOrderModel;
use App\Respositories\PayWorkerRespository;
use Illuminate\Support\Facades\Redis;
use Excel;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use App\Respositories\ChatRoomRespository;
use Illuminate\Support\Facades\Session;

//use App\Modules\Advertisement\Model\AdModel;
//use App\Modules\Advertisement\Model\AdTargetModel;
class UserController extends ManageController
{
    protected $payWorkerRespository;
    protected $chatRoomRespository;
    public function __construct(PayWorkerRespository $payWorkerRespository, ChatRoomRespository $chatRoomRespository)
    {
        parent::__construct();
        $this->initTheme('manage');
        $this->theme->setTitle('业主管理');
        $this->theme->set('manageType', 'User');

        $this->theme->set('managerId', $this->manager->id);

        $this->payWorkerRespository  = $payWorkerRespository;
        $this->chatRoomRespository = $chatRoomRespository;
    }


    public static $_user_type_list = array(
        "1" => "普通业主",
        "2" => "设计师",
        "3" => "管家",
        "4" => "监理",
        "5" => "工人",
    );


    /**
     * 普通用户列表
     *
     * @param Request $request
     * @return mixed
     */
    public function getUserList(Request $request) {
        $user_type = !empty($request->input('user_type')) ? $request->input('user_type') : '1'; //判断那种类型用户
        $list      = UserModel::select('users.name', 'users.sort_id', 'user_detail.realname', 'roles.display_name', 'user_detail.nickname', 'user_detail.created_at', 'user_detail.balance', 'users.id', 'users.last_login_time', 'users.status', 'users.user_type', 'user_detail.work_type', 'users.invite_uid', 'users.device_token_type')
            ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
            ->leftJoin('role_user', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('users.id', '!=', '255');//排除系统用户
        $all_user  = $list->get();
        $shop      = ShopModel::get()->toArray();
        if ($request->get('uid')) {
            $list = $list->where('users.id', $request->get('uid'));
        }
        if ($request->get('username')) {
            $list = $list->where('user_detail.nickname', 'like', '%' . $request->get('username') . '%');
        }

        if ($request->get('mobile')) {
            $list = $list->where('users.name', $request->get('mobile'));
        }

        if (!isset($_GET['status'])) {
            $status = -1;
        } else {
            $status = intval($request->get('status'));
        }

        if ($status != -1) {
            $list = $list->where('users.status', $status);
        }

        $order = $request->get('order') ? $request->get('order') : 'desc';
        if ($request->get('by')) {
            switch ($request->get('by')) {
                case 'id':
                    $list = $list->orderBy('users.id', $order);
                    break;
                case 'created_at':
                    $list = $list->orderBy('users.created_at', $order);
                    break;
            }
        } else {
            $list = $list->orderBy('users.created_at', $order);
        }

        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        //时间筛选
        $timeType = 'users.created_at';

        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));

            $list  = $list->where($timeType, '>', $start);

        }
        if ($request->get('end')) {
            $end  = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $list = $list->where($timeType, '<', $end);
        }

        if ($user_type == null || $user_type == "") {

            $list = $list->paginate($paginate);
        } else {
            $list = $list->where('users.user_type', '=', $user_type)->paginate($paginate);
        }
        $list = $list->appends(['user_type' => $user_type]);

        $index_user = [];
        foreach ($all_user as $k => $v) {
            if (!empty($v['invite_uid'])) {
                $index_user[] = UserModel::find($v['invite_uid'])->id;
            }
        }

        foreach ($list as $k => $v) {
            foreach (array_unique($index_user) as $n => $m) {
                if ($v['id'] == $m) {
                    $list[$k]['has_downline'] = 1;
                }
            }
            $user_invited                  = UserModel::find($v['invite_uid']);
            $list[$k]['invite_uid_mobile'] = empty($v['invite_uid']) ? '无推荐人' : (empty($user_invited) ? '查无此人' : $user_invited->name);

        }
        foreach ($list as $key => $value) {
            $list[$key]['is_agree'] = 0;
            foreach ($shop as $key2 => $value2) {
                if ($value2['uid'] == $value['id']) {
                    $list[$key]['is_agree'] = 1;
                }
            }
        }

        $workType = ConfigModel::where('alias', 'worker')->first();
        if (!empty($workType)) {
            $workTypeArr = \GuzzleHttp\json_decode($workType->rule, true);
        } else {
            $workTypeArr = [];
        }


        foreach($list as $key => $value){
            $list[$key]->worker_number = 'YZ'.sprintf("%06d", $value->id);
        }

        $data = [
            'status' => $request->get('status'),
            'list' => $list,
            'paginate' => $paginate,
            'order' => $order,
            'by' => $request->get('by'),
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'workTypeArr' => $workTypeArr
        ];

        $data['user_type_list'] = UserController::$_user_type_list;
        $data['user_type']      = $user_type;


        $search         = [
            'status' => $request->get('status'),
            'user_type' => $request->get('user_type'),
            'paginate' => $paginate,
            'order' => $order,
            'by' => $request->get('by'),
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'start' => $request->get('start'),
            'end' => $request->get('end')
        ];
        $data['search'] = $search;
        $data['export_url'] = "/manage/userListExport?status=".$search['status']."&mobile=".$search['mobile']."&user_type=".$search['user_type']."&start=".$search['start']."&end=".$search['end'];


        return $this->theme->scope('manage.userList', $data)->render();
    }










    //城市站用户管理
    public function cityStationUser(Request $request) {

        $manageInfo = ManagerModel::getManager();

//var_dump($manageInfo->toArray());exit;
        $user_type = !empty($request->input('user_type')) ? $request->input('user_type') : '2'; //判断那种类型用户
        $list      = UserModel::select('users.name', 'users.sort_id', 'user_detail.realname', 'user_detail.nickname', 'user_detail.created_at', 'user_detail.balance', 'users.id', 'users.last_login_time', 'users.status', 'users.user_type', 'user_detail.work_type', 'users.invite_uid', 'users.device_token_type')
            ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
            ->where('users.id', '!=', '255');//排除系统用户


        $all_user  = $list->where('serve_area_id',$manageInfo->manage_city)->get();



        $shop      = ShopModel::get()->toArray();
        if ($request->get('uid')) {
            $list = $list->where('users.id', $request->get('uid'));
        }
        if ($request->get('username')) {
            $list = $list->where('user_detail.nickname', 'like', '%' . $request->get('username') . '%');
        }

        if ($request->get('mobile')) {
            $list = $list->where('users.name', $request->get('mobile'));
        }

        if (!isset($_GET['status'])) {
            $status = -1;
        } else {
            $status = intval($request->get('status'));
        }

        if ($status != -1) {
            $list = $list->where('users.status', $status);
        }

        $order = $request->get('order') ? $request->get('order') : 'desc';
        if ($request->get('by')) {
            switch ($request->get('by')) {
                case 'id':
                    $list = $list->orderBy('users.id', $order);
                    break;
                case 'created_at':
                    $list = $list->orderBy('users.created_at', $order);
                    break;
            }
        } else {
            $list = $list->orderBy('users.created_at', $order);
        }

        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        //时间筛选
        $timeType = 'users.created_at';

        if ($request->get('start')) {
            $start = date('Y-m-d H:i:s', strtotime($request->get('start')));

            $list  = $list->where($timeType, '>', $start);

        }
        if ($request->get('end')) {
            $end  = date('Y-m-d H:i:s', strtotime($request->get('end')));
            $list = $list->where($timeType, '<', $end);
        }

        if ($user_type == null || $user_type == "") {

            $list = $list->paginate($paginate);
        } else {
            $list = $list->where('users.user_type', '=', $user_type)->paginate($paginate);
        }
        $list = $list->appends(['user_type' => $user_type]);

        $index_user = [];
        foreach ($all_user as $k => $v) {
            if (!empty($v['invite_uid'])) {
                $index_user[] = UserModel::find($v['invite_uid'])->id;
            }
        }

        foreach ($list as $k => $v) {
            foreach (array_unique($index_user) as $n => $m) {
                if ($v['id'] == $m) {
                    $list[$k]['has_downline'] = 1;
                }
            }
            $user_invited                  = UserModel::find($v['invite_uid']);
            $list[$k]['invite_uid_mobile'] = empty($v['invite_uid']) ? '无推荐人' : (empty($user_invited) ? '查无此人' : $user_invited->name);

        }
        foreach ($list as $key => $value) {
            $list[$key]['is_agree'] = 0;
            foreach ($shop as $key2 => $value2) {
                if ($value2['uid'] == $value['id']) {
                    $list[$key]['is_agree'] = 1;
                }
            }
        }

        $workType = ConfigModel::where('alias', 'worker')->first();
        if (!empty($workType)) {
            $workTypeArr = \GuzzleHttp\json_decode($workType->rule, true);
        } else {
            $workTypeArr = [];
        }


        foreach($list as $key => $value){
            $list[$key]->worker_number = 'YZ'.sprintf("%06d", $value->id);
        }

        $data = [
            'status' => $request->get('status'),
            'list' => $list,
            'paginate' => $paginate,
            'order' => $order,
            'by' => $request->get('by'),
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'workTypeArr' => $workTypeArr
        ];

        $data['user_type_list'] = UserController::$_user_type_list;
        $data['user_type']      = $user_type;


        $search         = [
            'status' => $request->get('status'),
            'user_type' => $request->get('user_type'),
            'paginate' => $paginate,
            'order' => $order,
            'by' => $request->get('by'),
            'uid' => $request->get('uid'),
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'mobile' => $request->get('mobile'),
            'start' => $request->get('start'),
            'end' => $request->get('end')
        ];
        $data['search'] = $search;
        $data['export_url'] = "/manage/userListExport?status=".$search['status']."&mobile=".$search['mobile']."&user_type=".$search['user_type']."&start=".$search['start']."&end=".$search['end'];


        return $this->theme->scope('manage.cityUserList', $data)->render();
    }


























    /**
     * @param Request $request
     * 用户导出
     */
    public function userListExport(Request $request) {
        $status    = $request->get('status');
        $mobile    = $request->get('mobile');
        $user_type = empty($request->get('user_type')) ? 1 : $request->get('user_type');
        $start     = $request->get('start');
        $end       = $request->get('end');
        $users     = UserModel::select('users.id', 'users.name', 'users.created_at', 'user_detail.realname', 'user_detail.balance')->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')->whereRaw('1 = 1');
        if ($status != -1 && !empty($status)) {
            $users = $users->where('users.status', $status);
        }

        if ($mobile) {
            $users = $users->where('users.name', $mobile);
        }

        if ($user_type) {
            $users = $users->where('users.user_type', $user_type);
        }

        if ($start) {
            $start = date('Y-m-d H:i:s', strtotime($start));
            $users = $users->where('users.created_at', '>', $start);
        }
        if ($end) {
            $end   = date('Y-m-d H:i:s', strtotime($end));
            $users = $users->where('users.created_at', '<', $end);
        }


        $users = $users->orderBy('users.created_at','desc')->get()->toArray();

        foreach ($users as $k => $v) {
            $data_user[$k] = [
                '编号'=>$v['id'], '真实姓名'=>$v['realname'], '手机号'=>$v['name'], '余额<¥>'=>$v['balance'],'创建时间'=>$v['created_at']
            ];
        }

        Excel::create('user_export_' . uniqid(), function ($excel) use ($data_user) {
            $excel->sheet('user_info', function ($sheet) use ($data_user) {
                $sheet->fromArray($data_user);
            });
        })->export('xls');

    }




    /**
     * 处理用户
     *
     * @param $uid
     * @param $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleUser(Request $request,$uid, $action)
    {
        $ids = $request->input('ids'); //接收get传的参数
        switch ($action){
            case 'enable':
                $status = 1;
                break;
            case 'disable':
                $status = 2;
                break;
        }
        if($ids) //判断是批量还是单个
        {
            $status = UserModel::whereIn('id', $ids)->update(['status' => $status]);
            $flag = 1;
        }
        else
        {
            $status = UserModel::where('id', $uid)->update(['status' => $status]);
            $flag =0 ;
        }
        if($flag&&$status)
        {
            return response()->json(['errCode'=>1]);
        }
        else{
            return back()->with(['message' => '操作成功']);
        }
    }

    /**
     * @param Request $request
     * 测试账户一键操作
     */
    public function systemAccountUpOrDown(Request $request) {
        $action = $request->action;
        switch ($action){
            case 'enable':
                $status = 1;
                break;
            case 'disable':
                $status = 2;
                break;
        }
        $ids = Config::get('task.TEST_ACCOUNT');
        $res = UserModel::whereIn('id', $ids)->update(['status' => $status]);

        if ($res){
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
     * 添加普通用户视图
     *
     * @return mixed
     */
    public function getUserAdd()
    {
        $province = DistrictModel::findTree(0);
        $data = [
            'province' => $province
        ];
 		return $this->theme->scope('manage.userAdd', $data)->render();
    }

    /**
     * 添加用户表单提交
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUserAdd(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ],
            [
                'name.required' => '必须填写姓名',
                'password.required' => '必须填写密码',
            ]
        );

        $is_repeat = UserModel::where('name', $request->get('name'))->where('status', 1)->get()->toArray();
        if (!empty($is_repeat)) {
            return redirect('manage/userAdd')->with(['message' => '该账户已存在']);
        }

        $salt   = \CommonClass::random(4);
        $data   = [
            'uid' => empty($request->get('uid')) ? 0 : $request->get('uid'),
            'name' => trim($request->get('name')),
            'mobile' => $request->get('name'),
            'qq' => '',
            'email' => mt_rand(100, 9999999) . mt_rand(100, 900) . '@qq.com',
            'user_type' => 1,
            'created_at' => date('Y-m-d H:i:s', time()),
            'password' => UserModel::encryptPassword($request->get('password'), $salt),
            'salt' => $salt,
            'avatar' => $request->get('user-avatar'),
        ];
        $status = UserModel::addUser($data);
        $this->chatRoomRespository->RegistEaseMob($data['name']);
        if ($status)
            return redirect('manage/userList')->with(['message' => '操作成功']);
    }

    /**
     * 检查用户名
     *
     * @param Request $request
     * @return string
     */
    public function checkUserName(Request $request){
        $username = $request->get('param');
        $status = UserModel::where('name', $username)->first();
        if (empty($status)){
            $status = 'y';
            $info = '';
        } else {
            $info = '用户名不可用';
            $status = 'n';
        }
        $data = array(
            'info' => $info,
            'status' => $status
        );
        return json_encode($data);
    }

    /**
     * 检测邮箱是否可用
     *
     * @param Request $request
     * @return string
     */
    public function checkEmail(Request $request){
        $email = $request->get('param');

        $status = UserModel::where('email', $email)->first();
        if (empty($status)){
            $status = 'y';
            $info = '';
        } else {
            $info = '邮箱已占用';
            $status = 'n';
        }
        $data = array(
            'info' => $info,
            'status' => $status
        );
        return json_encode($data);
    }

    /**
     * 编辑用户资料视图（业主）
     *
     * @param $uid
     * @return mixed
     */
    public function getUserEdit($uid)
    {
            $info = UserModel::select(
                'users.id','users.name','users.user_type',
                'user_detail.realname','user_detail.mobile',
                'users.created_at','user_detail.cost_of_design',
                'user_detail.qq','user_detail.province','users.email',
                'user_detail.city','user_detail.avatar','user_detail.address',
                'user_detail.star','user_detail.experience','user_detail.area', 'roles.id as roles_id'
                )->where('users.id', $uid)
                ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
                ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                ->leftJoin('roles','role_user.role_id','=','roles.id')
                ->first()->toArray();

        $role = Role::select('id','display_name')->get()->toArray();
        $tpl = 'manage.userDetail';
        $data = [
            'role'              => $role,
            'info'              => $info,
            'user_type_list'    => UserController::$_user_type_list
        ];
 		return $this->theme->scope($tpl, $data)->render();
    }

    /**
     * 编辑用户资料
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */

    public function postUserEdit(Request $request)
    {

        $data = array(
            'uid' => $request->get('uid'),
            'realname' => $request->get('realname'),
            'nickname' => $request->get('nickname'),
            'province' => $request->get('province'),
            'city' => $request->get('city'),
            'area' => $request->get('area'),
            'updated_at' => date('Y-m-d H:i:s', time()),
            'avatar'=>$request->get('user-avatar'),
            'cost_of_design' => $request->get('cost_of_design'),
            'card_number' => $request->get('card_number'),
            'address' => $request->get('detail_address'),
            'serve_area' => $request->get('serve_city'),
            'serve_province' => $request->get('serve_province'),
            'experience' => $request->get('experience'),
            'star' => empty($request->get('star'))?'':$request->get('star'),
            'sign' => !empty($request->get('sign')) ? $request->get('sign') : '',
            'introduce' => !empty($request->get('introduce')) ? $request->get('introduce') : '',
            'tag' => !empty($request->get('tag')) ? $request->get('tag') : '',
            'bank_account' => !empty($request->get('bank_account')) ? $request->get('bank_account') : '',
            'deposit_name' => !empty($request->get('deposit_name')) ? $request->get('deposit_name') : '',
            'demo' => !empty($request->get('demo')) ? $request->get('demo') : '',
            'work_type' => !empty($request->get('work_type')) ? $request->get('work_type') : '',
            'native_place' => !empty($request->get('native_place')) ? $request->get('native_place') : 0,
            'user_age' => !empty($request->get('user_age')) ? $request->get('user_age') : 0,
            'employee_num' => !empty($request->get('employee_num')) ? $request->get('employee_num') : 0,//成交量
            'receive_task_num' => !empty($request->get('receive_task_num')) ? $request->get('receive_task_num') : 0,//预约量
            'lat'=>!empty($request->get('lat')) ? $request->get('lat') : 0,
            'lng'=>!empty($request->get('lng')) ? $request->get('lng') : 0,
        );

        if(!empty($data['tag'])){
            $data['tag'] = serialize(explode('，',$data['tag']));
        }
        if(!empty($data['sign'])){
            $data['sign'] = serialize(explode('，',$data['sign']));
        }
        if(!empty($data['demo'])){
            $data['demo'] = serialize(explode('，',$data['demo']));
        }
        if(!empty($data['city'])){
           $city = DistrictModel::where('id', $data['city'])->first();
            if(!empty($city)){
                $data['city'] = $city->name;
            }
        }

        if(!empty($data['serve_area'])){
            $serve = DistrictModel::where('id', $data['serve_area'])->first();
            if(!empty($serve)){
                $data['serve_area'] = $serve->name;
                $serve_area_id = CoordinateModel::where('name','like','%'.$serve->name.'%')->orderBy('level','desc')->first();
                if(!empty($serve_area_id)){
                    $data['serve_area_id'] = $serve_area_id->id;
                }
            }
        }

        if (!empty($request->get('password'))) {
            $salt = \CommonClass::random(4);
            $password = UserModel::encryptPassword($request->get('password'), $salt);
            $data['salt'] = $salt;
            $data['password'] = $password;
        }

        //用户组

        $result = RoleUserModel::select('user_id')->where('user_id', $data['uid'])->get()->toArray();

        if ($result) {
            $k = RoleUserModel::where('user_id', $data['uid'])->update(array('role_id' => $request->get('role_id')));
        } else {
            $k = RoleUserModel::create([
                'user_id' => $data['uid'],
                'role_id' => $request->get('role_id'),
            ]);
        }

        $status = UserModel::editUser($data);

        if ($status){
//            return redirect('manage/userList')->with(['message' => '操作成功']);//?status=-1
            $userType = UserModel::where('id',$data['uid'])->first()->user_type;
            if($userType == 1){
                return redirect('manage/userEdit/'.$data['uid'])->with(['message' => '操作成功']);//?status=-1
            }else{
                return redirect('manage/workerEdit/'.$data['uid'])->with(['message' => '操作成功']);//?status=-1
            }
        }

    }

    /**
     * 编辑用户资料视图（非业主）
     *
     * @param $uid
     * @return mixed
     */
    public function getWorkerEdit($uid)
    {
        $info = UserModel::select(
                'users.id','user_detail.realname','users.name',
                'users.user_type','users.created_at','user_detail.mobile','user_detail.employee_num','user_detail.receive_task_num',
                'user_detail.qq','users.email','user_detail.province',
                'user_detail.city','user_detail.star','user_detail.experience','user_detail.nickname',
                'user_detail.area','user_detail.avatar','realname_auth.serve_area','user_detail.native_place','user_age',
                'realname_auth.card_number','user_detail.address','user_detail.cost_of_design','roles.id as roles_id',
                'realname_auth.card_front_side','realname_auth.card_back_dside',
                'user_detail.introduce','user_detail.sign','user_detail.tag','user_detail.demo','user_detail.cost_of_design','user_detail.work_type','user_detail.lat','user_detail.lng'
                )->where('users.id', $uid)
                ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
                ->leftJoin('realname_auth', 'users.id', '=', 'realname_auth.uid')
//                ->where('realname_auth.status',1)
                ->leftJoin('role_user','users.id','=', 'role_user.user_id')
                ->leftJoin('roles','role_user.role_id','=','roles.id')
                ->first();
        if(!empty($info)) {
            $info = $info->toArray();

            if (!empty($info['card_front_side'])) {
                $info['card_front_side'] = url($info['card_front_side']);
            }
            if (!empty($info['card_back_dside'])) {
                $info['card_back_dside'] = url($info['card_back_dside']);
            }

            if (!empty($info['tag'])) {
                $info['tag'] = implode('，', unserialize($info['tag']));
            }
            if (!empty($info['sign'])) {
                $info['sign'] = implode('，', unserialize($info['sign']));
            }
            if (!empty($info['demo'])) {
                $info['demo'] = implode('，', unserialize($info['demo']));
            }
            $role     = Role::select('id', 'display_name')->get()->toArray();
            $province = DistrictModel::findTree(0);

            if ($info['user_type'] == 2) {
                $tpl = 'manage.userDesignerDetail';
            }
            if ($info['user_type'] == 3) {
                $tpl = 'manage.userHousekeeperDetail';
            }
            if ($info['user_type'] == 4) {
                $tpl = 'manage.userSupervisorDetail';
            }
            $rule = [];
            if ($info['user_type'] == 5) {
                $worker = ConfigModel::getConfigByAlias('worker');
                if (empty($worker)) {
                    return redirect('/manage/addWorker')->with(['message' => '请先添加工种！']);
                }
                $worker = $worker->toArray();
                $rule   = json_decode($worker['rule'], true);
                $tpl    = 'manage.userLaborDetail';
            }


            $city       = DistrictModel::where('name', 'like', $info['city'] . '%')->first();
            $serve_area = DistrictModel::where('name', 'like', $info['serve_area'] . '%')->first();
            $area       = DistrictModel::where('id', $info['area'])->first();

            if (!empty($city)) {
                $info['province'] = $city->upid;
                $info['city']     = $city->id;
            }
            $info['serve_province'] = 0;
            if (!empty($serve_area)) {
                $info['serve_area']     = $serve_area->id;
                $info['serve_province'] = $serve_area->upid;
            }
            if (!empty($area)) {
                $info['area'] = $area->id;
            }

            if (!empty($info)) {
                $bank                 = BankAuthModel::where('uid', $uid)->where('status', 2)->first();
                $info['deposit_name'] = !empty($bank) ? $bank['deposit_name'] : '';
                $info['bank_account'] = !empty($bank) ? $bank['bank_account'] : '';
            }


            $data = [
                'role'              => $role,
                'info'              => $info,
                'user_type_list'    => UserController::$_user_type_list,
                'province'          => $province,
                'city'              => DistrictModel::getDistrictName($info['city']),
                'area'              => DistrictModel::getDistrictName($info['area']),
                'serve_province'    => $info['serve_province'],
                'serve_city'        => DistrictModel::getDistrictName($info['serve_area']),
                'worker'              => $rule
            ];
            return $this->theme->scope($tpl, $data)->render();
        }else{
            echo '该人员不存在';exit;
        }


    }


    /**
     * 系统用户列表
     *
     * @param Request $request
     * @return mixed
     */
   	public function getManagerList(Request $request)
   	{
        $merge = $request->all();
        $list = ManagerModel::select('manager.id','manager.username','roles.display_name','manager.status','manager.email','manager.telephone','manager.QQ')->leftJoin('role_user','manager.id','=','role_user.user_id')
           ->leftJoin('roles','roles.id','=','role_user.role_id');
        $roles = Role::get();
        if($request->get('uid')){
            $list = $list->where('manager.id',$request->get('uid'));
        }
        if($request->get('username')){

            $list = $list->where('manager.username','like','%'. $request->get('username').'%');
        }
        if($request->get('QQ')){

            $list = $list->where('manager.QQ','like','%'. $request->get('QQ').'%');
        }
        if($request->get('email')){

            $list = $list->where('manager.email','like','%'. $request->get('email').'%');
        }
        if($request->get('display_name') && $request->get('display_name') != '全部'){
            $list = $list->where('roles.id',$request->get('display_name'));
        }
        if($request->get('telephone')){

            $list = $list->where('manager.telephone','like','%'. $request->get('telephone').'%');
        }
        if ($request->get('status')!=""){
            $list = $list->where('manager.status', $request->get('status'));
        }
        if($request->get('role_id')!=""){
            $list = $list->where('roles.id', $request->get('role_id'));
        }

        $order = $request->get('order') ? $request->get('order') : 'desc';
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        $list = $list->orderBy('manager.id',$order)->paginate($paginate);
        $listArr = $list->toArray();
        $data = array(
            'merge' => $merge,
            'listArr' => $listArr,
            'status'=>$request->get('status'),
            'by' => $request->get('by'),
            'order' => $order,
            'display_name'=>$request->get('display_name'),
            'uid'=>$request->get('uid'),
            'username'=>$request->get('username'),
            'QQ'=>$request->get('QQ'),
            'email'=>$request->get('email'),
            'telephone'=>$request->get('telephone'),
            'list'=>$list,
            'roles'=>$roles,
            'role_id'=>$request->get('role_id'),
       );
		return $this->theme->scope('manage.managerList',$data)->render();
   	}

    /**
     * 处理用户
     *
     * @param $uid
     * @param $action
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleManage($uid, $action)
    {
        switch ($action){
            case 'enable':
                $status = 1;
                break;
            case 'disable':
                $status = 2;
                break;
        }
        $status = ManagerModel::where('id', $uid)->update(['status' => $status]);
        if ($status)
            return back()->with(['message' => '操作成功']);
    }

    /**
     * 验证系统用户名
     *
     * @param Request $request
     * @return string
     */
    public function checkManageName(Request $request){
        $username = $request->get('param');
        $status = ManagerModel::where('username', $username)->first();
        if (empty($status)){
            $status = 'y';
            $info = '';
        } else {
            $info = '用户名不可用';
            $status = 'n';
        }
        $data = array(
            'info' => $info,
            'status' => $status
        );
        return json_encode($data);
    }

    /**
     * 验证系统用户邮箱
     *
     * @param Request $request
     * @return string
     */
    public function checkManageEmail(Request $request){
        $email = $request->get('param');

        $status = ManagerModel::where('email', $email)->first();
        if (empty($status)){
            $status = 'y';
            $info = '';
        } else {
            $info = '邮箱已占用';
            $status = 'n';
        }
        $data = array(
            'info' => $info,
            'status' => $status
        );
        return json_encode($data);
    }

    /**
     * 批量删除用户
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postManagerDeleteAll(Request $request){
       // dd($request->all());
        $data = $request->except(['_token','_url']);
        //var_dump($data['chk']);exit;
        if(!$data['chk']){
            return  redirect('manage/managerList')->with(array('message' => '操作失败'));
        }
        $status = DB::transaction(function () use ($data) {
            foreach ($data['chk'] as $id) {
                ManagerModel::where('id', $id)->delete();
               RoleUserModel::where('user_id', $id)->delete();
            }
        });
        if(is_null($status))
        {
            return redirect()->to('manage/managerList')->with(array('message' => '操作成功'));
        }
        return  redirect()->to('manage/managerList')->with(array('message' => '操作失败'));
    }

    /**
     *删除用户
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function managerDel($id){
        $status = DB::transaction(function () use ($id){
            ManagerModel::where('id',$id)->delete();
            RoleUserModel::where('user_id',$id)->delete();
        });

        if (is_null($status))
            return redirect()->to('manage/managerList')->with(['message' => '操作成功']);
    }
    /**
     * 添加用户视图
     *
     * @return mixed
     */
   	public function managerAdd()
   	{
        $roles = Role::get();

        $province = DistrictModel::findTree(0);

        $data = array(
            'roles'=>$roles,
            'province' => $province,
            'city' => DistrictModel::getDistrictProvince(),
        );
		return $this->theme->scope('manage.managerAdd',$data)->render();
   	}

    /**
     * 系统用户表单提交
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postManagerAdd(Request $request)
    {
        $status = DB::transaction(function () use ($request) {
            $salt = \CommonClass::random(4);
            $data = [
                'username' => $request->get('username'),
                'realname' => $request->get('realname'),
                'telephone' => $request->get('telephone'),
                'QQ' => $request->get('QQ'),
                'email' => $request->get('email'),
                'password' => ManagerModel::encryptPassword($request->get('password'), $salt),
                'birth' => $request->get('birth'),
                'salt' => $salt,
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            ];
            ManagerModel::insert($data);
            $user = ManagerModel::where('username',$request->get('username'))->first();
            if($request->get('role_id')){
                $user->attachRole($request->get('role_id'));
            }

        });
        if (is_null($status))
            return redirect('manage/managerList')->with(['message' => '操作成功']);
    }

    /**
     * 系统用户详情
     *
     * @param $id
     * @return mixed
     */
   	public function managerDetail($id)
   	{
        $info = ManagerModel::select('manager.id','manager.username','manager.status','manager.email','manager.telephone','manager.QQ','manager.password','manager.manage_city','role_user.role_id')->leftJoin('role_user','manager.id','=','role_user.user_id')
            ->leftJoin('roles','roles.id','=','role_user.role_id')->where('manager.id',$id)->first();
        $roles = Role::get();
//var_dump($info->toArray());exit;
        $province = DistrictModel::findTree(0);
        $currentProvince = DistrictModel::select('id','upid')->where('id',$info['manage_city'])->first();

        if(empty($currentProvince)){
            $current_id = 0;
        }else{
            if($currentProvince->upid == 0){
                $current_id = $info->manage_city;
            }else{
                $current_id = $currentProvince->upid;
            }
        }

        $manageInfo = Session::get('manager');
//var_dump($info['manage_city']);exit;
        $data = array(
            'roles'=>$roles,
            'info'=>$info,
            'province' => $province,
            'currentProvince'=> $current_id,
            'city' => DistrictModel::getDistrictName($info['manage_city']),
            'manage_city'=>intval($manageInfo->manage_city)
        );
		return $this->theme->scope('manage.managerDetail',$data)->render();
   	}

    /**
     * 编辑用户资料
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postManagerDetail(Request $request)
    {
        $status = DB::transaction(function () use ($request) {
            $id = $request->get('uid');
            if(!ManagerModel::where('id',$id)->where('password',$request->get('password'))->first()) {
                $salt = \CommonClass::random(4);
                $data = array(
                    'realname' => $request->get('realname'),
                    'telephone' => $request->get('telephone'),
                    'QQ' => $request->get('QQ'),
                    'password' => ManagerModel::encryptPassword($request->get('password'), $salt),
                    'birth' => $request->get('birth'),
                    'salt' => $salt,
                    'manage_city'=>$request->get('city'),
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
            }else{
                $data = array(
                    'realname' => $request->get('realname'),
                    'telephone' => $request->get('telephone'),
                    'QQ' => $request->get('QQ'),
                    'birth' => $request->get('birth'),
                    'manage_city'=>$request->get('city'),
                    'created_at' => date('Y-m-d H:i:s', time()),
                    'updated_at' => date('Y-m-d H:i:s', time())
                );
            }
            ManagerModel::where('id', $id)->update($data);
            $user = ManagerModel::where('id',$id)->first();
            if(!RoleUserModel::where('user_id',$id)->where('role_id',$request->get('role_id'))->first())
                $user->attachRole($request->get('role_id'));

        });
       if (is_null($status))
            return redirect('manage/managerList')->with(['message' => '操作成功']);
    }


    /**
     * 系统组列表
     *
     * @return mixed
     */
    public function getRolesList()
    {
        $list = Role::select('roles.id', 'roles.display_name', 'roles.updated_at')->orderBy('roles.id', 'DESC')->paginate(10);
        $data = array(
            'list'=>$list
        );
        return $this->theme->scope('manage.rolesList',$data)->render();
    }

    /**
     * 添加系统组视图
     *
     * @return mixed
     */
    public function getRolesAdd()
    {
        $tree_menu = Permission::getPermissionMenu();
        $data = array(
            'list' =>$tree_menu,
        );
        return $this->theme->scope('manage.rolesAdd',$data)->render();
    }
    /**
     * 添加系统组
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRolesAdd(Request $request)
    {
          if(!count($request->get('id'))){
            return redirect('manage/rolesAdd')->with(['message' => '请设置用户组权限']);
        }
        $status = DB::transaction(function () use ($request) {
            $data = array(
                'name' => $request->get('name'),
                'display_name'=>$request->get('display_name'),
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            );
            $role_id = Role::insertGetId($data);
            foreach ($request->get('id') as $id) {
                $role_id = $role_id;
                $data2 = array(
                    'permission_id' => $id,
                    'role_id' => $role_id
                );
                $re2 = PermissionRoleModel::insert($data2);
            }
        });
        if (is_null($status))
            return redirect('manage/rolesList')->with(['message' => '操作成功']);
    }

    /**
     * 删除系统组列表
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getRolesDel($id)
    {
        $status = DB::transaction(function () use ($id) {
            Role::where('id', $id)->delete();
            PermissionRoleModel::where('role_id',$id)->delete();
        });
        if (is_null($status))
            return redirect()->to('manage/rolesList')->with(['message' => '操作成功']);
    }

    /**
     * 系统组详情页
     *
     * @param $id
     * @return mixed
     */
    public function getRolesDetail($id)
    {
        $tree_menu = Permission::getPermissionMenu();

        $info1 = Role::where('id',$id)->first();
        $info = Role::select('roles.name','permissions.id','permissions.display_name')->join('permission_role','roles.id','=','permission_role.role_id')
            ->join('permissions','permissions.id','=','permission_role.permission_id')->where('roles.id',$id)->get();
        $ids = array();
        foreach ($info as $v) {
            $ids[] .= $v['id'];
        }
        $data = array(
            'ids'=>$ids,
            'info1'=>$info1,
            'info'=>$info,
            'list'=>$tree_menu,
        );
        return $this->theme->scope('manage.rolesDetail',$data)->render();
    }

    /**
     * 更新系统组详情页
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRolesDetail(Request $request)
    {
        $status = DB::transaction(function () use ($request) {
            $rid = $request->get('rid');
            $data = array(
                'name' => $request->get('name'),
                'display_name'=>$request->get('display_name'),
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time())
            );
            Role::where('id', $rid)->update($data);

            PermissionRoleModel::where('role_id', $rid)->delete();

            if($request->get('id')) {
                foreach ($request->get('id') as $id) {
                    $role_id = $rid;
                    $data2 = array(
                        'permission_id' => $id,
                        'role_id' => $role_id
                    );
                    PermissionRoleModel::insert($data2);
                }
            }
        });
        if (is_null($status))
            return redirect('manage/rolesList')->with(['message' => '操作成功']);
    }

    /**
     * 权限列表
     *
     * @return mixed
     */
    public function getPermissionsList(Request $request)
    {
        $merge = $request->all();
        $list = Permission::select('permissions.id','permissions.name','permissions.display_name','permissions.module_type','menu.name as menu_name')
            ->leftJoin('menu','menu.id','=','permissions.module_type');
        if ($request->get('id')){
            $list = $list->where('permissions.id', $request->get('id'));
        }
        if ($request->get('display_name')){
            $list = $list->where('permissions.display_name','like','%'. $request->get('display_name').'%');
        }
        if ($request->get('name')){
            $list = $list->where('permissions.name','like','%'.  $request->get('name').'%');
        }
        $order = $request->get('order') ? $request->get('order') : 'desc';
        if ($request->get('module_type')!=""){
            $list = $list->where('permissions.module_type', $request->get('module_type'));
        }
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        $list = $list->orderBy('permissions.id',$order)->paginate($paginate);
        $listArr = $list->toArray();
//        dd($listArr);
        $type = ModuleTypeModel::get();
        $data = array(
            'merge' => $merge,
            'listArr' => $listArr,
            'id'=>$request->get('id'),
            'display_name'=>$request->get('display_name'),
            'name'=>$request->get('name'),
            'module_type'=>$request->get('module_type'),
            'type'=>$type,
            'list'=>$list,
            'paginate' => $paginate,
        );
        return $this->theme->scope('manage.permissionsList',$data)->render();
    }

    /**
     * 添加权限视图
     *
     * @return mixed
     */
    public function getPermissionsAdd()
    {
        $modules = ModuleTypeModel::get();
        $data = array(
            'modules'=>$modules
        );
        return $this->theme->scope('manage.permissionsAdd',$data)->render();
    }

    /**
     * 添加权限
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPermissionsAdd(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $status = DB::transaction(function() use($data){
            $re =  Permission::insertGetId($data);
            //创建权限和菜单之间的关系
            $permission_user = ['menu_id'=>$data['module_type'],'permission_id'=>$re];
            MenuPermissionModel::insert($permission_user);
        });

        if(is_null($status))
            return redirect('manage/permissionsList')->with(['message' => '操作成功']);
    }

    /**
     * 删除权限
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getPermissionsDel($id){
        $re = Permission::where('id',$id)->delete();
        if($re)
            return redirect()->to('manage/permissionsList')->with(['message' => '操作成功']);
    }

    /**
     * 权限详情页
     *
     * @param $id
     * @return mixed
     */
    public function getPermissionsDetail($id)
    {
        //获取上一项id
        $preId = Permission::where('id', '>', $id)->min('id');
        //获取下一项id
        $nextId = Permission::where('id', '<', $id)->max('id');
        $info = Permission::select('permissions.*','mp.menu_id')
            ->where('permissions.id',$id)
            ->join('menu_permission as mp','permissions.id','=','mp.permission_id')
            ->first();
        $modules = ModuleTypeModel::get();
        $data = array(
            'modules'=>$modules,
            'info'=>$info,
            'preId'=>$preId,
            'nextId'=>$nextId
        );
        return $this->theme->scope('manage.permissionsDetail',$data)->render();
    }

    /**
     * 更新权限
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPermissionsDetail(Request $request)
    {
        $id = $request->get('id');
        $menu_id = $request->get('menu_id');
        $data = $request->except('id','_token','_url','menu_id');


        $re = Permission::where('id',$id)->update($data);
        $permission = Permission::where('id',$id)->first();
        //删除原有的权限菜单关系
        $result1 = MenuPermissionModel::where('permission_id',$permission['id'])->delete();
        $result = MenuPermissionModel::firstOrCreate(['menu_id'=>$menu_id,'permission_id'=>$permission['id']]);
        if($re || $result)
            return redirect('manage/permissionsList')->with(['message' => '操作成功']);

    }


    /**
     * @return mixed
     * 添加监理页面
     */
    public function supervisorAdd() {
        $province = DistrictModel::findTree(0);
        $data     = [
            'info' => [],
            'province' => $province,
            'city' => DistrictModel::getDistrictProvince(),
            'area' => [],
            'type' => '4',
        ];
        return $this->theme->scope('manage.supervisor.supervisorAdd', $data)->render();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 插入监理数据
     */
    public function supervisorInfoInsert(Request $request) {
        $data2 = $request->except(['_token', '_url']);
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'user-avatar' => 'required',
            'province' => 'required',
            'city' => 'required',
            'card_number' => 'required|alpha_num',
            'deposit_name' => 'required',
            'introduce' => 'required',
            'tag' => 'required',
            'sign' => 'required',
            'native_place' => 'required',
            'detail_address' => 'required',
            'card_front_side' => 'required|image',
            'card_back_dside' => 'required|image',
            'bank_account' => 'required|numeric',
            'workStar' => 'required|between:0,6|numeric|integer',
            'user_age' => 'required|integer',

        ],
            [
                'name.required' => '必须填写姓名',
                'password.required' => '必须填写密码',
                'detail_address.required' => '必须填写地址',
                'card_front_side.required' => '必须上传身份证正面',
                'card_back_dside.required' => '必须上传身份证反面',
                'card_front_side.image' => '身份证正面必须是图片（jpeg、png、bmp、gif或者svg）',
                'card_back_dside.image' => '身份证反面件必须是图片（jpeg、png、bmp、gif或者svg）',
                'user-avatar.required' => '必须上传头像',
                'province.required' => '必须选择省份',
                'city.required' => '必须选择城市',
                'card_number.required' => '必须填写身份证',
                'deposit_name.required' => '必须填写开户银行',
                'introduce.required' => '必须填写简介',
                'tag.required' => '必须填写个人特长',
                'sign.required' => '必须填写客户评价',
                'native_place.required' => '必须填写籍贯',
                'bank_account.required' => '必须填写银行卡号',
                'bank_account.numeric' => '银行卡号为纯数字',
                'workStar.required' => '必须填写星级',
                'workStar.integer' => '星级为纯数字',
                'workStar.numeric' => '星级为纯数字',
                'workStar.between' => '星级:min - :max位',
                'user_age.required' => '必须填写年龄',
                'user_age.numeric' => '年龄为纯数字',
                'card_number.alpha_num' => '身份证有数字和字母',
            ]
        );

        $data['username']     = trim($request->get('name'));
        $data['password']     = $request->get('password');
        $data['user_type']    = 4;
        $data['work_type']    = 2;//1管家,2监理
        $data['avatar']       = $request->get('user-avatar');
        $data['province']     = $request->get('province');
        $data['city']         = $request->get('city');
        $data['card_number']  = $request->get('card_number');
        $data['deposit_name'] = $request->get('deposit_name');
        $data['introduce']    = $request->get('introduce');
        $data['tag']          = $request->get('tag');
        $data['sign']         = $request->get('sign');
        $data['bank_account'] = $request->get('bank_account');
        $data['workStar']     = $request->get('workStar');
        $data['user_age']     = $request->get('user_age');
        $data['native_place'] = $request->get('native_place');
        $this->chatRoomRespository->RegistEaseMob($data['username']);
        $levelInfo = DB::table('level')->where('type', 2)->first()->upgrade;//管家星级对应的分数
        $upgrade   = json_decode($levelInfo, true);
        //所选星级不同,得分是不同的

        foreach ($upgrade as $key => $value) {
            if ($data['workStar'] == intval($key)) {
                $data['score'] = (int)$value;
                break;
            }
        }
        $data['demo'] = $request->get('demo');

        $is_reg = UserModel::where('name', $data['username'])->count();
        if ($is_reg) {
            return redirect('manage/supervisorAdd')->with(['message' => '该账户已存在']);
        }


        $now                         = time();
        $realnameInfo['realname']    = $request->get('realname');         //真实姓名
        $realnameInfo['card_number'] = $request->get('card_number');      //身份证号码
        $realnameInfo['serve_area']  = DistrictModel::getDistrictName($request->get('serve_city'));       //服务区域
        $data_city                   = DistrictModel::where('id', $data['city'])->first();
        if (!empty($data_city)) {
            $data['city'] = $data_city->name;
            $data['serve_area_id'] = $data_city->id;
        }
        $realnameInfo['address']         = $request->get('detail_address');          //地址
        $realnameInfo['lat']             = 1;              //纬度 $request->get('lat')
        $realnameInfo['lng']             = 1;              //经度 $request->get('lng')
        $realnameInfo['experience']      = $request->get('experience');       //经验
        $realnameInfo['card_front_side'] = $request->file('card_front_side');       //身份证正面
        $realnameInfo['card_back_dside'] = $request->file('card_back_dside');       //身份证反面
        $realnameInfo['created_at']      = date('Y-m-d H:i:s', $now);
        $realnameInfo['updated_at']      = date('Y-m-d H:i:s', $now);


        $allowExtension  = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        $card_front_side = json_decode(\FileClass::uploadFile($realnameInfo['card_front_side'], $path = 'user', $allowExtension), true);
        $card_back_dside = json_decode(\FileClass::uploadFile($realnameInfo['card_back_dside'], $path = 'user', $allowExtension), true);


        if ($card_front_side['code'] != 200 || $card_back_dside['code'] != 200) {
            return redirect('manage/userList')->with(['message' => '图片上传失败']);
        } else {
            $realnameInfo['card_front_side'] = $card_front_side['data']['url'];
            $realnameInfo['card_back_dside'] = $card_back_dside['data']['url'];
        }

        if (UserModel::createUserMobile($data, true, $realnameInfo)) {
            $user    = UserModel::where('name', $data['username'])->where('user_type', $data['user_type'])->first()->id;
            $auth_id = RealnameAuthModel::where('uid', $user)->first()->id;
            RealnameAuthModel::realnameAuthPass($auth_id);
            return redirect('manage/userList')->with(['message' => '操作成功']);
        } else {
            return redirect('manage/supervisorAdd')->with(['message' => '操作失败']);
        }

    }

    /**
     * @return mixed
     * 添加管家页面
     */
    public function housekeeperAdd() {
        $province = DistrictModel::findTree(0);

        $data = [
            'info' => [],
            'province' => $province,
            'city' => DistrictModel::getDistrictProvince(),
            'area' => [],
            'type' => '3',
        ];

        return $this->theme->scope('manage.housekeeper.housekeeperAdd', $data)->render();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 插入管家数据
     */
    public function housekeeperDataInsert(Request $request) {
        $data2 = $request->except(['_token', '_url']);

        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'user-avatar' => 'required',
            'province' => 'required',
            'city' => 'required',
            'card_number' => 'required|alpha_num',
            'deposit_name' => 'required',
            'introduce' => 'required',
            'tag' => 'required',
            'sign' => 'required',
            'native_place' => 'required',
            'detail_address' => 'required',
            'card_front_side' => 'required|image',
            'card_back_dside' => 'required|image',
            'bank_account' => 'required|numeric',
            'workStar' => 'required|between:0,6|numeric|integer',
            'user_age' => 'required|integer',

        ],
            [
                'name.required' => '必须填写姓名',
                'password.required' => '必须填写密码',
                'detail_address.required' => '必须填写地址',
                'card_front_side.required' => '必须上传身份证正面',
                'card_back_dside.required' => '必须上传身份证反面',
                'card_front_side.image' => '身份证正面必须是图片（jpeg、png、bmp、gif或者svg）',
                'card_back_dside.image' => '身份证反面件必须是图片（jpeg、png、bmp、gif或者svg）',
                'user-avatar.required' => '必须上传头像',
                'province.required' => '必须选择省份',
                'city.required' => '必须选择城市',
                'card_number.required' => '必须填写身份证',
                'deposit_name.required' => '必须填写开户银行',
                'introduce.required' => '必须填写简介',
                'tag.required' => '必须填写个人特长',
                'sign.required' => '必须填写客户评价',
                'native_place.required' => '必须填写籍贯',
                'bank_account.required' => '必须填写银行卡号',
                'bank_account.numeric' => '银行卡号为纯数字',
                'workStar.required' => '必须填写星级',
                'workStar.integer' => '星级为纯数字',
                'workStar.numeric' => '星级为纯数字',
                'workStar.between' => '星级:min - :max位',
                'user_age.required' => '必须填写年龄',
                'user_age.numeric' => '年龄为纯数字',
                'card_number.alpha_num' => '身份证有数字和字母',
            ]
        );

        $data['username']     = trim($request->get('name'));
        $data['password']     = $request->get('password');
        $data['user_type']    = 3;
        $data['work_type']    = 1;//1管家,2监理
        $data['avatar']       = $request->get('user-avatar');
        $data['province']     = $request->get('province');
        $data['city']         = $request->get('city');
        $data['card_number']  = $request->get('card_number');
        $data['deposit_name'] = $request->get('deposit_name');
        $data['introduce']    = $request->get('introduce');
        $data['tag']          = $request->get('tag');
        $data['sign']         = $request->get('sign');
        $data['bank_account'] = $request->get('bank_account');
        $data['workStar']     = $request->get('workStar');
        $data['user_age']     = $request->get('user_age');
        $data['native_place'] = $request->get('native_place');
        $this->chatRoomRespository->RegistEaseMob($data['username']);
        $levelInfo = DB::table('level')->where('type', 1)->first()->upgrade;//管家星级对应的分数
        $upgrade   = json_decode($levelInfo, true);
        //所选星级不同,得分是不同的

        foreach ($upgrade as $key => $value) {
            if ($data['workStar'] == intval($key)) {
                $data['score'] = (int)$value;
                break;
            }
        }

        $is_reg = UserModel::where('name', $data['username'])->count();
        if ($is_reg) {
            return redirect('manage/housekeeperAdd')->with(['message' => '该账户已存在']);
        }
        $data['demo']                 = $request->get('demo');
        $data['cost_of_design']       = intval($request->get('cost_of_design'));
        $now                          = time();
        $realnameInfo['realname']     = $request->get('realname');         //真实姓名
        $realnameInfo['user_age']     = $request->get('user_age');         //年龄
        $realnameInfo['native_place'] = $request->get('native_place');         //籍贯
        $realnameInfo['card_number']  = $request->get('card_number');      //身份证号码

        $realnameInfo['serve_area'] = DistrictModel::getDistrictName($request->get('serve_city'));       //服务区域

        $data_city = DistrictModel::where('id', $data['city'])->first();
        if (!empty($data_city)) {
            $data['city'] = $data_city->name;
            $data['serve_area_id'] = $data_city->id;
        }

        $realnameInfo['address']         = $request->get('detail_address');          //地址
        $realnameInfo['lat']             = 1;              //纬度 $request->get('lat')
        $realnameInfo['lng']             = 1;              //经度 $request->get('lng')
        $realnameInfo['experience']      = $request->get('experience');       //经验
        $realnameInfo['card_front_side'] = $request->file('card_front_side');       //身份证正面
        $realnameInfo['card_back_dside'] = $request->file('card_back_dside');       //身份证反面
        $realnameInfo['created_at']      = date('Y-m-d H:i:s', $now);
        $realnameInfo['updated_at']      = date('Y-m-d H:i:s', $now);

        $allowExtension  = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        $card_front_side = json_decode(\FileClass::uploadFile($realnameInfo['card_front_side'], $path = 'user', $allowExtension), true);
        $card_back_dside = json_decode(\FileClass::uploadFile($realnameInfo['card_back_dside'], $path = 'user', $allowExtension), true);

        if ($card_front_side['code'] != 200 || $card_back_dside['code'] != 200) {
            return redirect('manage/userList')->with(['message' => '图片上传失败']);
        } else {
            $realnameInfo['card_front_side'] = $card_front_side['data']['url'];
            $realnameInfo['card_back_dside'] = $card_back_dside['data']['url'];
        }


        if (UserModel::createUserMobile($data, true, $realnameInfo)) {

            $user = UserModel::where('name', $data['username'])->where('user_type', $data['user_type'])->first()->id;

            $auth_id = RealnameAuthModel::where('uid', $user)->first()->id;
            RealnameAuthModel::realnameAuthPass($auth_id);
            return redirect('manage/userList')->with(['message' => '操作成功']);
        } else {
            return redirect('manage/housekeeperAdd')->with(['message' => '操作失败']);
        }

    }

    /**
     * @return mixed
     * 添加设计师页面
     */
    public function designerAdd() {
        $province = DistrictModel::findTree(0);
        $data     = [
            'info' => [],
            'province' => $province,
            'city' => DistrictModel::getDistrictProvince(),
            'area' => [],
            'type' => '2',
        ];
        return $this->theme->scope('manage.designerAdd', $data)->render();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 插入设计师数据
     */
    public function designerInfoInsert(Request $request) {
        $data2 = $request->except(['_token', '_url']);

        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'user-avatar' => 'required',
            'cost_of_design' => 'required|numeric',
            'province' => 'required',
            'city' => 'required',
            'card_number' => 'required|alpha_num',
            'deposit_name' => 'required',
            'introduce' => 'required',
            'tag' => 'required',
            'sign' => 'required',
            'native_place' => 'required',
            'detail_address' => 'required',
            'card_front_side' => 'required|image',
            'card_back_dside' => 'required|image',
            'bank_account' => 'required|numeric',
            'user_age' => 'required|integer',

        ],
            [
                'name.required' => '必须填写姓名',
                'cost_of_design.required' => '必须填写面积单价',
                'cost_of_design.numeric' => '面积单价必须是数值',
                'password.required' => '必须填写密码',
                'detail_address.required' => '必须填写地址',
                'card_front_side.required' => '必须上传身份证正面',
                'card_back_dside.required' => '必须上传身份证反面',
                'card_front_side.image' => '身份证正面必须是图片（jpeg、png、bmp、gif或者svg）',
                'card_back_dside.image' => '身份证反面件必须是图片（jpeg、png、bmp、gif或者svg）',
                'user-avatar.required' => '必须上传头像',
                'province.required' => '必须选择省份',
                'city.required' => '必须选择城市',
                'card_number.required' => '必须填写身份证',
                'deposit_name.required' => '必须填写开户银行',
                'introduce.required' => '必须填写简介',
                'tag.required' => '必须填写个人特长',
                'sign.required' => '必须填写客户评价',
                'native_place.required' => '必须填写籍贯',
                'bank_account.required' => '必须填写银行卡号',
                'bank_account.numeric' => '银行卡号为纯数字',
                'user_age.required' => '必须填写年龄',
                'user_age.numeric' => '年龄为纯数字',
                'card_number.alpha_num' => '身份证有数字和字母',
            ]
        );

        $data['username']       = trim($request->get('name'));
        $data['password']       = $request->get('password');
        $data['user_type']      = 2;
        $data['avatar']         = $request->get('user-avatar');
        $data['cost_of_design'] = $request->get('cost_of_design');
        $data['province']       = $request->get('province');
        $data['city']           = $request->get('city');

        $data['card_number']  = $request->get('card_number');
        $data['deposit_name'] = $request->get('deposit_name');
        $data['introduce']    = $request->get('introduce');
        $data['tag']          = $request->get('tag');
        $data['sign']         = $request->get('sign');
        $data['bank_account'] = $request->get('bank_account');

        $data['user_age']     = $request->get('user_age');
        $data['native_place'] = $request->get('native_place');
        $this->chatRoomRespository->RegistEaseMob($data['username']);

        $data['demo'] = $request->get('demo');

        $is_reg = UserModel::where('name', $data['username'])->count();
        if ($is_reg) {
            return redirect('manage/designerAdd')->with(['message' => '该账户已存在']);
        }

        $now                         = time();
        $realnameInfo['realname']    = $request->get('realname');         //真实姓名
        $realnameInfo['card_number'] = $request->get('card_number');      //身份证号码

        $realnameInfo['serve_area'] = DistrictModel::getDistrictName($request->get('serve_city'));       //服务区域

        $data_city = DistrictModel::where('id', $data['city'])->first();
        if (!empty($data_city)) {
            $data['city'] = $data_city->name;
            $data['serve_area_id'] = $data_city->id;
        }

        $realnameInfo['address']         = $request->get('detail_address');          //地址
        $realnameInfo['lat']             = 1;              //纬度 $request->get('lat')
        $realnameInfo['lng']             = 1;              //经度 $request->get('lng')
        $realnameInfo['experience']      = $request->get('experience');       //经验
        $realnameInfo['card_front_side'] = $request->file('card_front_side');       //身份证正面
        $realnameInfo['card_back_dside'] = $request->file('card_back_dside');       //身份证反面
        $realnameInfo['created_at']      = date('Y-m-d H:i:s', $now);
        $realnameInfo['updated_at']      = date('Y-m-d H:i:s', $now);

        foreach ($realnameInfo as $key => $value) {
            if (empty($value)) {
                return redirect('manage/designerAdd')->with(['message' => $key . '必要信息不可为空']);
            }
        }

        $allowExtension  = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        $card_front_side = json_decode(\FileClass::uploadFile($realnameInfo['card_front_side'], $path = 'user', $allowExtension), true);
        $card_back_dside = json_decode(\FileClass::uploadFile($realnameInfo['card_back_dside'], $path = 'user', $allowExtension), true);


        if ($card_front_side['code'] != 200 || $card_back_dside['code'] != 200) {
            return redirect('manage/userList')->with(['message' => '图片上传失败']);
        } else {
            $realnameInfo['card_front_side'] = $card_front_side['data']['url'];
            $realnameInfo['card_back_dside'] = $card_back_dside['data']['url'];
        }

        if (UserModel::createUserMobile($data, true, $realnameInfo)) {
            $user    = UserModel::where('name', $data['username'])->first()->id;
            $auth_id = RealnameAuthModel::where('uid', $user)->first()->id;
            RealnameAuthModel::realnameAuthPass($auth_id);
            return redirect('manage/userList')->with(['message' => '操作成功']);
        } else {
            return redirect('manage/designerAdd')->with(['message' => '操作失败']);
        }
    }

    // 设计师作品列表
    public function workGoodsList( Request $request , $worker_id ){
        $this->theme->setTitle('作品管理');
        $shop_id = ShopModel::where('uid',$worker_id)->first()->id;
        $goods   = GoodsModel::where('shop_id',$shop_id)->where('type',1)->where('is_delete',0)->get();

        $view = [
            'goods'=>$goods,
            'workder_id'=>$worker_id
        ];

        return $this->theme->scope('manage.manage_goods_list.goods_list', $view)->render();
    }


//    版块列表
    public function editSection( Request $request , $goods_id , $worker_id){
        $this->theme->setTitle('版块管理');
        $goods_id = intval($goods_id);
        $worker_id = intval($worker_id);
        $sections = UnionAttachmentModel::select('attachment.*')
                                        ->leftJoin('attachment', 'union_attachment.attachment_id', '=', 'attachment.id')
                                        ->where('union_attachment.object_id', $goods_id)->get();

        if(empty($sections->toArray())){
            return redirect('manage/workGoodsList/'.$worker_id)->with(['message' => '暂无版块图片信息']);exit;
        }
        $view = [
            'goods'=>$sections,
            'goods_id'=>$goods_id,
            'worker_id'=>$worker_id
        ];
        return $this->theme->scope('manage.manage_goods_list.section_list', $view)->render();
    }


//    版块信息提交
    public function section_edit_submit(Request $request){
        $title = $request->get('section_title');
        $desc = $request->get('section_desc');
        $worker_id = $request->get('worker_id');
        $goods_id = $request->get('goods_id');
        $arr = [];

        foreach($title as $key => $value){
            AttachmentModel::where('id',$key)->update(['title'=>$value , 'desc'=>$desc[$key]]);
//            $arr[] = array( 'id'=>$key,'title'=>$value,'desc'=>$desc[$key] );
        }


        return redirect('manage/editSection/'.$goods_id.'/'.$worker_id)->with(['message' => '修改成功']);



//        DB::table('users')->update( array(
//            array('email' => 'aaa@example.com', 'name' => 'zhangsan', 'age'=> 20),
//            array('email' => 'bbb@example.com', 'name' => 'wangwu', 'age'=> 25),
//            array('email' => 'ccc@example.com', 'name' => 'chenliu', 'age'=> 50),
//  ...
//) , 'email' );

    }


    // 删除设计师作品
    public function delWorkGoods( $id , $worker_id ){
        GoodsModel::where('id',$id)->update(['is_delete'=>1]);
        return redirect('manage/workGoodsList/'.$worker_id)->with(['message' => '删除成功']);
    }

    // 编辑设计师作品视图
    public function editWorkGoods( $goods_id ){
        $this->theme->setTitle('编辑作品');
        $info = GoodsModel::where('id',$goods_id)->first();
        $house = HouseModel::where('is_deleted',0)->get();
        $style = TaskCateModel::get();
        $view = [
            'info'=>$info,
            'goods_id'=>$goods_id,
            'house'=>$house,
            'style'=>$style
        ];
        return $this->theme->scope('manage.manage_goods_list.goods_edit', $view)->render();
    }

//    添加设计师作品视图
    public function addWorkGoods( $worker_id ){
        $this->theme->setTitle('添加作品');
        $house = HouseModel::where('is_deleted',0)->get();
        $style = TaskCateModel::get();
//        var_dump($style);exit;
        $view = [
            'worker_id'=>$worker_id,
            'house'=>$house,
            'style'=>$style
        ];
//        $work_id = $request->get('')
        return $this->theme->scope('manage.manage_goods_list.goods_add',$view)->render();
    }


//    处理添加或修改提交的作品信息
    public function handleWorkGoodsSub( Request $request ){
        $data['uid'] = $request->get('worker_id');
        $goods_id    = $request->get('goods_id');

        $section             = $request->get("section");
        $sectionArr['des']   = $section['des'];
        $sectionArr['title'] = $section['position'];
        $sectionArr['img']   = $request->file('section')['img'];

        $data['title']         = trim($request->get('goods_name'));
        $data['square']        = $request->get('goods_square');
        $data['house_id']      = $request->get('goods_house');
        $data['style_id']      = $request->get('goods_style');
        $data['goods_address'] = trim($request->get('goods_address'));
        $data['status']        = 1;
        $data['shop_id']       = ShopModel::where('uid', $data['uid'])->first()->id;
        $data['cate_id']       = $data['style_id'];     //这个参数是冗余的，保留一下
        $data['cover']         = $request->file('main_file');

        foreach($data as $key=>$value){
            if(empty($value)){
                return redirect()->with(['message' => '上传失败']);
            }
        }

        $result = \FileClass::uploadFile($data['cover'], 'sys',null,true);

        if ($result) {
            $result        = json_decode($result, true);
            $data['cover'] = $result['data']['url'];
        } else {
            return redirect()->with(['message' => '上传失败']);
        }


        if(!empty($goods_id)){
            //编辑
        }else{
            $ret = GoodsModel::create($data);

            if(!empty($sectionArr['img'][0])){
//                echo 1;exit;
                $sec['object_id'] = $ret->id;        //发布的作品id
                $sec['user_id']   = $data['uid'];       //用户id
                foreach($sectionArr['img'] as $key => $value){
                    $sec['picture']   = $value;       //图片
                    $sec['title']     = $sectionArr['title'][$key];
                    $sec['desc']      = $sectionArr['des'][$key];

                    $attachment_id = $this->fileUpload($sec);

                    $arrAttachment[] = [
                        'object_id' => $sec['object_id'],
                        'object_type' => 4,
                        'attachment_id' => $attachment_id,
                        'created_at' => date('Y-m-d H:i:s', time())
                    ];
//              TODO  未有处理某版块上传失败的操作
                }
                UnionAttachmentModel::insert($arrAttachment);
            }
        }


        if($ret){
            return redirect('/manage/workGoodsList/'.$data['uid'])->with(['message' => '操作成功']);
        }else{
            return redirect()->with(['message' => '操作失败']);
        }


    }


    // 上传作品版块图片
    public function fileUpload($data) {
        $file = $data['picture'];

        //将文件上传的数据存入到attachment表中
        $attachment = \FileClass::uploadFile($file, 'user',null,true);
        $attachment = json_decode($attachment, true);
        //判断文件是否上传
        if ($attachment['code'] != 200) {
            return redirect()->with(['message' => '文件上传失败']);
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
            return redirect()->with(['message' => '文件上传失败']);
        }
        //回传附件id
        return $result['id'];
    }





    //添加工人
    public function laborAdd() {
        $province = DistrictModel::findTree(0);
        $worker = ConfigModel::getConfigByAlias('worker');
        if(empty($worker)) {
            return redirect('/manage/addWorker')->with(['message'=>'请先添加工种！']);
        }
        $worker = $worker->toArray();
        $rule = json_decode($worker['rule'], true);

        $data = [
            'info' => [],
            'province' => $province,
            'city' => DistrictModel::getDistrictProvince(),
            'area' => [],
            'type' => 5,
            'worker' => $rule
        ];
        return $this->theme->scope('manage.labor.labor', $data)->render();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 工人数据插入
     */
    public function laborDataInsert(Request $request) {
        $data2 = $request->except(['_token', '_url']);

        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'user-avatar' => 'required',
            'province' => 'required',
            'city' => 'required',
            'card_number' => 'required|alpha_num',
            'deposit_name' => 'required',
            'native_place' => 'required',
            'detail_address' => 'required',
            'card_front_side' => 'required|image',
            'card_back_dside' => 'required|image',
            'bank_account' => 'required|numeric',
            'workStar' => 'required|between:0,6|numeric|integer',
            'work_type' => 'required|between:4,11|numeric|integer',
            'user_age' => 'required|integer',

        ],
            [
                'name.required' => '必须填写姓名',
                'password.required' => '必须填写密码',
                'detail_address.required' => '必须填写地址',
                'card_front_side.required' => '必须上传身份证正面',
                'card_back_dside.required' => '必须上传身份证反面',
                'card_front_side.image' => '身份证正面必须是图片（jpeg、png、bmp、gif或者svg）',
                'card_back_dside.image' => '身份证反面件必须是图片（jpeg、png、bmp、gif或者svg）',
                'user-avatar.required' => '必须上传头像',
                'province.required' => '必须选择省份',
                'city.required' => '必须选择城市',
                'card_number.required' => '必须填写身份证',
                'deposit_name.required' => '必须填写开户银行',
                'native_place.required' => '必须填写籍贯',
                'bank_account.required' => '必须填写银行卡号',
                'bank_account.numeric' => '银行卡号为纯数字',
                'workStar.required' => '必须填写星级',
                'workStar.integer' => '星级为纯数字',
                'workStar.numeric' => '星级为纯数字',
                'workStar.between' => '星级:min - :max位',
                'work_type.integer' => '星级为纯数字',
                'work_type.numeric' => '星级为纯数字',
                'work_type.between' => '星级:min - :max位',
                'user_age.required' => '必须填写年龄',
                'user_age.numeric' => '年龄为纯数字',
                'card_number.alpha_num' => '身份证有数字和字母',
            ]
        );
        $data['username']     = trim($request->get('name'));
        $data['password']     = $request->get('password');
        $data['user_type']    = 5;
        $data['avatar']       = $request->get('user-avatar');
        $data['workStar']     = $request->get('workStar');
        $data['work_type']    = $request->get('work_type');
        $data['province']     = $request->get('province');
        $data['city']         = $request->get('city');
        $data['card_number']  = $request->get('card_number');
        $data['deposit_name'] = $request->get('deposit_name');
        $data['bank_account'] = $request->get('bank_account');

        $levelInfo = DB::table('level')->where('type', $data['work_type'])->first()->upgrade;//管家星级对应的分数
        $upgrade   = json_decode($levelInfo, true);
        //所选星级不同,得分是不同的

        foreach ($upgrade as $key => $value) {
            if ($data['workStar'] == intval($key)) {
                $data['score'] = (int)$value;
                break;
            }
        }

        $data['user_age']     = $request->get('user_age');
        $data['native_place'] = $request->get('native_place');

        $is_reg = UserModel::where('name', $data['username'])->count();
        if ($is_reg) {
            return redirect('manage/laborAdd')->with(['message' => '该账户已存在']);
        }
        $now                         = time();
        $realnameInfo['realname']    = $request->get('realname');         //真实姓名
        $realnameInfo['card_number'] = $request->get('card_number');      //身份证号码
        $realnameInfo['serve_area']  = DistrictModel::getDistrictName($request->get('serve_city'));       //服务区域

        $data_city = DistrictModel::where('id', $data['city'])->first();
        if (!empty($data_city)) {
            $data['city'] = $data_city->name;
            $data['serve_area_id'] = $data_city->id;
        }

        $realnameInfo['address']         = $request->get('detail_address');          //地址
        $realnameInfo['lat']             = 1;              //纬度 $request->get('lat')
        $realnameInfo['lng']             = 1;              //经度 $request->get('lng')
        $realnameInfo['experience']      = $request->get('experience');       //经验
        $realnameInfo['card_front_side'] = $request->file('card_front_side');       //身份证正面
        $realnameInfo['card_back_dside'] = $request->file('card_back_dside');       //身份证反面
        $realnameInfo['created_at']      = date('Y-m-d H:i:s', $now);
        $realnameInfo['updated_at']      = date('Y-m-d H:i:s', $now);


        $allowExtension  = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        $card_front_side = json_decode(\FileClass::uploadFile($realnameInfo['card_front_side'], $path = 'user', $allowExtension), true);
        $card_back_dside = json_decode(\FileClass::uploadFile($realnameInfo['card_back_dside'], $path = 'user', $allowExtension), true);


        if ($card_front_side['code'] != 200 || $card_back_dside['code'] != 200) {
            return redirect('manage/userList')->with(['message' => '图片上传失败']);
        } else {
            $realnameInfo['card_front_side'] = $card_front_side['data']['url'];
            $realnameInfo['card_back_dside'] = $card_back_dside['data']['url'];
        }

        if (UserModel::createUserMobile($data, true, $realnameInfo)) {

            $user    = UserModel::where('name', $data['username'])->where('user_type', $data['user_type'])->first()->id;
            $auth_id = RealnameAuthModel::where('uid', $user)->first()->id;
            RealnameAuthModel::realnameAuthPass($auth_id);
            return redirect('manage/userList')->with(['message' => '操作成功']);
        } else {
            return redirect('manage/laborAdd')->with(['message' => '操作失败']);
        }
    }

    /**
     * 管家提交整改单已到需要平台匹配的阶段
     */

    public function projectChangeConfList() {
        $this->theme->setTitle('整改工程');
        $list = ProjectLaborChange::select('task.id', 'task.created_at', 'task.title')->leftJoin('task', 'task.id', '=', 'project_labor_changes.task_id')->where('project_labor_changes.status', 5)->get();
        $data = [
            'list' => $list,
            'type' => 'projectChangeConfList'
        ];

        return $this->theme->scope('manage.project_conf_list.projectConfList', $data)->render();
    }



    //获取需要配置工人的工程任务（业主 - 管家）
    // project_configure_tasks是记录最原始的版本
    // 这个方法是用于管家提交且用户确认的配置单，整改单不包含在其中;
    public function projectConfList(){
        $list = WorkOfferModel::select('task.id' , 'task.created_at' , 'task.title')->leftJoin('task','task.id','=','work_offer.task_id')
            ->where('work_offer.sn',2)
            ->where('work_offer.status',1)
            ->where('task.user_type',3)
            ->get();
        $data = [
            'list' => $list,
            'type' => 'projectConfDetail'
        ];
//        dd($data['type']);
        return $this->theme->scope('manage.project_conf_list.projectConfList',$data)->render();
    }


    //进入工程详细配置页
    public function projectConfDetail( $task_id ){
        $deatil     = ProjectConfigureTask::where('task_id',$task_id)->where('is_sure',1)->first();     //当期最终的配置单
        $taskDetail = TaskModel::where('id',$task_id)->first();

        $deatil = unserialize($deatil->project_con_list);

        $total  = $deatil['all_parent_price'];
        unset($deatil['all_parent_price']);
        $type = 'projectConfDetail';
        $arr = [];
        foreach($deatil as $key => $value){

            $arr[$key]['project_type_name'] = $value['parent_name'];
            $arr[$key]['row'] = count($value['childs'])+1;
            $arr[$key]['child'] = $value['childs'];
            $arr[$key]['id'] = $key;
            $arr[$key]['project_type'] = $value['parent_project_type'];
//            TODO 5点了，好困，不想改得更好了 ， 写得废了点，迟些再改回来
            $needWorks = ProjectConfigureModel::where('pid',0)->where('project_type',$value['parent_project_type'])->first();

            if($value['parent_project_type'] != 2){
                $arr[$key]['need_work'] = UserModel::select('user_detail.realname','user_detail.uid')
                                                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                                                    ->where('users.user_type',5)
                                                    ->where('user_detail.work_type' , $needWorks->work_type)
                                                    ->where('star','>=',$taskDetail->workerStar)
                                                    ->get()->toArray();
//                $value['parent_name'].'('.'YZ'.sprintf("%06d", $value['id']).')';
                foreach($arr[$key]['need_work'] as $key2 => $value2){
                    $arr[$key]['need_work'][$key2]['worker_number'] = 'YZ'.sprintf("%06d", $value2['uid']);
                }
            }else{
                $need = explode('-',$needWorks->work_type);
                $arr[$key]['need_work'] = UserModel::select('user_detail.realname','user_detail.uid')
                                                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                                                    ->where('users.user_type',5)
                                                    ->where('user_detail.work_type' , $need[0])
                                                    ->where('star','>=',$taskDetail->workerStar)
                                                    ->get()->toArray();

                $arr[$key]['need_work_2'] = UserModel::select('user_detail.realname','user_detail.uid')
                                                    ->leftJoin('user_detail' , 'users.id' , '=' , 'user_detail.uid')
                                                    ->where('users.user_type',5)
                                                    ->where('star','>=',$taskDetail->workerStar)
                                                    ->where('user_detail.work_type' , $need[1])
                                                    ->get()->toArray();

                foreach($arr[$key]['need_work'] as $key2 => $value2){
                    $arr[$key]['need_work'][$key2]['worker_number'] = 'YZ'.sprintf("%06d", $value2['uid']);
                }

                foreach($arr[$key]['need_work_2'] as $key2 => $value2){
                    $arr[$key]['need_work_2'][$key2]['worker_number'] = 'YZ'.sprintf("%06d", $value2['uid']);
                }
            }
        }

        $data = [
            'deatil' => $arr,
            'task_id'=>$task_id,
            'type'=>$type
        ];
//var_dump($arr['parent_1']);exit;
        return $this->theme->scope('manage.project_conf_list.firstConfDetail',$data)->render();
    }


    /**
     * 整改单进入详细配置页
     */
    public function projectChangeConfDetail($task_id) {

        $changeList = ProjectLaborChange::where('task_id', $task_id)->where('status', 5)->first();     //当期最终的配置单

        $detail     = unserialize($changeList->list_detail);

        $taskDetail = TaskModel::where('id', $task_id)->first();
        $total      = $detail['all_parent_price'];
        $type       = 'projectChangeConfDetail';
        unset($detail['all_parent_price']);

        $arr = [];
        // 当前选中的工人
        $now_worker = WorkOfferModel::where('task_id',$task_id)->where('sn',$changeList->sn)->first();
        $now_worker_arr = explode('-',$now_worker->to_uid);
        foreach($now_worker_arr as $key => $value){
            $change_work[] = $value;
        }

        $now_worker2 = ProjectLaborChange::where('task_id',$task_id)->get();
        foreach($now_worker2 as $key => $value){
            $now_worker_arr = explode('-',$value['old_labor']);
            foreach($now_worker_arr as $key2 => $value2){
                $change_work[] = $value2;
            }
        }


        $replace_work_type = [];
        // TODO 后期重构
        //判断是否需要两个工种都替换
        foreach($detail as $key => $value){
            if(!is_numeric($value)){
                foreach($value['childs'] as $key2 => $value2){
                    if(!in_array($value2['work_type'] , $replace_work_type)){
                        $replace_work_type[] = $value2['work_type'];
                    }
                }
            }
        }

//dd($replace_work_type);
        foreach ($detail as $key => $value) {
            $arr[$key]['project_type_name'] = $value['parent_name'];
            $arr[$key]['row']               = count($value['childs']) + 1;
            $arr[$key]['child']             = $value['childs'];
            $arr[$key]['id']                = $key;
            $arr[$key]['project_type']      = $value['parent_project_type'];

//var_dump($arr[$key]['row']);exit;
            //获取每个阶段可以选择的工种工人，
//  TODO 剔除替换过的工人

            if ($value['parent_project_type'] != 2) {
                $arr[$key]['need_work'] = UserModel::select('user_detail.realname', 'user_detail.uid')
                    ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
                    ->where('users.user_type', 5)
                    ->where('user_detail.work_type', $replace_work_type[0])
                    ->where('star', $taskDetail->workerStar)
                    ->get()->toArray();

                foreach($arr[$key]['need_work'] as $key6 => $value6){
                    $arr[$key]['need_work'][$key6]['worker_number'] = 'YZ'.sprintf("%06d", $value6['uid']);
                }

            } else {
                //TODO 这里写死了，之后再改回来吧
                $need = $replace_work_type;
                foreach($replace_work_type as $key2 => $value2){
                    if($value2 == '5'){
                        $arr[$key]['need_work'] = UserModel::select('user_detail.realname', 'user_detail.uid')
                            ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
                            ->where('users.user_type', 5)
                            ->where('user_detail.work_type', $value2)
                            ->where('star', '>=',$taskDetail->workerStar)//这里先拉取所有比业主要求的星级高的工人
                            ->get()->toArray();

                        foreach($change_work as $key3 => $value3){

                            foreach($arr[$key]['need_work'] as $key4 => $value4){
                                if($value3 == $value4['uid']){
                                    unset($arr[$key]['need_work'][$key4]);
                                }
                            }
                        }

                        foreach($arr[$key]['need_work'] as $key6 => $value6){
                            $arr[$key]['need_work'][$key6]['worker_number'] = 'YZ'.sprintf("%06d", $value6['uid']);
                        }

                    }
                    if($value2 == '7'){
                        $arr[$key]['need_work_2'] = UserModel::select('user_detail.realname', 'user_detail.uid')
                            ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
                            ->where('users.user_type', 5)
                            ->where('user_detail.work_type', $value2)
                            ->where('star', '>=',$taskDetail->workerStar)//这里先拉取所有比业主要求的星级高的工人
                            ->get()->toArray();

                        foreach($change_work as $key3 => $value3){
                            foreach($arr[$key]['need_work_2'] as $key4 => $value4){
                                if($value3 == $value4['uid']){
                                    unset($arr[$key]['need_work_2'][$key4]);
                                }
                            }
                        }

                        foreach($arr[$key]['need_work_2'] as $key6 => $value6){
                            $arr[$key]['need_work_2'][$key6]['worker_number'] = 'YZ'.sprintf("%06d", $value6['uid']);
                        }

                    }
                }

            }
        }


        $data = [
            'deatil' => $arr,
            'task_id' => $task_id,
            'type'=>$type
        ];

        return $this->theme->scope('manage.project_conf_list.firstConfDetail', $data)->render();
    }




    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 提交工人配置
     */
    public function subWorkerConf( Request $request ){
        $project = $request->get('project');
        $task_id = $request->get('task_id');
        $type    = $request->get('type');

        if($type == 'projectConfDetail'){
            // 平台为工程配置单匹配工人
            $taskDetail = WorkOfferModel::where('task_id',$task_id)->where('project_type',0)->where('sn',2)->where('status',1)->first();

            if(empty($taskDetail)){
                return redirect('manage/projectConfList')->with(['message' => '非法操作，工程处于非匹配节点']);
            }

            foreach($project as $key => $value){
                if($key == 2){
                    foreach($value as $key2 => $value2){
                        if(empty($value2)){
                            return redirect('manage/projectConfDetail/'.$task_id)->with(['message' => '请确认每个阶段已选择工人']);
                        }
                    }
                }
                if(empty($value)){
                    return redirect('manage/projectConfDetail/'.$task_id)->with(['message' => '请确认每个阶段已选择工人']);
                }
            }

            foreach ($project as $key => $value) {
                //            TODO 这里有两层是因为水电有两个工种
                if ($key == 2) {

                    WorkOfferModel::where('task_id', $task_id)->where('project_type', $key)->update(['to_uid' => implode('-', $value)]);
                    //推送给工人
                   /* $house_uid   = implode('-', $value);
                    $application = 50006;
                    $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_match_labor_finish')->first()->chn_name;
                    $woker_info  = UserModel::find($house_uid);
                    $woker_info->send_num += 1;
                    $woker_info->save();
                    //保存发送的消息
                    save_push_msg($message, $application, $house_uid);
                    PushServiceModel::pushMessageWorker($woker_info->device_token, $message, $woker_info->send_num, $application);*/
                } else {
                    if ($key == 7) {
                        // 第7阶段的工程由管家接手
                        WorkOfferModel::where('task_id', $task_id)->where('project_type', $key)->update(['to_uid' => $taskDetail->to_uid]);
                    } else {
                        WorkOfferModel::where('task_id', $task_id)->where('project_type', $key)->update(['to_uid' => $value[0]]);

                        //推送给工人
                        /*$house_uid   = $value[0];
                        $application = 50006;
                        $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_match_labor_finish')->first()->chn_name;
                        $woker_info  = UserModel::find($house_uid);
                        $woker_info->send_num += 1;
                        $woker_info->save();
                        //保存发送的消息
                        save_push_msg($message, $application, $house_uid);
                        PushServiceModel::pushMessageWorker($woker_info->device_token, $message, $woker_info->send_num, $application);*/
                    }
                }
            }
            WorkOfferModel::where('task_id',$task_id)->where('project_type',0)->where('sn',2)->update(['status'=>4]);

            $application            = 50006;
            //推送给业主
            $data_user['boss_uid'] = $taskDetail->from_uid;
/*            $message    = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_match_labor_finish')->first()->chn_name;
            $woker_info = UserModel::find($data_user['boss_uid']);
            $woker_info->send_num += 1;
            $woker_info->save();
            //保存发送的消息
            save_push_msg($message, $application, $data_user['boss_uid']);
            PushServiceModel::pushMessageBoss($woker_info->device_token, $message, $woker_info->send_num, $application);*/
            push_accord_by_equip($data_user['boss_uid'],$application,'message_match_labor_finish','',$task_id);

            //推送给管家
            $data_user['house_uid'] = $taskDetail->to_uid;
            //推送给监理(如果有的话)
            $project_position = TaskModel::find($task_id)->project_position;
            $super_task  = TaskModel::select('id')->where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 4)->first();//监理的单
            if (!empty($super_task)) {
                $visor_work             = WorkOfferModel::select('to_uid')->where('sn', 0)->where('task_id', $super_task->id)->first();
                if(!empty($visor_work)){
                    $data_user['visor_uid'] = $visor_work->to_uid;
                }
            }
            foreach ($data_user as $k => $v) {
/*                $message    = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_match_labor_finish')->first()->chn_name;
                $woker_info = UserModel::find($v);
                $woker_info->send_num += 1;
                $woker_info->save();
                //保存发送的消息
                save_push_msg($message, $application, $v);
                PushServiceModel::pushMessageWorker($woker_info->device_token, $message, $woker_info->send_num, $application);*/
                push_accord_by_equip($v, $application, 'message_match_labor_finish', '', $task_id);
            }
        }

        if($type == 'projectChangeConfDetail'){
            //平台为整改单匹配工人
            $project_type = key($project);
            $original = WorkOfferModel::where('task_id',$task_id)->where('project_type',$project_type)->first();
            if(empty($original)){
                return redirect('manage/projectChangeConfDetail/'.$task_id)->with(['message' =>'不存在的工程阶段']);
            }

            if($project_type == 2) {

                $original_worker = explode('-', $original->to_uid);  //原来的工人

                //  TODO 太废了，要重构
                foreach ($original_worker as $key => $value) {

                    $original_work_type = UserDetailModel::where('uid', $value)->first();
                    foreach ($project as $key2 => $value2) {
                        foreach ($value2 as $key3 => $value3) {
                            $sub_work_type = UserDetailModel::where('uid', $value3)->first();
                            if ($original_work_type->work_type == $sub_work_type->work_type) {
                                $original_worker[$key] = $value3;

                            }
                        }
                    }
                }
                $project[2] = $original_worker;
            }


                $ret = $this->platformAllotLabor($task_id, 1, $project);

                if (!$ret['status']) {
                    return redirect('manage/projectChangeConfDetail/' . $task_id)->with(['message' => $ret['error']]);
                } else {

                    WorkOfferModel::where('task_id',$task_id)->where('project_type',$project_type)->update(['status'=>0]);
                    return redirect('manage/projectChangeConfList')->with(['message' => $ret['error']]);
                }

        }
        return redirect('manage/projectConfList')->with(['message' => '操作成功']);
    }



    /**
     * 平台分配工人,更换工人,结算原工人的钱,存入冻结金(方案一)
     */
    protected function platformAllotLabor($task_id, $is_sure, $project) {

//        $task_id = 281;//任务id
//        $is_sure = 1;//确认
//        $project = [2 => [2, 9]];//换两个人,水电阶段
//        $project = [1 => [8]];//换一个人,其他阶段

        //把新工人的数据搞出来
        foreach ($project as $k => $v) {

            if (count($v) > 1) {
                $new_project['labor'] = implode($v, '-');
            } else {
                $new_project['labor'] = $v;
            }
            $new_project['project_type'] = $k;
        }

        //5泥水工,6木工,7水电工,8油漆工,9安装工,10拆除工work_type
        //1拆除 2水电 3防水 4泥工 5木工 6油漆 7综合project_type

        // 原阶段的数据
        $eachWorkerPrice = ProjectConfigureTask::where('task_id', $task_id)->where('is_sure', 1)->first();//拿到原始单
        if ($new_project['project_type'] == 2) {
            $eachWorkerPriceArr = unserialize($eachWorkerPrice->project_con_list)['parent_2'];//拿到水电阶段原始数据
        } else {
            $eachWorkerPriceArr = unserialize($eachWorkerPrice->project_con_list);
        }

        //整改阶段的数据
        $data_labor = ProjectLaborChange::where('task_id', $task_id)->where('project_type', $new_project['project_type'])->where('status', 5)->first();//找到整改单
        if (empty($data_labor)) return ['status' => false, 'error' => '找不到数据'];
        if ($new_project['project_type'] == 2) {
            $data_lists = unserialize($data_labor->list_detail)['parent_2'];//拿到水电阶段整改数据
        } else {
            $data_lists = unserialize($data_labor->list_detail);//拿到整改数据
        }

        $project_type_id = $new_project['project_type'];//整改阶段id
        $offer_detail    = WorkOfferModel::where('project_type', $project_type_id)->where('task_id', $task_id)->first();//整改阶段offer表对应的信息
        $old_labor       = $offer_detail->to_uid;//原来的工人

        //看业主选了几星的
        $task_info = TaskModel::where('id', $task_id)->first();
        $boss_uid = $task_info->uid;
        $work_star = $task_info->workerStar;
        //  TODO 从数据库找出来再判断
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

        //水电阶段,单独拿出来
        if ($project_type_id == 2) {

            $toUidArr    = explode('-', $old_labor);  // 原工人
            $toUidArrNew = explode('-', $new_project['labor']); //新工人

            $priceArr = [];
            //看原阶段选了水电和泥水分别是多少总价
            foreach ($eachWorkerPriceArr['childs'] as $key => $value) {
                if (empty($priceArr[$value['work_type']])) {
                    $priceArr[$value['work_type']] = 0;
                }
                $priceArr[$value['work_type']] += $value['child_price'] * $work_star_rate;
            }

            //看整改阶段选了水电和泥水分别是多少总价
            $priceArrNew = [];
            foreach ($data_lists['childs'] as $key => $value) {
                if (empty($priceArrNew[$value['work_type']])) {
                    $priceArrNew[$value['work_type']] = 0;
                }
                $priceArrNew[$value['work_type']] += $value['child_price'] * $work_star_rate;
            }

            //循环出需要付给老工人的钱
            foreach ($priceArr as $key => $value) {
                foreach ($priceArrNew as $key2 => $value2) {
                    if ($key == $key2) {
                        $pay_old_salary[$key] = $value - $value2;
                    }
                }
            }

            //  TODO 判断换了一个人还是两人
            foreach ($toUidArr as $key => $value) {
                foreach ($toUidArrNew as $key2 => $value2) {
                    if ($value == $value2) {        //这是只换了一个人
                        unset($toUidArr[$key]);
                        unset($toUidArrNew[$key]);
                    }
                }
            }

            //付钱啦
            foreach ($pay_old_salary as $key => $value) {
                foreach ($toUidArr as $key2 => $value2) {
                    $workerType = UserDetailModel::where('uid', $value2)->first()->work_type;
                    if ($workerType == $key) {

                        //扣款记录(业主)
                        $is_ordered_labor  = OrderModel::sepbountyOrder($boss_uid, $value, $task_id, $data_lists['parent_name'] . '更换原工人,结算原工人工资(冻结金->工作者)', 1, 1, $project_type_id);
                        $house_keeper_code = $is_ordered_labor->code;//管家在sub_order的订单编号
                        TaskOtherModel::bounty($value, $task_id, $boss_uid, $house_keeper_code, 5, 6, true);//扣冻结资金

                        //打款给老工人
                        $is_ordered_designer = platformOrderModel::sepbountyOrder($value2, $value, $task_id, $data_lists['parent_name'] . '业主更换原工人,结算工资', 2, 1, $project_type_id);
                        $increment_designer  = TaskOtherModel::bounty($value, $task_id, $value2, $is_ordered_designer->code, 1, 2, false, false, true);
                    }
                }
            }
            $new_labor = $new_project['labor']; //新工人编号,系统匹配
            //付款成功生成历史记录
            $handle_people          = WorkModel::where('task_id', $task_id)->first()->uid;//经手人
            $data_labor->status     = 6;//5.平台正在匹配中,6,匹配完成
            $data_labor->is_confirm = 1;//是否成功更换工人,0平台未更换,1已更换过
            $data_labor->new_labor  = $new_labor;//是否成功更换工人,0平台未更换,1已更换过
            $res_date               = TaskOtherModel::where('id', $task_id)->update(['end_at' => $data_labor->change_date]);//变更项目结束时间
            if (!$data_labor->save()) return ['status' => false, 'error' => '平台匹配失败1'];


            $old_total_price = $offer_detail->price;//拿到原来的价格
            $new_total_price = $data_lists['parent_price'] * $work_star_rate;//该整改阶段的总价(乘以星级)

            //需要支付给原工人的总价,一个笨方法
            $total_pay_old = 0;
            foreach ($pay_old_salary as $n => $m) {
                $total_pay_old += $m;
            }

            $offer_detail->to_uid = $new_labor;//新工人写进数据库
            $offer_detail->price  = $old_total_price - $total_pay_old;//新工人的工资写入数据库
            if ($new_total_price > $old_total_price) return ['status' => false, 'error' => '本阶段更改价格大于原阶段价格'];


            $data = [
                'old_labor' => $old_labor,
                'new_labor' => $new_labor,
                'handle_people' => $handle_people,
                'task_id' => $task_id,
                'list_changes' => serialize($data_lists),
                'is_sure' => $is_sure,
                'pay_old_worker' => $total_pay_old,
                'project_type_id' => $project_type_id
            ];

            $res_change_work_offer = $offer_detail->save(); //付完钱再保存数据
            $res_list_change       = ProjectConfigureChangeModel::create($data);        //付完钱再保存数据
            if ($res_list_change && $res_change_work_offer && $increment_designer) return ['status' => true, 'error' => '选择成功,钱已付原来工人'];

            return ['status' => false, 'error' => '确认失败'];


        } else {//普通结算

            $new_labor              = implode($new_project['labor']); //新工人编号,系统匹配
            $handle_people          = WorkModel::where('task_id', $task_id)->first()->uid;//经手人
            $data_labor->status     = 6;//5.平台正在匹配中,6,匹配完成
            $data_labor->is_confirm = 1;//是否成功更换工人,0平台未更换,1已更换过
            $data_labor->new_labor  = $new_labor;//新工人入库
            $res_date               = TaskOtherModel::where('id', $task_id)->update(['end_at' => $data_labor->change_date]);//变更项目结束时间
            if (!$data_labor->save()) return ['status' => false, 'error' => '平台匹配失败2'];



            $old_total_price      = $offer_detail->price;//拿到原来的价格
            $new_total_price      = $data_lists['all_parent_price'] * $work_star_rate;//该整改阶段的总价(要乘以星级汇率)
            $pay_old_worker       = $old_total_price - $new_total_price;//需要支付给原工人的总价
            $offer_detail->to_uid = $new_labor;//新工人写进数据库
            $offer_detail->price  = $new_total_price;//新工人的工资写入数据库

            if ($new_total_price > $old_total_price) return ['status' => false, 'error' => '本阶段更改价格大于原阶段价格'];


            $data = ['old_labor' => $old_labor, 'new_labor' => $new_labor, 'handle_people' => $handle_people, 'task_id' => $task_id, 'list_changes' => serialize($data_lists), 'is_sure' => $is_sure, 'pay_old_worker' => $pay_old_worker, 'project_type_id' => $project_type_id];

            //扣款记录(业主)
            $is_ordered_labor = OrderModel::sepbountyOrder($boss_uid, $pay_old_worker, $task_id, $data_lists['parent_' . $project_type_id]['parent_name'] . '更换原工人,结算原工人工资(冻结金->工作者)', 1);
            $house_keeper_code       = $is_ordered_labor->code;//管家在sub_order的订单编号
            TaskOtherModel::bounty($pay_old_worker, $task_id, $boss_uid, $house_keeper_code, 1, 6, true);//扣冻结资金

            //把原工人的工资存入原工人的冻结金里面
            $is_ordered_worker     = platformOrderModel::sepbountyOrder($old_labor, $pay_old_worker, $task_id, $data_lists['parent_' . $project_type_id]['parent_name'] . '业主更换原工人,结算工资', 2,1, $project_type_id);        //生成子订单
            $increment_designer    = TaskOtherModel::bounty($pay_old_worker, $task_id, $old_labor, $is_ordered_worker->code, 1, 2, false, false, true);//工人冻结金增加(多传个参数证明是工人,需要冻结增加而不是余额增加)
            $res_change_work_offer = $offer_detail->save(); //付完钱再保存数据
            $res_list_change       = ProjectConfigureChangeModel::create($data);        //付完钱再保存数据
            if ($res_list_change && $res_change_work_offer && $is_ordered_worker && $increment_designer)
                return ['status' => true, 'error' => '选择成功,钱已付原来工人'];

            return ['status' => false, 'error' => '确认失败'];
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 上传图片
     */

    public function uploadImg(Request $request){
        // TODO 这里要压缩下图片

        $file 	 = $request->file('file');
        $type	 = $request->get('img_type');

        $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');

        $result = \FileClass::uploadFile($file, $path = 'user' , $allowExtension);
        $result = json_decode($result, true);

        if ($result['code'] != 200) {
            return response()->json( array('error'=>$result['message']) , '503');
        }else{
            // $small = \FileClass::imageHandleSmall($result['data']);
        }

        return response()->json( ['full_path'=>url($result['data']['url']) , 'path'=>$result['data']['url']]);

    }


    //充值页面
    public function userRecharge(Request $request,$uid){
        $uid = intval($uid);
        $name = UserModel::where('id',$uid)->first();
        $data = [
            'uid'=>$uid,
            'name'=>$name
        ];
        return $this->theme->scope('manage.userRecharge',$data)->render();
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * 提交充值
     */
    public function postUserRecharge(Request $request) {

        $data['uid']              = $request->get('uid');
        $data['price']            = floatval($request->get('price'));
        $data['bankname']         = $request->get('bankname');
        $data['email_code']       = $request->get('email_code');
        $data['bank_transaction'] = $request->get('bank_transaction');

        $name = UserModel::where('id', $data['uid'])->first();

        $admin_email   = env('ADMIN_PHONE_NUM');
        $email_code    = $admin_email . '-' . $data['email_code'];
        $key_receive   = 'user:emailCode:' . $admin_email;
        $redis_receive = Redis::get($key_receive);
        if ($redis_receive != $email_code) {
            return back()->with(['message' => '验证码错误']);
        }
        if(empty($name)){
            return back()->with(['message' => '不存在的操作账号']);
        }
        foreach($data as $key => $value){
            if(empty($value)){
                return back()->with(['message' => '请填写充值参数']);
            }
        }

        $ret = DB::transaction(function() use($data){
            OrderModel::insert([
                'uid'=>$data['uid'],
                'code'=>OrderModel::randomCode($data['uid']),
                'title'=>'后台充值',
                'status'=>1,
                'bankname'=>$data['bankname'],
                'bank_transaction'=>$data['bank_transaction'],
                'task_id'=>0,
                'cash'=>$data['price'],
                'note'=>'后台充值',
                'created_at'=>date('Y-m-d H:i:s'),
                'payee_id'=>$data['uid'],
                'order_action'=>3
            ]);
            FinancialModel::insert([
                'action'=>3,
                'pay_type'=>4,
                'pay_account'=>'',
                'pay_code'=>$data['bank_transaction'],
                'cash'=>$data['price'],
                'uid'=>$data['uid'],
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ]);
            UserDetailModel::where('uid',$data['uid'])->increment('balance',$data['price']);
        });


        if(is_null($ret))
        {
            return redirect('manage/userList')->with(['message' => '充值成功']);
        }else{
            return back()->with(['message' => '充值失败']);
        }

    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 发送邮箱验证码
     */
    public function sendRechargeEmail(Request $request) {
        $uid         = $request->uid;
        $price       = $request->price;
        $user_detail = UserDetailModel::select('realname')->where('uid', $uid)->first();
        $user_info   = UserModel::find($uid);
        $real_name   = empty($user_detail->realname) ? (empty($user_info->name) ? '无名人士' : $user_info->name) : $user_detail->realname;
        $admin_email = env('ADMIN_PHONE_NUM');
        $code        = rand(100000, 999999);
        $key         = 'user:emailCode:' . $admin_email;
        $email_code  = $admin_email . '-' . $code;

        Redis::set($key, $email_code, 'EX', 120);

        $mail = new Message;
        $mail->setFrom('github_sw <964587505@qq.com>')
            ->addTo($admin_email)
            ->setSubject('充值验证码')
            ->setHTMLBody("您正在为" . $real_name . ",充值" . $price . "元,充值验证码为" . $code);

        $mailer        = new SmtpMailer(array(
            'host' => 'smtp.qq.com',
            'username' => '964587505@qq.com',
            'password' => 'vsapimkqinbgbfjf',//这里的密码可以在qq邮箱-账户-SMTP服务-生成授权码
            'secure' => 'ssl',
        ));
        $res           = $mailer->send($mail);
        if(is_null($res)){
            return response()->json(['msg'=>'已发送']);
        }else{
            return response()->json(['msg'=>'发送失败']);
        }
    }

    /**
     * 管家客诉列表
     */
    public function houseKeeperApply(Request $request) {

        $position         = $request->get('position_address');
/*        $worker_phone_num = $request->get('worker_phone_num');
        $cashout_status   = $request->get('cashout_status');
        $new_order        = $request->get('new_order');
        $pay_code         = $request->get('pay_code');*/
        $paginate         = $request->get('paginate') ? $request->get('paginate') : 5;
        $compla_data          = HouseKeeperComplaintModel::whereRaw('1 = 1');
        if ($position) {
            $compla_data = $compla_data->where('position_name', 'like', '%' . $position . '%');
        }
/*        if ($worker_phone_num) {
            $cashout = $cashout->where('worker_phone_num', $worker_phone_num);
        }
        if ($pay_code) {
            $cashout = $cashout->where('pay_code', $pay_code);
        }
        if ($new_order) {
            $cashout = $cashout->where('new_order', $new_order);
        }
        if ($cashout_status) {
            $cashout = $cashout->where('status', $cashout_status);
        }*/
        $data_list = $compla_data->paginate($paginate);

        $data      = [
            'all_data' => $data_list
        ];

        return $this->theme->scope('manage.houseKeeperApply', $data)->render();

    }

    /**
     * 管理员代替业主确认
     */
    public function houseKeeperApplyConfirm(Request $request) {
        $id = $request->id;
        $complaint_data = HouseKeeperComplaintModel::find($id);
        if($complaint_data->status==1){

            $data_msg = [
                'status_msg'=>'已确认,无需再确认'
            ];
            return response()->json($data_msg);
        }
        $task_id = $complaint_data->task_id;
        $taskInfo = TaskModel::find($task_id);
        $boss_uid = $taskInfo->uid;

        $curSnStatus = [];    //初始化阶段状态数组合

        $work         = WorkModel::where('status', 2)->where('task_id', $task_id)->first();
        // 判断任务去到哪一个工程阶段
        $ret = WorkOfferModel::where('work_id', $work['id'])->where('task_id', $task_id)
            ->where('status', '>', 0)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();

        foreach ($ret as $key => $value) {
            // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
            if ($value['status'] == 1.5||$value['status'] == 1||$value['status'] == 3) {
                $curSnStatus = $value;
                break;
            }

        }
        $data = [
            'sn'=>$complaint_data->sn,
            'task_id'=>$task_id,
            'from_uid'=>$boss_uid,
        ];

        //处于需要用户确认的阶段
        $res_accounts  = $this->payWorkerRespository->house_keeper_accounts_other($curSnStatus,$value,$work,$data,$taskInfo);
        $complaint_data->status=1;
        $complaint_data->save();
        if (is_null($res_accounts)) {
            $data_msg = [
                'status_msg'=>'操作成功'
            ];
            return response()->json($data_msg);
        }


    }


    /**
     * @param Request $request
     * @return mixed
     * 工程配置单列表
     */
    public function projectListManage(Request $request) {

        $id           = 1;
        $city_id      = 291;
        $all_data     = ProjectConfigureModel::select('district.name as city_name', 'project_configure_list.*')->where('project_type', 1)
            ->leftJoin('district', 'district.id', '=', 'project_configure_list.city_id')
            ->paginate(15);
        $project_name = ProjectConfigureModel::select('project_type', 'desc')->where('pid', 0)->where('city_id',$city_id)->orderBy('project_type', 'asc')->get();
        $city_data    = ProjectConfigureModel::select('city_id', 'district.name')->distinct('city_id')->leftJoin('district', 'district.id', '=', 'project_configure_list.city_id')->orderBy('district.spelling')->get();

        $data = [
            'new_data' => $all_data,
            'project_name' => $project_name,
            'city_data' => $city_data,
            'id' => $id,
            'city_id' => $city_id,
        ];
        return $this->theme->scope('manage.projectListManage', $data)->render();
    }

    /**
     * @param Request $request
     * 根据不同类型加载不同的配置单
     */
    public function projectListManageById($id=1,$city_id=291) {

        $project_name = ProjectConfigureModel::select('project_type', 'desc')->where('pid', 0)->where('city_id',$city_id)->orderBy('project_type', 'asc')->get();
        $all_data = ProjectConfigureModel::select('district.name as city_name','project_configure_list.*')->where('project_type',$id)->where('city_id',$city_id)
            ->leftJoin('district','district.id','=','project_configure_list.city_id')
            ->paginate(15);
        $city_data    = ProjectConfigureModel::select('city_id', 'district.name')->distinct('city_id')->leftJoin('district', 'district.id', '=', 'project_configure_list.city_id')->orderBy('district.spelling')->get();
        $data         = [
            'new_data' => $all_data,
            'project_name' => $project_name,
            'id' => $id,
            'city_data' => $city_data,
            'city_id' => $city_id,
        ];
        return $this->theme->scope('manage.projectListManage', $data)->render();
    }

    /**
     * @param Request $request
     * 根据不同id修改配置单
     */
    public function projectConfigureEdit(Request $request) {

        $data = [
            'city'=>DistrictModel::select('id','name')->where('name','like', '%'.'市'.'%')->get(),
            'province'=>DistrictModel::findTree(0),
            'project_data'=>ProjectConfigureModel::find($request->id)
        ];

        return $this->theme->scope('manage.ProjectConfigureEdit', $data)->render();
    }


    /**
     * @param Request $request
     * 添加配置单
     */
    public function projectConfigureAdd(Request $request) {

        $project_name = ProjectConfigureModel::select('project_type','desc','id','city_id')->where('pid',0)->orderBy('city_id','asc')->orderBy('project_type','asc')->get();
        $work_type = ProjectConfigureModel::select('work_type')->where('pid',0)->orderBy('project_type','asc')->distinct('work_type')->get();


        $arr = [];
        foreach($project_name as $key => $value){
            if(!in_array($value['city_id'] , $arr)){
                $arr[] = $value['city_id'];
            }
        }

        $cityArr = [];
        $cityInfo = DistrictModel::select('name','id')->whereIn('id',$arr)->get()->toArray();
        foreach($cityInfo as $key => $value){
            $cityArr[$value['id']] = $value['name'];
        }

        foreach($project_name as $key => $value){
            $project_name[$key]['city_name'] = $cityArr[$value['city_id']];
        }


        foreach ($work_type as $k => $v) {
            if (strpos($v['work_type'], '-')) {
                $work_type[$k]['work_type'] = explode('-', $v['work_type'])[1];
            }
        }
        $data = [
            'city'=>DistrictModel::select('id','name')->where('name','like', '%'.'市'.'%')->get(),
            'province'=>DistrictModel::findTree(0),
            'project_data'=>[],
            'project_name'=>$project_name,
            'work_type'=>$work_type,
        ];
//var_dump($project_name->toArray());exit;
        return $this->theme->scope('manage.ProjectConfigureEdit', $data)->render();
    }

    /**
     * @param Request $request
     * 提交配置单
     */
    public function projectConfigureSubmit(Request $request) {
        $id = $request->id;
        if (!empty($id)) {
            //编辑
            $this->validate($request, [
                'name' => 'required',
                'unit' => 'required',
                'cardnum' => 'required',
                'price' => 'required|numeric',
//                'serve_city' => 'required|numeric|integer',
//                'serve_province' => 'required|numeric|integer',
//                'work_type' => 'required|between:4,11|numeric|integer',
//                'project_type' => 'required|between:0,8|numeric|integer',
            ],
                [
                    'name.required' => '必须填写名称',
                    'unit.required' => '必须填写单位',
                    'work_type.required' => '必须选择工种',
                    'project_type.required' => '必须选择工程项目',
//                    'work_type.between' => '工种:min - :max位',
//                    'project_type.between' => '工程类型:min - :max位',
                    'cardnum.required' => '必须填写编号',
//                    'serve_city.required' => '必须选择城市',
//                    'serve_province.required' => '必须选择省份',
                    'price.numeric' => '单价必须是数字',
		    'price.required' => '必须填写单价',
                ]
            );

            $data_insert = [
                'name' => $request->name,
                'unit' => $request->unit,
                'price' => $request->price,
                'cardnum' => $request->cardnum,
//                'city_id' => $request->serve_city,
//                'provice_id' => $request->serve_province,
                'desc' => $request->desc
            ];
            if (ProjectConfigureModel::where('id', $id)->update($data_insert)) {
                return redirect('manage/projectListManage')->with(['message' => '编辑成功']);
            } else {
                return redirect('manage/projectListManage')->with(['message' => '编辑成功!']);
            }
        } else {

            $this->validate($request, [
                'name' => 'required',
                'unit' => 'required',
                'cardnum' => 'required',
                'price' => 'required|numeric',
                'serve_city' => 'required|numeric|integer',
                'serve_province' => 'required|numeric|integer',
                'work_type' => 'required|between:4,11|numeric|integer',
                'project_type' => 'required|between:0,8|numeric|integer',
            ],
                [
                    'name.required' => '必须填写名称',
                    'unit.required' => '必须填写单位',
                    'work_type.required' => '必须选择工种',
                    'project_type.required' => '必须选择工程项目',
                    'work_type.between' => '工种:min - :max位',
                    'project_type.between' => '工程类型:min - :max位',
                    'cardnum.required' => '必须填写编号',
                    'serve_city.required' => '必须选择城市',
                    'serve_province.required' => '必须选择省份',
                    'price.numeric' => '单价必须是数字',
                    'price.required' => '必须填写单价',
                ]
            );

            //新增
            $data_create = [
                'name' => $request->name,
                'unit' => $request->unit,
                'cardnum' => $request->cardnum,
                'price' => $request->price,
                'city_id' => $request->serve_city,
                'provice_id' => $request->serve_province,
                'work_type' => $request->work_type,
                'project_type' => $request->project_type,
                'pid' => get_pid_from_project_type($request->project_type),
                'desc' => $request->desc
            ];
            if (ProjectConfigureModel::create($data_create)) {
                return redirect('manage/projectListManage')->with(['message' => '添加成功']);
            } else {
                return redirect('manage/projectListManage')->with(['message' => '添加失败!']);
            }

        }

    }


    /**
     * @param Request $request
     * 根据不同删除配置单
     */
    public function projectConfigureDel(Request $request) {
        $id = $request->id;
        $data_one = ProjectConfigureModel::find($id);
        if($data_one->delete()){
            return back()->with(['message' => '操作成功']);
        }else{
            return back()->with(['message' => '操作失败']);
        }

    }

    /**
     * @param Request $request
     * @return mixed
     * 商家信息列表
     */
    public function businessInfo(Request $request) {

        $paginate       = $request->get('paginate') ? $request->get('paginate') : 5;
        $MerchantDetail = MerchantDetail::whereRaw('1 = 1');
        $MerchantDetail = $MerchantDetail->paginate($paginate);

        foreach ($MerchantDetail as $item => $value) {
            $MerchantDetail[$item]['brand_logo'] = empty($value['brand_logo']) ? '' : url($value['brand_logo']);
            $MerchantDetail[$item]['popular_img'] = empty($value['popular_img']) ? '' : url($value['popular_img']);
        }
        $new_data = [
            'MerchantDetail' => $MerchantDetail
        ];

        return $this->theme->scope('manage.businessInfo', $new_data)->render();
    }

    /**
     * @param Request $request
     * @return mixed
     * 商家信息编辑
     */
    public function businessInfoEdit(Request $request, $id) {

        $MerchantDetail = MerchantDetail::find($id);
        $MerchantDetail['brand_logo'] = empty($MerchantDetail['brand_logo']) ? '' : url($MerchantDetail['brand_logo']);
        $new_data                     = [
            'MerchantDetail' => $MerchantDetail
        ];
        return $this->theme->scope('manage.businessInfoEdit', $new_data)->render();
    }

    /**
     * @param Request $request
     * @return mixed
     * 商家信息添加
     */

    public function businessInfoAdd() {
        return $this->theme->scope('manage.businessInfoEdit')->render();
    }


    /**
     * 保存商家信息
     */
    public function saveBusinessInfo(Request $request) {

        $id            = $request->id;
        $business_data = MerchantDetail::find($id);

        $lat = empty($request->lat)?0:$request->lat;
        $lng = empty($request->lng)?0:$request->lng;
//        var_dump($lat,$lng);exit;

        if (!empty($id)) {
            //编辑
            $this->validate($request, [

                'name' => 'required',
                'ad_slogan' => 'required',
                'mobile' => 'required',
                'brand_name' => 'required',
            ],
                [
                    'brand_logo.image' => '品牌图片必须是（jpeg、png、bmp、gif或者svg）',
                    'popular_img.image' => '推广图片必须是图片（jpeg、png、bmp、gif或者svg）',
                    'name.required' => '必须填写联系人',
                    'ad_slogan.required' => '必须填写广告语',
                    'mobile.required' => '必须填写手机号码',
//                    'mobile.numeric' => '手机号必须填写纯数字',
//                    'mobile.integer' => '手机号必须填写纯数字',
                    'brand_name.required' => '必须填写广告名称',
                ]
            );


            $brand_logo_file  = empty($request->file('brand_logo')) ? '' : $request->file('brand_logo');       //品牌名称
            $popular_img_file = empty($request->file('popular_img')) ? '' : $request->file('popular_img');      //推广图片

            $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
            //品牌名称不为空
            if (!empty($brand_logo_file)) {
                $brand_logo = json_decode(\FileClass::uploadFile($brand_logo_file, $path = 'shop', $allowExtension), true);
                if ($brand_logo['code'] != 200) {
                    return back()->with(['message' => '图片上传失败']);
                } else {
                    $brand_logo_url = $brand_logo['data']['url'];
                }
            } else {
                $brand_logo_url = $business_data->brand_logo;
            }
            if (!empty($popular_img_file)) {
                $popular_img = json_decode(\FileClass::uploadFile($popular_img_file, $path = 'shop', $allowExtension), true);
                if ($popular_img['code'] != 200) {
                    return back()->with(['message' => '图片上传失败']);
                } else {
                    $popular_img_url = $popular_img['data']['url'];
                }
            } else {
                $popular_img_url = $business_data->popular_img;
            }


            $data_update = [
                'name' => $request->name,//联系人
                'mobile' => trim($request->mobile),//联系方式
                'address' => $request->address,//地址
                'ad_slogan' => $request->ad_slogan,//广告语
                'brand_name' => $request->brand_name,//品牌名称
                'brand_logo' => $brand_logo_url,//品牌LOGO
                'popular_img' => $popular_img_url,//推广图片
                'lat'=>$lat,
                'lng'=>$lng
            ];
            if (MerchantDetail::where('id', $id)->update($data_update))

                return redirect('manage/businessInfo')->with(['message' => '操作成功']);
//            return $this->theme->scope('manage.businessInfo')->render();
//                return back()->with(['message' => '操作成功']);

        } else {
            //新增

            $this->validate($request, [
                'brand_logo' => 'required|image',
//                'popular_img' => 'required|image',
//                'name' => 'required',
                'ad_slogan' => 'required',
                'mobile' => 'required|numeric|integer',
                'brand_name' => 'required',
            ],
                [
                    'brand_logo.required' => '必须上传品牌图片',
//                    'popular_img.required' => '必须上传推广图片',
                    'brand_logo.image' => '品牌图片必须是（jpeg、png、bmp、gif或者svg）',
                    'popular_img.image' => '推广图片必须是图片（jpeg、png、bmp、gif或者svg）',
//                    'name.required' => '必须填写联系人',
                    'ad_slogan.required' => '必须填写广告语',
                    'mobile.required' => '必须填写手机号码',
                    'mobile.numeric' => '手机号必须填写纯数字',
                    'mobile.integer' => '手机号必须填写纯数字',
                    'brand_name.required' => '必须填写广告名称',
                ]
            );


            $brand_logo_file  = $request->file('brand_logo');       //品牌名称
            $popular_img_file = empty($request->file('popular_img')) ? '' : $request->file('popular_img');       //推广图片
            $allowExtension   = array('jpg', 'gif', 'jpeg', 'bmp', 'png');

            if (!empty($brand_logo_file) && !empty($popular_img_file)) {
                $brand_logo  = json_decode(\FileClass::uploadFile($brand_logo_file, $path = 'shop', $allowExtension), true);
                $popular_img = json_decode(\FileClass::uploadFile($popular_img_file, $path = 'shop', $allowExtension), true);
                if ($brand_logo['code'] != 200 || $popular_img['code'] != 200) {
                    return back()->with(['message' => '图片上传失败']);
                } else {
                    $brand_logo_url  = $brand_logo['data']['url'];
                    $popular_img_url = $popular_img['data']['url'];
                }
            } else {
                //只传一张
                $brand_logo = json_decode(\FileClass::uploadFile($brand_logo_file, $path = 'shop', $allowExtension), true);
                if ($brand_logo['code'] != 200) {
                    return back()->with(['message' => '图片上传失败']);
                }
                $brand_logo_url  = $brand_logo['data']['url'];
                $popular_img_url = '';
            }


            $data_insert = [
                'name' => $request->name,//联系人
                'mobile' => $request->mobile,//联系方式
                'address' => $request->address,//联系方式
                'ad_slogan' => $request->ad_slogan,//广告语
                'brand_name' => $request->brand_name,//品牌名称
                'brand_logo' => $brand_logo_url,//品牌LOGO
                'popular_img' => $popular_img_url,//推广图片
                'lng'=>$lng,
                'lat'=>$lat
            ];
            if (MerchantDetail::create($data_insert))
                return redirect('manage/businessInfo')->with(['message' => '操作成功']);
//                return $this->theme->scope('manage.businessInfo')->render();
//                return back()->with(['message' => '操作成功']);

        }

    }

    /**
     * @param Request $request
     * 删除商家信息
     */
    public function businessInfoDelete(Request $request) {
        if (MerchantDetail::destroy($request->id)) {
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
     * 启动广告和新手图片查看
     */
    public function adImgList(Request $request) {

        $img_list      = AdImg::orderBy('id', 'desc')->where('type', 'ad_img')->get();
        $help_img_list = AdImg::orderBy('id', 'desc')->where('type', 'help_img')->limit(1)->get();

        $img_list = $img_list->merge($help_img_list);
        $data     = [
            'img_list' => $img_list,
        ];
        return $this->theme->scope('manage.adImgList', $data)->render();
    }

    /**
     * 启动广告和新手图片删除
     */
    public function adImgDelete(Request $request) {
        $id = $request->id;
        if (AdImg::where('id', $id)->update(['deleted_at' => date('Y-m-d H:i:s')])) {
            $data = [
                'msg' => '删除成功'
            ];
        } else {
            $data = [
                'msg' => '删除失败'
            ];
        }
        return response()->json($data);
    }

    /**
     * 新手图片提交
     */
    public function adHelpImgSubmit(Request $request) {

        $this->validate($request, [

            'all_img' => 'image',
        ],
            [
                'all_img.image' => '上传的必须是（jpeg、png、bmp、gif或者svg）图片',
            ]
        );

        $id      = $request->get('edit-id');
        $all_img = empty($request->file('all_img')) ? '' : $request->file('all_img');      //新手图片
//        $filename = $all_img->getClientOriginalName();
//        $firstname = explode('.',$filename)[0];


        //编辑
        if (!empty($id)) {
            $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
            $ad_imgs        = json_decode(\FileClass::uploadFile($all_img, $path = 'ad', $allowExtension), true);
            if ($ad_imgs['code'] != 200) {
                return back()->with(['message' => '图片上传失败']);
            }
            $ad_imgs_url = $ad_imgs['data']['url'];
            $res_uplode  = AdImg::where('id', $id)->update(['url' => $ad_imgs_url]);
        } else {
            //新增
            $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
            $ad_imgs        = json_decode(\FileClass::uploadFile($all_img, $path = 'ad', $allowExtension), true);
            if ($ad_imgs['code'] != 200) {
                return back()->with(['message' => '图片上传失败']);
            }
            $ad_imgs_url = $ad_imgs['data']['url'];
            $data_create = [
                'type' => 'help_img',
                'url' => $ad_imgs_url,
            ];
            $res_uplode  = AdImg::create($data_create);
        }
        if ($res_uplode) {
            return back()->with(['message' => '操作成功']);
        }

    }


    /**
     * @param Request $request
     * 启动广告单独提交
     */
    public function AdImgSubmit(Request $request) {
        $this->validate($request, [

            'all_img' => 'image',
        ],
            [
                'all_img.image' => '上传的必须是（jpeg、png、bmp、gif或者svg）图片',
            ]
        );
        $id      = $request->get('edit-id');
        $all_img = empty($request->file('all_img')) ? '' : $request->file('all_img');      //新手图片
        //文件名
        $filename = "head.jpg";
        //路径
        $destinationPath = 'attachment/ad/';
        //移动图片到目录
        if (!$all_img->move($destinationPath, $filename)) {
            return 'error';
        }

    }

    /**
     * 新手图片显示
     */
    public function adHelpImgEdit(Request $request, $img_id) {
        $type         = AdImg::find($img_id)->type;
        $all_help_img = AdImg::where('type', $type)->get();
        $hidden       = 0;
        if ($type == 'ad_img') {
            $hidden = 1;
        }
        $data = [
            'all_help_img' => $all_help_img,
            'hidden' => $hidden,
        ];
        return $this->theme->scope('manage.adHelpImgEdit', $data)->render();

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * excel上传
     */
    public function ProjectListExcelUpload(Request $request) {

        $excel_list = $request->file('excel_list');      //新手图片
        if (!$request->hasFile('excel_list')) {
            return back()->with(['message' => '上传文件为空']);
        }

        //判断文件上传过程中是否出错
        if (!$excel_list->isValid()) {
            return back()->with(['message' => '文件上传出错']);
        }

        $destPath = realpath(public_path('excel'));
        if (!file_exists($destPath))
            mkdir($destPath, 0755, true);
        $filename = $excel_list->getClientOriginalName();
        if (!$excel_list->move($destPath, $filename)) {
            return back()->with(['message' => '保存文件失败']);
        }


        Excel::load(public_path() . '/excel/' . "$filename", function ($reader) {

            $all_data = $reader->toArray()[0];
            $all_data_other = ProjectConfigureModel::select('pid','name','work_type','project_type','city_id','price','desc','cardnum','unit','provice_id')->where('pid',0)->where('city_id',291)->get()->toArray();

            if(!empty($all_data)){

                $is_repeat = ProjectConfigureModel::where('city_id',$all_data[0]['city_id'])->first();

                if(!empty($is_repeat)){
                    echo '该城市已存在工程配置单，若需添加请单独添加，<a href="projectListManage">点击返回</a>';exit;
//                    return redirect('manage/projectListManage')->with(['message' => '该城市已存在工程配置单，若需添加请单独添加']);
//                    return back()->with(['message' => '该城市已存在工程配置单，若需添加请单独添加']);
                }
            }

            foreach ($all_data_other as $k=>$v){
                if (!empty(trim($all_data[0]['city_id'])) && !empty(trim($all_data[0]['provice_id']))) {
                    $all_data_other[$k]['city_id'] = trim($all_data[0]['city_id']);
                    $all_data_other[$k]['provice_id'] = trim($all_data[0]['provice_id']);
                }else{
                    return back()->with(['message' => '第一行没有填写城市id和省份id']);
                }
            }
            foreach ($all_data_other as $k=>$v){
                ProjectConfigureModel::create($v);
            }
            foreach ($all_data as $item => $value) {
                if (!empty($value['name']) && !empty($value['city_id'])) {
                    trim($value['name']);
                    trim($value['provice_id']);
                    trim($value['cardnum']);
                    trim($value['unit']);
                    trim($value['num']);
                    trim($value['desc']);
                    trim($value['price']);
                    trim($value['city_id']);
                    trim($value['work_type']);
                    trim($value['project_type']);
                    trim($value['pid']);
                    ProjectConfigureModel::create($value);
                }
            }
        });

        return back()->with(['message' => '处理成功']);

    }

    /**
     * @param Request $request
     * 用户排序
     */
    public function userSortByAdmin(Request $request) {
        $uid     = $request->get('uid');
        $sort_id = $request->get('sort_id');
        if (UserModel::where('id', $uid)->update(['sort_id' => $sort_id])) {
            $data = [
                'sort_id' => $sort_id
            ];
        } else {
            $data = [
                'sort_id' => 'error'
            ];
        }
        return response()->json($data);
    }


    /**
     * @param Request $request
     * 直播排序
     */
    public function broadcastSort(Request $request) {
        $task_id = $request->get('task_id');
        $sort_id = $request->get('sort_id');

        if(!is_numeric($sort_id)){
            $data = [
                'sort_id' => '您输入的不是一个整数'
            ];
        }else{
            TaskModel::where('id', $task_id)->update(['broadcastOrderBy' => $sort_id]);
            $data = [
                'sort_id' => $sort_id
            ];
        }
        return response()->json($data);
    }

    /**
     * @param Request $request
     * 直播隐藏
     */
    public function broadcastHidden(Request $request) {
        $hidden_status = $request->get('hidden_status');
        $task_id       = $request->get('task_id');
        if (($hidden_status == 1) || ($hidden_status == 2)) {
            TaskModel::where('id', $task_id)->update(['hidden_status' => $hidden_status]);
                $data = [
                    'hidden_status' => $hidden_status
                ];
        } else {
            $data = [
                'hidden_status' => '只能输入1或者2'
            ];
        }
        return response()->json($data);
    }


    public function getUserDownline(Request $request,$id) {
        $this->theme->set('authAction', '欢迎登录');
        $this->theme->setTitle('欢迎登录');

        $username = $request->get('username');//用户名
        $password = $request->get('password');//密码

        $user_login = UserModel::find($id);

        $data = UserModel::select('name', 'id', 'created_at')->where('invite_uid', $user_login->id)->get();
        foreach ($data as $k => $v) {
            $data[$k]['name'] = $v['name'];
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
                $taskDetail[$k]['name'] = $v['name'];
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
        return $this->theme->scope('manage.getUserDownline', $data)->render();
    }






    public function region(Request $request){
        $this->theme->setTitle('可选区域管理');
        $name = $request->get('name');
        if(!empty($name)){
            $list = CoordinateModel::select('id','name','work_select')->where('level', 2)->where('name','like',$name.'%')->orderBy('work_select', 'desc')->paginate(15);
        }else{
            $list = CoordinateModel::select('id','name','work_select')->where('level', 2)->orderBy('work_select', 'desc')->paginate(15);
        }

        $data = [
            'result' => $list,
            'name'=>$name
        ];
        return $this->theme->scope('manage.region', $data)->render();
    }

    public function changeCurrentStatus(Request $request,$id,$work_select){
        if($work_select == 1){
            $work_select = 0;
        }else{
            $work_select = 1;
        }
        CoordinateModel::where('id',$id)->update(['work_select'=>$work_select]);
        return redirect('manage/region')->with(['message' => '操作成功']);
    }












}
