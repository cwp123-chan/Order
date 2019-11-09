<?php

namespace App\Http\Middleware;

use App\Http\Controllers\logMsg;
use Closure;
use Illuminate\Support\Facades\Response;

class AdminApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function writeLog($user,$content){
        $file = __FILE__;
        (new logMsg)->logWrite("admin.log",$user,$content,$file);
    }
    public function handle($request, Closure $next)
    {
        $this->writeLog("superAdmin","===========================================================================正在执行展示商品页面操作".strlen($request->all()["token"]));

        if(strlen($request->all()["token"]) !== 4){

        // 利用中间件 给 header 加上 Access-Control-Allow-Origin 等头信息 解决跨域问题
                    $response = $next($request);
                    $origin = $request->server('HTTP_ORIGIN') ? $request->server('HTTP_ORIGIN') : '';
                    // 这里设置 允许 访问 的内容
                    $allow_origin = [
                        'http://127.0.0.1:8080',
                        '*'
                    ];
                    if (in_array($origin, $allow_origin)) {
                        $response->header('Access-Control-Allow-Origin', "*");
                        $response->header('Access-Control-Allow-Headers', '*');
                        $response->header('Access-Control-Expose-Headers', 'Authorization, authenticated');
                        $response->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, OPTIONS');
                        $response->header('Access-Control-Allow-Credentials', 'true');
                    }
                    return $response;
                }
    }
}
