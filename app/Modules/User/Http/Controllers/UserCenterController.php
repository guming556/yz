<?php
namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\UserCenterController as BasicUserCenterController;
use App\Http\Requests;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Http\Requests\PasswordRequest;
use App\Modules\User\Http\Requests\UserInfoRequest;
use App\Modules\User\Model\AttachmentModel;
use App\Modules\User\Model\DistrictModel;
use App\Modules\User\Model\TagsModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\UserFocusModel;
use App\Modules\User\Model\UserTagsModel;
use App\Modules\User\Model\UserModel;
use App\Modules\User\Model\AuthRecordModel;
use Auth;
use Illuminate\Http\Request;
use Gregwar\Image\Image;
use Illuminate\Support\Facades\Session;
use Theme;
use App\Modules\Task\Model\WorkAttachmentModel;
class UserCenterController extends BasicUserCenterController
{

    public function __construct()
    {
        parent::__construct();
        $this->user = Auth::user();
    }

    /**
     * 用户中心首页页面
     */
    public function index()
    {
        $this->initTheme('userindex');//主题初始化
        $this->theme->setTitle('用户中心');
        $this->theme->set('keywords','用户中心,管理中心,用户管理中心');
        $this->theme->set('description','用户中心，用户管理中心。');
        //关联查询用户的detail数据
        $user_data = UserModel::select('users.name as nickname', 'user_detail.avatar', 'user_detail.balance')
            ->where('users.id', $this->user['id'])
            ->join('user_detail', 'users.id', '=', 'user_detail.uid')
            ->first()->toArray();
        $domain = \CommonClass::getDomain();
        $user_data['avatar_url'] = $domain . '/' . $user_data['avatar'] . md5($this->user['id'] . 'large') . '.jpg';
        //查询用户三种银行信息绑定是否成功
        $userModel = new UserModel();
        $user_auth = $userModel->isAuth($this->user['id']);
        //查询用户的绑定关系
//        $userAuthOne = AuthRecordModel::where('uid',$this->user['id'])->where('status',2)
//            ->whereIn('auth_code',['bank','alipay'])->get()->toArray();
//        $userAuthTwo = AuthRecordModel::where('uid',$this->user['id'])->where('status',1)
//            ->whereIn('auth_code',['realname','enterprise'])->get()->toArray();
//        $userAuth = array_merge($userAuthOne,$userAuthTwo);
        if (!empty($userAuth) && is_array($userAuth)) {
            foreach ($userAuth as $k => $v) {
                $authCode[] = $v['auth_code'];
            }
            if (in_array('realname', $authCode)) {
                $realName = true;
            } else {
                $realName = false;
            }
//            if (in_array('bank', $authCode)) {
//                $bank = true;
//            } else {
//                $bank = false;
//            }
//            if (in_array('alipay', $authCode)) {
//                $alipay = true;
//            } else {
//                $alipay = false;
//            }
            if (in_array('enterprise', $authCode)) {
                $enterprise = true;
            } else {
                $enterprise = false;
            }
        } else {
            $realName = false;
            $bank = false;
            $alipay = false;
            $enterprise = false;
        }
        $authUser = array(
            'realname' => $realName,
            'bank' => $bank,
            'alipay' => $alipay,
            'enterprise' => $enterprise

    );
        //关注和粉丝数量
        $focus_num = UserFocusModel::where('uid', $this->user['id'])->count();
        $fans_num = UserFocusModel::where('focus_uid', $this->user['id'])->count();

        //关注的人
//        $focus_data = UserFocusModel::select('user_focus.*', 'ud.avatar', 'us.name as nickname')
//            ->where('user_focus.uid', $this->user['id'])
//            ->join('user_detail as ud', 'user_focus.focus_uid', '=', 'ud.uid')
//            ->leftjoin('users as us','us.id','=','user_focus.focus_uid')
//            ->get()->toArray();

        //我接受的任务id
        $my_task = WorkModel::where('uid', $this->user['id'])->where('status','>',0)->where('status','<',4)->lists('task_id')->toArray();

        $my_task_id = array_flatten($my_task);

        $my_task_id = array_unique($my_task_id);
        $my_task_data = TaskModel::select('task.*', 'ud.avatar', 'us.name as nickname', 'tc.name as category_name')
            ->whereIn('task.id', $my_task_id)
//            ->where('task.status',7)
            ->join('user_detail as ud', 'ud.uid', '=', 'task.uid')
            ->leftjoin('cate as tc', 'tc.id', '=', 'task.cate_id')
            ->leftjoin('users as us','us.id','=','task.uid')
            ->orderBy('task.created_at','desc')->get()->toArray();
        $status = [
            'status' => [
                0 => '暂不发布',
                1 => '已经发布',
                2 => '赏金托管',
                3 => '业主发布',
                4 => '投稿中',
                5 => '业主选稿',
                6 => '任务公示',
                7 => '工程进行中',
                8 => '双方互评',
                9 => '成功完成',
                10 => '任务失败',
                11=>'维权中'
            ]
        ];

        $first_arr = $task_arr = [];

//        dd($my_task_data);
        foreach($my_task_data as $key => &$value){
            $work_offer_detail = WorkOfferModel::where('task_id',$value['id'])->where('to_uid',$this->user['id'])->orderBy('sn','asc')->get()->toArray();
            $work_detail = WorkModel::where('task_id',$value['id'])->where('uid',$this->user['id'])->first();




            // 1是初始提交  ， 3是业主驳回并重新提交
           // dd($work_offer_detail);
            $value['work_offer_status'] = 0;
            if($value['status'] < 8){
                $value['status_text'] = '进行中';
            }elseif($value['status'] == 9){
                $value['status_text'] = '已完结';
            }elseif($value['status'] == 8){
                $value['status_text'] = '评价中';
            }else{
                $value['status_text'] = '失败';
            }
            $value['is_first_upload'] = false;
            $value['is_deep_upload'] = false;
            $first_arr[$value['id']] = [];
            $task_arr[$value['id']] = [];
            foreach($work_offer_detail as $key2 => $value2){
                if($value2['sn'] == 1 && $work_offer_detail[$key2-1]['status'] == 4){
                    if($value2['status'] == 0){
                        if($value2['upload_status'] == 0){
                            $value['status_text'] = '上传初步设计图纸';
                            $value['is_first_upload'] = true;
                            break;
                        }
                    }

                    if( $value2['status'] == 3 ){
                        if($value2['upload_status'] == 0){
                            $value['status_text'] = '初步设计驳回待处理';
                            $value['is_first_upload'] = true;


                            $is_have_upload = WorkAttachmentModel::select('attachment.name','attachment.url','attachment.desc')->where('work_attachment.task_id',$value['id'])
                                ->where('work_attachment.type',1)
                                ->where('work_attachment.img_type',1)
                                ->where('work_attachment.work_id',$work_detail->id)
                                ->leftJoin('attachment','work_attachment.attachment_id','=','attachment.id')->get();



                            foreach($is_have_upload as $k1 => &$v1){
                                $v1['url'] = url($v1['url']);
                            }
                            $first_arr[$value['id']] = $is_have_upload;
                            break;
                        }
                    }
                }

                if($value2['sn'] == 2 && $work_offer_detail[$key2-1]['status'] == 4 ){
                    if( $value2['status'] == 0 ){
                        if($value2['upload_status'] == 0){
                            $value['status_text'] = '上传深化设计图纸';
                            $value['is_deep_upload'] = true;
                            break;
                        }
                    }

                    if( $value2['status'] == 3 ){
                        if($value2['upload_status'] == 0){
                            $value['is_deep_upload'] = true;
                            $value['status_text'] = '深化设计驳回待处理';

                            $is_have_upload_deep = WorkAttachmentModel::select('attachment.name','attachment.url','attachment.desc')->where('work_attachment.task_id',$value['id'])
                                ->where('work_attachment.type',1)
                                ->where('img_type',2)
                                ->where('work_attachment.work_id',$work_detail->id)
                                ->leftJoin('attachment','work_attachment.attachment_id','=','attachment.id')->get();

                            foreach($is_have_upload_deep as $k2 => &$v2){
                                $v2['url'] = url($v2['url']);
                            }
                            $task_arr[$value['id']] = $is_have_upload_deep;
                            break;
                        }
                    }
                }
            }
        }

        $view = [
            'user_data' => $user_data,
            'user_auth' => $user_auth,
            'auth_user' => $authUser,
            'focus_num' => $focus_num,
            'fans_num' => $fans_num,
            'my_task' => $my_task_data,
            'domain' => $domain,
            'task_arr'=>json_encode($task_arr),
            'first_arr'=>json_encode($first_arr),
        ];
//        dd(json_encode($task_arr,JSON_UNESCAPED_SLASHES));

        $this->theme->set('TYPE',1);
        return $this->theme->scope('user.index', $view)->render();
    }



    //设计师提交初步设计图纸
    public function designerSubImg(Request $request){
        $data['plane_img']          = $request->file('plane_img');
        $data['effect_img']         = $request->file('effect_img');
        $data['hydroelectric_img']  = $request->file('hydroelectric_img');
        $data['construct_img']      = $request->file('construct_img');
        $data['task_id']            = $request->get('task_id');

        foreach($data as $key => $value){
            if($key=='task_id')continue;
            if(empty($value)){
                echo '部分图纸未上传或获取不到任务id，<a href="#" onclick="window.history.go(-1);">点击</a>返回上传对应的设计图纸';exit;
            }
        }

        $imgArr = [];
        foreach($data as $key => $value){
            if($key == 'task_id'){
                continue;
            }else{
                $result = \FileClass::uploadFile($value, 'task', array('jpg', 'gif', 'jpeg', 'bmp', 'png'),true);
                $result = json_decode($result, true);
                if ($result['code'] == 200) {
                    $name = explode('.',$result['data']['name']);
                    $result['data']['name'] = $key;
                    $imgArr[] = $result['data'];
                }else{
                    echo '图纸上传失败，<a href="#" onclick="window.history.go(-1);">点击</a>返回并联系管理员';exit;
                }
            }
        }

        $work_id = WorkModel::where('task_id',$data['task_id'])->where('status','>',0)->where('uid',$this->user['id'])->first();

        foreach($imgArr as $key => $value){
            $value['user_id'] = $this->user['id'];
            $value['status'] = 1;
            $value['disk'] = 'task';
            $value['created_at'] = date('Y-m-d H:i:s',time());
            $id = AttachmentModel::create($value);
            $workAttachmentArr['attachment_id'] = $id->id;
            $workAttachmentArr['task_id'] = $data['task_id'];
            $workAttachmentArr['work_id'] = $work_id->id;
            $workAttachmentArr['type'] = 1;
            $workAttachmentArr['created_at'] = date('Y-m-d H:i:s',time());
            $workAttachmentArr['img_type'] = 1;
            $ret = WorkAttachmentModel::create($workAttachmentArr);
        }

        $updateRet = WorkOfferModel::where('task_id',$data['task_id'])->where('work_id',$work_id->id)->where('sn',1)->update(['upload_status'=>1,'status'=>1]);
        if($updateRet){
            return redirect('user/index')->with(['message' => '提交成功']);
        }else{
            return redirect('user/index')->with(['message' => '提交失败']);
        }

//        return response()->json( ['full_path'=>url($result['data']['url']) , 'path'=>$result['data']['url']]);
    }


    //设计师提交深化设计图纸
    public function designerSubDeepImg(Request $request){
        $data['deep_1']  = $request->file('deep_1');
        $data['deep_2']  = $request->file('deep_2');
        $data['deep_3']  = $request->file('deep_3');
        $data['deep_4']  = $request->file('deep_4');
        $data['deep_5']  = $request->file('deep_5');
        $data['deep_6']  = $request->file('deep_6');
        $data['deep_7']  = $request->file('deep_7');
        $data['deep_8']  = $request->file('deep_8');
        $data['deep_9']  = $request->file('deep_9');
        $data['deep_10'] = $request->get('content');
        $data['task_id'] = $request->get('deep_task_id');
        $step_status     = $request->get('deep_step_status');
        $work_id = WorkModel::where('task_id',$data['task_id'])->where('status','>',0)->where('uid',$this->user['id'])->first();

        if($step_status == 2){
            $handleAttachmentArr = [];
            $attachment = WorkAttachmentModel::select('attachment.*')->where('work_attachment.task_id',$data['task_id'])->where('work_attachment.work_id',$work_id->id)
                                                ->where('work_attachment.img_type',2)->where('work_attachment.type',1)
                                                ->leftJoin('attachment','attachment.id','=','work_attachment.attachment_id')
                                                ->where('attachment.status',1)->get();
            foreach($attachment->toArray() as $key => $value){
                $handleAttachmentArr[$value['name']] = $value;
            }

            foreach($data as $key => $value){
                if($key=='task_id')continue;
                if(empty($value))continue;
                if(isset($handleAttachmentArr[$key])){
                    if($key == 'deep_10'){
                        AttachmentModel::where('id',$handleAttachmentArr[$key]['id'])->update(['desc'=>$value]);
                    }else{
                        $result = \FileClass::uploadFile($value, 'task', array('jpg', 'gif', 'jpeg', 'bmp', 'png'),true);
                        $result = json_decode($result, true);
                        if ($result['code'] == 200) {
                            AttachmentModel::where('id',$handleAttachmentArr[$key]['id'])->update(['url'=>$result['data']['url']]);
                        }else{

                            echo '图纸上传失败，<a href="#" onclick="window.history.go(-1);">点击</a>返回并联系管理员';exit;
                        }
                    }
                }else{
                    if($key == 'deep_10'){
                        $imgArr = ['url'=>'','name'=>'deep_10','type'=>'txt','size'=>0,'disk'=>'public','desc'=>$value];
                    }else{
                        $result = \FileClass::uploadFile($value, 'task', array('jpg', 'gif', 'jpeg', 'bmp', 'png'),true);
                        $result = json_decode($result, true);
                        if ($result['code'] == 200) {
                            $name = explode('.',$result['data']['name']);
                            $result['data']['name'] = $key;
                            $imgArr = $result['data'];
                        }else{

                            echo '图纸上传失败，<a href="#" onclick="window.history.go(-1);">点击</a>返回并联系管理员';exit;
                        }
                    }

                    $imgArr['user_id'] = $this->user['id'];
                    $imgArr['status'] = 1;
                    $imgArr['disk'] = 'task';
                    $imgArr['created_at'] = date('Y-m-d H:i:s',time());
                    $imgArr['desc'] = isset($imgArr['desc'])?$imgArr['desc']:'';
                    $id = AttachmentModel::create($imgArr);

                    $workAttachmentArr['attachment_id'] = $id->id;
                    $workAttachmentArr['task_id'] = $data['task_id'];
                    $workAttachmentArr['work_id'] = $work_id->id;
                    $workAttachmentArr['type'] = 1;
                    $workAttachmentArr['img_type'] = 2;
                    $workAttachmentArr['created_at'] = date('Y-m-d H:i:s',time());
                    $ret = WorkAttachmentModel::create($workAttachmentArr);
                }

            }
        }else{
            $i = 0;
            foreach($data as $key => $value){
                if($key=='task_id'){
                    if(empty($value)){
                        echo '对应的任务状态不正常';exit;
                    }
                }
                if($key == 'deep_10'){
                    continue;
                }
                if(!empty($value)){
                    $i += 1;
                }
            }
            if($i < 1){
                echo '至少上传一份图纸，<a href="#" onclick="window.history.go(-1);">点击</a>返回';exit;
            }

            $imgArr = [];

            foreach($data as $key => $value){
                if($key == 'task_id'){
                    continue;
                }elseif($key == 'deep_10'){
                    if(empty($value))continue;
                    $imgArr[] = ['url'=>'','name'=>'deep_10','type'=>'txt','size'=>0,'disk'=>'public','desc'=>$value];
                }elseif(!empty($value)){
                    $result = \FileClass::uploadFile($value, 'task', array('jpg', 'gif', 'jpeg', 'bmp', 'png'),true);
                    $result = json_decode($result, true);
                    if ($result['code'] == 200) {
                        $name = explode('.',$result['data']['name']);
                        $result['data']['name'] = $key;
                        $imgArr[] = $result['data'];
                    }else{

                        echo '图纸上传失败，<a href="#" onclick="window.history.go(-1);">点击</a>返回并联系管理员';exit;
                    }
                }
            }

            foreach($imgArr as $key => $value){
                $value['user_id'] = $this->user['id'];
                $value['status'] = 1;
                $value['disk'] = 'task';
                $value['created_at'] = date('Y-m-d H:i:s',time());
                $value['desc'] = isset($value['desc'])?$value['desc']:'';

                $id = AttachmentModel::create($value);
//            echo 1;exit;
                $workAttachmentArr['attachment_id'] = $id->id;
                $workAttachmentArr['task_id'] = $data['task_id'];
                $workAttachmentArr['work_id'] = $work_id->id;
                $workAttachmentArr['type'] = 1;
                $workAttachmentArr['img_type'] = 2;
                $workAttachmentArr['created_at'] = date('Y-m-d H:i:s',time());
                $ret = WorkAttachmentModel::create($workAttachmentArr);
            }
        }
        $updateRet = WorkOfferModel::where('task_id',$data['task_id'])->where('work_id',$work_id->id)->where('sn',2)->update(['upload_status'=>1]);
        if($updateRet){
            return redirect('user/index')->with(['message' => '提交成功']);
        }else{
            return redirect('user/index')->with(['message' => '提交失败']);
        }

    }



    /**
     * 用户详细信息修改页面
     * @return mixed
     */
    public function info()
    {
        $this->initTheme('userinfo');//主题初始化
        $this->theme->setTitle('用户中心');
        //创建用户的信息
        $uinfo = UserDetailModel::findByUid($this->user['id']);
        //查询省信息
        $province = DistrictModel::findTree(0);
        //查询城市数据
        if (!is_null($uinfo['province'])) {
            $city = DistrictModel::findTree($uinfo['province']);
        } else {
            $city = DistrictModel::findTree($province[0]['id']);
        }
        //查询地区信息
        if (!is_null($uinfo['city'])) {
            $area = DistrictModel::findTree($uinfo['city']);
        } else {
            $area = DistrictModel::findTree($city[0]['id']);
        }

        $view = array(
            'uinfo' => $uinfo,
            'province' => $province,
            'city' => $city,
            'area' => $area,
            'user' => $this->user,
        );
        return $this->theme->scope('user.info', $view)->render();
    }

    /**
     * 用户信息更新，在第一次的时候创建
     * @param UserInfoRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function infoUpdate(UserInfoRequest $request)
    {
        $data = $request->except(['_token','_url']);

        $result = UserDetailModel::where('uid', $this->user['id'])->update($data);

        if (!$result) {
            return redirect()->back()->with(['error' => '修改失败！']);
        }

        return redirect()->back()->with(['massage' => '修改成功！']);
    }

    /**
     * 用户修改密码页
     * @return mixed
     */
    public function loginPassword()
    {
        $this->initTheme('userinfo');
        $this->theme->setTitle('修改密码');

        $view = [
            'user' => $this->user,
        ];

        return $this->theme->scope('user.userpassword', $view)->render();
    }

    /**
     * 用户修改密码
     * @param PasswordRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function passwordUpdate(PasswordRequest $request)
    {
        //验证用户的密码
        $data = $request->except(['_token','_url']);

        //验证原密码是否正确
        if (!UserModel::checkPassword($this->user['email'], $data['oldpassword'])) {
            return redirect()->back()->with('error', '原始密码错误！');
        }
        $result = UserModel::psChange($data, $this->user);

        if (!$result) {
            return redirect()->back()->with('error' . '密码修改失败！');  //回传错误信息
        }
        Auth::logout();
        return redirect('login')->with(['message' => '修改密码成功，请重新登录']);
    }

    /**
     * 用户修改支付密码
     * @return mixed
     */
    public function payPassword()
    {
        $this->initTheme('userinfo');
        $this->theme->setTitle('修改支付密码');
        UserDetailModel::closeTips();

        $view = [
            'user' => $this->user,
        ];
        return $this->theme->scope('user.paypassword', $view)->render();
    }

    /**
     * 检测发送邮件倒计时时间(修改支付密码)
     */
    public function checkInterVal(){
        $sendTime = Session::get('send_code_time');
        $nowTime = time();
        if(empty($sendTime)){
            return response()->json(['errCode'=>3]);
        }else{
            if($nowTime - $sendTime < 60 ){//时间在0-60
                return response()->json(['errCode'=>1,'interValTime'=>60-($nowTime - $sendTime)]);
            }else{
                return response()->json(['errCode'=>2]);//大于60
            }
        }
    }

    /**
     * 用户修改密码发送邮件
     */
    public function sendEmail(Request $request)
    {
        $email = $request->get('email');
        //验证用户填写邮箱
        if ($email != $this->user['email']) {
            return response()->json(['errCode' => 0, 'errMsg' => '请输入注册时候填写的邮箱！']);
        }
        $result = \MessagesClass::sendCodeEmail($this->user);

        if (!$result) {
            return response()->json(['errCode' => 0, 'errMsg' => $result]);
        } else {
            Session::put('send_code_time', time());
            return response()->json(['errCode' => 1]);
        }
    }

    /**
     * 验证用户输入邮箱是否注册邮箱
     * @param Request $request
     * @return mixed
     */
    public function checkEmail(Request $request)
    {
        $sendTime = Session::get('send_code_time');
        $nowTime = time();
        if ($nowTime - $sendTime < 60) {
            return response()->json(['errCode' => 0, 'errMsg' => '请稍后点击发送验证码！']);
        }
        $email = $request->get('email');
        //验证用户填写邮箱
        if ($email != $this->user['email']) {
            return response()->json(['errCode' => 0, 'errMsg' => '请输入注册时候填写的邮箱！']);
        } else {
            return response()->json(['errCode' => 1]);
        }
    }

    /**
     * 验证用户的验证码跳转修改密码页面
     */
    public function validateCode(Request $request)
    {
        $this->initTheme('userinfo');
        $this->theme->setTitle('修改支付密码');
        //验证验证码
        $code = $request->get('code');
        $email = $request->get('email');
        $session_code = Session::get('payPasswordCode');
        if ($code != $session_code) {
            return redirect()->to('user/payPassword')->withInput(['email' => $email, 'code' => $code])->withErrors(['code' => '验证码错误']);
        }

        return $this->theme->scope('user.paypasswordupdate')->render();
    }

    /**
     * 用户修改支付密码提交
     * @param PasswordRequest $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function payPasswordUpdate(PasswordRequest $request)
    {
        $data = $request->except(['_token','_url']);

        $result = UserModel::payPsUpdate($data, $this->user);

        if (!$result) {
            return redirect()->back()->with('error', '密码修改失败！');  //回传错误信息
        }

        return redirect()->to('user/payPassword')->with('message', '密码修改成功！');
    }

    /**
     * 标签修改页面
     * @return mixed
     */
    public function skill()
    {
        $this->initTheme('userinfo');
        $this->theme->setTitle('标签设置');
        //查询用户原有的标签id
        $tag = UserTagsModel::myTag($this->user['id']);
        $tags = array_flatten($tag);
        //查询所有标签
        $hotTag = TagsModel::findAll();

        $view = array(
            'hotTag' => $hotTag,
            'tags' => $tags,
            'user' => $this->user,
        );
        return $this->theme->scope('user.sign', $view)->render();
    }

    /**
     * 用户设置标签一次性添加
     * @param Request $request
     */
    public function skillSave(Request $request)
    {
        $data = $request->all();

        $tags = explode(',', $data['tags']);
        //查询用户所有的标签id
        $old_tags = UserTagsModel::myTag($this->user['id']);
        $old_tags = array_flatten($old_tags);
        //验证用户更改了标签
        if ((empty($data['tags']) && $data['tags'] != 'change')) {
            return redirect()->back()->withErrors(['tags_name' => '请更新标签后提交！']);
        }

        //判断是在添加标签还是在删除标签
        if (count($tags) > count($old_tags)) {
            //判断用户有多少个标签
            if (count($tags) > 5) {
                return redirect()->back()->withErrors(['tags_name' => '一个用户最多只能有五个标签']);
            }
            $dif_tags = array_diff($tags, $old_tags);
            $result = UserTagsModel::insert($dif_tags, $this->user['id']);
            if (!$result)
                return redirect()->back()->with('error', '更新标签错误');  //回传错误信息
        } else if (count($tags) < count($old_tags)) {
            $dif_tags = array_diff($old_tags, $tags);
            $result = UserTagsModel::tagDelete($dif_tags, $this->user['id']);
            if (!$result)
                return redirect()->back()->with('error', '更新标签错误');  //回传错误信息
        } else if (count($tags) == count($old_tags)) {
            //增加新标签
            $dif_tags = array_diff($tags, $old_tags);
            if(empty($dif_tags))
            {
                return redirect()->back()->withErrors(['tags_name' => '请更新标签后提交！']);
            }
            $result2 = UserTagsModel::insert($dif_tags, $this->user['id']);
            //删除老标签
            $dif_tags = array_diff($old_tags, $tags);
            $result = UserTagsModel::tagDelete($dif_tags, $this->user['id']);
            if (!$result && !$result2)
                return redirect()->back()->with('error', '更新标签错误');  //回传错误信息
        }

        return redirect()->back()->with('massage', '标签更新成功');
    }

    /**
     * 用户头像设置页
     * @return mixed
     */
    public function userAvatar()
    {
        $theme = Theme::uses('default')->layout('usercenter');
        $theme->setTitle('头像设置');
        //查询用户的头像信息
        $user_detail = UserDetailModel::findByUid($this->user['id']);

        $view = [
            'avatar' => $user_detail['avatar'],
            'id' => $this->user['id']
        ];

        return $this->theme->scope('user.avatar', $view)->render();
    }

    /**
     * ajax头像裁剪
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function avatarEdit(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $data = $data['data'];
        //查询用户头像路径
        $user_head = UserDetailModel::findByUid($this->user['id']);
        $path = $user_head['avatar'] . md5($this->user['id'] . 'large') . '.jpg';
        $img = Image::open($path);
        $img->crop(intval($data['x']), intval($data['y']), intval($data['width']), intval($data['height']));
        $result = $img->save($path);
        $domain = \CommonClass::getDomain();
        $json = [
            'status' => 1,
            'message' => '成功保存',
            'url' => $path,
            'path' => $domain . '\\' . $path
        ];
        //生成三张图片
        $result2 = \FileClass::headHandle($json, $this->user['id']);

        if (!$result || !$result2) {
            array_replace($json, ['status' => 0, 'message' => '编辑失败']);
        }
        return response()->json($json);
    }

    /**
     * ajax头像上传
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxAvatar(Request $request)
    {
        $file = $request->file('avatar');
        //处理上传图片
        $result = \FileClass::uploadFile($file, $path = 'user');
        $result = json_decode($result, true);

        //判断文件是否上传
        if ($result['code'] != 200) {
            return response()->json(['code' => 0, 'message' => $result['message']]);
        }
        //产生一条新纪录
        $attachment_data = array_add($result['data'], 'status', 1);
        $attachment_data['created_at'] = date('Y-m-d H:i:s', time());
        //将记录写入到attchement表中
        $result2 = AttachmentModel::create($attachment_data);
        if (!$result2)
            return response()->json(['code' => 0, 'message' => $result['message']]);

        //删除原来的头像
        $avatar = \CommonClass::getAvatar($this->user['id']);
        if (file_exists($avatar)) {
            $file_delete = unlink($avatar);
            if ($file_delete) {
                AttachmentModel::where('url', $avatar)->delete();
            } else {
                AttachmentModel::where('url', $avatar)->update(['status' => 0]);
            }
        }
        //修改用户头像
        $data = [
            'avatar' => $result['data']['url']
        ];
        $result3 = UserDetailModel::updateData($data, $this->user['id']);
        if (!$result3) {
            return \CommonClass::formatResponse('文件上传失败');
        }

        return response()->json($result);
    }

    /**
     * ajax获取城市、地区数据
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCity(Request $request)
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
    public function ajaxArea(Request $request)
    {
        $id = intval($request->get('id'));
        if (!$id) {
            return response()->json(['errMsg' => '参数错误！']);
        }
        $area = DistrictModel::findTree($id);
        return response()->json($area);
    }





}
