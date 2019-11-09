<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
* 日志生成类
* write by vson
* time:2017-06-14
* for:record and check
* */

class logMsg extends Controller
{


    /*
     * 清除一个月之前的日志
     * */

    protected function logDirClear($logDir = '../storage/logs'){
        date_default_timezone_set('PRC');
        if(is_dir($logDir)){
            $dirHandle = opendir($logDir);
            while(($dirName = readdir($dirHandle)) != false){
                $subDir = $logDir.'/'.$dirName;
                if($dirName == '.' || $dirName == '..'){
                    continue;
                }else{
                    $monthDate = date('Y-m-d', strtotime("- 30 day",time()));
                    if(strtotime($monthDate) > strtotime($dirName)){
                        if(is_dir($subDir)){
                            $this->logFileClear($subDir);
                            rmdir($subDir);
                        }
                    }
                }
            }
            closedir($dirHandle);
        }
    }
    protected function logFileClear($fileDir){
        date_default_timezone_set('PRC');
        if(is_dir($fileDir)){
            $fileHandle = opendir($fileDir);
            while(($fileName = readdir($fileHandle)) != false){
                $subDir = $fileDir.'/'.$fileName;
                if($fileName == '.' || $fileName == '..'){
                    continue;
                }else{
                    if(is_dir($subDir)){
                        $this->logFileClear($subDir);
                        rmdir($subDir);
                    }else{
                        unlink($subDir);
                    }
                }
            }
            closedir($fileHandle);
        }
    }
    /*
     * 生成新日志
     * */
    public function logWrite($fileName, $user, $content,$fileList){
        date_default_timezone_set('PRC');
        $this->logDirClear();
        $logDir = '../storage/logs';
        $now = date('Y-m-d');
        $nowDir = $logDir.'/'.$now;
        if(!is_dir($nowDir)){mkdir($nowDir, 0777, true);
        }
        $fileDir = $nowDir.'/'.$fileName;
        $fileContent = $user.'在'.date('Y-m-d H:i:s').'时操作文件'.$fileList.'内容为：'.$content;
        file_put_contents($fileDir, $fileContent."\n====================\n", FILE_APPEND);
    }

}
