<?php

namespace App\Http\Controllers\v4\Api;


use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\Task\Model\ProjectPositionModel;
use App\Respositories\ChatRoomRespository;
use App\Modules\User\Model\DistrictModel;

class ProjectPositionController extends BaseController
{

    protected $chatRoomRespository;

    public function __construct(ChatRoomRespository $chatRoomRespository) {
        $this->chatRoomRespository   = $chatRoomRespository;
    }


    /**
     * 全部查找
     *
     */

    public function getAll(Request $request)
    {   
        $user_id = intval($request->get('user_id'));
        $data = ProjectPositionModel::select('id','project_position as address','region','lat','lng','square','room_config')->where('deleted','0')
            ->where('uid',$user_id)
            ->orderBy('created_at','desc')
            ->get()->toArray();

        foreach ($data as $key => &$value) {
            $room_config = json_decode($value['room_config']);
            foreach ($room_config as $key2 => $value2) {
                $value[$key2] = $value2;
            }
            unset($value['room_config']);
        }

        return $this->success($data);
    }

    /**
     * 按条件查询
     *
     */
    public function getPosition(Request $request)
    {
        $p_id = $request->get('id');
        $data = ProjectPositionModel::select('id','project_position','room_config','square','lat','lng')
            ->where('id',$p_id)->where('deleted','0')
            ->first()->toArray();

        $room_config = json_decode($data['room_config']);
        foreach ($room_config as $key => $value) {
            $data[$key] = $value;
        }
        unset($data['room_config']);
        return $this->success($data);
        

        // return $this->error(['error'=> '请求失败'],'500');
    }

    /**
     * 修改
     *
     */

    public function savePosition(Request $request)
    {
      
        $setting = array(
            'bedroom'    =>'居室',   //居室
            'living_room'=>'客厅',   //客厅
            'kitchen'    =>'厨房',   //厨房
            'washroom'   =>'卫生间',   //卫生间
            'balcony'    =>'阳台'      //  阳台
        );

        $room_config = '';
        foreach ($setting as $key => $value) {
            $room_config[$key] = !empty($request->json($key))?$request->json($key):0;
        }

        $data['room_config']      = json_encode($room_config);
        $data['project_position'] = $request->json('address');
        $data['lat']              = $request->json('lat');
        $data['lng']              = $request->json('lng');
        $data['square']           = $request->json('square'); //房屋面积
        $data['updated_at']       = date('Y-m-d H:i:s', time());
        $data['id']               = $request->json('project_id');
        $data['uid']              = $request->json('user_id');
        $data['region']           = $request->json('region');
        foreach ($data as $key => $value) {
            if(empty($value)){
                return $this->error($key.'必要资料为空' ,500);
            }
        }

        $mapApi = 'https://api.map.baidu.com/geocoder/v2/?location='.$data['lat'].','.$data['lng'].'&output=json&pois=1&ak=ZQEAQICL6vg3MLfqP9yEYz3X';
        $positionInfo = \GuzzleHttp\json_decode(file_get_contents($mapApi),true);
        if($positionInfo['status'] == 0){
            $cityName = $positionInfo['result']['addressComponent']['city'];
            $cityId = DistrictModel::where('name', 'like', $cityName.'%')->first();       //服务区域
            if(!empty($cityId)){
                $data['position_city_id'] = $cityId->id;
            }
        }


        $ret = ProjectPositionModel::where('deleted','0')->where('id',$data['id'])->where('uid',$data['uid'])->update($data);

        if($ret)
        {
            return $this->error('修改成功',0);
        }
        return $this->error('修改失败' , 500);
        
    }


    /**
     * 更新地区id
     */
    public function updatePosition(){
        $ret = ProjectPositionModel::where('deleted',0)->get()->toArray();
        foreach($ret as $key => $value){
            $mapApi = 'https://api.map.baidu.com/geocoder/v2/?location='.$value['lat'].','.$value['lng'].'&output=json&pois=1&ak=ZQEAQICL6vg3MLfqP9yEYz3X';
            $positionInfo = \GuzzleHttp\json_decode(file_get_contents($mapApi),true);
            if($positionInfo['status'] == 0){
                $cityName = $positionInfo['result']['addressComponent']['city'];
                $cityId = DistrictModel::where('name', 'like', $cityName.'%')->first();       //服务区域
                if(!empty($cityId)){
                    ProjectPositionModel::where('id',$value['id'])->update(['position_city_id'=>$cityId->id]);
                }
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 添加工地
     */
    public function createProjectPosition(Request $request) {
        $setting = array(
            'bedroom' => '居室',   //居室
            'living_room' => '客厅',   //客厅
            'kitchen' => '厨房',   //厨房
            'washroom' => '卫生间',   //卫生间
            'balcony' => '阳台'      //  阳台
        );

        $room_config = [];
        foreach ($setting as $key => $value) {
            $room_config[$key] = !empty($request->json($key)) ? $request->json($key) : 0;
        }

        $data['room_config']      = json_encode($room_config);
        $data['project_position'] = $request->json('address');
        $data['lat']              = $request->json('lat');
        $data['lng']              = $request->json('lng');
        $data['square']           = $request->json('square'); //房屋面积
        $data['updated_at']       = date('Y-m-d H:i:s', time());
        $data['uid']              = $request->json('user_id');
        $data['region']           = $request->json('region');

        foreach ($data as $key => $value) {
            if (empty($value)) {
                return $this->error('必要资料为空');
            }
        }



        $mapApi = 'https://api.map.baidu.com/geocoder/v2/?location='.$data['lat'].','.$data['lng'].'&output=json&pois=1&ak=ZQEAQICL6vg3MLfqP9yEYz3X';
        $positionInfo = \GuzzleHttp\json_decode(file_get_contents($mapApi),true);
        if($positionInfo['status'] == 0){
            $cityName = $positionInfo['result']['addressComponent']['city'];
            $cityId = DistrictModel::where('name', 'like', $cityName.'%')->first();       //服务区域
            if(!empty($cityId)){
                $data['position_city_id'] = $cityId->id;
            }
        }


        $user_info = UserModel::find($data['uid']);
        if (empty($user_info)) return $this->error('找不到用户');
        $ret = ProjectPositionModel::create($data);

        //创建聊天室
        if ($ret) {
            $user_info      = UserModel::find($data['uid']);
            $user_mobile    = (array)$user_info->name;
            $chat_room_name = $data['region'] . $data['project_position'];
            $roles          = ['admin' => $user_mobile];
            //$chat_room_id   = $this->chatRoomRespository->CreateChatRooms($chat_room_name, $user_mobile, $roles);
            $chat_room_id   = 0;

            ProjectPositionModel::where('id', $ret->id)->update(['chat_room_id' => $chat_room_id]);
            return $this->error('添加成功',0);
        } else {
            return $this->error('添加失败');
        }

    }


    /**
     * 删除
     */

    public function delPosition(Request $request)
    {
        $id = $request->json('id');
        // $data = ProjectPositionModel::destroy($id);
        $data = ProjectPositionModel::where('id',$id)->update(['deleted' => 1]);
        if($data)
        {
            return $this->error('删除成功',0);
        }else{
            return $this->error('删除失败' , 500);
        }
    }
}