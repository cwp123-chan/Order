<?php

namespace App;

use App\Http\Controllers\cateController;
use App\Http\Controllers\logMsg;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class cateModel extends Model
{
    protected $table = "pre_category";
    public const DELSTATUS = 4;
    public function writeLog($user,$content){
        $file = __FILE__;
        (new logMsg)->logWrite("admin.log",$user,$content,$file);
    }
    public function addcate($data){
        $showData = cateModel::where("status","<",self::DELSTATUS)->where("name","=",$data['categoryName'])->get();
        $charArr = $data["categoryCharname"][0];

//        echo ($showData);
        if(count($showData) !== 0){
            $status = [
                "status"=>"0",
                "msg" => "分类名称不得重复"
            ];
            return $status;
        }else if($data["categoryStatus"] == self::DELSTATUS){
            $this->writeLog("superAdmin","添加了已被删除的分类");
            $data = [
                "status"=>"0",
                "msg"=>"请填写正确的状态信息"
            ];
            return $data;
        }else{
                $dataArr = [
                "categoryCharname1"=>$charArr['categoryAttr1'],
                "categoryCharname2"=>$charArr['categoryAttr2'],
                "categoryCharname3"=>$charArr['categoryAttr3']
            ];
            $jsonData = json_encode($dataArr);
            $cate = new cateModel;
            $cate->name = $data["categoryName"];
            $cate->property = $jsonData;
            $cate->sort = $data["categorySort"];
            $cate->status = $data["categoryStatus"];
            $cate->save();
//            $msg = $cate->property;
//            $prg = preg_match_all('/^\$/',$msg,$matches);
//            return $msg;

            $status = [
                "status"=>"1",
                "msg" => "数据添加成功",
                "data" => $cate
            ];
            return $status;

        }


    }
    public function showCate($counts){
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
        if(empty($counts["cateId"])){
            $showData = cateModel::where("status","<",self::DELSTATUS)->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
        }else{
            $showData = cateModel::where("status","<",self::DELSTATUS)->where("id","=",$counts["cateId"])->paginate($perPage =$count , $columns = ['*'], $pageName = '', $page = $num);
        }


        $total = $showData->toArray()["total"];
        $totalPage = ceil($total/$count);
        // 判断所给 页码 是否符合要求
        if(!empty($counts["page"])) {
            if ($counts["page"] < 0 || $counts["page"] > $totalPage) {
                $data = [
                    "status" => "false",
                    "msg" => "所需页码不存在"
                ];
                return $data;
            }
        }
        $showData = $showData->toArray()["data"];
//        $showData->property = json_decode($showData->property,true);
//        for($i = 0 ; $i < count($showData); $i++){
//            $showData[$i]->property  = json_decode($showData[$i]->property,true);
//        }
        $data = [
            "status"=>"true",
            "totalPage"=>$totalPage,
            "total"=>$total,
            "data"=>$showData,
        ];
        return $data;
    }

    public function updateCate($data){
        $updataId = cateModel::where("status","<",self::DELSTATUS)->find($data["categoryId"]);
        $charArr = $data["categoryCharname"][0];
        $nameData = cateModel::where("name","=",$data["categoryName"])->get();

        if(empty($updataId)){
            $this->writeLog("superAdmin","分类不存在");
            $data = [
              "status"=>"0",
              "msg"=>"该分类不存在"
            ];
            return $data;
        }else if(count($nameData) !== 0){
            $this->writeLog("superAdmin","分类名发生重复");
            $data = [
                "status"=>"0",
                "msg"=>"分类名不得重复"
            ];
            return $data;
        }else if($data["categoryStatus"] == self::DELSTATUS){
            $this->writeLog("superAdmin","添加了已被删除的分类");
            $data = [
                "status"=>"0",
                "msg"=>"该分类已被删除"
            ];
            return $data;
        }else{
            $this->writeLog("superAdmin","进行更新数据");
            $dataArr = [
                "categoryCharname1"=>$charArr['categoryAttr1'],
                "categoryCharname2"=>$charArr['categoryAttr2'],
                "categoryCharname3"=>$charArr['categoryAttr3']
            ];
            $jsonData = json_encode($dataArr);
            $updataId->name = $data["categoryName"];
            $updataId->property = $jsonData;
            $updataId->sort = $data["categorySort"];
            $updataId->status = $data["categoryStatus"];
            $updataId->save();
            $this->writeLog("superAdmin","数据更新成功");
            return $updataId;
        }
    }

    public function deleteCate($counts){
        $updataId = cateModel::where("status","<",self::DELSTATUS)->find($counts);
        if(empty($updataId)){
            $this->writeLog("superAdmin","正在修改已被删除的分类或本身不存在的分类 已报错");
            $data = [
                "status"=>"0",
                "msg"=>"该分类不存在"
            ];
            return $data;
        }else{
            $updataId->status = self::DELSTATUS;
            $updataId->save();
            $this->writeLog("superAdmin","数据更新成功");
            $data = [
                "status"=>"1",
                "msg"=>"删除成功",
                "data"=>$updataId['id']
            ];
            return $data;
        }
    }
}
