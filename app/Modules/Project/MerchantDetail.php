<?php

namespace App\Modules\Project;

use App\Modules\User\Model\UserDetailModel;
use Illuminate\Database\Eloquent\Model;

class MerchantDetail extends Model
{
    protected $fillable = [
        'name', 'mobile', 'ad_slogan', 'brand_name', 'address', 'lat', 'lng', 'brand_logo','popular_img'
    ];
}
