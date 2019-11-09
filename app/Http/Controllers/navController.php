<?php

namespace App\Http\Controllers;

use App\navModel;
use Illuminate\Http\Request;

class navController extends Controller
{
    //
    public function showNav(Request $request){
        $data = (new navModel)->showNav($request->all());
        return $data;
    }

    public function createNav(Request $request){
        $data = (new navModel)->createNav($request->all());
        return $data;
    }

    public function updataNav(Request $request){
        $data = (new navModel)->updataNav($request->all());
        return $data;
    }

    public function deleteNav(Request $request){
        $data = (new navModel)->deleteNav($request->all());
        return $data;
    }
}
