<?php

/**
 * Global helpers file with misc functions
 **/

if (!function_exists('app_name')) {
    /**
     * Helper to grab the application name
     *
     * @return mixed
     */
    function app_name() {
        return config('app.name');
    }
}

if (!function_exists('access')) {
    /**
     * Access (lol) the Access:: facade as a simple function
     */
    function access() {
        return app('access');
    }
}

if (!function_exists('javascript')) {
    /**
     * Access the javascript helper
     */
    function javascript() {
        return app('JavaScript');
    }
}

if (!function_exists('gravatar')) {
    /**
     * Access the gravatar helper
     */
    function gravatar() {
        return app('gravatar');
    }
}

/**
 *  * isUsername函数:检测是否符合用户名格式
 *  * $Argv是要检测的用户名参数
 *  * $RegExp是要进行检测的正则语句
 *  * 返回值:符合用户名格式返回用户名,不是返回false
 *  */
function isUsername($Argv) {
    $RegExp = '/^[a-zA-Z0-9_]{3,16}$/'; //由大小写字母跟数字组成并且长度在3-16字符直接
    return preg_match($RegExp, $Argv) ? $Argv : false;
}

function isLoginUsername($Argv) {
    $RegExp = '/^[a-zA-Z\x{4e00}-\x{9fa5}]{2,20}$/u';
    return preg_match($RegExp, $Argv) ? $Argv : false;
}

/**
 * isMail函数:检测是否为正确的邮件格式
 * 返回值:是正确的邮件格式返回邮件,不是返回false
 */
function isMail($Argv) {
    $RegExp = '/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/';
    return preg_match($RegExp, $Argv) ? $Argv : false;
}

/**
 * isSmae函数:检测参数的值是否相同
 * 返回值:相同返回true,不相同返回false
 */
function isSame($ArgvOne, $ArgvTwo, $Force = false) {
    return $Force ? $ArgvOne === $ArgvTwo : $ArgvOne == $ArgvTwo;
}

/**
 * isQQ函数:检测参数的值是否符合QQ号码的格式
 * 返回值:是正确的QQ号码返回QQ号码,不是返回false
 */
function isQQ($Argv) {
    $RegExp = '/^[1-9][0-9]{5,11}$/';
    return preg_match($RegExp, $Argv) ? $Argv : false;
}

/**
 * 44. * isMobile函数:检测参数的值是否为正确的中国手机号码格式
 * 45. * 返回值:是正确的手机号码返回手机号码,不是返回false
 * 46. */
//function isMobile($Argv) {
//    $RegExp = '/^(?:13|15|18)[0-9]{9}$/';
//    return preg_match($RegExp, $Argv) ? $Argv : false;
//}


function isMobile($mobile) {
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 62. * isNickname函数:检测参数的值是否为正确的昵称格式(Beta)
 * 63. * 返回值:是正确的昵称格式返回昵称格式,不是返回false
 * 64. */
function isNickname($Argv) {
    $RegExp = '/^\s*$|^c:\\con\\con$|[%,\*\"\s\t\<\>\&\'\(\)]|\xA1\xA1|\xAC\xA3|^Guest|^\xD3\xCE\xBF\xCD|\xB9\x43\xAB\xC8/is'; //Copy From DZ
    return preg_match($RegExp, $Argv) ? $Argv : false;
}

/**
 * isChinese函数:检测参数是否为中文
 * 返回值:是返回参数,不是返回false
 */
function isChinese($Argv, $Encoding = 'utf8') {
    $RegExp = $Encoding == 'utf8' ? '/^[\x{4e00}-\x{9fa5}]+$/u' : '/^([\x80-\xFF][\x80-\xFF])+$/';
    return preg_match($RegExp, $Argv) ? $Argv : False;
}

function isIp($Argv) {
    $RegExp = '/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/';
    return preg_match($RegExp, $Argv) ? $Argv : False;
}

/**
 * 检测是否是第三方支付平台账号（邮箱或手机）
 */
function isPayacc($Argv) {
    $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
    if (strpos($Argv, '@') || preg_match($pattern, $Argv)) {
        return true;
    }
    return false;
}

/**
 * 对邮箱或手机进行标星
 */
function hideStar($str) {
    if (strpos($str, '@')) {
        $email_array = explode("@", $str);
        $prevfix     = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3);
        $count       = 0;
        $str         = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count);
        $rs          = $prevfix . $str;
    } else {
        $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
        if (preg_match($pattern, $str)) {
            $rs = preg_replace($pattern, '$1****$2', $str);
        } else {
            $rs = substr($str, 0, 3) . "***" . substr($str, -3);
        }
    }
    return $rs;
}

function build_order_no($prefix = 'O') {
    return $prefix . date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 生成商品货号
 * @return string
 */
function createGoodsNo() {
    return 'SD' . time() . rand(10000, 99999);
}

function multi_unique($array) {
    foreach ($array as $k => $v) {
        $new = [];
        foreach ($v as $key => $val) {
            $new[$key] = serialize($val);
        }
        $uniq[$k] = array_unique($new);
    }

    foreach ($uniq as $key => $val) {
        $newArr = [];
        foreach ($val as $j => $c) {
            $newArr[$j] = unserialize($c);
        }

        $newArray[$key] = $newArr;
    }
    return $newArray;
}

if (!function_exists('settings')) {

    function settings($key = null) {
        if (is_null($key)) {
            return app('settingRepository');
        }
        return app('settingRepository')->getSetting($key);
    }
}

/**
 * 格式化字符串
 * @param $text
 * @param null $length
 * @return string
 */
function plainText($text, $length = null) {
    $text = strip_tags($text);

    $length = (int)$length;
    if (($length > 0) && (mb_strlen($text) > $length)) {
        $text = mb_substr($text, 0, $length, 'UTF-8');
        $text .= '...';
    }
    return $text;
}

/**
 * 格式化标准日期形式 1986-5-1 to 1986-05-01
 * @param $date
 */
function plainDate($date) {
    $dateArr = explode('-', $date);
    foreach ($dateArr as $key => $val) {
        if (strlen($val) < 2) {
            $dateArr[$key] = '0' . $val;
        }
    }
    return implode('-', $dateArr);
}

/**
 * 生成会员卡号
 * @return string
 */
function createCardNo() {
    return '10' . time() . rand(10, 999);
}


/**
 * 生成随机字符串
 * @param int $length
 * @return string
 */
function generate_random_string($length = 10) {
    $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


/**
 * 递归创建目录
 *
 * 与 mkdir 不同之处在于支持一次性多级创建, 比如 /dir/sub/dir/
 *
 * @param  string
 * @param  int
 * @return boolean
 */
function make_dir($dir, $permission = 0777) {
    $dir = rtrim($dir, '/') . '/';

    if (is_dir($dir)) {
        return TRUE;
    }

    if (!make_dir(dirname($dir), $permission)) {
        return FALSE;
    }

    return @mkdir($dir, $permission);
}

function create_content_image_from_base64($type, $id, $base64_string, $prefix = null) {
    $out_file_path = '/uploads/' . $type . '/' . $id . '/';

    $real_file_path = public_path() . $out_file_path;

    if (!file_exists($real_file_path)) {
        make_dir($real_file_path);
    }

    $file_name = generate_random_string();

    if ($prefix) {
        $file_name = $prefix . '-' . $file_name;
    }

    base64_to_image($base64_string, $real_file_path . $file_name . '.jpg');

    return $out_file_path . $file_name . '.jpg';
}

/**
 * 把64位的图片编码数据转化为实际的图片存储
 * @param $base64_sting
 * @param $output_file
 * @return mixed
 */
function base64_to_image($base64_sting, $output_file) {
    $base64_body = substr(strstr($base64_sting, ','), 1);
    $data        = base64_decode($base64_body);
    file_put_contents($output_file, $data);
    return $output_file;
}

function my_image_resize($src_file, $dst_file, $new_width, $new_height) {
    $new_width  = intval($new_width);
    $new_height = intval($new_width);
    if ($new_width < 1 || $new_height < 1) {
        echo "params width or height error !";
        exit();
    }
    if (!file_exists($src_file)) {
        echo $src_file . " is not exists !";
        exit();
    }
    // 图像类型
    $type         = exif_imagetype($src_file);
    $support_type = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
    if (!in_array($type, $support_type, true)) {
        echo "this type of image does not support! only support jpg , gif or png";
        exit();
    }
    //Load image
    switch ($type) {
        case IMAGETYPE_JPEG :
            $src_img = imagecreatefromjpeg($src_file);
            break;
        case IMAGETYPE_PNG :
            $src_img = imagecreatefrompng($src_file);
            break;
        case IMAGETYPE_GIF :
            $src_img = imagecreatefromgif($src_file);
            break;
        default:
            echo "Load image error!";
            exit();
    }
    $w       = imagesx($src_img);
    $h       = imagesy($src_img);
    $ratio_w = 1.0 * $new_width / $w;
    $ratio_h = 1.0 * $new_height / $h;
    $ratio   = 1.0;
    // 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了）
    if (($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
        if ($ratio_w < $ratio_h) {
            $ratio = $ratio_h; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大
        } else {
            $ratio = $ratio_w;
        }
        // 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求
        $inter_w   = (int)($new_width / $ratio);
        $inter_h   = (int)($new_height / $ratio);
        $inter_img = imagecreatetruecolor($inter_w, $inter_h);
        //var_dump($inter_img);
        imagecopy($inter_img, $src_img, 0, 0, 0, 0, $inter_w, $inter_h);
        // 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像
        // 定义一个新的图像
        $new_img = imagecreatetruecolor($new_width, $new_height);
        //var_dump($new_img);exit();
        imagecopyresampled($new_img, $inter_img, 0, 0, 0, 0, $new_width, $new_height, $inter_w, $inter_h);
        switch ($type) {
            case IMAGETYPE_JPEG :
                imagejpeg($new_img, $dst_file, 100); // 存储图像
                break;
            case IMAGETYPE_PNG :
                imagepng($new_img, $dst_file, 100);
                break;
            case IMAGETYPE_GIF :
                imagegif($new_img, $dst_file, 100);
                break;
            default:
                break;
        }
    } // end if 1
    // 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪
    // =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
    else {
        $ratio = $ratio_h > $ratio_w ? $ratio_h : $ratio_w; //取比例大的那个值
        // 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大
        $inter_w   = (int)($w * $ratio);
        $inter_h   = (int)($h * $ratio);
        $inter_img = imagecreatetruecolor($inter_w, $inter_h);
        //将原图缩放比例后裁剪
        imagecopyresampled($inter_img, $src_img, 0, 0, 0, 0, $inter_w, $inter_h, $w, $h);
        // 定义一个新的图像
        $new_img = imagecreatetruecolor($new_width, $new_height);
        imagecopy($new_img, $inter_img, 0, 0, 0, 0, $new_width, $new_height);
        switch ($type) {
            case IMAGETYPE_JPEG :
                imagejpeg($new_img, $dst_file, 100); // 存储图像
                break;
            case IMAGETYPE_PNG :
                imagepng($new_img, $dst_file, 100);
                break;
            case IMAGETYPE_GIF :
                imagegif($new_img, $dst_file, 100);
                break;
            default:
                break;
        }
    }// if3
}// end function


/**
 * @param $id_arr
 * @return mixed
 * 根据前端提供的id生成报价单
 */
function create_configure_lists($id_arr) {
    if (!is_array($id_arr)) {
        $id_arr = json_decode($id_arr, true);
    }
    ksort($id_arr);
    $data['all_parent_price'] = 0;  //所有项加起来的总价
    foreach ($id_arr as $key => $value) {
//            if(is_numeric($value))continue;
//            $new_key = str_replace('parent_','',$key);
        $parentInfo = DB::table('project_configure_list')
                ->select('name', 'project_type')
                ->where('project_type', $key)
                ->where('pid', 0)
                ->first();
//        var_dump($parentInfo);exit();
        $key                               = 'parent_'.$key;
        $data[$key]['parent_name']         = $parentInfo->name;
        $data[$key]['parent_project_type'] = $parentInfo->project_type;
        $data[$key]['parent_price']        = 0;

        $work_type = DB::table('project_configure_list')
            ->select('work_type')
            ->where('work_type', '>', 0)
            ->distinct()->where('project_type', $parentInfo->project_type)
            ->lists('work_type');

        $data[$key]['need_work_type'] = $work_type;
        foreach ($value as $key2 => $value2) {
            $arr = get_object_vars(
                DB::table('project_configure_list')->select(
                    'id',
                    'name',
                    'desc',
                    'price as unit_price',
                    'project_type',
                    'work_type',
                    'unit'
                )->find($value2['child_id'])
            );

            $arr['user_need_num']   = $value2['child_num'];
            $arr['child_price']     = $arr['user_need_num'] * $arr['unit_price'];
            $data[$key]['childs'][] = $arr;
            $data[$key]['parent_price'] += $arr['child_price'];
        }
        $data['all_parent_price'] += $data[$key]['parent_price'];
    }
    return $data;
}

/**
 * @param $id_arr
 * @return mixed
 * 小订单
 */
function create_configure_lists_small_order($id_arr) {
    if (!is_array($id_arr)) {
        $id_arr = json_decode($id_arr, true);
    }

    ksort($id_arr);
    $all_price = 0;  //所有项加起来的总价

    foreach ($id_arr as $key => $value) {

        $arr = get_object_vars(
            DB::table('project_configure_list')->select(
                'id',
                'name',
                'desc',
                'price as unit_price',
                'project_type',
                'work_type',
                'unit'
            )->find($value['child_id']));


        $arr['user_need_num'] = $value['child_num'];
        $arr['child_price']   = $arr['user_need_num'] * $arr['unit_price'];
        $data['childs'][]     = $arr;
        $all_price += $arr['child_price'];
    }
    $data['all_price'] = $all_price;
    return $data;
}

/**
 * @param $workerStar
 * @return float|int
 * 不同星级不同的百分比
 */
function rate_choose($workerStar){
    switch ($workerStar) {
        case 1:
            $workerStar = 1;
            break;
        case 2:
            $workerStar = 1.1;
            break;
        case 3:
            $workerStar = 1.2;
            break;
        case 4:
            $workerStar = 1.3;
            break;
        case 5:
            $workerStar = 1.4;
            break;
        default:
            $workerStar = 1;
    }
    return $workerStar;
}

/**
 * @param $workerStar
 * @return float|int
 * 1 待审核 2 二审通过 3 三审通过 4 审核不通过 5 已打款
 * 审核状态
 */
function cash_out_status($status){

    switch ($status) {
        case 1:
            $status_info = '待审核';
            break;
        case 2:
            $status_info = '二审通过';
            break;
        case 3:
            $status_info = '三审通过';
            break;
        case 4:
            $status_info = '审核不通过';
            break;
        case 5:
            $status_info = '已打款';
            break;
        default:
            $status_info = '待审核';
    }
    return $status_info;
}

/**
 * @param $workerStar
 * @return float|int
 * work状态
 */
function work_status($status) {
    switch ($status) {
        case 0:
            $status_work = '业主未确认工作者';
            break;
        case 1:
            $status_work = '已确认工作者';
            break;
        case 2:
            $status_work = '任务进行中';
            break;
        case 3:
            $status_work = '成功结束';
            break;
        case 4:
            $status_work = '失败结束';
            break;
        default:
            $status_work = 1;
    }
    return $status_work;
}


/**
 * @param $workerStar
 * @return float|int
 * work_offer状态
 */
function work_offer_status($status) {
    switch ($status) {
        case 0:
            $work_offer_status = '未开始';
            break;
        case 1:
            $work_offer_status = '工作者提交';
            break;
        case 2:
            $work_offer_status = '业主确认';
            break;
        case 3:
            $work_offer_status = '业主退回';
            break;
        case 3.5:
            $work_offer_status = '监理退回';
            break;
        case 4:
            $work_offer_status = '完成';
            break;
        default:
            $work_offer_status = '工作者提交';
    }
    return $work_offer_status;
}


/**
 * @param $img_type
 * @return string
 * 图片类型
 */
function img_type($img_type) {
    switch ($img_type) {
        case 1:
            $img_type_name = '初步设计';
            break;
        case 2:
            $img_type_name = '深化设计';
            break;
        default:
            $img_type_name = '初步设计';
    }
    return $img_type_name;
}

/**
 * @param $img_type
 * @return string
 * 工程类型
 */
function get_project_type($project_type) {
    switch ($project_type) {
        case 1:
            $project_type_name = '拆除工程';
            break;
        case 2:
            $project_type_name = '水电工程';
            break;
        case 3:
            $project_type_name = '防水工程';
            break;
        case 4:
            $project_type_name = '泥工工程';
            break;
        case 5:
            $project_type_name = '木工工程';
            break;
        case 6:
            $project_type_name = '油漆工程';
            break;
        case 7:
            $project_type_name = '其他工程';
            break;
        default:
            $project_type_name = '其他工程';
    }
    return $project_type_name;
}

function work_offer_status_title($status){
    //进程 0未开始 1工作端submit 1.5监理确认 2用户commit 3业主退回 3.5监理退回 4done
    switch ($status){
        case 0:
            $status_title = '未开始';
            break;
        case 1:
            $status_title = '提交';
            break;
        case 1.5:
            $status_title = '监理确认';
            break;
        case 2:
            $status_title = '用户确认';
            break;
        case 3:
            $status_title = '业主退回';
            break;
        case 3.5:
            $status_title = '监理退回';
            break;
        case 4:
            $status_title = '完成';
            break;
        default:
            $status_title = '未开始';
    }
    return $status_title;
}




/**
 * @param $project_type
 * @return int
 * 根据project_type获取上级pid
 */
function get_pid_from_project_type($project_type){
    switch ($project_type) {
        case 1:
            $pid = 27;
            break;
        case 2:
            $pid = 57;
            break;
        case 3:
            $pid = 105;
            break;
        case 4:
            $pid = 108;
            break;
        case 5:
            $pid = 157;
            break;
        case 6:
            $pid = 300;
            break;
        case 7:
            $pid = 1;
            break;
        default:
            $pid = 27;
    }
    return $pid;
}

/**
 * @param $img_type
 * @return string
 */
function img_name($img_name_eng) {
    switch ($img_name_eng) {
        case 'plane_img':
            $img_name_china = '平面图';
            break;
        case 'effect_img':
            $img_name_china = '参考效果图';
            break;
        case 'hydroelectric_img':
            $img_name_china = '参考效果图';
            break;
        case 'construct_img':
            $img_name_china = '参考效果图';
            break;
        case 'deep_1':
            $img_name_china = '平面布局图';
            break;
        case 'deep_2':
            $img_name_china = '原始结构';
            break;
        case 'deep_3':
            $img_name_china = '结构拆改';
            break;
        case 'deep_4':
            $img_name_china = '墙体地位';
            break;
        case 'deep_5':
            $img_name_china = '平面索引';
            break;
        case 'deep_6':
            $img_name_china = '天花布局';
            break;
        case 'deep_7':
            $img_name_china = '墙地面铺贴图';
            break;
        case 'deep_8':
            $img_name_china = '水电布局图';
            break;
        case 'deep_9':
            $img_name_china = '效果图';
            break;
        case 'deep_10':
            $img_name_china = '材料说明';
            break;
        default:
            $img_name_china = '图纸';
    }
    return $img_name_china;
}

/**
 * @param $user_type_id
 * @return string
 * 设计师
 */
function get_user_type_name($user_type_id){
    switch ($user_type_id) {
        case 1:
            $user_type_name = '业主';
            break;
        case 2:
            $user_type_name = '设计师';
            break;
        case 3:
            $user_type_name = '管家';
            break;
        case 4:
            $user_type_name = '监理';
            break;
        case 5:
            $user_type_name = '工人';
            break;
        default:
            $user_type_name = '工人';
    }
    return $user_type_name;
}

/**
 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
 */
function getEaseMobToken() {
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

function get_work_type_name($work_type){
    switch ($work_type) {
        case 1:
            $work_type_name = '管家';
            break;
        case 2:
            $work_type_name = '监理';
            break;
        case 5:
            $work_type_name = '泥水工';
            break;
        case 6:
            $work_type_name = '木工';
            break;
        case 7:
            $work_type_name = '水电工';
            break;
        case 8:
            $work_type_name = '油漆工';
            break;
        case 9:
            $work_type_name = '安装工';
            break;
        case 10:
            $work_type_name = '拆除工';
            break;
        default:
            $work_type_name = '工作者';
    }
    return $work_type_name;
}


function get_work_type_name_other($work_type){
    switch ($work_type) {
        case 1:
            $work_type_name = '管家';
            break;
        case 2:
            $work_type_name = '监理';
            break;
        case 5:
            $work_type_name = '泥水';
            break;
        case 6:
            $work_type_name = '木工';
            break;
        case 7:
            $work_type_name = '水电';
            break;
        case 8:
            $work_type_name = '油漆';
            break;
        case 9:
            $work_type_name = '安装';
            break;
        case 10:
            $work_type_name = '拆除';
            break;
        default:
            $work_type_name = '工作者';
    }
    return $work_type_name;
}


/**
 * 加密方法
 * @param string $str
 * @return string
 */
function encrypt($str,$screct_key){
    //AES, 128 模式加密数据 CBC
    $screct_key = base64_decode($screct_key);
    $str = trim($str);
    $str = addPKCS7Padding($str);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
    $encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
    return base64_encode($encrypt_str);
}

/**
 * 解密方法
 * @param string $str
 * @return string
 */
function decrypt($str,$screct_key){
    //AES, 128 模式加密数据 CBC
    $str = base64_decode($str);
    $screct_key = base64_decode($screct_key);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
    $encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
    $encrypt_str = trim($encrypt_str);

    $encrypt_str = stripPKSC7Padding($encrypt_str);
    return $encrypt_str;

}

/**
 * 填充算法
 * @param string $source
 * @return string
 */
function addPKCS7Padding($source){
    $source = trim($source);
    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

    $pad = $block - (strlen($source) % $block);
    if ($pad <= $block) {
        $char = chr($pad);
        $source .= str_repeat($char, $pad);
    }
    return $source;
}
/**
 * 移去填充算法
 * @param string $source
 * @return string
 */
function stripPKSC7Padding($source){
    $source = trim($source);
    $char = substr($source, -1);
    $num = ord($char);
    if($num==62)return $source;
    $source = substr($source,0,-$num);
    return $source;
}

/**
 * @param $img
 * @return string
 * 图片类型
 */
function start_img_type($img) {
    switch ($img) {
        case 'ad_img':
            $img_name = '广告图片';
            break;
        case 'help_img':
            $img_name = '引导图片';
            break;
        default:
            $img_name = '广告图片';
    }
    return $img_name;
}

/**
 * @param $application
 * @return mixed
 * 推送(接受或者拒绝订单)
 */
function get_reply_order_msg($application) {
    $message = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_reply_order')->first()->chn_name;
    return $message;
}

/**
 * @param $application
 * @return mixed
 * 推送(业主创建订单)
 */
function get_create_order_msg($application) {
    $message = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_create_order')->first()->chn_name;
    return $message;
}

/**
 * 保存推送消息
 */
function save_push_msg($message, $application, $uid) {
    //保存发送的消息
    $data_send['message']     = $message;
    $data_send['application'] = $application;
    $data_send['uid']         = $uid;
    \App\Modules\User\Model\UsersMessageSendModel::create($data_send);
    return true;
}


/**
 * 每一次的确认,状态改变的推送
 */
function change_status_msg($uid, $title) {
    //推送给工作者
    $application = 50001;
    $message     = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_order_status')->first()->chn_name;
    $woker_info  = \App\Modules\User\Model\UserModel::find($uid);
    $woker_info->send_num += 1;
    $woker_info->save();
    //保存发送的消息
    save_push_msg($message, $application, $uid);
    \App\PushServiceModel::pushMessageWorker($woker_info->device_token, $title . $message, $woker_info->send_num, $application);
    return true;
}
/**
 * 每一次的确认,状态改变的推送(给业主)
 */
function change_status_msg_to_boss($uid, $title) {
    //推送给业主
    $application = 50001;
    $message     = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', 'message_order_status')->first()->chn_name;
    $woker_info  = \App\Modules\User\Model\UserModel::find($uid);
    $woker_info->send_num += 1;
    $woker_info->save();
    //保存发送的消息
    save_push_msg($message, $application, $uid);
    \App\PushServiceModel::pushMessageBoss($woker_info->device_token, $title . $message, $woker_info->send_num, $application);
    return true;
}

/**
 * @param $uid
 * @param $title
 * @return bool
 * 推送给业主
 */
function small_order_to_boss($uid, $application, $eng_name = 'message_small_order', $message_other = '') {
     $chn_name_info    = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', $eng_name)->first();
    if (empty($chn_name_info)) {
        return true;
    } else {
        $message = $chn_name_info->chn_name;
    }
    $woker_info = \App\Modules\User\Model\UserModel::find($uid);
    $woker_info->send_num += 1;
    $woker_info->save();
    //保存发送的消息
    $msg_total = $message_other . $message;
    save_push_msg($msg_total, $application, $uid);
    \App\PushServiceModel::pushMessageBoss($woker_info->device_token, $msg_total, $woker_info->send_num, $application);
    return true;
}

function mb_trim($string){
    // u模式符表示 字符串被当成 UTF-8处理
    return preg_replace('/(^\s+)|(\s+$)/u', '', $string);
}

/**
 * @param $uid
 * @param $title
 * @return bool
 * 推送给工作者
 */
function small_order_to_worker($uid, $application, $eng_name = 'message_small_order', $message_other = '') {
    $chn_name_info    = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', $eng_name)->first();

    if (empty($chn_name_info)) {
        return true;
    } else {
        $message = $chn_name_info->chn_name;
    }

    $woker_info = \App\Modules\User\Model\UserModel::find($uid);
    $woker_info->send_num += 1;
    $woker_info->save();
    //保存发送的消息
    $msg_total = $message_other . $message;
    save_push_msg($msg_total, $application, $uid);
    \App\PushServiceModel::pushMessageWorker($woker_info->device_token, $msg_total, $woker_info->send_num, $application);
    return true;
}

/**
 * @param $uid
 * @param $application
 * @param string $eng_name
 * @param string $message_other
 * @return bool
 * 推送给boss(安卓)
 */

function android_push_to_boss($uid, $application, $eng_name = 'message_small_order', $message_other = '', $custom = []) {
    $message    = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', $eng_name)->first()->chn_name;
    $woker_info = \App\Modules\User\Model\UserModel::find($uid);
    //保存发送的消息
    $msg_total = $message_other . $message;
    save_push_msg($msg_total, $application, $uid);
    $res = \App\PushServiceModel::pushMessageBossAndroid($woker_info->device_token, $msg_total, $custom);
    return $res;
}

/**
 * @param $uid
 * @param $application
 * @param string $eng_name
 * @param string $message_other
 * @return bool
 * 推送给工作者(安卓)
 */
function android_push_to_worker($uid, $application, $eng_name = 'message_small_order', $message_other = '', $custom = []) {
    $message    = \App\PushSentenceList::select('chn_name')->where('nameBelongType', $application)->where('eng_name', $eng_name)->first()->chn_name;
    $woker_info = \App\Modules\User\Model\UserModel::find($uid);
    //保存发送的消息
    $msg_total = $message_other . $message;
    save_push_msg($msg_total, $application, $uid);
    \App\PushServiceModel::pushMessageBossAndroid($woker_info->device_token, $msg_total, $custom);
    return true;
}


/**
 * @param $uid
 * @param $application
 * @param $eng_name
 * @param string $message_other
 * @param array $custom
 * 推送总入口
 */
function push_accord_by_equip($uid, $application, $eng_name, $message_other = '', $task_id = '') {
    $user_info = \App\Modules\User\Model\UserModel::find($uid);
    if (!empty($task_id)) {
        $custom = [
            'task_id' => $task_id,
            'task_type' => \App\Modules\Task\Model\TaskModel::find($task_id)->user_type
        ];
    } else {
        $custom = [
            'task_id' => '',
            'task_type' => ''
        ];
    }
    $token_type = $user_info->device_token_type;
    if ($token_type == 'iOS') {
        if ($user_info->user_type == 1) {
            small_order_to_boss($uid, $application, $eng_name, $message_other);
        } else {
            small_order_to_worker($uid, $application, $eng_name, $message_other);
        }
    } else {
        if ($user_info->user_type == 1) {
            android_push_to_boss($uid, $application, $eng_name, $message_other, $custom);
        } else {
            android_push_to_worker($uid, $application, $eng_name, $message_other, $custom);
        }
    }
}


/**
 * @param $deviceToken
 * @return array
 * 安卓token截取
 */
function deviceTokenChange($deviceToken){
    $deviceToken = explode('-', $deviceToken);
    if ($deviceToken[0] == 'Android') {
        $deviceToken = $deviceToken[1];
    } else {
        $deviceToken = $deviceToken[0];
    }
    return $deviceToken;
}

/**
 * @param $deviceToken
 * @return array
 * ios token截取
 */
function deviceTokenChangeIOS($deviceToken) {
    $deviceToken = explode('-', $deviceToken);
    if ($deviceToken[0] == 'iOS') {
        $deviceToken = $deviceToken[1];
    } else {
        $deviceToken = $deviceToken[0];
    }
    return $deviceToken;
}

function methodA(){
    echo "A is Called";
}

/**
 * @param $user_type_user
 * @param $task
 * @param $id
 * @return mixed
 * 根据监理订单找管家
 */
function FindHouseTaskId($task_id) {
    $user_type        = \App\Modules\Task\Model\TaskModel::find($task_id)->user_type;
    $project_position = \App\Modules\Task\Model\TaskModel::find($task_id)->project_position;
    if ($user_type == 4) {
        $housekeeper_task = \App\Modules\Task\Model\TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 3)->first();
        $real_id          = $housekeeper_task->id;
    } else {
        $real_id = $task_id;
    }
    return $real_id;
}

/**
 * @param $user_type_user
 * @param $task
 * @param $id
 * @return mixed
 * 根据管家找监理
 */
function FindSuperTaskId($task_id) {
    $user_type        = \App\Modules\Task\Model\TaskModel::find($task_id)->user_type;
    $project_position = \App\Modules\Task\Model\TaskModel::find($task_id)->project_position;
    if ($user_type == 3) {
        $super_task = \App\Modules\Task\Model\TaskModel::where('project_position', $project_position)->where('status', '<', 9)->where('user_type', 4)->first();
        $real_id          = $super_task->id;
    } else {
        $real_id = $task_id;
    }
    return $real_id;
}

/**
 * @param $status
 * @return mixed|string
 * 状态:1.管家提交该工程,2.业主驳回管家整改单,3.业主付款,4.管家提交验收,5.业主确认,6.结算完成(新约单),7.异常结单(小订单)
 */
function small_order_status($status) {
    $list = [
        1 => "管家提交该工程",
        2 => "业主驳回管家整改单",
        3 => "业主付款",
        4 => "管家提交验收",
        5 => "业主确认",
        6 => "结算完成",
        7 => "异常结单",
    ];

    return isset($list[$status]) ? $list[$status] : "数据错误";
}




