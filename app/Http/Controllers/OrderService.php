<?php

namespace App\Http\Controllers;
use App\AddressModel;
use App\cartModel;
use App\orderDetialModel;
use App\orderExpressModel;
use App\orderItemModel;
use App\orderProMode;
use App\orderSkuModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\toJSON;

class OrderService extends Controller
{
    //
    CONST EXPRESSNUM = 1001;
    CONST ADDRESSID = 1;
    public function show($content){
//
//        $content = file_get_contents("php://input");
//        $content = json_decode($content,true);
        $addressResult = AddressModel::where("user_id","=",$content["userId"])->find($content["addressId"]);
        if(!empty($addressResult)){
            $cartResult = cartModel::where("user_id","=",$content["userId"])->get(["id","quantity"]);
            $cartSkuId = [];
            $cartArry = [];
            for( $i = 0 ; $i < count($cartResult) ; $i ++){
                $cartArry[$i] = $cartResult[$i]->id;
            }
            $diff = array_diff($content["cartId"],$cartArry);
            if(count($diff) == 0){
                $skuResult = cartModel::whereIn("id",$content["cartId"])->get(["sku_id","id"])->toArray();
                for($j = 0;$j<count($skuResult); $j++){
                    $skuId[$j] = $skuResult[$j]["sku_id"];
                }
                $cartSkuResult = orderSkuModel::whereIn("id",$skuId)->get(["product_id","quantity","version","price","weight"])->toArray();
                if(count($cartSkuResult)){
                    /// ////////////////////////////
                    $num = [];
                    // 检查购物车商品库存是否与sku商品库存一致
                    for ($a = 0; $a<count($content["cartId"]);$a++){
                        $quantityNum = orderSkuModel::where("id","=",$skuResult[$a]["sku_id"])->get(["quantity","price","weight"]);
                        $cartNum = cartModel::where("sku_id","=",$skuResult[$a]["sku_id"])->get("quantity");
                        $num[$a]["sku_id"][$skuResult[$a]["sku_id"]] =$quantityNum[0]["quantity"]-$cartNum[0]["quantity"];
                        $num[$a]["attr"]["price"] = $quantityNum[0]["price"]*$cartNum[0]["quantity"];
                        $num[$a]["attr"]["weight"] = $quantityNum[0]["weight"]*$cartNum[0]["quantity"];
                        if($num[$a]["sku_id"][$skuResult[$a]["sku_id"]] < 0){
                            return response()->json([
                                "status" => false,
                                "data" => "购物车ID为".$skuResult[$a]["id"]."商品库存不足,请检查后提交"
                            ],200);
                        }
                    }
                    // 判断购物车是否全部符合要求
                    if(count($num) == count($content["cartId"])){
                        $expressResult = orderExpressModel::where("id","=",self::EXPRESSNUM)->get();
                        $expressListMoney = [];
                        $expressCate = orderSkuModel::whereIn("id",$skuId)->get();
                        // 计算不同库存所属商品的运费类型
                        for ($s = 0; $s < count($num); $s++){
                            $skuRs = orderSkuModel::where("id","=",key($num[$s]["sku_id"]))->get("product_id");
                            $expressListMoney[$skuRs[0]["product_id"]][$s]["price"] = $num[$s]["attr"]["price"];
                            $expressListMoney[$skuRs[0]["product_id"]][$s]["weight"] = $num[$s]["attr"]["weight"];
                            $expressListMoney[$skuRs[0]["product_id"]][$s]["sku_id"] = key($num[$s]["sku_id"]);
                        }
//                        echo json_encode($expressListMoney);
                        // 将同一个商品进行分组处理运费
                        if(count($expressListMoney)!== 0){
                            $expressAll = [];
                            foreach ($expressListMoney as $key => $value){
                                $money = 0;
                                $wg = 0;
                                foreach ($value as $kk => $vv){
                                    $money += $vv["price"];
                                    $wg += $vv["weight"];
                                    $expressAll[$key]["price"] = $money;
                                    $expressAll[$key]["weight"] = $wg;
                                }
                            }

                            $expressLastMoney = [];
//                            计算运费
                            foreach ($expressAll as $ks=>$vs){
                                if(floatval($vs["price"]) <= $expressResult[0]["min_money"]){
                                    print_r($vs["price"]);
                                    $expressLastMoney[$ks] = 0 ;
                                }else{
                                    $expressLastMoney[$ks] = ($vs["weight"]/$expressResult[0]["weight"]) * $expressResult[0]["fee"];
                                }
                            }
                            // 得到商品运费
                            $allOrder = [];
                            $allOrder["product"] = [
                                $expressAll
                            ];
                            $allOrder["express"] = [
                                $expressLastMoney
                            ];
                            $allOrder["cart"] = [
                                $content
                            ];
                            return $allOrder;
                        }else{
                            return response()->json([
                                "status" => false,
                                "data" => "商品分类不存在"
                            ],200);
                        }
                    }else{
                        return response()->json([
                            "status" => false,
                            "data" => "商品库存不足"
                        ],200);
                    }
                }else{
                    return response()->json([
                        "status" => false,
                        "data" => "该购物车商品不存在"
                    ],200);
                }
            }else{
                return response()->json([
                    "status" => false,
                    "data" => "不存在的购物车ID".json_encode(array_values($diff))
                ],200);
            }
        }else{
            return response()->json([
                "status" => false,
                "data" => "userId与addressId不匹配"
            ],200);
        }
    }
//    生成随机编号
    function get_salt($len=6, $ignoreCase=true)
    {
        //return substr(uniqid(rand()), -$len);
        $discode="123546789wertyupkjhgfdaszxcvbnm".($ignoreCase?'': 'QABCDEFGHJKLMNPRSTUVWXYZ');
        $code_len = strlen($discode);
        $code = "";
        for($j=0; $j<$len; $j++){
            $code .= $discode[rand(0, $code_len-1)];
        }
        return $code;
    }
    public function create(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $class = new OrderService;
        $allData = $class->show($content);
        $userName = User::where("id","=",$content["userId"])->get("name");
        $AddressPeo = AddressModel::where("user_id","=",$content["userId"])->get();
        $productId = array_keys($allData["product"][0]);
        $get = new tagController;
        try{
        $addressData =  $AddressPeo[self::ADDRESSID];
        for($k = 0; $k<count($allData["express"][0]);$k++ ){
            $orderNumber = $get->get_salt(13, true);
            $orderNumberResult = new orderDetialModel;
                $orderNumberResult->number = $orderNumber;
                $orderNumberResult->user_id =$allData["cart"][0]["userId"];
                $orderNumberResult->product_fee = $allData["product"][0][$productId[$k]]["price"];
                $orderNumberResult->express_fee = $allData["express"][0][$productId[$k]];
                $orderNumberResult->total_fee = $allData["product"][0][$productId[$k]]["price"] + $allData["express"][0][$productId[$k]]["price"];
                $orderNumberResult->status = "10";
                $orderNumberResult->delivery_status = "10";
                $orderNumberResult->payment_status = "10";
                $orderNumberResult->receiver_name = $addressData["name"];
                $orderNumberResult->receiver_province = $addressData["province"];
                $orderNumberResult->receiver_city = $addressData["city"];
                $orderNumberResult->receiver_district = $addressData["district"];
                $orderNumberResult->receiver_detail = $addressData["detail"];
                $orderNumberResult->receiver_mobile = $addressData["mobile"];
                $orderResult = $orderNumberResult->save();
                if($orderResult){
                    $proName = orderProMode::where("id","=",$productId[$k])->get(["name","id"]);
                    $skuId = cartModel::whereIn("id",$allData["cart"][0]["cartId"])->get(["sku_id","quantity"])->toArray();
                    $proSkuIdArry = [];
                    for($i = 0;$i<count($skuId);$i++){
                        $proSkuId = orderSkuModel::where("id","=",$skuId[$i]["sku_id"])->where("product_id","=",$proName[0]["id"])->first();
                        if(!empty($proSkuId)){
                            $proSkuIdArry[$i] = $proSkuId;
                            $proCartQuantity = cartModel::where("sku_id","=",$proSkuIdArry[$i]["id"])->get("quantity");
                            $orderItrm = new orderItemModel;
                            $orderItrm->order_id = $orderNumberResult["id"];
                            $orderItrm->product_id = $productId[$k];
                            $orderItrm->product_full_name = $proName[0]["name"];
                            $orderItrm->sku_id = $proSkuIdArry[$i]["id"];
                            $orderItrm->quantity = $proCartQuantity[0]["quantity"];
                            $orderItrm->price = $proSkuIdArry[$i]["price"];
                            $orderItrm->save();
                            DB::commit();
                        }
                    }
                }
        }
        return $orderItrm;
        }catch(\Exception $e){
            $msg = [
                "status"=>"false",
                "msg" => "商品存入失败"
            ];
            return $msg;
            DB::rollBack();
        }
    }
}


