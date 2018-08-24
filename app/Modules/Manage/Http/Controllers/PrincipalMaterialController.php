<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\ManageController;
use Illuminate\Http\Request;
use DB;

//主材控制器
class PrincipalMaterialController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('主材管理');
        $this->theme->set('manageType', 'Overview');
    }

    /**
     * 分类列表
     *
     */
    public function principalCategoryList(Request $request)
    {
        $cate = DB::table('principal_material_cate')->select('id','name')->where('deleted',0)->get();
        $data = [
            'cate'=>$cate,
        ];
        return $this->theme->scope('manage.principal.category',$data)->render();
    }

    /**
     * 主材材料列表
     *
     */
    public function principalGoodsList(Request $request)
    {
        $goods = DB::table('principal_material_goods as g')->select('g.*','brand_name','c.name as cate_name')
                    ->leftJoin('principal_material_cate as c','g.cate_id','=','c.id')
                    ->where('c.deleted',0)->paginate(10);

        foreach($goods as $key => &$value){
            $value->img = !empty($value->img)?url($value->img):'';
        }

        $data = [
            'goods'=>$goods,
        ];
        return $this->theme->scope('manage.principal.goods',$data)->render();
    }



    /**
     * 添加或编辑主材
     *
     */
    public function goodsEdit(Request $request)
    {
        $id = intval($request->get('id'));
//$id = 0;
        $goods = DB::table('principal_material_goods')->where('deleted',0)->where('id',$id)->first();

        if(!empty($goods)){
            $goods->img_2 = $goods->img;
            $goods->img = !empty($goods->img)?url($goods->img):'';
        }

        $cates = DB::table('principal_material_cate')->where('deleted',0)->get();
        $data = [
            'goods'=>$goods,
            'cates'=>$cates,
            'id'=>$id
        ];
        return $this->theme->scope('manage.principal.goodsEdit',$data)->render();
    }

    /**
     * 主材资料提交
     */
    public function subGoodsEdit(Request $request) {
        $id                     = intval($request->get('edit_id'));
        $date['name']           = $request->get('name');
        $date['brand_name']     = $request->get('brand_name');
        $date['cate_id']        = $request->get('cate_id');
        $date['img']            = $request->get('user-avatar');
        $date['model_name']     = $request->get('model_name');
        $date['specifications'] = $request->get('specifications');

        if(!empty($id)){
            DB::table('principal_material_goods')->where('id',$id)->update($date);
        }else{
            DB::table('principal_material_goods')->insert($date);
        }
        return redirect('/manage/principalGoodsList')->with(array('message' => '操作成功'));
    }


    /**
     * 添加或编辑主材分类
     *
     */
    public function categoryEdit(Request $request)
    {
        $id = intval($request->get('id'));
        $cate = DB::table('principal_material_cate')->where('deleted',0)->where('id',$id)->first();
        $data = [
            'cate'=>$cate,
            'id'=>$id
        ];
        return $this->theme->scope('manage.principal.categoryEdit',$data)->render();
    }

    /**
     * 主材分类资料提交
     */
    public function subCategoryEdit(Request $request) {
        $id                     = intval($request->get('edit_id'));
        $date['name']           = $request->get('name');
        if(!empty($id)){
            DB::table('principal_material_cate')->where('id',$id)->update($date);
        }else{
            DB::table('principal_material_cate')->insert($date);
        }
        return redirect('/manage/principalCategoryList')->with(array('message' => '操作成功'));
    }


}
