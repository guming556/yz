<?php

namespace App\Modules\User\Model;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\DB;
use App\Modules\User\Model\RealnameAuthModel;
use App\Modules\User\Model\BankAuthModel;

class UserModel extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    
    protected $table = 'users';

    protected $primaryKey = 'id';

    
    protected $fillable = [
        'name', 'email', 'email_status', 'password', 'alternate_password', 'salt', 'status', 'overdue_date', 'validation_code', 'expire_date',
        'reset_password_code', 'remember_token','source','device_token','send_num','user_type','invite_uid','sort_id'
    ];

    
    protected $hidden = ['password', 'remember_token'];


    
    static function encryptPassword($password, $sign = '')
    {
        return md5(md5($password . $sign));
    }

    
    static function checkPassword($username, $password, $user_type = 'email')
    {   
        if($user_type === 'email'){
            $user = UserModel::where('name', $username)->orWhere('email', $username)->first();
        }else{
            // $user = UserModel::where('name', $username)->where('user_type', $user_type)->first();
            $user = UserModel::where('name', $username)->first();
            // var_dump($user->toArray());exit;
        }
        
        if ($user) {
            $password = self::encryptPassword($password, $user->salt);

            if ($user->password === $password) {
                return true;
            }
        }
        return false;
    }
    
    static function checkPayPassword($email, $password)
    {
        $user = UserModel::where('email', $email)->first();
        if ($user) {
            $password = self::encryptPassword($password, $user->salt);
            if ($user->alternate_password == $password) {
                return true;
            }
        }
        return false;
    }
    
    static function psChange($data, $userInfo)
    {
        $user = new UserModel;
        $password = UserModel::encryptPassword($data['password'], $userInfo['salt']);
        $result = $user->where(['id'=>$userInfo['id']])->update(['password'=>$password]);

        return $result;
    }

    
    static function payPsUpdate($data, $userInfo)
    {
        $user = new UserModel;
        $password = UserModel::encryptPassword($data['password'], $userInfo['salt']);
        $result = $user->where(['id'=>$userInfo['id']])->update(['alternate_password'=>$password]);

        return $result;
    }

    //这里是邮箱激活账户
    static function createUser(array $data)
    {
        
        $salt = \CommonClass::random(4);
        $validationCode = \CommonClass::random(6);
        $date = date('Y-m-d H:i:s');
        $now = time();
        $userArr = array(
            'name' => $data['username'],
            'email' => $data['email'],
            'password' => UserModel::encryptPassword($data['password'], $salt),
            'alternate_password' => UserModel::encryptPassword($data['password'], $salt),
            'salt' => $salt,
            'last_login_time' => $date,
            'overdue_date' => date('Y-m-d H:i:s', $now + 60*60*3),
            'validation_code' => $validationCode,
            'created_at' => $date,
            'updated_at' => $date
        );
        $objUser = new UserModel();
        
        $status = $objUser->initUser($userArr);

        if ($status){
            $emailSendStatus = \MessagesClass::sendActiveEmail($data['email']);
            if (!$emailSendStatus){
                $status = false;
            }
            return $status;
        }
    }


//  手机注册
    static function createUserMobile(array $data, $is_worker = false, $realnameInfo = array()) {

        $salt           = \CommonClass::random(4);
        $validationCode = \CommonClass::random(6);
        $date           = date('Y-m-d H:i:s');
        $now            = time();
        $userArr        = array(
            'name' => $data['username'],
            'email' => time() . $salt . '@qq.com',   //手机注册随机分配邮箱
            'user_type' => $data['user_type'],
            'password' => UserModel::encryptPassword($data['password'], $salt),
            'alternate_password' => UserModel::encryptPassword($data['password'], $salt),
            'salt' => $salt,
            'last_login_time' => $date,
            'overdue_date' => date('Y-m-d H:i:s', $now + 60 * 60 * 3),
            'validation_code' => $validationCode,
            'created_at' => $date,
            'updated_at' => $date,
            'status' => ($data['user_type'] == '1') ? 1 : 0,
            'email_status' => 2,
            'score' => isset($data['score']) ? $data['score'] : 0,
            'source' => 2,
            'card_number' => isset($data['card_number']) ? $data['card_number'] : '',
            'deposit_name' => isset($data['deposit_name']) ? $data['deposit_name'] : '',
            'introduce' => isset($data['introduce']) ? $data['introduce'] : '',
            'tag' => !empty($data['tag']) ? serialize(explode('，', $data['tag'])) : '',
            'sign' => !empty($data['sign']) ? serialize(explode('，', $data['sign'])) : '',
            'bank_account' => isset($data['bank_account']) ? $data['bank_account'] : '',
            'demo' => !empty($data['demo']) ? serialize(explode('，', $data['demo'])) : '',
            'user_age' => isset($data['user_age']) ? $data['user_age'] : '',
            'native_place' => isset($data['native_place']) ? $data['native_place'] : '',
            'avatar' => isset($data['avatar']) ? $data['avatar'] : '',
            'cost_of_design' => isset($data['cost_of_design']) ? $data['cost_of_design'] : '',
            'workStar' => isset($data['workStar']) ? $data['workStar'] : '',
            'work_type' => isset($data['work_type']) ? $data['work_type'] : 0,
            'province' => isset($data['province']) ? $data['province'] : '',
            'city' => isset($data['city']) ? $data['city'] : '',
            'nickname' => isset($data['nickname']) ? $data['nickname'] : '',
            'invite_uid' => isset($data['invite_uid']) ? $data['invite_uid'] : '',
            'serve_area_id' => isset($data['serve_area_id']) ? $data['serve_area_id'] : 0,
        );

        $realnameInfo['username'] = $data['username'];
        $objUser                  = new UserModel();

        $status = $objUser->initUser($userArr, $is_worker, $realnameInfo);
        return $status;
    }



    // 处理提交信息
    public function initUser(array $data, $is_worker = false, array $realnameInfo) {

        $status = DB::transaction(function() use ($data , $is_worker , $realnameInfo){

        if (isset($data['avatar'])) {
            $avatar = $data['avatar'];
            unset($data['avatar']);
        }
        if (isset($data['cost_of_design'])) {
            $cost_of_design = $data['cost_of_design'];
            unset($data['cost_of_design']);
        }
        if (isset($data['workStar'])) {
            $workStar = $data['workStar'];
            unset($data['workStar']);
        }
        if (isset($data['work_type'])) {
            $work_type = $data['work_type'];
            unset($data['work_type']);
        }
        if (isset($data['province'])) {
            $province = $data['province'];
            unset($data['province']);
        }
        if (isset($data['city'])) {
            $city = $data['city'];
            unset($data['city']);
        }
        if (isset($data['score'])) {
            $score = $data['score'];
            unset($data['score']);
        }
        if (isset($data['nickname'])) {
            $nickname = $data['nickname'];
            unset($data['nickname']);
        }
        //年龄
        if (isset($realnameInfo['user_age'])) {
            $user_age = $realnameInfo['user_age'];
            unset($realnameInfo['user_age']);
        }
        //籍贯
        if (isset($realnameInfo['native_place'])) {
            $native_place = $realnameInfo['native_place'];
            unset($realnameInfo['native_place']);
        }

        //TODO Create方法默认就是只接收model里面规定的字段,为什么还要转来转去,需要重构
        $data['uid'] = UserModel::create($data)->id;

        $data['experience'] = !empty($realnameInfo['experience']) ? $realnameInfo['experience'] : '0';
        $data['nickname']   = $realnameInfo['username'];

        if (isset($avatar)) {
            $data['avatar'] = $avatar;
        }
        if (isset($cost_of_design)) {
            $data['cost_of_design'] = $cost_of_design;
        }
        if (isset($workStar)) {
            $data['star'] = $workStar;
        }
        if (isset($work_type)) {
            $data['work_type'] = $work_type;
        }
        //年龄
        if (isset($user_age)) {
            $data['user_age'] = $user_age;
        }
        //籍贯
        if (isset($native_place)) {
            $data['native_place'] = $native_place;
        }
        if (isset($province)) {
            $data['province'] = $province;
        }
        if (isset($city)) {
            $data['city'] = $city;
        }
        if (isset($score)) {
            $data['score'] = $score;
        }
        if (isset($nickname)) {
            $data['nickname'] = $nickname;
        }
        $data['address'] = empty($realnameInfo['address']) ? '' : $realnameInfo['address'];
        $data['lat']     = empty($realnameInfo['lat']) ? '' : $realnameInfo['lat'];
        $data['lng']     = empty($realnameInfo['lng']) ? '' : $realnameInfo['lng'];
        $data['city']    = isset($data['city']) ? $data['city'] : '';

        UserDetailModel::create($data);

        if ($is_worker) {
            //  TODO 如果是工作端的注册，就顺便把认证信息填入
            $realnameInfo['uid'] = $data['uid'];
            // 记录一下提交记录
            $authRecordInfo['uid']       = $realnameInfo['uid'];
            $authRecordInfo['username']  = $data['name'];
            $authRecordInfo['auth_code'] = 'realname';

            RealnameAuthModel::createRealnameAuth($realnameInfo, $authRecordInfo, $data['user_type']);

            $bank = [];

            if (isset($data['bank_account']) && !empty($data['bank_account']) && isset($data['deposit_name']) && !empty($data['deposit_name'])) {
                $bank['bank_account'] = $data['bank_account'];
                $bank['deposit_name'] = $data['deposit_name'];
            }

            if (!empty($bank)) {
                $bank['status'] = 2;
                $ret            = BankAuthModel::where('uid', $data['uid'])->first();
                if (!empty($ret)) {
                    BankAuthModel::where('uid', $data['uid'])->where('id', $ret->id)->update($bank);
                } else {
                    $bank['uid']        = $data['uid'];
                    $bank['created_at'] = date('Y-m-d H:i:s');
                    $authRecordInfo     = [
                        'uid' => $bank['uid'],
                        'auth_code' => 'bank',
                        'status' => 1,
                        'auth_time' => date('Y-m-d H:i:s')
                    ];
                    BankAuthModel::createBankAuth($bank, $authRecordInfo);
                }
            }


        }
        });

        return is_null($status) ? true : $status;
    }

    
    static function getUserName($id)
    {
        $userInfo = UserModel::where('id',$id)->first();
        return $userInfo->name;
    }

    
    public function isAuth($uid)
    {
        $auth = AuthRecordModel::where('uid',$uid)->where('status',4)->first();
        $bankAuth = BankAuthModel::where('uid',$uid)->where('status',4)->first();
        $aliAuth = AlipayAuthModel::where('uid',$uid)->where('status',4)->first();
        $data['auth'] = is_null($auth)?true:false;
        $data['bankAuth'] = is_null($bankAuth)?true:false;
        $data['aliAuth'] = is_null($aliAuth)?true:false;

        return $data;
    }

    
    static function editUser($data)
    {
        foreach ($data as $k => $v) {
            if (empty($v)) unset($data[$k]);
        }
        unset($data['serve_province']);
        $status = DB::transaction(function () use ($data){
            $update_data = array();

            if (!empty($data['password'])) {
                $update_data['salt']     = $data['salt'];
                $update_data['password'] = $data['password'];
                unset($data['salt'],$data['password']);
            }
            if (!empty($data['email'])) {
                $update_data['email'] = $data['email'];
            }
            if (isset($data['serve_area'])) {
                $authUpdate['serve_area'] = $data['serve_area'];
                unset($data['serve_area']);
            }
            if (isset($data['card_number'])) {
                $authUpdate['card_number'] = $data['card_number'];
                unset($data['card_number']);
            }
            $bank = [];
            if (isset($data['bank_account']) && !empty($data['bank_account']) && isset($data['deposit_name']) && !empty($data['deposit_name'])) {
                $bank['bank_account'] = $data['bank_account'];
                $bank['deposit_name'] = $data['deposit_name'];
                unset($data['bank_account'], $data['deposit_name']);
            }
            
            if (!empty($update_data)) UserModel::where('id', $data['uid'])->update($update_data);
	    
            UserDetailModel::where('uid', $data['uid'])->update($data);
            $authUpdate = [];
            if (!empty($authUpdate)) {
                RealnameAuthModel::where('uid', $data['uid'])->where('status', 1)->update($authUpdate);
            }
            if (!empty($bank)) {
                $bank['status'] = 2;
                $ret            = BankAuthModel::where('uid', $data['uid'])->first();
                if (!empty($ret)) {
                    BankAuthModel::where('uid', $data['uid'])->where('id', $ret->id)->update($bank);
                } else {
                    $bank['uid']    = $data['uid'];
                    $authRecordInfo = [
                        'uid' => $bank['uid'],
                        'auth_code' => 'bank',
                        'status' => 1,
                        'auth_time' => date('Y-m-d H:i:s')
                    ];
                    BankAuthModel::createBankAuth($bank, $authRecordInfo);
                }
            }
        });

        return is_null($status) ? true : false;
    }

    
    static function addUser($data)
    {
        $status = DB::transaction(function () use ($data) {
            $users_create = UserModel::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'salt' => $data['salt'],
                'status' => 1,
            ]);
            $data['uid']  = $users_create->id;
            UserDetailModel::create($data);
        });
        return is_null($status) ? true : false;
    }

}
