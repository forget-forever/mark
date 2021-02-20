<?php
/**
 * Created by visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/7/20
 * Time: 14:10
 */

namespace app\api\model;

use app\api\lib\Exception\NetworkException;
use app\api\lib\Exception\ParameterException;
use think\Db;

class ExtraLogin
{
    public static function doLogin($studentID, $password)
    {
        $result = [];
        $msg = Db::table('extra_info')
        ->where('studentID', $studentID)
        ->select();
        if(!$msg){
            throw new ParameterException();
        }
        if($msg[0]['password'] == $password){
            $result['name'] = $msg[0]['name'];
            $result['studentID']  = $msg[0]['studentID'];
            $result['school'] = $msg[0]['school'];
            $result['profession'] = $msg[0]['profession'];
            $result['class'] = $msg[0]['class'];
        }else{
            throw new NetworkException();
        }
        return $result;
    }
}