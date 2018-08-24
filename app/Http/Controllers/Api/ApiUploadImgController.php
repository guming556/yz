<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use Gregwar\Image\Image;

class ApiUploadImgController extends BaseController
{
    //
    //用户类 上传图片
//
    public function uploadImg(Request $request){
	// TODO 这里要压缩下图片

        $file           = $request->file('file');
        $type           = !empty($request->get('img_type')) ? $request->get('img_type') : 'user';
        $allowExtension = array('jpg', 'gif', 'jpeg', 'bmp', 'png');
        $result         = \FileClass::uploadFile($file, $path = $type, $allowExtension);
        $result         = json_decode($result, true);

        if ($result['code'] != 200) {
        	return $this->error($result['message']);
        }else{
        	// $small = \FileClass::imageHandleSmall($result['data']);
        }

       return $this->success(['full_path'=>url($result['data']['url']) , 'path'=>$result['data']['url']]);
        	
    }

    public function createThumb(){
        ini_set("memory_limit","-1");
        $dir="attachment/handleImg/user_att/";
        $file=scandir($dir);

        foreach($file as $key => $value){
            if($value=='.'||$value=='..')continue;
            \FileClass::uploadFile($dir.$value);exit;
            var_dump(getimagesize($dir.$value));exit;
            $a = Image::open($dir.$value)->zoomCrop(640,320)->save(str_replace('.','_medium.',$dir.$value));
            $b = Image::open($a)->zoomCrop(320,160)->save(str_replace('_medium','_small',$a));
//            Image::open($data['medium_url'])->resize(320)->save($data['small_url']);
//            ->zoomCrop(640,320,'center')
        }

    }




}
