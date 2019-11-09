<?php

namespace App;

use App\Http\Controllers\tagController;
use Illuminate\Database\Eloquent\Model;

class navModel extends Model
{
    //
    protected $table = "pre_nav";
    const navList = [
        "0"=> "顶部导航",
        "1"=> "banner图",
        "2"=> "icon",
        "3"=> "4张大图"
    ];
    const linkList = [
        "0"=> "商品分类页面",
        "1"=> "商品购买页面",
        "2"=> "商品活动页面",
        "3"=> "店铺"
    ];
    const NAVSTATUS = 4;
    public function showNav($counts){
        if(empty($counts["page"])){
            $num = 1;
        }else{
            $num = $counts["page"];
        }
        if(empty( $counts["counts"])) {
            $count = 10;
        }else{
            if($counts["counts"]<=0){
                $data = [
                    "status"=>"false",
                    "msg"=>"所需记录参数不正确"
                ];
                return $data;
            }else{
                $count = $counts["counts"];
            }
        }
        if(empty($counts["id"])){
            $showData = navModel::where("status","<",self::NAVSTATUS)->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
            $cl = new tagController;
            for($k = 0;$k<count($showData);$k++){
                $picArr = [];
                for($i = 0 ; $i < count(json_decode($showData[$k]->picture)); $i ++){
                    $adrr =  explode(".",json_decode($showData[$k]->picture)[$i])[1].'.'.explode(".",json_decode($showData[$k]->picture)[$i])[2];
                    $picArr[$i] = $cl->thisUrl . $adrr;
                }
                $showData[$k]->picture = $picArr;
            }

            $allData = [
                $showData,
                self::navList,
                self::linkList
            ];
            return $allData;
        }else{
            $showData = navModel::where("status","<",self::NAVSTATUS)->where("id","=",$counts["id"])->get();
            $cl = new tagController;
            $picArr = [];
            for($i = 0 ; $i < count(json_decode($showData[0]->picture)); $i ++){
               $adrr =  explode(".",json_decode($showData[0]->picture)[$i])[1].'.'.explode(".",json_decode($showData[0]->picture)[$i])[2];
              $picArr[$i] = $cl->thisUrl . $adrr;
            }
            $showData[0]->picture = $picArr;
            $allData = [
                $showData,
                self::navList,
                self::linkList
            ];
            return $allData;
        }

    }

    public function createNav($data){
            $navData = new navModel;
            $navData->position_id = $data["positionId"];
            $navData->title = $data["title"];
            $navData->picture = $data["picture"];
            $navData->link_type = $data["linkType"];
            $navData->link_target = $data["linkTarget"];
            $navData->status = $data["status"];
            $navData->save();
            return $navData;
    }

    public function updataNav($data){
             $tagId = navModel::where("id","=",$data["id"])->where("status","<",self::NAVSTATUS)->get();
             if(count($tagId) == 0){
                 return [
                     "status"=>false,
                     "msg"=>"该导航不存在"
                 ];
             }
             $navData = navModel::find($data["id"]);
            $navData->position_id = $data["positionId"];
            $navData->title = $data["title"];
            $navData->picture = $data["picture"];
            $navData->link_type = $data["linkType"];
            $navData->link_target = $data["linkTarget"];
            $navData->status = $data["status"];
            $navData->save();
            return $navData;
    }

    public function deleteNav($data){
        $tagId = navModel::where("id","=",$data["id"])->where("status","<",self::NAVSTATUS)->get();
        if(count($tagId) == 0){
            return [
                "status"=>false,
                "msg"=>"该导航不存在"
            ];
        }
            $navData = navModel::find($data["id"]);
            $navData->status = self::NAVSTATUS;
            $navData->save();
            return $navData;
    }
}
