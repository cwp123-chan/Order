<?php

namespace App;

use http\Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class registerModel extends Model
{
    protected $table = 'pre_admin';

    public function register ($data){
        $username = $data["username"];
        $password = encrypt($data["password"]);
        $showData = registerModel::where("username","=",$data['username'])->get();
        if(count($showData) == 0){
            try {
                $addAdmin = new registerModel;
                $addAdmin->username = $username;
                $addAdmin->password = $password;
                $result = $addAdmin->save();
                if($result){
                    $userInfo = registerModel::where("username","=",$data['username'])->get();
                    $tokenData = [
                        "username"=>$userInfo[0]->username,
                        "password"=>$userInfo[0]->password
                    ];
                    $token = md5(json_encode($tokenData));
                    $user_id = $userInfo[0]->id;
                    $tokenModel = new tokenModel;
                    $tokenModel->user_id = $user_id;
                    $tokenModel->token = $token;
                    $result2 = $tokenModel->save();
                    if($result2){
                        DB::commit();
                        return $tokenModel;
                    }else{
                        $msg = [
                            "status"=>"false",
                            "msg" => "token存入失败"
                        ];
                        return $msg;
                        DB::rollBack();
                    }
                }
            }catch(\Exception $e){
                $msg = [
                    "status"=>"false",
                    "msg" => "用户注册失败"
                ];
                return $msg;
                DB::rollBack();
            }
        }else{
            return [
                "status"=>"false",
                "msg" => "用户名不得重复"
            ];
        }
    }
}
