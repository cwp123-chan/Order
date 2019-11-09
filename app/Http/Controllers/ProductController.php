<?php

namespace App\Http\Controllers;

use App\AdminProductModel;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public const PROSTATUS = 4;
    public function writeLog($user,$content){
        $file = __FILE__;
        (new logMsg)->logWrite("admin.log",$user,$content,$file);
    }

    public function showProduct(Request $request){
        $this->writeLog("superAdmin","正在执行展示商品页面操作");
        if(!empty(($request->all())["detail"])){
            $data = (new AdminProductModel)->showProduct($request->all());
        }else{
            $data = (new AdminProductModel)->showProduct($request->all());
        }
        return $data;
    }
    public function addProduct(Request $request){

        $content = file_get_contents("php://input");
//        return [
//            "status"=>"false",
//            "msg" => "$content"
//        ];
        $content = json_decode($content,true);
//        $this->writeLog("superAdmin","===========================================================================正在执行展示商品页面操作".$content);


        for ($i = 0; $i < count($content["tag"]); $i++) {
            if($content["tag"][$i]["tagStatus"] == self::PROSTATUS){
                $data = [
                    "status"=>"false",
                    "msg" => "该标签状态不得为4"
                ];
                $this->writeLog("superAdmin","该标签状态不得为4");
                return $data;
            }
        }

        for ($i = 0; $i < count($content["sku"]); $i++) {
            if($content["sku"][$i]["skuStatus"] == self::PROSTATUS){
                $data = [
                    "status"=>"false",
                    "msg" => "该库存状态不得为4"
                ];
                $this->writeLog("superAdmin","该库存状态不得为4");
                return $data;
            }
        }
        if($content["productStatus"] == self::PROSTATUS){
            $data = [
              "status"=>"false",
              "msg" => "该商品状态不得为4"
            ];
            $this->writeLog("superAdmin","该商品状态不得为4");
            return $data;
        }
        $data = (new AdminProductModel)->addProduct($content);
        return $data;
    }

    public function updataProduct(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $tagidArr = [];
        for ($i = 0; $i < count($content["tag"]); $i++) {
            $tagidArr[$i] = $content["tag"][$i]["tagId"];
            if($content["tag"][$i]["tagStatus"] == self::PROSTATUS){
                $data = [
                    "status"=>"false",
                    "msg" => "该标签状态不得为4"
                ];
                $this->writeLog("superAdmin","该标签状态不得为4");
                return $data;
            }
        }
        if (count($tagidArr) != count(array_unique($tagidArr))) {
            $data = [
                "status"=>"false",
                "msg" => "tagId重复"
            ];
            return $data;
        }
            for ($i = 0; $i < count($content["sku"]); $i++) {
            if($content["sku"][$i]["skuStatus"] == self::PROSTATUS){
                $data = [
                    "status"=>"false",
                    "msg" => "该库存状态不得为4"
                ];
                $this->writeLog("superAdmin","该库存状态不得为4");
                return $data;
            }
        }
        if($content["productStatus"] == self::PROSTATUS){
            $data = [
                "status"=>"false",
                "msg" => "该商品状态不得为4"
            ];
            $this->writeLog("superAdmin","该商品状态不得为4");
            return $data;
        }
        $data = (new AdminProductModel)->updataProduct($content);
        return $data;
    }

    public function deleteProduct(Request $request){
        $data = (new AdminProductModel)->deleteProduct($request->all());
        return $data;
    }

}
