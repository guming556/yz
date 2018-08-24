<?php

use Illuminate\Database\Seeder;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('level')->delete();
        
        \DB::table('level')->insert(array (
            0 => 
            array (
                'id' => 1,
                'offer_1' => '{"price":"","company":""}',
                'offer_2' => '{"price":"","company":""}',
                'offer_3' => '{"price":"","company":""}',
                'offer_4' => '{"price":"","company":""}',
                'offer_5' => '{"price":"","company":""}',
                'type' => '1',
                'upgrade' => '{"1":"0","2":"0","3":"0","4":"0","5":"0"}',
                'score' => '{"1":"0","2":"0","3":"0","4":"0","5":"0"}',
                'created_at' => '2017-02-13 10:36:54',
                'updated_at' => '2017-02-13 10:36:54'
            ),
            1 => 
            array (
                'id' => 2,
                'offer_1' => '{"price":"","company":""}',
                'offer_2' => '{"price":"","company":""}',
                'offer_3' => '{"price":"","company":""}',
                'offer_4' => '{"price":"","company":""}',
                'offer_5' => '{"price":"","company":""}',
                'type' => '2',
                'upgrade' => '{"1":"0","2":"0","3":"0","4":"0","5":"0"}',
                'score' => '{"1":"0","2":"0","3":"0","4":"0","5":"0"}',
                'created_at' => '2017-02-13 10:36:54',
                'updated_at' => '2017-02-13 10:36:54'
            )
        ));
    }
}
