<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\BasicController;
use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Modules\Manage\Model\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class StaffingController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('设置管理');
        $this->theme->set('manageType', 'Staffing');
    }

    public function getService()
    {
        $config = ConfigModel::getConfigByAlias('cash')->toArray();
        $data = array(
            'data' => json_decode($config['rule'], true)
        );
        return $this->theme->scope('manage.config.interface', $data)->render();
    }
}
