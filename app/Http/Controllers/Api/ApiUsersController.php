<?php

namespace App\Http\Controllers\Api;


use App\Modules\Advertisement\Model\AdImg;
use App\Modules\Advertisement\Model\CompanyBankInfo;
use App\Modules\Advertisement\Model\DigCryptModel;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\Manage\Model\ServiceModel;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\workDesignerLog;
use App\Modules\User\Model\BankAuthModel;
use App\Modules\User\Model\CommentModel;
use App\Modules\User\Model\CoordinateModel;
use App\PushSentenceList;
use App\PushServiceModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use Validator;
use Auth;
use App\Modules\User\Model\UserModel;

use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

use App\Modules\Task\Model\TaskCateModel;
use App\Modules\Shop\Models\GoodsModel;

use App\Modules\Manage\Model\SpaceModel;
use App\Modules\Manage\Model\HouseModel;
use App\Modules\Verificationcode\Models\VerificationModel;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\RefuseReasonModel;
// use App\Modules\User\Http\Requests\RegisterRequest;
use App\Modules\Order\Model\OrderModel;
use DB;
use App\Respositories\TaskRespository;
use App\Respositories\ChatRoomRespository;
use App\Respositories\TaskOperaRespository;


class ApiUsersController extends BaseController
{

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $taskRespository;
    protected $chatRoomRespository;
    protected $taskOperaRespository;

    public function __construct(TaskRespository $taskRespository, ChatRoomRespository $chatRoomRespository,TaskOperaRespository $taskOperaRespository) {
        $this->taskRespository     = $taskRespository;
        $this->chatRoomRespository = $chatRoomRespository;
        $this->taskOperaRespository = $taskOperaRespository;
    }

    /**
     * @param array $validatorData
     * @return mixed
     * 验证规则     参数一：要验证的数据 参数二：用户类型
     */
    public function rule( $validatorData = array() ){
        $rule = Validator::make(
            $validatorData,
            [
                'username' => 'mobile_phone|size:11|unique:users,name,null,id',
                // 'username' => 'mobile_phone|size:11|unique:users,name,null,id,user_type,'.$validatorData['user_type'],
                'password' => 'required|between:6,12|string',
                'user_type'=> 'required',
            ],
            [
                'username.mobile_phone'   => '请输入一个手机号码',
                'username.size'     => '国内的手机号码长度为11位',
                'username.unique'   => '该手机号码已注册',

                'user_type.required'=> '请选择注册的角色',

                'password.required' => '6-12位的数字或字母组合',
                'password.between'  => '6-12位的数字或字母组合',
                'password.string'   => '6-12位的数字或字母组合',
            ]
        );
        return $rule;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 注册
     */
    public function register(Request $request){
        $data['code']       = $request->json('code');
        $data['username']   = $request->json('username');
        $data['password']   = $request->json('password');
        $data['user_type']  = $request->json('user_type');

        $validator = $this->rule($data);
        if ($validator->fails()){
            return response()->json( array('error'=>$validator->messages()->first()) , '403');
        }

        $this->chatRoomRespository->RegistEaseMob($data['username']);

    
        foreach ($data as $key => $value) {
            if(empty($value)){
                return response()->json( array('error'=>$key.'注册资料不完整') , '403');
            }
        }
            
        if( $data['user_type'] === '1' ){
            $checkCode = VerificationModel::checkCode( $data['code'] , $data['username'] );

            if(!$checkCode){
                return response()->json( array('error'=>'验证码不正确') , '403');
            }

            if ( UserModel::createUserMobile( $data ) ){
                VerificationModel::sendWelcomeMsg($data['username']);
                return response()->json( array('username'=>$data['username'],'user_type'=>$data['user_type'],'status'=>1) );
            }

        }else{
            $now = time();
            $realnameInfo['realname'] = $request->json('realname');         //真实姓名
            $realnameInfo['card_number'] = $request->json('card_number');      //身份证号码
            $realnameInfo['serve_area'] = $request->json('serve_area');       //服务区域
            $realnameInfo['address'] = $request->json('address');          //地址
            $realnameInfo['lat'] = $request->json('lat');              //纬度
            $realnameInfo['lng'] = $request->json('lng');              //经度
            $realnameInfo['experience'] = $request->json('experience');       //经验
            $realnameInfo['card_front_side'] = $request->json('card_front_side');       //身份证正面
            $realnameInfo['card_back_dside'] = $request->json('card_back_dside');       //身份证反面
            $realnameInfo['created_at'] = date('Y-m-d H:i:s', $now);
            $realnameInfo['updated_at'] = date('Y-m-d H:i:s', $now);

            if ($data['user_type'] == 2) {
                $data['workStar'] = 1;
                $data['score']    = 0;
            } else if ($data['user_type'] == 3) {
                $levelInfo        = DB::table('level')->where('type', 1)->first()->upgrade;//管家1星级对应的分数
                $upgrade          = json_decode($levelInfo, true);
                $data['workStar'] = 1;
                $data['score']    = $upgrade[1];
            } elseif ($data['user_type'] == 4) {
                $levelInfo        = DB::table('level')->where('type', 2)->first()->upgrade;//监理1星级对应的分数
                $upgrade          = json_decode($levelInfo, true);
                $data['workStar'] = 1;
                $data['score']    = $upgrade[1];
            } else {
                $levelInfo        = DB::table('level')->where('type', 5)->first()->upgrade;//工人1星级对应的分数
                $upgrade          = json_decode($levelInfo, true);
                $data['workStar'] = 1;
                $data['score']    = $upgrade[1];
            }

            if(UserModel::createUserMobile( $data , true , $realnameInfo )){
                return response()->json( array('username'=>$data['username'],'user_type'=>$data['user_type'],'status'=>0) );
            }
        }

        return response()->json( array('error'=>'注册失败或下一步失败' ) , '500');
    }

    /**
     * 业主或设计师获取聊天室列表
     */
    public function getChatRoom(Request $request) {
        $user_id       = $request->get('user_id');
        //不是业主的话
        $users = UserModel::find($user_id);

        if (empty($users))
            return $this->responseError('找不到该用户');

        $user_type = $users->user_type;
        $all_chat_room = [];
        if ($user_type != 1) {
            $work_info = WorkModel::select('task_id')->where('uid', $user_id)->where('status', '>', 0)->where('status', '<', 3)->get();
            if (!$work_info->isEmpty()) {
                foreach ($work_info as $v) {
                    $all_boss = TaskModel::select('project_position')->where('id', $v['task_id'])->get();
                }
                foreach ($all_boss as $k => $v) {
                    $all_chat_room = ProjectPositionModel::select('chat_room_id', 'region', 'project_position')->where('id', $v['project_position'])->get();
                }
            }
        } else {
            $all_chat_room = ProjectPositionModel::select('chat_room_id', 'region', 'project_position')->where('uid', $user_id)->get();
        }

        return response()->json($all_chat_room);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 登录接口
     */

    public function login(Request $request){
        $error       = array();
        $name        = $request->json('username');
        $password    = $request->json('password');


        $user_type  = intval($request->json('user_type'));

        if (!UserModel::checkPassword($name, $password , $user_type)) {
            $error['error'] = '请输入正确的帐号或密码';
        } else {
            // $user = UserModel::where('name', $name)->where('user_type',$user_type)->first();
            $user = UserModel::where('name', $name)->first();
            if (!empty($user) && $user->status == 2){
                $error['error'] = '该账户已禁用';
            }
        }

        if (!empty($error)) {
            return response()->json( $error , '403');
        }


        $throttles = $this->isUsingThrottlesLoginsTrait();  //  TODO 判断是否使用了trait中的方法，但不明白有什么意义
        if($user_type != 1){
            $user = UserModel::where('name', $name)->where('user_type' , '>' , 1)->first();
        }
        if($user_type == 1){
            $user = UserModel::where('name', $name)->where('user_type',$user_type)->first();
        }

        if ( empty($user) ){
            return response()->json( array('error'=>'用户不存在') , '500');
        }

        if ($user && !$user->status) {
            return response()->json( array('error'=>'账户未激活') , '403');
        }

        $loginTime = UserModel::where('name', $name)->update(['last_login_time' => date('Y-m-d H:i:s')]);

        if($loginTime){
            $useInfo = UserModel::select('users.user_type', 'users.id', 'users.status', 'ud.avatar', 'ud.lat', 'ud.lng', 'ud.city', 'ud.nickname', 'ud.realname', 'ud.balance', 'ud.star', 'ud.balance_status', 'ud.experience', 'ud.introduce', 'ud.tag', 'ud.address')->join('user_detail as ud', 'ud.uid', '=', 'users.id')
                ->where('users.name', $name)
                ->first();

            $useInfo['avatar']   = empty($useInfo['avatar']) ? '' : url($useInfo['avatar']);
            $useInfo['nickname'] = is_null($useInfo['nickname']) ? '' : $useInfo['nickname'];
            $useInfo['city']     = is_null($useInfo['city']) ? '' : $useInfo['city'];
            $useInfo['realname'] = is_null($useInfo['realname']) ? '' : $useInfo['realname'];
            $useInfo['tag']      = empty(unserialize($useInfo['tag'])) ? [] : unserialize($useInfo['tag']);

            return response()->json($useInfo->toArray());
        }
        return response()->json( ['error'=>'登录失败'] , '500');
    }


    /**
     * 登出(清空机器码)
     */
    public function logout(Request $request) {

        $uid       = $request->json('user_id');
        $user_info = UserModel::find($uid);
        if (empty($user_info)) return response()->json(['error' => '找不到该用户'], '500');
        $user_info->device_token = '';//清空机器码
        $user_info->send_num = 0;//清空次数
        if ($user_info->save()) {
            return response()->json(['message' => 'success'], 200);
        };

        return response()->json(['message' => 'error'], 500);
    }

    /**
     * 保存机器码
     */
    public function saveDeviceToken(Request $request) {

        $deviceToken = empty($request->json('deviceToken')) ? '' : trim($request->json('deviceToken'));
        $uid         = $request->json('user_id');
        $user_info   = UserModel::find($uid);
        if (empty($user_info)) return response()->json(['error' => '找不到该用户'], '500');
        $user_type = $user_info->user_type;
        $now_token_type     = $user_info->device_token;
        
        //判断是ios端还是安卓端
        if (!empty($deviceToken)) {
            $deviceToken    = explode('-', $deviceToken);
            $deviceTokenNew = $deviceToken[1];
            $token_type     = $user_info->device_token_type;

            //覆盖

            if ($deviceToken[1] != $now_token_type) {  //原机器码和传过来的机器码不相等
                //通知原来的用户账户被挤下线
                //安卓端推送给ios
                $android_to_android = false;

                if($token_type == 'Android'){       // 原来的机器码如果是安卓的
                    $android_to_android = true;   //安卓推安卓
                }

                if($android_to_android){
                    if ($user_type == 1) {
                        android_push_to_boss($uid, 50008,'message_change_token');
                    } else {
                        android_push_to_worker($uid, 50009,'message_change_token_2');
                    }
                }else{
                    if ($user_type == 1) {
                        //推送给业主
                        small_order_to_boss($uid, 50008, 'message_change_token');
                    } else {
                        //推送给工作者
                        small_order_to_worker($uid, 50009, 'message_change_token_2');
                    }
                }
                $user_info->device_token_type = $deviceToken[0];
            }

//            if ($deviceToken[0] != $token_type) {
//                //通知原来的用户账户被挤下线
//                //安卓端推送给ios
//                if ($deviceToken[0] == 'Android') {
//                    if ($user_type == 1) {
//                        //推送给业主
//                        small_order_to_boss($uid, 50008, 'message_change_token');
//                    } else {
//                        //推送给工作者
//                        small_order_to_worker($uid, 50009, 'message_change_token_2');
//                    }
//                } else {
//                    //ios端挤掉安卓
//                    if ($user_type == 1) {
//                        android_push_to_boss($uid, 50008,'message_change_token');
//                    } else {
//                        android_push_to_worker($uid, 50009,'message_change_token_2');
//                    }
//                }
//                $user_info->device_token_type = $deviceToken[0];
//            }
            $user_info->device_token = $deviceTokenNew;//保存机器码
        }

        if ($user_info->save()) {
            return response()->json(['message' => 'success'], 200);
        };

        return response()->json(['message' => 'error'], 500);
/*        //通知
        $message     = '你妈妈喊你回家吃饭!';//消息内容
        $deviceToken = 'facef2f1c76e74af853949031f9c27a64e22764c24aa7eaee27e56c464725d33';//应用对应机器码(去掉空格)
        PushServiceModel::pushMessage($deviceToken, $message, 1);*/
    }

    /**
     * @param $uid
     * @return mixed
     * 获取用户个人中心数据
     */
    public function getUserInfoByid(Request $request) {

        $uid     = $request->get('uid');
        $useInfo = UserModel::select('users.user_type', 'users.id', 'users.status', 'ud.avatar', 'ud.lat', 'ud.lng', 'ud.city', 'ud.nickname', 'ud.realname', 'ud.balance', 'ud.star', 'ud.balance_status', 'ud.experience', 'ud.introduce', 'ud.tag', 'ud.address','ud.cost_of_design')->join('user_detail as ud', 'ud.uid', '=', 'users.id')
            ->where('users.id', $uid)
            ->first();

        $useInfo['avatar']   = empty($useInfo['avatar']) ? '' : url($useInfo['avatar']);
        $useInfo['nickname'] = is_null($useInfo['nickname']) ? '' : $useInfo['nickname'];
        $useInfo['city']     = is_null($useInfo['city']) ? '' : $useInfo['city'];
        $useInfo['realname'] = is_null($useInfo['realname']) ? '' : $useInfo['realname'];
        $useInfo['tag']      = empty(unserialize($useInfo['tag'])) ? [] : unserialize($useInfo['tag']);
        return response()->json($useInfo);

    }

    // TODO
    // 忘记密码
    public function forgetPassword(Request $request) {
        $code              = $request->json('code');
        $data['name']      = $request->json('username');
        $data['password']  = $request->json('password');
        $data['user_type'] = $request->json('user_type');

        $checkCode = VerificationModel::checkCode($code, $data['name']);


        if ($checkCode) {
            // 修改密码
            $salt        = \CommonClass::random(4);
            $newPassword = UserModel::encryptPassword($data['password'], $salt);

            $user_type = intval($data['user_type']);

//            if ($user_type == 0) {
//                $state = UserModel::where('name', $data['name'])->where('user_type', '>', 1)->update(['password' => $newPassword, 'salt' => $salt]);
//            }
//
//            if ($user_type == 1) {
//            
                $state = UserModel::where('name', $data['name'])->update(['password' => $newPassword, 'salt' => $salt]);
//            }

            if ($state) {
                return response()->json(['success' => '修改成功']);
            }

            return response()->json(['error' => '修改失败'], '500');
        }

        return response()->json(['error' => '验证码错误'], '403');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据电话号码返回名称和姓名
     */
    public function getAvatarByPhoeNum(Request $request) {
        $phone_num = $request->get('phone_num');
        $user_info = UserModel::select('user_detail.nickname', 'user_detail.avatar', 'user_detail.realname', 'users.user_type')
            ->where('users.name', $phone_num)
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'users.id')
            ->first();

        if (empty($user_info))
            return response()->json([]);
        $user_info->avatar   = url($user_info->avatar);
        $user_info->nickname = empty($user_info->nickname) ? $user_info->realname : $user_info->nickname;
        unset($user_info->realname);
        return response()->json($user_info);
    }


    /**
     * @param Request $request
     * @return int
     * 替换摄像头url
     */
    public function submitLiveTVUrl(Request $request) {
        $live_tv_url = $request->live_tv_url;
        $task_id     = $request->task_id;
        //找到工地
        $project_position = TaskModel::find($task_id)->project_position;
        ProjectPositionModel::where('id', $project_position)->update(['live_tv_url' => $live_tv_url]);
        $data = [
            "live_tv_url"=>$live_tv_url
        ];
        return response()->json($data);
    }


    // 获取风格列表
    public function getStyles(){
        $category_data = TaskCateModel::findByPid([0] , ['id','name']);
        return response()->json( $category_data );
    }

 
    // 获取空间列表
    public function getSpaces(){
        $space_data = SpaceModel::select('id','name')->where('is_deleted' , 0)->get()->toArray();
        return response()->json( $space_data );
    }

        // 获取户型列表
    public function getHouses(){
        $house_data = HouseModel::select('id','name')->where('is_deleted' , 0)->get()->toArray();
        return response()->json( $house_data );
    }


    /**
     * @param $index
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 有designers参数的话,是获取所有设计师,有works,是获取所有设计师作品
     */
    public function worksLists() {

        $data['styles']    = TaskCateModel::findByPid([0], ['id', 'name']);
        $data['spaces']    = SpaceModel::select('id', 'name')->where('is_deleted', 0)->get()->toArray();
        $data['houses']    = HouseModel::select('id', 'name')->where('is_deleted', 0)->get()->toArray();
        $data['districts'] = DistrictModel::getDistrictProvinceFiles();

        $data['recommend'] = ShopModel::select('user_detail.avatar', 'user_detail.uid', 'user_detail.nickname', 'users.name')
            ->where('shop.is_recommend', 1)
            ->where('shop.status', 1)
            ->where('users.status', 1)
            ->where('users.user_type', 2)
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'shop.uid')
            ->leftJoin('users', 'user_detail.uid', '=', 'users.id')
            ->limit(3)
            ->get()->toArray();

        // TODO 更改资料后，shop表的头像也要更新   设计师列表

        // 只有设计师才有作品
        $data['items'] = GoodsModel::select('goods.id', 'goods.goods_address', 'goods.view_num', 'goods.title', 'goods.cover', 'goods.uid', 'user_detail.avatar', 'user_detail.receive_task_num')
            ->where('goods.status', 1)
            ->where('goods.type', 1)
            ->where('is_delete', 0)
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'goods.uid')
            ->leftJoin('users', 'user_detail.uid', '=', 'users.id')
            ->where('users.user_type',2)
            ->orderBy('goods.updated_at', 'desc')
            ->get()->toArray();

        foreach ($data['items'] as $key => &$value) {
            //删除禁用的设计师作品
            if (UserModel::find($value['uid'])->status == 2) {
                unset($data['items'][$key]);
            }

            $value['receive_task_num'] = floatval($value['receive_task_num']);
            $value['avatar']           = !empty($value['avatar']) ? url($value['avatar']) : '';

            $cover           = array();
            $cover[]         = url(str_replace('.', '{@fill}.', $value['cover']));
            $attatchment_ids = UnionAttachmentModel::where('object_id', '=', $value['id'])->where('object_type', 4)->lists('attachment_id')->toArray();
            $attatchment_ids = array_flatten($attatchment_ids);
            $attatchment     = AttachmentModel::select('url')->whereIn('id', $attatchment_ids)->get()->toArray();

            foreach ($attatchment as $key3 => $value3) {

                $cover[] = url(str_replace('.', '{@fill}.', $value3['url']));
            }

            $value['cover'] = $cover;

        }
        $data['items'] = array_values($data['items']);
        unset($value);


        foreach ($data['recommend'] as $key => &$value) {

            $value['nickname'] = !empty($value['nickname']) ? $value['nickname'] : $value['name'];
            $value['avatar']   = !empty($value['avatar']) ? url($value['avatar']) : '';
            unset($value['name']);
        }

        return response()->json($data);
    }


    /**
     * 获取所有设计师
     */
    public function designerLists(){

        $data['styles']    = TaskCateModel::findByPid([0], ['id', 'name']);
        $data['spaces']    = SpaceModel::select('id', 'name')->where('is_deleted', 0)->get()->toArray();
        $data['houses']    = HouseModel::select('id', 'name')->where('is_deleted', 0)->get()->toArray();
        $data['districts'] = DistrictModel::getDistrictProvinceFiles();

        $data['recommend'] = ShopModel::select('user_detail.avatar','user_detail.uid','user_detail.nickname','users.name')
            ->where('shop.is_recommend',1)
            ->where('shop.status',1)
            ->where('users.status',1)
            ->where('users.user_type',2)
            ->leftJoin('user_detail','user_detail.uid','=','shop.uid')
            ->leftJoin('users','user_detail.uid','=','users.id')
            ->limit(3)
            ->get()->toArray();


            $data['items'] = ShopModel::select('user_detail.nickname','user_detail.realname','user_detail.city','user_detail.address','user_detail.cost_of_design','user_detail.avatar','user_detail.uid','user_detail.experience','user_detail.receive_task_num')
                ->where('shop.status',1)
                ->where('users.status',1)
                ->where('users.user_type',2)
                ->leftJoin('user_detail' , 'user_detail.uid','=','shop.uid')
                ->leftJoin('users','user_detail.uid','=','users.id')
                ->leftJoin('realname_auth' , 'realname_auth.uid' , '=' , 'users.id')
                ->orderBy('users.sort_id')
                ->get()->toArray();

            foreach ($data['items'] as $key => $value) {
                $data['items'][$key]['receive_task_num'] = floatval($value['receive_task_num']);
                $data['items'][$key]['experience']       = floatval($value['experience']);
                $data['items'][$key]['avatar']           = !empty($value['avatar']) ? url($value['avatar']) : '';
                $data['items'][$key]['nickname']         = !empty($value['nickname']) ? $value['nickname'] : $value['realname'];

                unset($data['items'][$key]['realname']);
                $goods = GoodsModel::select('id','view_num')
                    ->where('is_delete',0)
                    ->where('type',1)
                    ->where('goods.status',1)
                    ->where('uid',$value['uid'])
                    ->get()->toArray();
                $data['items'][$key]['view_num'] = $goods;
//                //浏览量
                if (!empty($goods)) {
                    foreach ($data['items'][$key]['view_num'] as $n => $m) {
                        $data['items'][$key]['view_num'] = $m['view_num'];
                    }
                } else {
                    $data['items'][$key]['view_num'] = 0;
                }
            }

        foreach ($data['recommend'] as $key => &$value) {
            $value['nickname'] = !empty($value['nickname'])?$value['nickname']:$value['name'];
            $value['avatar'] = !empty($value['avatar'])?url($value['avatar']):'';
            unset($value['name']);
        }



        return response()->json( $data );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 用户确认接单工作端人选
     */

    public function interview(Request $request) {
        $data['user_id'] = intval($request->json('user_id'));    //业主id
        $data['work_id'] = intval($request->json('designer_id'));//雇佣者id
        $data['task_id'] = intval($request->json('task_id'));    //任务id

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json(['error' => '参数不完整'], '500');
            }
        }


        // 判断任务是否属于该用户
        $taskInfo = TaskModel::where('id', $data['task_id'])->first();

        if (empty($taskInfo)) {
            return response()->json(['error' => '无对应任务数据'], '500');
        }
        //找到工地
        $project_info = ProjectPositionModel::find($taskInfo->project_position);
        if (empty($project_info)) return response()->json(['error' => '找不到工地'], '500');
        $chat_room_id = (int)$project_info->chat_room_id;
        $userInfo     = UserModel::find($data['work_id']);
        if (empty($userInfo)) return response()->json(['error' => '找不到预约的工作者'], '500');
        $user_name     = $userInfo->name;
        $res_chat_room = $this->chatRoomRespository->addWorkToChatRoom($chat_room_id, $user_name);
        if (empty($res_chat_room)) {
            return response()->json(['error' => '操作失败'], '500');
        }


        $is_accet = WorkModel::where('task_id', $data['task_id'])->where('uid', $data['work_id'])->where('status', 1)->first();

        if ($is_accet) {
            return response()->json(['error' => '该任务已确认工作人选'], '500');
        }

        $work = WorkModel::where('task_id', $data['task_id'])->where('uid', $data['work_id'])->first();

        if (!empty($work)) {
            TaskModel::where('id', $data['task_id'])->update(['status' => 6]);
            $status = WorkModel::where('task_id', $data['task_id'])->where('uid', $data['work_id'])->update(['status' => 1]);

/*            //推送给工作者
            $application = 50002;
            $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_interview_worker')->first()->chn_name;
            $userInfo->send_num += 1;
            $userInfo->save();
            //保存发送的消息
            save_push_msg($message, $application, $data['work_id']);
            PushServiceModel::pushMessageWorker($userInfo->device_token, $message, $userInfo->send_num, $application);*/
            $application = 50002;
            push_accord_by_equip($data['work_id'], $application, 'message_interview_worker', '', $data['task_id']);
            if ($taskInfo['user_type'] == 2) {
                $status_log = workDesignerLog::where('task_id', $data['task_id'])->where('new_uid', $data['work_id'])->update(['boss_confirm' => 1]);
            }

        } else {
            return response()->json(['error' => '操作失败'], '500');
        }


        $data['title'] = '工作者报价流程';
        $data['sn']    = '0';

        if ($taskInfo['user_type'] == 2) {
            $data['type'] = 'designer';
        }
        if ($taskInfo['user_type'] == 3) {
            $data['type'] = 'housekeeper';
        }
        if ($taskInfo['user_type'] == 4) {
            $data['type'] = 'overseer';
        }
        // TODO 这里暂时写死，后期改成从数据库获取
        $data['percent'] = json_encode(array('0.2', '0.4', '0.4'));

        $data['price']    = '0';
        $data['from_uid'] = $data['user_id'];
        $data['to_uid']   = $data['work_id'];
        $data['status']   = '0';
        $data['work_id']  = $work['id'];

        $status = WorkOfferModel::create($data);

        if ($status) {
            //选定之后,将该工作者加入聊天室
            return response()->json(['message' => '确认约谈人选成功']);
        }
        return response()->json(['error' => '操作失败'], '500');
    }


    // 用户确认支付设计师的报价单
    public function payDesignerPrice(Request $request) {

        $data['from_uid'] = $request->json('user_id');
        $data['task_id']  = $request->json('task_id');
        $data['password'] = $request->json('password');        // 若是余额支付，要提交密码
        $work             = WorkModel::where('task_id', $data['task_id'])->where('status', '>=', 1)->first();//获取任务
        if ($work->status == 2) return response()->json(['error' => '该订单已支付'], '403');

        //找到该用户,扣除余额补充到保证金里面
        $user_boss = UserDetailModel::where('uid', $data['from_uid'])->first();
        $userInfo  = UserModel::where('id', $data['from_uid'])->where('status', 1)->where('user_type', 1)->first();
        $diff      = $user_boss->balance - $work->price;

        if ($diff < 0) {
            // TODO 这里使用第三方支付补缴
            return response()->json(['error' => '余额不足，需使用第三方补缴', 'difference' => abs($diff)], '500');
        } else {
            // 这里是使用余额支付
            $password = UserModel::encryptPassword($data['password'], $userInfo['salt']);
            if ($password != $userInfo['password']) {
                return response()->json(['error' => '您的支付密码不正确'], '403');
            }
        }
        $origin_order = OrderModel::where('task_id', $data['task_id'])->where('uid', $data['from_uid'])->first();
        if (empty($origin_order)) return response()->json(['error'=>'找不到该订单'],500);

        //找到预约金
        $poundage_service = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;

        //实付金额
        $real_pay = $work->price - $poundage_service;
        $origin_order->cash += $real_pay;
        $origin_order_res = $origin_order->save();//一张订单,订单编号唯一

        //sub_order加入扣费记录
        $is_ordered       = OrderModel::sepbountyOrder($data['from_uid'], $real_pay, $data['task_id'], '支付设计师报价表(转入冻结金)', 1);
        $decrement        = TaskModel::bounty($real_pay, $data['task_id'], $data['from_uid'], $origin_order->code, 1, 5);//一次性扣除
        $res_frozen_money = UserDetailModel::where('uid', $data['from_uid'])->update(['frozen_amount' => $user_boss->frozen_amount += $real_pay]);//把扣除金额写进冻结资金

        if (!empty($work) && $res_frozen_money && $decrement && $origin_order_res && $is_ordered) {
            $ret = WorkOfferModel::where('from_uid', $data['from_uid'])
                ->where('task_id', $data['task_id'])
                ->where('work_id', $work['id'])
                ->where('sn', 0)
                ->update(['status' => 2]);
            if ($ret) {
                $ret_1 = WorkOfferModel::where('from_uid', $data['from_uid'])
                    ->where('task_id', $data['task_id'])
                    ->where('work_id', $work['id'])
                    ->where('sn', 0)
                    ->update(['status' => 4]);

                // 设计步骤开始
                $ret_2 = WorkModel::where('id', $work['id'])->update(['status' => 2]);
                if ($ret_2) {

                    $arr = array(
                        '1' => array(
                            'type' => 'designer',
                            'task_id' => $data['task_id'],
                            'sn' => 1,
                            'title' => "初步设计",
                            'percent' => '0.2',
                            'price' => 0.2 * $work['price'],
                            'work_id' => $work['id'],
                            'from_uid' => $data['from_uid'],
                            'to_uid' => $work['uid'],
                            'status' => 0
                        ),
                        '2' => array(
                            'type' => 'designer',
                            'task_id' => $data['task_id'],
                            'sn' => 2,
                            'title' => "深化设计",
                            'percent' => '0.4',
                            'price' => 0.4 * $work['price'],
                            'work_id' => $work['id'],
                            'from_uid' => $data['from_uid'],
                            'to_uid' => $work['uid'],
                            'status' => 0
                        ),
                        '3' => array(
                            'type' => 'designer',
                            'task_id' => $data['task_id'],
                            'sn' => 3,
                            'title' => "施工指导",
                            'percent' => '0.4',
                            'price' => 0.4 * $work['price'],
                            'work_id' => $work['id'],
                            'from_uid' => $data['from_uid'],
                            'to_uid' => $work['uid'],
                            'status' => 0
                        )
                    );

                    foreach ($arr as $key => $value) {
                        WorkOfferModel::create($value);
                    }
                    //推送给设计师
                    $application = 40007;
/*                    $work_data   = UserModel::find($work->uid);
                    $message     = PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_boss_pay_salary')->first()->chn_name;
                    $work_data->send_num += 1;
                    $work_data->save();

                    //保存发送的消息
                    save_push_msg($message, $application, $work_data->id);
                    PushServiceModel::pushMessageWorker($work_data->device_token, $message, $work_data->send_num, $application);*/
                    push_accord_by_equip($work->uid, $application, 'message_boss_pay_salary', '', $data['task_id']);
                    return response()->json(['message' => '支付成功']);
                }
                return response()->json(['error' => '步骤一支付失败'], '500');
            }
            return response()->json(['error' => '步骤二支付失败'], '500');
        }

        return response()->json(['error' => '步骤三支付失败'], '500');
    }



    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据uid拼接数据
     */
    public function userTasks(Request $request) {

        $room_config_arr = array(
            'bedroom' => '房',
            'living_room' => '厅',
            'kitchen' => '厨',
            'washroom' => '卫',
        );
        $data['uid'] = intval($request->get('user_id'));        //用户id
        $tasks = $this->taskRespository->getUserTasks($data['uid'],$room_config_arr);
        return response()->json($tasks);
    }
    

    /**
     * @param Request $request
     * @param $uid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 获取余额接口
     */
    public function getUserBalance(Request $request) {

        $uid = $request->get('uid');
        if (empty($uid)) {
            return response()->json(['error' => 'uid为空'], '500');
        } else {
            $balance       = UserDetailModel::where('uid', $uid)->first()->balance;
            $frozen_amount = UserDetailModel::where('uid', $uid)->first()->frozen_amount;
            return response()->json(['balance' => number_format($balance, 2), 'frozen' => number_format($frozen_amount, 2)]);
        }
    }

    /**
     * 获取拒绝原因
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function getRefuseReason() {
        $data_reason = RefuseReasonModel::all();
        return response()->json($data_reason);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 保存拒绝原因
     */
    public function saveWorkRefuseReason(Request $request){
        $refuse_ids             = $request->json('refuse_ids');
        $work_id                = $request->json('work_id');
        $data                   = WorkModel::find($work_id);
        $data->refuse_reason_id = serialize($refuse_ids);
        if ($data->save()) {
            return response()->json('success');
        } else {
            return response()->json('error', '403');
        }
    }


//    根据条件筛选
    public function filterList( $type , $style_id , $house_id , $space_id ){
        if($type == 'worker'){

        }

        if($type == 'goods'){

        }
    }



    /**
     * 评价列表
     */
    public function getEvaluateOfWorker(Request $request) {
        $uid = $request->get('uid');
        //头像,姓名,头像,评价id
        $comments = CommentModel::select('comments.from_uid', 'comments.total_score', 'comments.comment', 'comments.created_at', 'user_detail.avatar', 'user_detail.realname', 'user_detail.nickname')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'comments.from_uid')
            ->where('to_uid', $uid)->orderBy('comments.id', 'desc')->get();

        foreach ($comments as $k => $v) {
            if(empty($v['created_at'])){
                $comments[$k]['created_at'] = '';
            }
            $comments[$k]['avatar']    = url($v['avatar']);
            $comments[$k]['boss_name'] = empty($v['nickname']) ? $v['realname'] : $v['nickname'];
            unset($v['realname'], $v['nickname']);
        }
        return response()->json($comments);
    }

    /**
     * 业主评价工人
     */
    public function evaluateWorker(Request $request) {
        $data_labor    = $request->json('data_labor');
        $work_offer_id = $request->json('work_offer_id');
        $res_comment   = CommentModel::where('work_offer_id', $work_offer_id)->first();

        if (!empty($res_comment)) {
            return response()->json(['error' => '您已经评论过了']);
        }
        foreach ($data_labor as $k => $v) {
            $res_evaluate = $this->taskOperaRespository->evaluateWorker($v, $work_offer_id);
        }

        if ($res_evaluate) {
            return response()->json(['message' => '操作成功']);
        } else {
            return response()->json(['error' => '操作失败'], 500);
        }
    }

    /**
     * 业主评价管家和监理和设计师
     */
    public function evaluateHouser(Request $request) {

        $data_labor = $request->json('data_labor');

        foreach ($data_labor as $k => $v) {
            $work_offer_info = WorkOfferModel::where('task_id', $v['task_id'])->where('sn', 0)->first();
            $work_offer_id   = $work_offer_info->id;
            $res_evaluate    = $this->taskOperaRespository->evaluateHouser($v['worker_id'], $v['score'], $work_offer_id, $v['comment'], $v['task_id'], $work_offer_info);
        }

        if ($res_evaluate) {
            return response()->json(['message' => '操作成功']);
        } else {
            return response()->json(['error' => '操作失败,已评论过'], 500);
        }
    }




    /**
     * 检测是否已评价过该工人
     */
    public function evaluateStatus(Request $request) {
        $work_offer_id   = $request->get('work_offer_id');
        $work_offer_data = WorkOfferModel::find($work_offer_id);
        if (empty($work_offer_data))
            return response()->json(['error' => '请求失败'], 500);
        else
            return response()->json(['status' => $work_offer_data->evaluate_status]);
    }

    /**
     * 检测是否已评价过该管家和监理
     */
    public function evaluateStatusOfHouser(Request $request) {

        $task_id         = $request->get('task_id');
        $house_work_info = WorkModel::where('task_id', $task_id)->where('status', '>', 0)->first();
        if (empty($house_work_info)) return response()->json(['error' => '系统错误'], 500);
        //默认取报价阶段的状态判断有没有评价过
        $work_offer_data = WorkOfferModel::where('task_id', $task_id)->where('to_uid', $house_work_info->uid)->where('sn', 0)->first();
        if (empty($work_offer_data))
            return response()->json(['error' => '请求失败'], 500);
        else
            return response()->json(['status' => $work_offer_data->evaluate_status]);
    }

    /**
     * 根据work_offer_id拿到工人
     * @param Request $request
     */
    public function getLaborEvaluate(Request $request) {
        $work_offer_id   = $request->get('work_offer_id');
        $work_offer_data = WorkOfferModel::find($work_offer_id);
        if (empty($work_offer_data))
            return response()->json(['error' => '请求失败'], 500);
        $labors = explode('-', $work_offer_data->to_uid);
        foreach ($labors as $k => $v) {
            $data['labors'][] = UserDetailModel::select('uid','avatar', 'realname', 'work_type')->where('uid', $v)->first();
        }
        foreach ($data['labors'] as $k => $v) {
            $data['labors'][$k]['avatar']    = url($v['avatar']);
            $data['labors'][$k]['work_type'] = get_work_type_name($v['work_type']);
        }
        return response()->json($data);
    }

    /**
     * 根据task_id拿到管家和监理详细
     * @param Request $request
     */
    public function getHouserKpperEvaluate(Request $request) {

        $task_id         = $request->get('task_id');
        $house_work_info = WorkModel::where('task_id', $task_id)->where('status', '>', 0)->first();
        $task_data       = TaskModel::find($task_id);

        if (empty($task_data)) {
            return response()->json(['error' => '找不到该订单'], 500);
        }
        //根据管家的task_id找监理的task_id
        $supervisor_task_info = TaskModel::select('id')->where('project_position', $task_data->project_position)->where('status', '<', 9)->where('user_type', 4)->first();

        if (empty($house_work_info)) {
            return response()->json(['error' => '找不到该管家信息'], 500);
        }
        if (empty($supervisor_task_info)) {
            return response()->json(['error' => '找不到该监理信息'], 500);
        }
        $super_work_info = WorkModel::where('task_id', $supervisor_task_info->id)->where('status', '>', 0)->first();

        if (empty($super_work_info)) {
            return response()->json(['error' => '找不到该管家或者监理信息'], 500);
        }
        $designer_task_info = TaskModel::select('id')->where('project_position', $task_data->project_position)->where('status', '<', 9)->where('user_type', 2)->first();
        $designer_work_info      = WorkModel::where('task_id', $designer_task_info->id)->where('status', '>', 0)->first();


        $data                     = [];
        $data['house']            = UserDetailModel::select('uid', 'avatar', 'realname', 'users.user_type')
            ->where('uid', $house_work_info->uid)
            ->leftJoin('users', 'users.id', '=', 'user_detail.uid')
            ->first();
        $data['house']['task_id'] = $task_id;

        $data['super']            = UserDetailModel::select('uid', 'avatar', 'realname', 'users.user_type')
            ->where('uid', $super_work_info->uid)
            ->leftJoin('users', 'users.id', '=', 'user_detail.uid')
            ->first();
        $data['super']['task_id'] = $supervisor_task_info->id;

        $data['designer']            = UserDetailModel::select('uid', 'avatar', 'realname', 'users.user_type')
            ->where('uid', $designer_work_info->uid)
            ->leftJoin('users', 'users.id', '=', 'user_detail.uid')
            ->first();
        $data['designer']['task_id'] = $designer_work_info->id;
        foreach ($data as $k => $v) {
            $data[$k]['avatar']    = url($v['avatar']);
            $data[$k]['work_type'] = get_user_type_name($v['user_type']);
        }
        return response()->json(array_values($data));
    }






    /************************************************************************************************************
     * 重构的接口
     *********************************************************************************************************/

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据task_id获取订单详细
     */
    public function getUserTaskDetail(Request $request) {
        $task_id                     = intval($request->get('task_id'));
        $last_amendment_sheet_status = 0;//客诉中心状态
        $last_delay_date_status      = 0;//延期单状态
        $is_have_dismantle           = 0;//判断是否有拆除
        $left_days                   = '';//剩余多少天
        //手续费，到系统配置里面找
        $poundage_service_price = ServiceModel::where('identify', 'SHOUXUFEI')->first()->price;
        $room_config_arr        = array('bedroom' => '房','living_room' => '厅','kitchen' => '厨','washroom' => '卫','balcony' => '阳台');

        $tasks = TaskModel::select(
            'task.uid','task.cancel_order','task.type_id as type_model',
            'p.room_config','task.created_at','p.square','task.status',
            'task.project_position as project_position_id',
            'c.name as favourite_style','task.id as task_id','task.user_type',
            'task.view_count','task.show_cash','p.region',
            'p.project_position','users.name','user_detail.mobile','user_detail.avatar as boss_avatar',
            'user_detail.nickname as boss_nike_name'
            )->where('task.id', $task_id)
            ->where('task.status', '>=', 3)
            ->where('task.bounty_status', 1)
            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
            ->leftJoin('users', 'users.id', '=', 'task.uid')
            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
            ->distinct('task.id')
            ->get()->toArray();

        foreach ($tasks as $key => &$value) {
            $str                  = '';
            $value['created_at']  = date('Y-m-d', strtotime($value['created_at']));
            $value['real_mobile'] = !empty($value['mobile']) ? $value['mobile'] : $value['name'];
            $value['boss_avatar'] = !empty($value['boss_avatar']) ? url($value['boss_avatar']) : '';
            unset($value['mobile'], $value['name']);
            $room_config_decode = json_decode($value['room_config']);

            foreach ($room_config_decode as $key2 => $value2) {
                if (isset($room_config_arr[$key2])) {
                    $str .= $value2 . $room_config_arr[$key2];
                }
            }

            $value['room_config'] = $str;
            //定金抵扣显示
            $value['poundage_service_price'] = (int)$poundage_service_price;

            //如果是设计师的单子,要连表
            if ($value['type_model'] == 2) {
                $workers = WorkModel::select(
                    'user_detail.uid','user_detail.nickname','user_detail.avatar',
                    'user_detail.mobile','user_detail.city as address','work.status',
                    'user_detail.cost_of_design','work.price',
                    'work.actual_square','work_designer_logs.is_refuse'
                     )->where('work.task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->leftJoin('work_designer_logs', 'work_designer_logs.new_uid', '=', 'work.uid')
                    ->where('work_designer_logs.task_id', $value['task_id'])
                    ->distinct('work.uid')
                    ->get()->toArray();
            } else {
                $workers = WorkModel::select(
                    'user_detail.uid','user_detail.nickname','user_detail.avatar',
                    'user_detail.mobile','user_detail.city as address',
                    'work.status','user_detail.cost_of_design',
                    'work.price','work.actual_square','work.is_refuse'
                    )->where('task_id', $value['task_id'])
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                    ->get()->toArray();
            }

            $work_data = WorkModel::where('task_id', $value['task_id'])->where('status', '>=', 1)->first();

            $value['actual_pay']  = 0;
            $value['total_price'] = 0;
            if (!empty($work_data)) {
                $value['actual_pay']  = $work_data->price - (int)$poundage_service_price;
                $value['total_price'] = $work_data->price;
            }

            //2：设计师 3：管家 4：监理
            $data_id['house_keeper_task_id'] = TaskModel::where('project_position', $value['project_position_id'])->where('status', '<', 9)->where('user_type', 3)->lists('id');
            $data_id['supervisor_task_id']   = TaskModel::where('project_position', $value['project_position_id'])->where('status', '<', 9)->where('user_type', 4)->lists('id');
            $data_id['designer_task_id']     = TaskModel::where('project_position', $value['project_position_id'])->where('status', '<', 9)->where('user_type', 2)->lists('id');

            $employ = [];
            foreach ($data_id as $n => $m) {
                if (empty($m)) {
                    unset($data_id[$n]);
                }
                foreach ($m as $o => $p) {
                    $employ[$n] = WorkModel::select(
                        'user_detail.uid','user_detail.nickname','user_detail.avatar',
                        'user_detail.mobile','user_detail.city as address'
                        )->where('task_id', $p)
                        ->where('status', '>', 1)
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'work.uid')
                        ->get()->toArray();
                }
            }

            if (!empty($employ)) {
                foreach ($employ as $n => $m) {
                    foreach ($m as $o => $p) {
                        $employ[$n][$o]['avatar'] = !empty($p['avatar']) ? url($p['avatar']) : '';
                    }
                }
            }

            $value['employs']     = $employ;
            foreach ($value['employs'] as $e => $t) {
                if (empty($t)) {
                    unset($value['employs'][$e]);
                }
            }

            foreach ($workers as $key3 => $value3) {
                $serve_area                = RealnameAuthModel::where('uid', $value3['uid'])->first()->serve_area;
                $workers[$key3]['avatar']  = !empty($value3['avatar']) ? url($value3['avatar']) : '';
                $workers[$key3]['mobile']  = !empty($value3['mobile']) ? $value3['mobile'] : UserModel::find($value3['uid'])->name;
                $value['actual_square']    = $value3['actual_square'];
                $workers[$key3]['address'] = empty($serve_area) ? '' : $serve_area;//服务区域
                $value['address']          = $value3['address'];
                $value['cost_of_design']   = $value3['cost_of_design'];
            }

            $value['workers'] = $workers;
            unset($value['uid']);

            if ($value['status'] == 7) {

                $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                    ->where('task_id', $value['task_id'])
                    ->orderBy('sn', 'ASC')
                    ->get()->toArray();

                //监理订单类型
                if ($value['user_type'] == 4) {

                    $is_replace              = false;
                    $project_position        = $value['project_position_id'];
                    $house_keeper_task       = TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
                    $house_keeper_work       = WorkModel::where('task_id', $house_keeper_task->id)->first();//有管家抢单
                    $house_keeper_work_payed = WorkModel::where('task_id', $house_keeper_task->id)->where('status', 2)->first();//已支付管家
                    $jianli_work             = WorkModel::where('task_id', $value['task_id'])->where('status', 2)->first();//监理已付款

                    if (!empty($house_keeper_task) && !empty($house_keeper_work) && !empty($house_keeper_work_payed) && $jianli_work) {
                        $chang_task_id = $house_keeper_task->id;
                    } else {
                        $chang_task_id = $value['task_id'];
                    }

                    $work_offer_status_1 = WorkOfferModel::select('project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price', 'task_id')
                        ->where('task_id', $chang_task_id)
                        ->orderBy('sn', 'ASC')
                        ->get()->toArray();

                    foreach ($work_offer_status_1 as $o => $p) {
                        if ($p['sn'] > 0 && $p['status'] > 0) {
                            $is_replace = true;
                            break;
                        }
                    }
                    if ($is_replace) {
                        $work_offer_status = $work_offer_status_1;
                    }
                }

                //返回work_offer中status为0的前一条数据
                foreach ($work_offer_status as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($work_offer_status[$n]);
                    }
                    //给第一次报价的钱
                    if ($m['sn'] == 0) {
                        $value['total_price'] = $m['price'];
                    }
                    // 判断是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }

                //status全部为0的情况判断下
                if (empty($work_offer_status)) {
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'task_id' => $value['task_id'], 'project_type' => 0];
                } else {
                    $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                }
                $tasks[$key]['node']             = $value['status'];
                $tasks[$key]['sn']               = $last_work_offer_status['sn'];
                $tasks[$key]['status']           = $last_work_offer_status['status'];
                $tasks[$key]['count_submit']     = $last_work_offer_status['count_submit'];
                $tasks[$key]['task_status_time'] = date('m-d H:i', strtotime($last_work_offer_status['task_status_time']));

                if ($value['user_type'] == 4) {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $last_work_offer_status['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();

                    $last_delay_date_status = ProjectDelayDate::where('task_id', $last_work_offer_status['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                } else {
                    $amendment_sheet_status = ProjectLaborChange::where('task_id', $value['task_id'])->where('project_type', $last_work_offer_status['project_type'])->orderBy('id', 'DESC')->first();
                    $last_delay_date_status = ProjectDelayDate::where('task_id', $value['task_id'])->where('sn', $last_work_offer_status['sn'])->orderBy('id', 'DESC')->first();
                }

                if (!empty($amendment_sheet_status)) {
                    $last_amendment_sheet_status = $amendment_sheet_status->status;
                }
                if (!empty($last_delay_date_status)) {
                    $last_delay_date = $last_delay_date_status->is_sure;
                    if ($last_delay_date == 5) {
                        $last_delay_date_status = 0;
                    } else {
                        $last_delay_date_status = $last_delay_date;
                    }
                } else {
                    $last_delay_date_status = 0;
                }

                //查看剩余多少天
                $end_data  = TaskModel::find($task_id)->end_at;
                $left_days = empty($end_data) ? '' : ceil((strtotime($end_data) - time()) / (3600 * 24));
            } else {

                $rob_time_offer = WorkOfferModel::where('task_id', $value['task_id'])->where('sn', 0)->first();
                $rob_time_work  = WorkModel::where('task_id', $value['task_id'])->first();
                if (!empty($rob_time_offer)) {
                    $rob_time = $rob_time_offer->created_at->format('m-d H:i:s');
                } elseif (!empty($rob_time_work)) {
                    $rob_time = date('m-d H:i', strtotime($rob_time_work->created_at));
                } else {
                    $rob_time = TaskModel::find($task_id)['created_at']->format('m-d H:i:s');
                }

                if (empty($value['cancel_order'])) {
                    $tasks[$key]['cancel_order'] = ['cancel_order_node' => 0, 'cancel_order_sn' => 0, 'cancel_order_status' => 0];
                } else {
                    $cancel_order_node           = explode('-', $value['cancel_order']);
                    $tasks[$key]['cancel_order'] = ['cancel_order_node' => $cancel_order_node[0], 'cancel_order_sn' => $cancel_order_node[1], 'cancel_order_status' => $cancel_order_node[2]];
                }

                $tasks[$key]['node']             = $value['status'];
                $tasks[$key]['sn']               = 0;
                $tasks[$key]['status']           = 0;
                $tasks[$key]['count_submit']     = 0;
                $tasks[$key]['task_status_time'] = $rob_time;
            }
            $tasks[$key]['is_have_dismantle']           = $is_have_dismantle;
            $tasks[$key]['last_amendment_sheet_status'] = $last_amendment_sheet_status;
            $tasks[$key]['last_delay_date_status']      = $last_delay_date_status;
            $tasks[$key]['left_days']                   = $left_days;
        }
        //筛选出锁定人员
        foreach ($tasks as $o => $p) {
            foreach ($p['workers'] as $n => $m) {
                if ($m['status'] > 0) {
                    $tasks[$o]['lock_uid'] = $m['uid'];
                }
            }
        }
        return response()->json($tasks);
    }

    /**
     * @param Request $request
     * 根据uid获取银行卡
     */
    public function getUserBankInfoByid(Request $request) {
        $uid           = $request->get('uid');
        $uid_bank_info = BankAuthModel::select('deposit_name', 'bank_account')->where('uid', $uid)->first();
        if (empty($uid_bank_info)) {
            $uid_bank_info['deposit_name'] = '';
            $uid_bank_info['bank_account'] = '';
        } else {
            $str                           = $uid_bank_info['bank_account'];
            $uid_bank_info['bank_account'] = substr($str, 0, 4) . " **** **** **** " . substr($str, -3);
        }
        return response()->json($uid_bank_info);
    }

    /**
     * @param Request $request
     * 生成邀请码
     */
    public function GenInvitationCode(Request $request) {
        $uid           = $request->uid;

        $DigCryptModel = new DigCryptModel();
        $code          = $DigCryptModel->en($uid);
        $host          = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        $url           = $host . '/user/regByMobile/' . $code;
        return response()->json(['url'=>$url]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 渲染试图
     */
    public function registerPlatform(Request $request,$code) {
        return view('yizhuang.registerInvite',compact('code'));
    }

    /**
     * 提交注册,跳转到下载app页面
     */
    public function postRegisterByCode(Request $request) {

        $this->validate($request,
            [
                'verify_code' => 'required|between:100000,999999|numeric|integer',
                'tel' => 'required',
                'password' => 'required',
            ],
            [
                'verify_code.required' => '必须填写验证码',
                'tel.required' => '必须填写手机号码',
                'password.required' => '必须填写密码',
                'verify_code.between' => '验证码介于:min - :max'
            ]
        );
        $invite_code        = empty($request->code_invite) ? 0 : $request->code_invite;

        $data['code']       = $request->get('verify_code');//验证码
        $data['username']   = $request->get('tel');//手机号
        $data['password']   = $request->get('password');//密码
        $data['user_type']  = empty($request->get('user_type')) ? 1 : $request->get('user_type');//业主
        $DigCryptModel      = new DigCryptModel();
        $data['invite_uid'] = is_numeric($invite_code) ? $invite_code : $DigCryptModel->de($invite_code);//解码邀请码
        $data['nickname']   = empty($request->name) ? $request->get('tel') : $request->name;//昵称
        if (!(isMobile($data['username']))) {
            return response()->json(['error'=>['您输入的不是手机号码']],500);
        }

//        $res_select_invite_uid = UserModel::select('invite_uid')->where('invite_uid', $data['invite_uid'])->first();
//        if ($res_select_invite_uid) {
//            $data['invite_uid'] = 0;
//        }
        $checkCode = VerificationModel::checkCode($data['code'], $data['username']);
        if (!$checkCode) {
            return response()->json(['error' => ['验证码不正确!']],500);
        }
        $is_exist_user = UserModel::where('name', $data['username'])->where('status', 1)->first();
        if (empty($is_exist_user)) {
            UserModel::createUserMobile($data);
            $this->chatRoomRespository->RegistEaseMob($data['username']);
            VerificationModel::sendWelcomeMsg($data['username']);
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['error' => ['您已经注册过了']], 500);
        }

    }
    /**
     * @param Request $request
     * 获取线下转账的公司信息
     */
    public function getCompanyBankInfo(Request $request) {
        $all_data = CompanyBankInfo::orderBy('id','desc')->first();
        return response()->json($all_data);
    }

    /**
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 新手图片一次性给前端
     */
    public function getAllAdAndHelpImg() {
        $all_imgs = AdImg::select('type', 'url', 'id')->orderBy('id','asc')->get();
        $data     = [];
        foreach ($all_imgs as $k => $v) {
            $all_imgs[$k]['url'] = url($v['url']);
            if ($v['type'] == 'ad_img') {
                $data['ad_img'][] = $v;
            } else {
                $data['help_img'][] = $v;
            }
        }

        return response()->json($data);
    }


//    /**
//     * @param Request $request
//     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
//     * 启动广告先给前端
//     */
//    public function getStartAd(Request $request) {
//        $all_imgs = AdImg::select('type', 'url', 'id')->where('type','ad_img')->get();
//        $data     = [];
//        foreach ($all_imgs as $k => $v) {
//            $all_imgs[$k]['url'] = url($v['url']);
//            if ($v['type'] == 'ad_img') {
//                $data['ad_img'][] = $v;
//            } else {
//                $data['help_img'][] = $v;
//            }
//        }
//        return response()->json($data);
//    }

    /**
     * 根据管家输入的信息显示对应的价格,不能超过管家本身的星级
     */
    public function getHousePriceByStar(Request $request) {
        $uid       = $request->get('uid');
        $user_info = UserModel::find($uid);
        if (empty($user_info)) {
            return response()->json(['error' => '找不到该用户'], 500);
        }
        $user_type = $user_info->user_type;
        if ($user_type == 3) {
            $config1 = LevelModel::getConfigByType(1)->toArray();
        } elseif ($user_type == 4) {
            $config1 = LevelModel::getConfigByType(2)->toArray();
        } else {
            return response()->json(['error'=>'找不到星级规则'], 500);
        }
        $workerStarPrice = LevelModel::getConfig($config1, 'price');
        $m               = 0;
        foreach ($workerStarPrice as $k => $v) {
            $m++;
            $workerStarPrice[$k]->company = $m;
        }
        return response()->json($workerStarPrice);
    }


    /**
     *
     * 获取全国省市区经纬度
     * @return mixed
     *
     */
    public function getCoordinate(Request $request) {

        $list = CoordinateModel::findTree();
        return response()->json($list);

    }


    /**
     * 批量补充工作者的服务城市id , 已coor表的数据为基准
     */
    public function setUserServeId(){
  
        $users = UserModel::select("users.id",'user_detail.serve_area_id','realname_auth.serve_area')->join('user_detail','users.id','=','user_detail.uid')
            ->join('realname_auth','realname_auth.uid','=','users.id')
            ->whereIn('users.user_type',[2,3,4])
            ->where('user_detail.serve_area_id','=',0)
            ->get()->toArray();
        $list = CoordinateModel::findTree();


//        $b = strpos( '宜兴市' , '宜兴');
//        var_dump($b);exit;

        foreach($list as $key => $value){
            foreach($users as $key2 => $value2){

                    if( strpos( $value['name'] , $value2['serve_area']) !==false ){
                        UserDetailModel::where('uid',$value2['id'])->update(['serve_area_id'=>$value['id']]);
                    }

            }
        }


        return response()->json($users);
    }

}
