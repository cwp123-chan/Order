<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\navModel;
use Faker\Generator as Faker;

$factory->define(navModel::class, function (Faker $faker) {
    return [
        //
        "position_id" => $faker->numberBetween(0,3),
        "title" => $faker->word,
        "picture"=>$faker->randomElement([
           "[\"./tagControllerImgReal/a2769ae0df333ca8568c7601433ab647/2019/11/06/10/2019-11-06-10-40-54.jpg\"]",
            "[\"./tagControllerImgReal/a2769ae0df333ca8568c7601433ab647/2019/11/06/10/2019-11-06-10-37-09.jpg\"]",
            "[\"./tagControllerImgReal/ee683a487d7e3312838409cced7e9e81/2019/11/05/02/2019-11-05-02-31-24.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-21-40.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-21-40.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-21-55.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-20.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-20.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-25.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-45.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-45.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-22-50.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-08.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-13.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-28.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-28.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-35.jpg\"]",
            "[\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-28.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-35.jpg\",\"./tagControllerImgReal/66503aa8bd6d5b4e22391ea5e6592588/2019/11/07/03/2019-11-07-03-23-48.jpg\"]"

        ]),
        "link_type"=>$faker->numberBetween(0,3),
        "link_target"=>$faker->imageUrl(),
        "status"=>$faker->numberBetween(0,4),
    ];
});
