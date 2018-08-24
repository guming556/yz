<?php

namespace App\Modules\Manage\Http\Controllers;


use App\Http\Controllers\ManageController;
use App\Modules\Manage\Model\MaterialsModel;
use App\Http\Controllers\BaseController;
use App\Http\Requests;
use Illuminate\Http\Request;



class MaterialsController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('管理员账号管理');
        $this->theme->set('manageType', 'Materials');

    }


    public function materiaList(Request $request)
    {
        $serach = $request->all();
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        $materlist = MaterialsModel::select('id','name','content','price','status','sell_num');
        //根据输入名称查询
        if($request->get('name'))
        {
            $materlist = $materlist->where('materials.name','like','%'.e($request->get('name')).'%');
        }
        $mlist= $materlist->orderby('updated_at','asc')->paginate($paginate);
        $data = $mlist->toArray();
        $view =[
            'mList' => $data,
            'merge'=>$serach
        ];

        return $this->theme->scope('manage.materiaList', $view)->render();

    }


    /**
     * 下架
     */
    public function down($id)
    {
        $id = intval($id);
        $res = MaterialsModel::where('id',$id) ->update(['status => 0']);
        if(!$res)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'下架失败！']);
        }
        else{
            return response()->json(['errCode'=>1,'id'=> $id ]);
        }

    }

    /**
     * 删除
     */

    public function delMaterial($id)
    {
        $id = intval($id);
        $res = MaterialsModel::destroy('id',$id);
        if(!$res)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        else{
            return response()->json(['errCode'=>1,'id'=> $id ]);
        }
    }


    /**
     * 编辑
     *
     */

    public function editmaterials($id)
    {
        $id = intval($id);
        $maInfo = MaterialsModel::find($id);
        $data =[
            'maInfo' => $maInfo,
        ];

        return $this->theme->scope('manage.editmaterials', $data)->render();

    }

    public function savematerials(Request $request)
    {
        $data = $request ->except('_token');
        $result['id'] = $data['id'];
        $result['name'] = $data['name'];
        $result['content'] = $data['content'];
        $result['price'] = $data['price'];
        $result['sell_num'] = $data['sell_num'];
        $result['count'] = $data['count'];
        $result['updated_at'] = date('Y-m-d H:i:s',time());
        if(isset($data['id']))//更新
        {
            $code = MaterialsModel::where('id',$data['id'])->update($result);
        }
        else{
            $result['created_at'] = date('Y-m-d H:i:s',time());
            $code = MaterialsModel::create($result);
        }
        if ($code) {
            return redirect('/manage/materiaList')->with(array('message' => '操作成功'));
        } else {
            return redirect('/manage/materiaList')->with(array('message' => '操作成功'));
        }
        return false;
    }
}