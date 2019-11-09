<?php

namespace App\Http\Controllers;

use App\tagModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class tagController extends Controller
{
    //
    const TAGSTATUS = 4;
    public $thisUrl = "http://127.0.0.1:8000";
    public function showTag(Request $request){
        if(!empty($request->all()) && !empty(($request->all())["productId"])){
             $data = (new tagModel)->showTag($request->all());
             return $data;
         }else {
            $data = (new tagModel)->showAddTag();
            return $data;
        }
    }
    public function createTag(Request $request){
        if(($request->all())["tagStatus"] < self::TAGSTATUS ){
            $data = (new tagModel)->createTag($request->all());
            return $data;
        }
    }

    public function updataTag(Request $request){
        if(($request->all())["tagStatus"] < self::TAGSTATUS ){
            $data = (new tagModel)->updataTag($request->all());
            return $data;
        }else {
            return [
                "status"=>false,
                "msg"=>"该标签不存在"
            ];
        }
    }

    public function deleteTag(Request $request){
        if(is_numeric(($request->all())["tagId"]) && is_numeric(($request->all())["productId"]) ){
            $data = (new tagModel)->deleteTag($request->all());
            return $data;
        }else{
            return [
                "status"=>"false",
                "msg"=>"填入的id不正确"
            ];
        }
    }

    /**@name 获取随机字符串，大小写字母+数字，转变不区分大小写
     *@param $len 长度
     *@param $ignoreCase 忽略大小写
     * */
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

// 传入临时文件夹 返回key
// 这边 模拟 token 为 md5(当前 日期)
    public function picUpload(Request $request){
        // 判断前台数据是否为post
        if ($request->isMethod('POST')) {
            $fileChar = $request->file("name");
            if($fileChar->isValid()){
                //获取文件的扩展名
                $ext = $fileChar->getClientOriginalExtension();
                //获取文件的绝对路径
                $path = $fileChar->getRealPath();
                // 假设一个token值
                $token = md5(date('Y-m-d'));
                //定义文件名
                $get = new tagController;
                $filename = $get->get_salt(13, true).'.'.$ext;
                // 定义 新地址的目录路径
                $dirname = date('Y/m/d/h/');

                if(is_dir('./tagControllerImgTmp/'.$token.'/'.$dirname)){
                    $newDir = './tagControllerImgTmp/'.$token.'/'.$dirname;
                    $returnDir = '/tagControllerImgTmp/'.$token.'/'.$dirname.$filename;
                    // 单个图片的key值

                    $key = md5($newDir.$filename);
                    // 启用 redis;
                    // 需要服务端启用 redis

                    Redis::set($key,$newDir.$filename);

                    $values = Redis::get($key);
                    // 将 临时文件移到 自己定义的文件夹中 并返回给前端 key

                    copy($path,$newDir.$filename);

                    unlink($path);

                    return [
                        "status"=>"true",
                        "key"=>$key,
                        "value"=>$values,
                        "tmp"=>(new tagController)->thisUrl.$returnDir
                    ];
                }else{
                    mkdir('./tagControllerImgTmp/'.$token.'/'.$dirname,0700,true);
                    $newDir = './tagControllerImgTmp/'.$token.'/'.$dirname;
                    $returnDir = '/tagControllerImgTmp/'.$token.'/'.$dirname.$filename;
                    // 单个图片的key值
                    $key = md5($newDir.$filename);
                    Redis::set($key,$newDir.$filename);
                    $values = Redis::get($key);
                    copy($path,$newDir.$filename);
                    unlink($path);
                    return [
                        "status"=>"true",
                        "key"=>$key,
                        "tmp"=>(new tagController)->thisUrl.$returnDir
                    ];
                }
            }else{
                return [
                    "status"=>"false",
                    "msg" =>"文件不存在"
                ];
            }
        }else{
            return [
              "status"=>"false",
                "msg" =>"不支持的传输类型"
            ];
        }
    }
    // 加入正式文件夹 存入路径给数据库
    public function pushPicUpload(Request $request){
        if(empty($request->all())){
            return [
                "status"=>"false",
                "msg" => "请输入值"
            ];
        }else{
            $theNewPathAll = [];
            //循环查询前端的key是否存在redis中 并创建新的文件目录启用 文件目录后 传到数据库中
            for ($i = 0; $i < count($request->all()["key"]); $i++){
                if(($request->all()["key"])[$i]){
                    $oldPath = Redis::get(($request->all()["key"])[$i]);
                    $tokenPath = explode("/",explode("./tagControllerImgTmp/",$oldPath)[1]);
                    $picSuffix = $tokenPath[5];
                    $tokenPath[5] = "";
                    $tokenPath = implode("/",$tokenPath);
                    if(!empty($oldPath)){
                        if(is_dir('./tagControllerImgReal/'.$tokenPath)){
                            $newPicPath = './tagControllerImgReal/'.$tokenPath.$picSuffix;
                            copy($oldPath,$newPicPath);
                            $theNewPathAll[$i] = $newPicPath;
                        }else{
                            mkdir('./tagControllerImgReal/'.$tokenPath,0700,true);
                            $newPicPath = './tagControllerImgReal/'.$tokenPath.$picSuffix;
                            copy($oldPath,$newPicPath);
                            $theNewPathAll[$i] = $newPicPath;
                        }

                    }else{
                        return [
                          "status"=>"false",
                          "msg" => "key值不正确"
                        ];
                    }
                }else{
                    return [
                        "status"=>"false",
                        "msg"=>"请传入正确的key值"
                    ];
                }
            }
            if($request->all()["func"] == "create"){
                // 判断前台的操作 如果为 床架 就进入创建表
                return $theNewPathAll;

            }else if($request->all()["func"] == "upload"){
                // 如果是 更新 就进入更新表
                return $theNewPathAll;
                // 最后 如果上传成功 删除临时文件
            }else{
                return [
                    "status"=>"false",
                    "msg" => "创建失败"
                ];
            }
        }

    }

}
