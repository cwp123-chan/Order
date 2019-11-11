<?php

namespace App\Http\Controllers;

use App\CateModel;
use Illuminate\Http\Request;

class CateController extends Controller
{
    public function addCate(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $data = (new CateModel)->addcate($content);
        return $data;
    }
    // 展示 所有分类
    public function showCate(Request $request){
        $data = (new CateModel)->showCate($request->all());
        return $data;
    }
    // 更新 分类
    public function updateCate(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $data = (new CateModel)->updateCate($content);
        return $data;
    }
    public function deleteCate(Request $request){
        $data = (new CateModel)->deleteCate($request->categoryId);
        return $data;
    }
}
