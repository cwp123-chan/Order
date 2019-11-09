<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class skuModel extends Model
{
    const SKUMDSTATUS = 4;

    protected $table = 'pre_sku';
    public function showSku($data){
        $ProId = AdminProductModel::where("id", "=", $data["productId"])->where("status", "<", AdminProductModel::PREDELSTATUS)->get();
        if (count($ProId) == 0) {
            return [
                "status" => "false",
                "msg" => "该商品不存在"
            ];
        } else {
            $data = skuModel::where("status", "<", self::SKUMDSTATUS)->where("product_id", "=", $data["productId"])->get();
            for ($i = 0; $i < count($data); $i++) {
                switch ($data[$i]["status"]) {
                    case "1" :
                        $data[$i]["status"] = "上架";
                        break;
                    case "2" :
                        $data[$i]["status"] = "下架";
                        break;
                    case "3" :
                        $data[$i]["status"] = "备货";
                        break;
                    case "4" :
                        $data[$i]["status"] = "删除";
                        break;
                    default :
                        $data[$i]["status"] = "该功能正在制作";
                        break;
                }

            }
            return $data;
        }
    }
    public function createSku($data){
//        $skuId = skuModel::where("id","=",$data["skuId"])->get();
        $ProId = AdminProductModel::where("id","=",$data["productId"])->where("status","<",AdminProductModel::PREDELSTATUS)->get();
        if(count($ProId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该商品不存在"
            ];
        }else{
            $skuData = new skuModel;
            $skuData->product_id = $data["productId"];
            $skuData->original_price = $data["productOriPrice"];
            $skuData->price = $data["productLocPrice"];
            $skuData->attr1 = $data["productAttr1"];
            $skuData->attr2 = $data["productAttr2"];
            $skuData->attr3 = $data["productAttr3"];
            $skuData->quantity = $data["productQuantity"];
            $skuData->sort = $data["skuSort"];
            $skuData->status = $data["skuStatus"];
            $skuData->save();
            return $skuData;
        }


    }
    public function updataSku($data){
        $skuId = skuModel::where("id","=",$data["skuId"])->where("status","<",self::SKUMDSTATUS)->get();
        $ProId = AdminProductModel::where("id","=",$data["productId"])->where("status","<",AdminProductModel::PREDELSTATUS)->get();
        if(count($ProId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该商品不存在"
            ];
        }else if(count($skuId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该分类不存在"
            ];
        }else{
            $skuData = skuModel::find($data["skuId"]);
            $skuData->product_id = $data["productId"];
            $skuData->original_price = $data["productOriPrice"];
            $skuData->price = $data["productLocPrice"];
            $skuData->attr1 = $data["productAttr1"];
            $skuData->attr2 = $data["productAttr2"];
            $skuData->attr3 = $data["productAttr3"];
            $skuData->quantity = $data["productQuantity"];
            $skuData->sort = $data["skuSort"];
            $skuData->status = $data["skuStatus"];
            $skuData->save();
            return $skuData;
        }


    }

    public function deleteSku($data){
        $skuId = skuModel::where("id","=",$data["skuId"])->where("status","<",self::SKUMDSTATUS)->get();
        $ProId = AdminProductModel::where("id","=",$data["productId"])->where("status","<",AdminProductModel::PREDELSTATUS)->get();

        if(count($ProId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该商品不存在"
            ];
        }else if(count($skuId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该分类不存在"
            ];
        }else{
            $skuData = skuModel::find($data["skuId"]);
            $skuData->product_id = $data["productId"];
            $skuData->status = self::SKUMDSTATUS;
            $st = $skuData->save();
            if($st){
                return [
                    "status"=>true,
                    "id"=>$skuData["id"]
                ];
            }
        }
    }
}
