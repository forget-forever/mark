<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-07 22:04:06 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-11-07 23:00:51
 */

namespace app\api\model;

use think\Db;
use app\api\lib\Exception\MissException;
use app\api\lib\Exception\IDPasswordException;
use app\api\lib\Exception\IpStop;
use app\api\lib\Exception\PasswordEasyException;

class Loginvpn{
    public function doLogin($studentID, $password){
        $cookievpn = $this->vpnLogin($studentID, $password);
        // $result = $this->jwLogin($studentID, $password, $cookievpn);
        return $cookievpn;
    }
    public function vpnLogin($studentID, $password){
        $res = Db::table('vpn_list')->select();
        return $res[0]['value'];
        // $cookieSource = get_headers('http://webvpn.jxust.edu.cn/users/sign_in' , 1);
        // var_dump($cookieSource);
        $cookievpn = (new GetCookie())->newGetCookie('http://webvpn.jxust.edu.cn/');
        // echo $cookievpn;
        // exit;
        $page = (new Utils())->curlOther('http://webvpn.jxust.edu.cn/users/sign_in', '', $cookievpn);
        $inputValue = [];
        preg_match_all('/<input.*?>(.*?)/ism', $page, $inputValue);
        preg_match_all('/(value=")([^\r\n]+)(")/i', $inputValue[0][1], $authenticity_tokenSource);
        $authenticity_token = $authenticity_tokenSource[2][0];
        // var_dump($inputValue);
        // echo $authenticity_token;
        $cookievpn = (new GetCookie())->curlCookie($page);
        // echo $cookievpn;
        // exit;
        $data = ['utf8'=>'✓', 'authenticity_token' => $authenticity_token, 
        'user[login]' => $studentID, 'user[password]' => $password,
        'user[dymatice_code]' => 'unknown', 'commit' => '登录 Login'];
        $data = http_build_query($data);
        $page = (new Utils())->curl('http://webvpn.jxust.edu.cn/users/sign_in', $data, $cookievpn);
        // 重定向获取链接和新的cookie
        // echo $page;exit;
        if(strpos($page, "用户名或密码错误")||strpos($page, "帐号已被锁定")||strpos($page, "IP登录尝试次数过多")){
            throw new IDPasswordException();
        }
        $url = (new Utils())->getLocation($page);
        $cookievpn = (new GetCookie())->curlCookie($page);
        $page = (new Utils())->curlOther($url[2][0], '', $cookievpn);
        $url = (new Utils())->getLocation($page);
        $cookievpn = (new GetCookie())->curlCookie($page);
        $page = (new Utils())->curlOther($url[2][0], '', $cookievpn);
        if(!strpos($page, "退出登录")){
            $url = (new Utils())->getLocation($page);
            $cookievpn = (new GetCookie())->curlCookie($page);
            $page = (new Utils())->curlOther($url[2][0], '', $cookievpn);
        }
        // $url = (new Utils())->getLocation($page);
        // $cookievpn = (new GetCookie())->curlCookie($page);
        // $page = (new Utils())->curlOther($url[2][0], '', $cookievpn);
        // echo $url[2][0];
        // exit;
        // echo $page;exit;
        if(strpos($page, "退出登录")){
            $cookievpn = (new GetCookie())->getMoreCookie($page);
            return $cookievpn;
        }else{
            return false;
        }
    }
    
    
}
?>