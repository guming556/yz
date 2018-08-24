<?php

namespace App\Modules\Advertisement\Model;

use Illuminate\Database\Eloquent\Model;

class CompanyBankInfo extends Model {

    protected $table = 'company_bank_info';

    protected $fillable = ['company_name', 'bank_name', 'bank_account', 'comment'];

}
