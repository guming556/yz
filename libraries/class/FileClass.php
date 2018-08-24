<?php
use League\Flysystem\Util\MimeType;
use Illuminate\Support\Facades\DB;
use \App\Modules\User\Model\AttachmentModel;
use \App\Modules\Manage\Model\ConfigModel;
use Gregwar\Image\Image;
use Illuminate\Support\Facades\Storage;

class FileClass
{
    /**
     * 上传文件
     *
     * @param object $file
     * @param string $path
     * @param array $allowExtension
     * @return string
     *
     */
    static function uploadFile($file, $path = 'default', $allowExtension = null,$designerUpload = false)
    {
        $fileTypeArr = ['task', 'sys', 'user', 'default', 'shop','ad'];
        if (!in_array($path, $fileTypeArr)) {
            return CommonClass::formatResponse('未定义的文件上传目录', 1001);
        }
        // TODO DIRECTORY_SEPARATOR改为了 /
        $attachmentPath = 'attachment';
        switch ($path) {
            case 'task':
                $disk = 'public';
                $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
                break;
            case 'sys':
                $disk = 'public';
                $filePath = $attachmentPath . '/' . $path . '/';
                break;
            case 'user':
                $disk = 'public';
                $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
                break;
            case 'shop':
                $disk = 'public';
                $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
                break;
            case 'ad':
                $disk = 'public';
                $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
                break;
            default:
                $disk = 'local';
                $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
                break;
        }

        $fileSize = $file->getClientSize();

        $attachmentConfig = ConfigModel::getConfigByType('attachment');

        if ($fileSize > $file->getMaxFilesize() || $fileSize > $attachmentConfig['attachment']['size'] * 1024 * 1024) {
            return CommonClass::formatResponse('上传文件超出服务器大小限制', 1002);
        }

        //判断文件上传过程中是否出错
        if ($file->isValid()) {

            $mimeType = MimeType::getExtensionToMimeTypeMap();

            $configExtension = explode('|', $attachmentConfig['attachment']['extension']);

            if (!empty($configExtension)) {
                if (!in_array(strtolower($file->getClientOriginalExtension()), $configExtension)) {
                    return CommonClass::formatResponse('文件类型不允许上传', 1003);
                }
            }
            if (isset($allowExtension)) {
                foreach ($allowExtension as $item) {
                    if (!in_array(strtolower(FileClass::getMimeTypeByExtension($item)), $mimeType)) {
                        return CommonClass::formatResponse('文件类型不允许上传', 1003);
                    }
                }
            }

            if (!in_array($file->getMimeType(), $mimeType)) {
                return CommonClass::formatResponse('未知文件类型', 1004);
            }
            $clientName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $realName = md5((substr($clientName, 0, (strlen($clientName) - strlen($extension) - 1))) . time()) . '.' . $extension;
            $status = Storage::disk($disk)->put($filePath . $realName, file_get_contents($file->getRealPath()));

            if ($status) {
                $data = array();
                $data['url'] = $filePath . $realName;
                $data['name'] = $clientName;
                $data['type'] = $extension;
                $data['size'] = $fileSize / 1024;
                $data['user_id'] = Auth::user()['id'];
                $data['disk']    = $disk;
                $arr             = getimagesize($data['url']);

//                dd($arr);
                //图片宽度
                $realName_chai      = explode('.', $realName);
                $data['medium_url'] = $filePath . $realName_chai[0] . '_medium' .'.'. $realName_chai[1];
                $data['small_url']  = $filePath . $realName_chai[0] . '_small' .'.'. $realName_chai[1];



                if ($designerUpload) {
                    if($arr[0] > 1920){
                        $setWidth  = 1920;
                        $setHeight = 1920*(floor($arr[1]/$arr[0]));
                        Image::open($data['url'])->zoomCrop($setWidth,$setHeight)->save($data['url']);
                    }
                    Image::open($data['url'])->zoomCrop(640,320)->save($data['medium_url']);
                    Image::open($data['medium_url'])->zoomCrop(320,160)->save($data['small_url']);
                }
                return CommonClass::formatResponse('上传成功', 200, $data);
            }
        }
        return CommonClass::formatResponse('文件上传失败', 1005);

    }

    /**
     *头像生成方法
     * 同时生成三张不同分辨率的图片
     */
    static function headUpload($file, $uid)
    {
        //图片保存的路径

        $filePath = 'attachment' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . date('Y/m/d') . DIRECTORY_SEPARATOR;

        $fileSize = $file->getClientSize();
        if ($file->getClientSize() >= $file->getMaxFilesize()) {
            return CommonClass::formatResponse('上传文件超出服务器大小限制');
        }
        //判断文件上传过程中是否出错
        if ($file->isValid()) {
            $mimeType = MimeType::getExtensionToMimeTypeMap();
            if (!in_array($file->getMimeType(), $mimeType)) {
                return CommonClass::formatResponse('文件类型不允许上传');
            }
            $clientName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $realName = md5($uid . 'large') . '.' . $extension;
            if ($file->move($filePath, $realName)) {
                $data = array();
                $data['url'] = $filePath . $realName;
                $data['path'] = \CommonClass::getDomain() . '\\' . $filePath . $realName;
                $data['name'] = $clientName;
                $data['type'] = $extension;
                $data['size'] = $fileSize / 1024;
                $data['avatar'] = $filePath;
                $data['status'] = 1;

                //处理生成三张图片
                $result = self::headHandle($data, $uid);
                if (!$result) {
                    return CommonClass::formatResponse('文件上传失败');
                }
                return CommonClass::formatResponse('上传成功', 200, $data);
            };
        }
        return CommonClass::formatResponse('文件上传失败');
    }



    /**
     * 图片生成缩略图并覆盖原图
     *
     */
    static function thumbnailUploadImage($file,$type = 'user',$disk='public',$defaultSize = 1024) {
        //图片保存的路径
        //        $filePath = $attachmentPath . '/' . $path . '/' . date('Y/m/d') . '/';
        $filePath = 'attachment' . '/' . $type . '/' . date('Y/m/d') . '/';

//var_dump($file);exit;
        if ($file->getClientSize() >= $file->getMaxFilesize()) {
            return CommonClass::formatResponse('上传文件超出服务器大小限制');
        }

        //判断文件上传过程中是否出错
        if ($file->isValid()) {
            $mimeType = MimeType::getExtensionToMimeTypeMap();
            if (!in_array($file->getMimeType(), $mimeType)) {
                return CommonClass::formatResponse('文件类型不允许上传');
            }

            $extension  = $file->getClientOriginalExtension();
            $clientName = $file->getClientOriginalName();
            $realName   = md5(time().mt_rand(0,99)) . '.' . $extension;

            $status = Storage::disk($disk)->put($filePath . $realName, file_get_contents($file->getRealPath()));

            if ($status) {
                $data        = array();
                $data['url'] = $filePath . $realName;
                $data['name'] = $clientName;
                $arr             = getimagesize($data['url']);

                if ($arr[0] > $defaultSize) {
                    $setWidth  = $defaultSize;
                    $setHeight = $defaultSize * (round($arr[1] / $arr[0] , 2));
                    Image::open($data['url'])->zoomCrop($setWidth, $setHeight)->save($data['url']);
                }else{
                    if($arr[1] > $defaultSize){
                        $setHeight  = $defaultSize;
                        $setWidth = $defaultSize * (round($arr[0] / $arr[1] , 2));
                        Image::open($data['url'])->zoomCrop($setWidth, $setHeight)->save($data['url']);
                    }
                }

                return CommonClass::formatResponse('上传成功', 200, $data);
            }else{
                return CommonClass::formatResponse('文件上传失败');
            }
        }
        return CommonClass::formatResponse('文件上传失败');
    }





    /**
     * 根据后缀获取MimeType
     *
     * @param array|string $extension
     * @return string | array
     */
    static function getMimeTypeByExtension($extension)
    {
        $mimeType = MimeType::getExtensionToMimeTypeMap();
        if (is_array($extension)) {
            foreach ($extension as $item) {
                $arrMimeType[] = $mimeType[$item];
            }
            return $arrMimeType;
        }
        return $mimeType[$extension];
    }

    /**
     * 利用图片处理生成三张默认分辨率的图片
     */
    static function headHandle($data, $uid, $size = array('large' => array(150, 150), 'middle' => array(100, 100), 'small' => array(50, 50)))
    {
        $file = 'attachment' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . date('Y/m/d') . DIRECTORY_SEPARATOR;
        foreach ($size as $k => $v) {
            $img = Image::open($data['url']);
            is_dir($file) || mkdir($file);  //如果不存在则创建目录
            $filePath = $file . md5($uid . $k) . '.' . 'jpg';
            $img->cropResize($v[0], $v[1], '#ffffff');
            $result = $img->save($filePath);
            $data['url'] = $filePath;
            AttachmentModel::create($data);
            if (!$result) {
                return false;
            }
        }

        return true;
    }



    /**
     * 利用图片处理生成小一点的图片  分辨率
     */
    // static function imageHandleSmall( $data, $width = 100 , $height = 100 )
    // {
    //     $file = 'attachment' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . date('Y/m/d') . DIRECTORY_SEPARATOR;
  
    //         $img = Image::open($data['url']);
    //         $filePath = $data['url'] .'.' . 'jpg';
    //         $img->cropResize(400, '', '#ffffff');
    //         $result = $img->save($filePath);
    //         $data['url'] = $filePath;

    //         if (!$result) {
    //             return false;
    //         }
        

    //     return true;
    // }



    /**
     *
     * 普通office文件上传
     */
    static function officeFileUpload($file,$uid,$allowExtension=[])
    {
        //文件保存的路径
        $filePath = 'attachment/auxiliary/' . date('Y/m/d') . '/';
        $fileSize = $file->getClientSize();
        if ($file->getClientSize() >= $file->getMaxFilesize()) {
            return CommonClass::formatResponse('上传文件超出服务器大小限制');
        }

        //判断文件上传过程中是否出错
        if ($file->isValid()) {

            $mimeType = MimeType::getExtensionToMimeTypeMap();


            if (isset($allowExtension)) {
                foreach ($allowExtension as $item) {

                    if (!in_array(strtolower(FileClass::getMimeTypeByExtension($item)), $mimeType)) {
                        return CommonClass::formatResponse('文件类型不允许上传', 1003);
                    }
                }
            }
//            "application/vnd.ms-office"

            if (!in_array($file->getMimeType(), $mimeType)) {
                return CommonClass::formatResponse('未知文件类型', 1004);
            }

            $clientName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $realName = md5(time()) . '.' . $extension;
            if ($file->move($filePath, $realName)) {

                $data = array();
                $data['url'] = $filePath . $realName;
                $data['path'] = \CommonClass::getDomain() . '\\' . $filePath . $realName;
                $data['name'] = $clientName;
                $data['type'] = $extension;
                $data['size'] = $fileSize / 1024;
                $data['filePath'] = $filePath;
                $data['status'] = 1;
                return CommonClass::formatResponse('上传成功', 200, $data);
            }
        }
        return CommonClass::formatResponse('文件上传失败');
    }





    /**
     * Luploader插件的图片上传处理（手机网页端使用）
     */
    // 图片上传处理
    static function LUploaderImage($file){
        preg_match("/data\:image\/([a-z]{1,5})\;base64\,(.*)/", $file, $r);
        $imgname      = 'bl' . time() . rand(10000, 99999) . '.' . $r[1];
        $imgname2jpeg = 'bl' . time() . rand(10000, 99999) . '.jpeg';

        $path = 'attachment/repair/' . date('Y/m/d').'/';

        is_dir($path) || mkdir($path);  //如果不存在则创建目录

        $ret = Storage::disk('public')->put($path . $imgname, base64_decode($r[2]));

        return ['path'=>$path.$imgname,'status'=>intval($ret)];

//        $originalImageUrl = $path . $imgname;
//        $handleImageUrl = $path . $imgname2jpeg;
//        $originalImageSize = getimagesize($originalImageUrl);
//        FileClass::ImageToJPG($originalImageUrl , $handleImageUrl , $originalImageSize[0], $originalImageSize[1], true);



    }

    static function ImageCreateFromBMP($filename) {
        if (!$f1 = fopen($filename, "rb")) return FALSE;

        $FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1, 14));
        if ($FILE ['file_type'] != 19778) return FALSE;

        $BMP            = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' .
            '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
        $BMP ['colors'] = pow(2, $BMP ['bits_per_pixel']);
        if ($BMP ['size_bitmap'] == 0) $BMP ['size_bitmap'] = $FILE ['file_size'] - $FILE ['bitmap_offset'];
        $BMP ['bytes_per_pixel']  = $BMP ['bits_per_pixel'] / 8;
        $BMP ['bytes_per_pixel2'] = ceil($BMP ['bytes_per_pixel']);
        $BMP ['decal']            = ($BMP ['width'] * $BMP ['bytes_per_pixel'] / 4);
        $BMP ['decal'] -= floor($BMP ['width'] * $BMP ['bytes_per_pixel'] / 4);
        $BMP ['decal'] = 4 - (4 * $BMP ['decal']);
        if ($BMP ['decal'] == 4) $BMP ['decal'] = 0;

        $PALETTE = array();
        if ($BMP ['colors'] < 16777216) {
            $PALETTE = unpack('V' . $BMP ['colors'], fread($f1, $BMP ['colors'] * 4));
        }

        $IMG  = fread($f1, $BMP ['size_bitmap']);
        $VIDE = chr(0);
        $res  = imagecreatetruecolor($BMP ['width'], $BMP ['height']);
        $P    = 0;
        $Y    = $BMP ['height'] - 1;
        while ($Y >= 0) {
            $X = 0;
            while ($X < $BMP ['width']) {
                if ($BMP ['bits_per_pixel'] == 24)
                    $COLOR = unpack("V", substr($IMG, $P, 3) . $VIDE);
                elseif ($BMP ['bits_per_pixel'] == 16) {
                    $COLOR     = unpack("n", substr($IMG, $P, 2));
                    $COLOR [1] = $PALETTE [$COLOR [1] + 1];
                } elseif ($BMP ['bits_per_pixel'] == 8) {
                    $COLOR     = unpack("n", $VIDE . substr($IMG, $P, 1));
                    $COLOR [1] = $PALETTE [$COLOR [1] + 1];
                } elseif ($BMP ['bits_per_pixel'] == 4) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 2) % 2 == 0) $COLOR [1] = ($COLOR [1] >> 4); else $COLOR [1] = ($COLOR [1] & 0x0F);
                    $COLOR [1] = $PALETTE [$COLOR [1] + 1];
                } elseif ($BMP ['bits_per_pixel'] == 1) {
                    $COLOR = unpack("n", $VIDE . substr($IMG, floor($P), 1));
                    if (($P * 8) % 8 == 0) $COLOR [1] = $COLOR [1] >> 7;
                    elseif (($P * 8) % 8 == 1) $COLOR [1] = ($COLOR [1] & 0x40) >> 6;
                    elseif (($P * 8) % 8 == 2) $COLOR [1] = ($COLOR [1] & 0x20) >> 5;
                    elseif (($P * 8) % 8 == 3) $COLOR [1] = ($COLOR [1] & 0x10) >> 4;
                    elseif (($P * 8) % 8 == 4) $COLOR [1] = ($COLOR [1] & 0x8) >> 3;
                    elseif (($P * 8) % 8 == 5) $COLOR [1] = ($COLOR [1] & 0x4) >> 2;
                    elseif (($P * 8) % 8 == 6) $COLOR [1] = ($COLOR [1] & 0x2) >> 1;
                    elseif (($P * 8) % 8 == 7) $COLOR [1] = ($COLOR [1] & 0x1);
                    $COLOR [1] = $PALETTE [$COLOR [1] + 1];
                } else
                    return FALSE;
                imagesetpixel($res, $X, $Y, $COLOR [1]);
                $X++;
                $P += $BMP ['bytes_per_pixel'];
            }
            $Y--;
            $P += $BMP ['decal'];
        }

        fclose($f1);
        return $res;
    }



    static function ImageToJPG($srcFile, $dstFile, $towidth, $toheight,$del = false) {
        $quality = 80;
        $data    = @GetImageSize($srcFile);

        switch ($data['2']) {

            case 1:

                $im = imagecreatefromgif($srcFile);
                break;
            case 2:

                $im = imagecreatefromjpeg($srcFile);
                break;
            case 3:
                $im = imagecreatefrompng($srcFile);

                break;

            case 6:

                $im = ImageCreateFromBMP($srcFile);

                break;
        }

// $dstX=$srcW=@ImageSX($im);

// $dstY=$srcH=@ImageSY($im);

        $srcW = @ImageSX($im);
        $srcH = @ImageSY($im);
        $dstX = $towidth;
        $dstY = $toheight;

        $ni = @imageCreateTrueColor($dstX, $dstY);

        @ImageCopyResampled($ni, $im, 0, 0, 0, 0, $dstX, $dstY, $srcW, $srcH);
        @ImageJpeg($ni, $dstFile, $quality);
        @imagedestroy($im);
        @imagedestroy($ni);

        if($del){
            unlink($srcFile);
        }


    }





}