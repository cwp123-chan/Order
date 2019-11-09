<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\skuModel;
use Faker\Generator as Faker;

$factory->define(skuModel::class, function (Faker $faker) {
    return [
        //
        "product_id"=>\App\AdminProductModel::all()->random()->id,
        "original_price"=>$faker->randomFloat(2,0,10000),
        "price"=>$faker->randomFloat(2,0,10000),
        "attr1"=>$faker->word,
        "attr2"=>$faker->word,
        "attr3"=>$faker->word,
        "quantity"=>$faker->randomNumber(5, false),
        "sort" => $faker->numberBetween(0,1000),
        "status"=>$faker->numberBetween(0,4),
    ];
});
