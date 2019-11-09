<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\tagModel;
use Faker\Generator as Faker;

$factory->define(tagModel::class, function (Faker $faker) {
    return [
        //
        "product_id"=>\App\AdminProductModel::all()->random()->id,
        "type"=>$faker->numberBetween(1,3),
        "value"=>$faker->randomElement([
            "[\"./tagControllerImgReal/ee683a487d7e3312838409cced7e9e81/2019/11/05/03/2019-11-05-03-04-39.PNG\"]",
            "[\"./tagControllerImgReal/ee683a487d7e3312838409cced7e9e81/2019/11/05/08/2019-11-05-08-58-35.jpg\",\"./tagControllerImgReal/ee683a487d7e3312838409cced7e9e81/2019/11/05/09/2019-11-05-09-06-54.jpg\"]",
            "[\"./tagControllerImgReal/a2769ae0df333ca8568c7601433ab647/2019/11/06/08/2019-11-06-08-27-12.jpg\"]",
            "[\"./tagControllerImgReal/a2769ae0df333ca8568c7601433ab647/2019/11/06/08/2019-11-06-08-36-24.jpg\"]",
            "[\"./tagControllerImgReal/a2769ae0df333ca8568c7601433ab647/2019/11/06/08/2019-11-06-08-39-33.jpg\"]"
        ]),
        "status"=>$faker->numberBetween(0,4),
    ];
});
