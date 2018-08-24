<?php

use Illuminate\Database\Seeder;

class OrderConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('config')->insert(array(
        	0 =>
        	array (
        		'id' => 112,
                'alias' => 'order_public_time',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '接单时间限制',
                'desc' => '默认为0不做限制',
        	),
        	1 =>
        	array (
        		'id' => 113,
                'alias' => 'order_server_line_time',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '服务者发起线下约谈限制',
                'desc' => '默认为0不做限制',
        	),
        	2 =>
        	array (
        		'id' => 114,
                'alias' => 'order_owner_line_time',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '业主确认线下约谈时间限制',
                'desc' => '默认为0不做限制',
        	),
        	3 =>
        	array (
        		'id' => 115,
                'alias' => 'order_owner_chose_time',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '业主选择服务者时间限制',
                'desc' => '默认为0不做限制',
        	),
        	4 =>
        	array (
        		'id' => 116,
                'alias' => 'order_server_not_come',
                'rule' => '{"day":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '接单后未上门',
                'desc' => '默认为0不做限制',
        	),
        	5 =>
        	array (
        		'id' => 117,
                'alias' => 'order_server_refuse',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '不能接单时间限制',
                'desc' => '默认为0不做限制',
        	),
        	6 =>
        	array (
        		'id' => 118,
                'alias' => 'order_server_credit_scoring',
                'rule' => '{"score":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '信用评分下降',
                'desc' => '默认为0不做限制',
        	),
        	7 =>
        	array (
        		'id' => 119,
                'alias' => 'order_server_line_time',
                'rule' => '',
                'type' => 'pay_config_order',
                'title' => '扣除预约金',
                'desc' => '默认为0不做限制',
        	),
        	8 =>
        	array (
        		'id' => 120,
                'alias' => 'order_owner_refuse',
                'rule' => '{"hour":"","minute":"","status":""}',
                'type' => 'pay_config_order',
                'title' => '不能预约服务时间限制',
                'desc' => '默认为0不做限制',
        	),
        	9 =>
        	array (
        		'id' => 121,
                'alias' => 'order_server_get_max',
                'rule' => '{"number":"",status":""}',
                'type' => 'pay_config_order',
                'title' => '同时进行订单上限',
                'desc' => '默认为0不做限制',
        	),
        	10 =>
        	array (
        		'id' => 122,
                'alias' => 'order_percentage',
                'rule' => '',
                'type' => 'pay_config_order',
                'title' => '订单提出比例',
                'desc' => '默认为0不做限制',
        	),
        ));
    }
}
