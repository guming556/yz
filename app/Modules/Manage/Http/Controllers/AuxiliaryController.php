<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\ManageController;
use DB;

class AuxiliaryController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('辅材包管理');
        $this->theme->set('manageType', 'Auxiliary');
    }

//    /**
//     * @return mixed
//     * 获取辅材包列表
//     */
//    public function auxiliary(){
//        $list = DB::table('auxiliary')->get();
//        foreach($list as $key => &$value){
//            $value->detail_url = !empty($value->detail_url)?url($value->detail_url):'';
//        }
//        $data = array(
//            'list' => $list
//        );
//        return $this->theme->scope('manage.auxiliary.auxiliary', $data)->render();
//    }
//
//
//    /**
//     * @param $id
//     * @return mixed
//     * 获取辅材包详细
//     */
//    public function auxiliaryDetail($id){
//        $list = DB::table('auxiliary_detail')->where('pid',$id)->get();
//        $data = array(
//            'list' => $list ,
//            'auxiliary_id' => $id
//        );
//
//        return $this->theme->scope('manage.auxiliary.auxiliary_detail', $data)->render();
//    }
//
//    /**
//     * @param Request $request
//     * @param $id
//     * @return \Illuminate\Http\RedirectResponse
//     * 删除辅材包
//     */
//    public function deleteAuxiliary(Request $request,$id){
//        $id = intval($id);
//        $ret = DB::table('auxiliary')->where('id',$id)->update(['deleted'=>1]);
//        if($ret){
//            return redirect()->back()->with(['message'=>'删除成功！']);
//        }
//        return redirect()->back()->with(['message'=>'删除失败！']);
//    }
//
//
//    /**
//     * @param Request $request
//     * @return \Illuminate\Http\RedirectResponse
//     * 编辑辅材包
//     */
//    public function editAuxiliary(Request $request){
//        $id = intval($request->get('id'));
//        $data['name']  = $request->get('name');
//        $data['price'] = intval($request->get('price'));
//        $excel = $request->file('xls');
//
//        if(empty($data['name']) ||  empty($data['price'])){
//            return redirect()->back()->with(['message'=>'请填写辅材包单价和辅材包名称！']);
//        }
//
//
///*        $excel_list = $request->file('aux_list');      //新手图片
//        if (!$request->hasFile('aux_list')) {
//            return back()->with(['message' => '上传文件为空']);
//        }
//
//        //判断文件上传过程中是否出错
//        if (!$excel_list->isValid()) {
//            return back()->with(['message' => '文件上传出错']);
//        }
//
//        $destPath = realpath(public_path('aux_manage'));
//        if (!file_exists($destPath))
//            mkdir($destPath, 0755, true);
//        $filename = $excel_list->getClientOriginalName();
//        if (!$excel_list->move($destPath, $filename)) {
//            return back()->with(['message' => '保存文件失败']);
//        }
//        Excel::load(public_path() . '/aux_manage/' . "$filename", function ($reader) {
//            $all_data = $reader->toArray()[0];
//            dd($all_data);
////            foreach ($all_data as $item => $value) {
////                if (!empty($value['name']) && !empty($value['city_id'])) {
////                    ProjectConfigureModel::create($value);
////                }
////            }
//        });
//        return back()->with(['message' => '处理成功']);*/
//
//
//        if(!empty($excel)){
//            $ret = json_decode(\FileClass::officeFileUpload($excel,'auxiliary',['xls','xlsx']),true);
//            if ($ret['code'] != 200) {
//                return redirect()->back()->with(['message'=>'文件上传失败！']);
//            }else{
//                $data['detail_url'] = $ret['data']['url'];
//            }
//        }
//
//        if(!empty($id)){
//            $data['updated_at'] = date('Y-m-d H:i:s');
//            $editRet = DB::table('auxiliary')->where('id',$id)->update($data);
//            if($editRet){
//                return redirect()->back()->with(['message'=>'修改成功！']);
//            }
//            return redirect()->back()->with(['message'=>'修改失败！']);
//        }else{
//            $data['updated_at'] = date('Y-m-d H:i:s');
//            $data['created_at'] = date('Y-m-d H:i:s');
//            $insertRet = DB::table('auxiliary')->insert(['price'=>$data['price'] , 'name'=>$data['name'] , 'detail_url'=>$ret['data']['url']]);
//            if($insertRet){
//                return redirect()->back()->with(['message'=>'添加成功！']);
//            }
//            return redirect()->back()->with(['message'=>'添加失败！']);
//        }
//
//    }

}
