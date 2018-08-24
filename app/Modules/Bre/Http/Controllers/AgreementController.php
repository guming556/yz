<?php


namespace App\Modules\Bre\Http\Controllers;


use App\Http\Controllers\IndexController;
use App\Modules\Manage\Model\AgreementModel;
use Illuminate\Routing\Controller;


class AgreementController extends IndexController
{
    public function __construct()
    {
        parent::__construct();
        $this->initTheme('main');
    }

    
    public function index($codeName)
    {
        if($codeName == 'register'){
            $agreeList = AgreementModel::whereIn('code_name',['server_agree','supervisor_agree','designer_agree','housekeeper_agree','worker_agree'])->get();
            $data = array(
                'agreeList' => $agreeList
            );
            return $this->theme->scope('bre.allRegisterAgree',$data)->render();
        }else{
            $agree = AgreementModel::where('code_name',$codeName)->first();
            $data = array(
                'agree' => $agree
            );
            $this->theme->setTitle($agree['name']);
            return $this->theme->scope('bre.agree',$data)->render();
        }

    }









}