<?php

use Illuminate\Database\Seeder;

class init_test_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('test')->insert([
            'uuid' => '1'
        ]);
    }
}
