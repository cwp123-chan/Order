<?php

namespace App\Http\Controllers;

use App\cateModel;
use Illuminate\Http\Request;

class cateController extends Controller
{
    public function addCate(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $data = (new cateModel)->addcate($content);
        return $data;
    }
    // 展示 所有分类
    public function showCate(Request $request){
        $data = (new cateModel)->showCate($request->all());
        return $data;
    }
    // 更新 分类
    public function updateCate(Request $request){
        $content = file_get_contents("php://input");
        $content = json_decode($content,true);
        $data = (new cateModel)->updateCate($content);
        return $data;
    }
    public function deleteCate(Request $request){
        $data = (new cateModel)->deleteCate($request->categoryId);
        return $data;
    }
}
