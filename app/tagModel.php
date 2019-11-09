<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tagModel extends Model
{
  protected $table = "pre_product_tag";
    const TAGMDSTATUS = 4;
  public function showTag($data){
      $ProId = AdminProductModel::where("id", "=", $data["productId"])->where("status", "<", AdminProductModel::PREDELSTATUS)->get();
      if (count($ProId) == 0) {
          return [
              "status" => "false",
              "msg" => "该商品不存在"
          ];
      } else {
          $data = tagModel::where("status","<",self::TAGMDSTATUS)->where("product_id","=",$data["productId"])->get();
            for ($i = 0 ; $i < count($data) ; $i ++ ){
                switch($data[$i]["type"]){
                    case "1" : $data[$i]["type"] = "保质期";break;
                    case "2" : $data[$i]["type"] = "促销宣传语";break;
                    case "3" : $data[$i]["type"] = "图片地址";break;
                    case "4" : $data[$i]["type"] = "其他";break;
                    default : $data[$i]["type"] = "该功能正在制作";break;
                }
                switch($data[$i]["status"]){
                    case "1" : $data[$i]["status"] = "上架";break;
                    case "2" : $data[$i]["status"] = "下架";break;
                    case "3" : $data[$i]["status"] = "备货";break;
                    case "4" : $data[$i]["status"] = "删除";break;
                    default : $data[$i]["status"] = "该功能正在制作";break;
                }

            }
          return $data;

      }
  }

  public function showAddTag(){
      return [
          ["type"=>1,"msg"=>"保质期"],
          ["type"=>2,"msg"=>"描述"],
          ["type"=>3,"msg"=>"图片"],
      ];
  }

  public function createTag($data)
  {
      $ProId = AdminProductModel::where("id", "=", $data["productId"])->where("status", "<", AdminProductModel::PREDELSTATUS)->get();
      if (count($ProId) == 0) {
          return [
              "status" => "false",
              "msg" => "该商品不存在"
          ];
      } else {
          $tagData = new tagModel;
          $tagData->product_id = $data["productId"];
          $tagData->type = $data["type"];
          $tagData->value = $data["tagValue"];
          $tagData->status = $data["tagStatus"];
          $tagData->save();
          return $tagData;
      }
  }


    public function updataTag($data)
    {
        $tagId = tagModel::where("id","=",$data["tagId"])->where("status","<",self::TAGMDSTATUS)->get();

        $ProId = AdminProductModel::where("id", "=", $data["productId"])->where("status", "<", AdminProductModel::PREDELSTATUS)->get();
        if (count($ProId) == 0) {
            return [
                "status" => "false",
                "msg" => "该商品不存在"
            ];
        }else if(count($tagId) == 0){
            return [
                "status" => "false",
                "msg" => "该标签不存在"
            ];
        }else {
        $tagData = tagModel::find($data["tagId"]);
        $tagData->product_id = $data["productId"];
        $tagData->type = $data["type"];
        $tagData->value = $data["tagValue"];
        $tagData->status = $data["tagStatus"];
        $tagData->save();
        return $tagData;
    }

    }

    public function deleteTag($data){
        $tagId = tagModel::where("id","=",$data["tagId"])->where("status","<",self::TAGMDSTATUS)->get();
        $ProId = AdminProductModel::where("id","=",$data["productId"])->where("status","<",AdminProductModel::PREDELSTATUS)->get();

        if(count($ProId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该商品不存在"
            ];
        }else if(count($tagId) == 0){
            return [
                "status"=>"false",
                "msg"=>"该标签不存在"
            ];
        }else{
            $skuData = tagModel::find($data["tagId"]);
            $skuData->product_id = $data["productId"];
            $skuData->status = self::TAGMDSTATUS;
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
