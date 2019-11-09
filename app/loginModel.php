<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class loginModel extends Model
{
    //
    protected $table = "pre_admin";
    public function login($data)
    {
        if (!empty($data["token"])) {
            $showData = tokenModel::where("token", "=", $data["token"])->get();
            $showPwd = loginModel::where("username", "=", $data["username"])->get();
            $pwd = decrypt($showPwd[0]->password);

            if (count($showData) !== 0 && $pwd == $data["password"]) {
                return [
                    "status" => "true",
                    "token" => $data["token"],
                    "msg" => "登陆成功"
                ];
            } else {
                return [
                    "status" => "false",
                    "msg" => "用户名密码token错误"
                ];
            }
        } else {
            $showPwd = loginModel::where("username", "=", $data["username"])->get();
            $pwd = decrypt($showPwd[0]->password);
            if (count($showPwd) !== 0 && $pwd == $data["password"]) {
                $userInfo = registerModel::where("username","=",$data['username'])->get();
                $tokenData = [
                    "username"=>$userInfo[0]->username,
                    "password"=>$userInfo[0]->password
                ];
                $token = md5(json_encode($tokenData));
                return [
                    "status" => "true",
                    "token" => $token,
                   "username" => $showPwd[0]->username,
                    "msg" => "登陆成功"
                ];
            } else {
                return [
                    "status" => "false",
                    "msg" => "用户名密码token错误"
                ];
            }
        }
    }
}
