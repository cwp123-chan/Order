<?php

namespace App\Http\Controllers;

use App\loginModel;
use Illuminate\Http\Request;

class loginController extends Controller
{
    //
    public function login(Request $request){
        if(!empty($request->username)&&!empty($request->password)){
            $data = (new loginModel)->login($request->all());
            return $data;
        }else{
            return [
                "status"=>"false",
                "msg"=>"用户名或密码不得为空"
            ];
        }
    }
}
