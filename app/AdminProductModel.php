<?php

namespace App;

use App\Http\Controllers\logMsg;
use App\tagModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminProductModel extends Model
{
    protected $table = 'pre_product';
    public const PREDELSTATUS = 4;
    public function writeLog($user,$content){
        $file = __FILE__;
        (new logMsg)->logWrite("admin.log",$user,$content,$file);
    }
    public function showProduct($counts){
        $this->writeLog("superAdmin","展示商品列表");
        if(empty($counts["page"])){
            $this->writeLog("superAdmin","前台未传值到后台");
            $num = 1;
        }else{
            $num = $counts["page"];
        }
        if(empty( $counts["counts"])) {
            $this->writeLog("superAdmin","前台未传值到后台");
            $count = 10;
        }else{
            if($counts["counts"]<=0){
                $this->writeLog("superAdmin","前台传递错误数据当前每页记录数".$counts["counts"]);
                $data = [
                    "status"=>"false",
                    "msg"=>"所需记录参数不正确"
                ];
                return $data;
            }else{
                $count = $counts["counts"];
            }
        }
        $skuData = [];
        $catesData = [];
        $skuDataMsg = [];
        $catesDataMsg = [];
        $tagDataMsg = [];
        $showData = [];
        if(empty( $counts["productId"]) && empty( $counts["categoryId"]) && empty($counts["detail"])){
            $showData = AdminProductModel::where("status","<",self::PREDELSTATUS)->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
        }else if(!empty( $counts["productId"]) && !empty( $counts["categoryId"]) && empty($counts["detail"])){
            $showData = AdminProductModel::where("status","<",self::PREDELSTATUS)->where("id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $skuData = skuModel::where("status","<",skuModel::SKUMDSTATUS)->where("product_id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $catesData = cateModel::where("status","<",cateModel::DELSTATUS)->where("id","=",$counts["categoryId"])->get();

        }else if(!empty( $counts["productId"]) && !empty( $counts["categoryId"]) && !empty($counts["detail"])){
            if($counts["detail"] == 1){
            $showData = AdminProductModel::where("status","<",self::PREDELSTATUS)->where("id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $skuDataMsg = skuModel::where("status","<",skuModel::SKUMDSTATUS)->where("product_id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $catesDataMsg = cateModel::where("status","<",cateModel::DELSTATUS)->where("id","=",$counts["categoryId"])->get();
            $allCatesDataMsg = cateModel::where("status","<",cateModel::DELSTATUS)->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $tagDataMsg = tagModel::where("status","<",tagModel::TAGMDSTATUS)->where("product_id","=",$counts["productId"])->get();
            }else{
                return [
                    "status"=>"false",
                    "msg"=>"功能代码不准确(detail值只能取1或0)"
                ];
            }
        }else{
            $showData = AdminProductModel::where("status","<",self::PREDELSTATUS)->where("id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $skuData = skuModel::where("status","<",skuModel::SKUMDSTATUS)->where("product_id","=",$counts["productId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);

        }


        $total = $showData->toArray()["total"];
        $totalPage = ceil($total/$count);
        // 判断所给 页码 是否符合要求
        if(!empty($counts["page"])) {
            if ($counts["page"] < 0 || $counts["page"] > $totalPage) {
                $this->writeLog("superAdmin","前台传递错误数据当前页码为".$counts["page"]);
                $data = [
                    "status" => "false",
                    "msg" => "所需页码不存在"
                ];
                return $data;
            }
        }
        $showData = $showData->toArray()["data"];
        if(count($skuData) !== 0 && count($catesData) == 0){
            $allData = [
                $showData,$skuData
            ];
        }else if(count($skuData) !== 0 && count($catesData) !== 0){
            $allData = [
                $showData,$skuData,$catesData
            ];
        }else if(count($catesDataMsg) !== 0 && count($showData) !== 0)
        {
            $allData = [
                $showData,
                $skuDataMsg,
                $catesDataMsg,
                $tagDataMsg,
                $allCatesDataMsg
            ];
        }else{
            if(count($showData) == 0 ){
                $showData = [
                    "status"=>"false",
                    "msg" => "该商品不存在"
                ];
                $allData = $showData;
            }else{
                $allData = $showData;
            }
        }

        $data = [
            "status"=>"true",
            "totalPage"=>$totalPage,
            "total"=>$total,
            "data"=>$allData,
        ];
        return $data;
    }
    public function addProduct($data){
        $showData = AdminProductModel::where("name","=",$data['productName'])->get();
        $content = strval($data["productDisc"]);
        if(count($showData) !== 0){
            return [
              "data"=>"商品名字不得重复",
              "status"=>"false"
            ];
        }
        DB::beginTransaction();
        $this->writeLog("superAdmin","当前正在执行的操作====>开启事务");
        try{
            $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_product");
            $product = new AdminProductModel;
            $product->category_id = $data["productCate"];
            $product->name = $data["productName"];
            $product->sale_num = $data["productSale"];
            $product->sort = $data["productSort"];
            $product->content = $content;
            $product->status = $data["productStatus"];
            $result1 = $product->save();
            if($result1){
                $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_product_tag");
                $tagArr = [];
                for ($i = 0; $i < count($data["tag"]); $i++) {
                    $tag = new tagModel;
                    $tag->product_id =  $product["id"];
                    $tag->status = $data["tag"][$i]["tagStatus"];
                    $tag->value = $data["tag"][$i]["tagValue"];
                    $tag->type = $data["tag"][$i]["type"];
                    $result2 = $tag->save();
                    array_push($tagArr,$tag);
                };
                if($result2){
                    $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_sku");
                    $skuArr = [];
                    for ($i = 0; $i < count($data["sku"]); $i++) {
                        $sku = new skuModel;
                        $sku->product_id =  $product["id"];
                        $sku->original_price = $data["sku"][$i]["productOriPrice"];
                        $sku->price = $data["sku"][$i]["productLocPrice"];
                        $sku->attr1 = $data["sku"][$i]["productAttr1"];
                        $sku->attr2 = $data["sku"][$i]["productAttr2"];
                        $sku->attr3 = $data["sku"][$i]["productAttr3"];
                        $sku->quantity = $data["sku"][$i]["productQuantity"];
                        $sku->status = $data["sku"][$i]["skuStatus"];
                        $sku->sort = $data["sku"][$i]["skuSort"];
                        $result3 = $sku->save();
                        array_push($skuArr,$sku);
                    };
                    DB::commit();
                    return $skuArr;
                }else{
                    $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_sku 失败 进行回滚");
                    $msg = [
                        "status"=>"false",
                        "msg" => "库存存入失败"
                    ];
                    return $msg;
                    DB::rollBack();
                }
            }else{
                $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_product_tag 失败 进行回滚");
                $msg2 = [
                    "status"=>"false",
                    "msg" => "标签存入失败"
                ];
                return $msg2;
                DB::rollBack();
            }
//
        }catch(\Exception $e){
            $this->writeLog("superAdmin","当前正在执行的操作====>事务操作失败 进行回滚");
            $msg = [
                "status"=>"false",
                "msg" => "商品存入失败"
            ];
            return $msg;
            DB::rollBack();
        }
    }


    public function updataProduct($data){
        $showData = AdminProductModel::where("name","=",$data['productName'])->get();
        $showId = AdminProductModel::where("id","=",$data['productId'])->get();
        $content = strval($data["productDisc"]);
        if(count($showData) !== 0){
            return [
                "data"=>"商品名字不得重复",
                "status"=>"false"
            ];
        }else if(count($showId) == 0){
            return [
                "data"=>"该商品分类不存在",
                "status"=>"false"
            ];
        }
        DB::beginTransaction();
        $this->writeLog("superAdmin","当前正在执行的操作====>开启事务");
        try{
            $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_product");
            $product = AdminProductModel::where("status","<",self::PREDELSTATUS)->find($data["productId"]);
            $product->category_id = $data["productCate"];
            $product->name = $data["productName"];
            $product->sale_num = $data["productSale"];
            $product->sort = $data["productSort"];
            $product->content = $content;
            $product->status = $data["productStatus"];
            $result1 = $product->save();
            if($result1){
                $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_product_tag");
                for ($i = 0; $i < count($data["tag"]) ; $i++) {

                    // 如果前端的标签中 tagid 不存在 或者 对应 的product_id 不正确 则新增标签
                    $realTag = tagModel::where("id",'=',$data["tag"][$i]["tagId"])->where("product_id","=",$data["productId"])->get();
                    if(count($realTag) !== 0){
                        $tag = tagModel::where("status","<",self::PREDELSTATUS)->find($data["tag"][$i]["tagId"]);
                        $tag->product_id = $product["id"];
                        $tag->status = $data["tag"][$i]["tagStatus"];
                        $tag->value = $data["tag"][$i]["tagValue"];
                        $tag->type = $data["tag"][$i]["type"];
                        $result2 = $tag->save();
                    }else{
                        $tag = new tagModel;
                        $tag->product_id = $product["id"];
                        $tag->status = $data["tag"][$i]["tagStatus"];
                        $tag->value = $data["tag"][$i]["tagValue"];
                        $tag->type = $data["tag"][$i]["type"];
                        $result2 = $tag->save();
                    }
                };
                if($result2){
                    $skuArr = [];
                    for ($i = 0; $i < count($data["sku"]); $i++) {
                        $realData = skuModel::where("id","=",$data["sku"][$i]["skuId"])->get();
                        if(count($realData) !== 0){
                            $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_sku".$data["sku"][0]["skuId"]);
                            $sku = skuModel::where("status","<",self::PREDELSTATUS)->find($data["sku"][$i]["skuId"]);
                            $sku->product_id =  $product["id"];
                            $sku->original_price = $data["sku"][$i]["productOriPrice"];
                            $sku->price = $data["sku"][$i]["productLocPrice"];
                            $sku->attr1 = $data["sku"][$i]["productAttr1"];
                            $sku->attr2 = $data["sku"][$i]["productAttr2"];
                            $sku->attr3 = $data["sku"][$i]["productAttr3"];
                            $sku->quantity = $data["sku"][$i]["productQuantity"];
                            $sku->status = $data["sku"][$i]["skuStatus"];
                            $sku->sort = $data["sku"][$i]["skuSort"];
                            $sku->save();
                            array_push($skuArr,$sku);
                        }else{

                            $sku = new skuModel;
                            $sku->product_id =  $product["id"];
                            $sku->original_price = $data["sku"][$i]["productOriPrice"];
                            $sku->price = $data["sku"][$i]["productLocPrice"];
                            $sku->attr1 = $data["sku"][$i]["productAttr1"];
                            $sku->attr2 = $data["sku"][$i]["productAttr2"];
                            $sku->attr3 = $data["sku"][$i]["productAttr3"];
                            $sku->quantity = $data["sku"][$i]["productQuantity"];
                            $sku->status = $data["sku"][$i]["skuStatus"];
                            $sku->sort = $data["sku"][$i]["skuSort"];
                            $sku->save();
                            array_push($skuArr,$sku);
                        }

                    };
                    DB::commit();
                    return $skuArr;
                }else{
                    $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_sku 失败 进行回滚");
                    $msg = [
                        "status"=>"false",
                        "msg" => "库存存入失败"
                    ];
                    return $msg;
                    DB::rollBack();
                }
            }else{
                $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_product_tag 失败 进行回滚");
                $msg2 = [
                    "status"=>"false",
                    "msg" => "标签存入失败"
                ];
                return $msg2;
                DB::rollBack();
            }

        }catch(\Exception $e){
            $this->writeLog("superAdmin","当前正在执行的操作====>事务操作失败 进行回滚");
            $msg = [
                "status"=>"false",
                "msg" => "商品存入失败"
            ];
            DB::rollBack();
            return $msg;
        }
    }

    public function deleteProduct($data){
        $showId = AdminProductModel::where("id","=",$data['productId'])->get();
        if(count($showId) == 0){
            return [
                "data"=>"该商品分类不存在",
                "status"=>"false"
            ];
        }
        DB::beginTransaction();
        $this->writeLog("superAdmin","当前正在执行的操作====>开启事务");
        try{
            $this->writeLog("superAdmin","当前正在执行的操作====>存入表pre_product");
            $product = AdminProductModel::where("status","<",self::PREDELSTATUS)->find($data["productId"]);
            $product->status = self::PREDELSTATUS;
            $result1 = $product->save();
            if($result1){
                $showTags = tagModel::where("status","<",self::PREDELSTATUS)->where("product_id","=",$data['productId'])->get();
                $this->writeLog("superAdmin","当前正在执行的操作====>删除Tag");
                if(count($showTags) == 0){
                    $this->writeLog("superAdmin","当前正在操作 tag表为空");
                    DB::commit();
                    return [
                        "status"=>"warning",
                        "msg" => "该商品不存在标签,仅删除商品列表"
                    ];
                }
                $tagArr = [];
                for ($i = 0; $i < count($showTags) ; $i++) {
                    $tag = tagModel::where("status","<",self::PREDELSTATUS)->find($showTags[$i]["id"]);
                    $tag->status = self::PREDELSTATUS;
                    $result2 = $tag->save();
                    array_push($tagArr,$tag);
                };
                if($result2){
                    $showSkus = skuModel::where("status","<",self::PREDELSTATUS)->where("product_id","=",$data['productId'])->get();
                    if(count($showSkus) == 0){
                        $this->writeLog("superAdmin","当前正在操作 tag表为空");
                        DB::commit();
                        return [
                            "status"=>"warning",
                            "msg" => "该商品不存在库存,仅删除商品列表与标签"
                        ];
                    }
                    $skuArr = [];
                    for ($i = 0; $i < count($showSkus) ; $i++) {
                        $sku = skuModel::where("status","<",self::PREDELSTATUS)->find($showSkus[$i]["id"]);
                        $sku->status = self::PREDELSTATUS;
                        $result3 = $sku->save();
                        array_push($skuArr,$sku);
                    };
                    DB::commit();
                    return $skuArr;
                }else{
                    $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_sku 失败 进行回滚");
                    $msg = [
                        "status"=>"false",
                        "msg" => "库存存入失败"
                    ];
                    return $msg;
                    DB::rollBack();
                }
            }else{
                $this->writeLog("superAdmin","当前正在执行的操作====>存入 pre_product_tag 失败 进行回滚");
                $msg2 = [
                    "status"=>"false",
                    "msg" => "标签存入失败"
                ];
                return $msg2;
                DB::rollBack();
            }

        }catch(\Exception $e){
            $this->writeLog("superAdmin","当前正在执行的操作====>事务操作失败 进行回滚");
            $msg = [
                "status"=>"false",
                "msg" => "商品存入失败"
            ];
            DB::rollBack();
            return $msg;
        }
    }

}
