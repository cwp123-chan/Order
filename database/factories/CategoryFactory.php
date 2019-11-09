<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\cateModel;
use Faker\Generator as Faker;
use Illuminate\Support\Str;  //用于加载字符类型的

$factory->define(cateModel::class, function (Faker $faker) {
    return [
        //
        "name"=>$faker->word,
        "property"=>$faker->randomElement([
            "{\"categoryCharname1\":\"\u89c4\u683c\",\"categoryCharname2\":\"\u54c1\u724c\",\"categoryCharname3\":\"\u5927\u5c0f\"}",
            "{\"categoryCharname1\":\"\u5185\u5b58\",\"categoryCharname2\":\"\u5c3a\u5bf8\",\"categoryCharname3\":\"\u5206\u8fa8\u7387\"}",
            "{\"categoryCharname1\":\"\u5c3a\u5bf8\",\"categoryCharname2\":\"\u4e09\u56f4\",\"categoryCharname3\":\"\u989c\u8272\"}",
            "{\"categoryCharname1\":\"\u96f6\u98df\",\"categoryCharname2\":\"\u96f6\u98df\",\"categoryCharname3\":\"\u96f6\u98df\"}",
            "{\"categoryCharname1\":\"\u6280\u80fd\",\"categoryCharname2\":\"\u540d\u79f0\",\"categoryCharname3\":\"\u542b\u91cf\"}",
            "{\"categoryCharname1\":\"\u5927\u5e45\u5ea6\",\"categoryCharname2\":\"\u963f\u8428\u5fb7\",\"categoryCharname3\":\"\u4f46\u662f\"}"
        ]),
        "sort" => $faker->numberBetween(0,1000),
        "status"=>$faker->numberBetween(0,4),
    ];
});
