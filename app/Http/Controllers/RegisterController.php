<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\registerModel;
use App\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;
class RegisterController extends Controller
{
    public function register(Request $request){
//        return $request;
        if(!empty($request->username)&&!empty($request->password)){
            if($request->repassword == $request->password){
                $data = (new registerModel)->register($request->all());
                return $data;
            }else{
                return [
                    "status"=>"false",
                    "msg"=>"两次密码不正确"
                ];
            }
        }else{
            return [
                "status"=>"false",
                "msg"=>"用户名或密码不得为空"
            ];
        }
    }
}
