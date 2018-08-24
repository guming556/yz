<?php

namespace App\Modules\Verificationcode\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class VerificationcodeDatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		// $this->call('App\Modules\Verificationcode\Database\Seeds\FoobarTableSeeder');
	}
}
