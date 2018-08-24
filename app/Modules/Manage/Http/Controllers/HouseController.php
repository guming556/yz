<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\ManageController;
use App\Modules\Manage\Model\HouseModel;
class HouseController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('基本设置');
        $this->theme->set('manageType', 'House');
    }

    //
    public function getHouse(){
    	$houses = HouseModel::get()->toArray();

    	$data = [
            'houses' => $houses,
        ];

    	// var_dump($houses);exit;
        return $this->theme->scope('manage.house.house' , $data)->render();
    }


    public function houseDelete($id)
    {
        $id = intval($id);
        $result = HouseModel::destroy($id);
        if(!$result)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        // HouseModel::refreshAreaCache();
        return response()->json(['errCode'=>1,'id'=>$id]);
    }



    public function houseCreate(Request $request)
    {
        $data = $request->except(['_token','_url']);
        
        if(count($data['change_ids'])>0)
        {
            foreach($data['name'] as $k=>$v)
            {
                $change_ids = explode(' ',$data['change_ids']);
                if(in_array($k,$change_ids)){
                    $result = HouseModel::where('id',$k)->update(['name'=>$v]);
                    if(!$result)
                    {
                        HouseModel::firstOrCreate(['name'=>$v]);
                    }
                }
            }
            
            // HouseModel::refreshHouseCache();
        }

        return redirect()->back()->with(['massage'=>'修改成功！']);
    }


    





















}
