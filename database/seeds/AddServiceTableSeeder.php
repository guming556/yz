<?php

use Illuminate\Database\Seeder;

class AddServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('service')->insert([
            'title' => '发任务手续费',
            'description' => '发任务手续费',
            'price' => 20,
            'type'  => 1,
            'identify' => 'SHOUXUFEI',
            'price' => 20,
            'created_at' => '2016-06-06 16:09:02',
            'updated_at' => '2016-07-18 11:27:23',
        ]);
    }
}
