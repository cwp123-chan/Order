<?php

namespace App\Http\Controllers;

use App\skuModel;
use Illuminate\Http\Request;

class skuController extends Controller
{
    //
    const SKUSTATUS = 4;
    public function showSku(Request $request){
        if(is_numeric(($request->all())["productId"])){
            $data = (new skuModel)->showSku($request->all());
            return $data;
        }
    }
    public function createSku(Request $request){
        if(($request->all())["skuStatus"] < self::SKUSTATUS ){
            $data = (new skuModel)->createSku($request->all());
            return $data;
        }
    }

    public function updataSku(Request $request){
        if(($request->all())["skuStatus"] >= self::SKUSTATUS ){
            return [
                "status"=>"false",
                "msg" => "该库存状态不得为4"
            ];
        }
        if(is_numeric(($request->all())["skuId"]) && ($request->all())["skuStatus"] < self::SKUSTATUS ){
            $data = (new skuModel)->updataSku($request->all());
            return $data;
        }
    }

    public function deleteSku(Request $request){
        if(is_numeric(($request->all())["skuId"]) && is_numeric(($request->all())["productId"]) ){
            $data = (new skuModel)->deleteSku($request->all());
            return $data;
        }else{
            return [
                "status"=>"false",
                "msg"=>"填入的id不正确"
            ];
        }
    }
}
