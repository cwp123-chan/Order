<?php

namespace App\Http\Controllers;

use App\LoginModel;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        if(!empty($request->username)&&!empty($request->password)){
            $data = (new LoginModel)->login($request->all());
            return $data;
        }else{
            return [
                "status"=>"false",
                "msg"=>"用户名或密码不得为空"
            ];
        }
    }
}
