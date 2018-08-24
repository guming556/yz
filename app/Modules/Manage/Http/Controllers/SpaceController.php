<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\ManageController;
use Illuminate\Http\Request;
use App\Modules\Manage\Model\SpaceModel;
class SpaceController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('基本设置');
        $this->theme->set('manageType', 'Space');
    }


    public function getSpace(){
    	$spaces = SpaceModel::get()->toArray();

    	$data = [
            'spaces' => $spaces,
        ];

    	// var_dump($houses);exit;
        return $this->theme->scope('manage.space.space' , $data)->render();
    }


    public function spaceDelete($id)
    {
        $id = intval($id);
        $result = SpaceModel::destroy($id);
        if(!$result)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        // SpaceModel::refreshAreaCache();
        return response()->json(['errCode'=>1,'id'=>$id]);
    }



    public function spaceCreate(Request $request)
    {
        $data = $request->except(['_token','_url']);
        
        if(count($data['change_ids'])>0)
        {
            foreach($data['name'] as $k=>$v)
            {
                $change_ids = explode(' ',$data['change_ids']);
                if(in_array($k,$change_ids)){
                    $result = SpaceModel::where('id',$k)->update(['name'=>$v]);
                    if(!$result)
                    {
                        SpaceModel::firstOrCreate(['name'=>$v]);
                    }
                }
            }
            
            // SpaceModel::refreshHouseCache();
        }

        return redirect()->back()->with(['massage'=>'修改成功！']);
    }


}
