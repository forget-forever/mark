<?php

/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/8/20
 * Time: 22:40
 */

namespace app\api\model;

use think\Db;
use app\api\lib\exception\MissException;

class CheckCookie
{
    public function checkCookie($cookie)
    {
        $url = "http://jw.webvpn.jxust.edu.cn/jsxsd/framework/xsMain_new.jsp?t1=1";
        $result = (new Utils)->curlOther($url, "", $cookie);
        preg_match_all('/<div class="middletopdwxxcont">([^<>\n]+)/', $result, $msg);
        // var_dump($msg);exit;
        if(count($msg[1]) > 0){
            return 'success';
        }else{
            throw new MissException(['code' => 505, 'msg' => '基础信息获取异常', 'errorCode' => 999]);
        }
    }
}
