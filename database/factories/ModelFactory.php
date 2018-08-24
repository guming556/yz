<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Modules\User\Model\UserDetailModel::class, function (Faker\Generator $faker) {
    return [
        'uid' => 12,
        'realname' => $faker->name,
        'realname_status' => 0,
        'sex' => 0,
        'mobile' => $faker->phoneNumber,
        'mobile_status' => 0,
        'nickname' => $faker->name,
        'qq' => $faker->numberBetween(100000000, 999999999),
        'qq_status' => 0,
        'wechat' => $faker->name,
        'wechat_status' => 0,
        'card_number' => $faker->numberBetween(430100000000000000, 430199999999999999),
        'province' => $faker->numberBetween(1, 36),
        'city' => $faker->city,
        'area' => $faker->shuffleArray(['深圳', '上海', '广州']),
        'avatar' => 'attachment/user/2017/02/17/3adcf4c45094552588b36c63459c4be3.jpg',
        'avatar' => $faker->sentences,
        'introduce' => $faker->sentences,
        'sign' => $faker->sentences,
    ];
});
