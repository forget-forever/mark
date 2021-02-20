<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-12 23:56:03 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-09-12 23:56:39
 */


namespace app\api\controller\v1;

header('Access-Control-Allow-Origin:*');


use app\api\model\CheckParams;
use app\api\model\NewLogin;
use app \ api \ model \ Login as LoginModel;
use app\api\model\ExtraLogin;
use think\Controller;
use app\api\lib\Exception\NetworkingException;


/**
 * Class Login 登录类
 * @package app\api\controller\v1
 */
class Login extends Controller
{
    public function login($studentID, $password, $cookie)
    {
        if(strlen($studentID) == 10){
            CheckParams::checkParams($studentID, $password);
            $result = (new LoginModel())->doLogin($studentID, $password, $cookie);
        }else {
            $result = ExtraLogin::doLogin($studentID, $password);
            // $result['cookieEha'] = $cookie;
        }
        if ($result == null){
            throw new NetworkingException();
        }
        return $result;
    }
}