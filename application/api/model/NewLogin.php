<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-12 19:20:15 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-11-07 23:00:12
 */

namespace app\api\model;

use think\Db;
use app\api\lib\Exception\MissException;
use app\api\lib\Exception\IDPasswordException;
use app\api\lib\Exception\IpStop;
use app\api\lib\Exception\PasswordEasyException;
use app\api\lib\Exception\FirstLoginException;

class NewLogin
{
    public function doLogin($student, $password, $cookie)
    {
        $result = [];
        // 从前端获取到了办事大厅的cookie的话就不需要用账号和密码登陆了
        if(strlen($cookie) > 10){
            $result = $this->curlJW($cookie, $student, $password, true);
        }else {
            $result = $this->firstLogin($student, $password);
        }
        // $result = $this->firstLogin($student, $password);
        return $result;
        // $res = Db::table('login_info')->where('studentID', $student)->select();
        // $result = [];
        // if (count($res) > 0) {
        //     ini_set('date.timezone', 'Asia/Shanghai');
        //     $time = date('Y-m-d G:i:s');
        //     if (((strtotime($time) - strtotime($res[0]['time'])) < (60 * 60 * 24 * 6)) && ($res[0]['password'] == $password)) {
        //         $result = $this->curlJW($res[0]['cookieEhall'], $student, $password, true);
        //     } else {
        //         $result = $this->firstLogin($student, $password);
        //     }
        // } else {
        //     $result = $this->firstLogin($student, $password);
        // }
        // return $result;
    }
    public function firstLogin($student, $password)
    {   
        // $cookievpn = (new Loginvpn())->vpnLogin($student, $password);
        // if(!$cookievpn){
        //     $cookievpn = (new Loginvpn())->vpnLogin($student, $password);
        // }
        // if(!$cookievpn) {
        //     throw new MissException(['code' => 505, 'msg' => 'vpn登陆异常', 'errorCode' => 999]);
        // }
        // 之前是加了数据库存储的，定时任务刷新，现在不必了
        // $sql = "SELECT * FROM `cookievpn` where `fail` <= 3";
        // $res = Db::query($sql);
        // $cookievpn = '';
        // for($i = 0; $i < count($res); $i++){
        //     $page = (new Utils())->curlOther('http://webvpn.jxust.edu.cn/', '', $res[$i]['cookievpn']);
        //     if(strpos($page, '网上办事大厅')){
        //         Db::table('cookievpn')->where('id', $res[$i]['id'])->update(['lastTime' => $time]);
        //         $cookievpn = $res[$i]['cookievpn'];
        //     }
        //     if($cookievpn != ''){
        //         break;
        //     }
        // }
        // if($cookievpn == ''){
        //     $cookievpn = (new Loginvpn())->vpnLogin('1420172578', 'z123654');
        // }
        $page = (new Utils())->curl('http://authserver.webvpn.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fehall.jxust.edu.cn%2Flogin%3Fservice%3Dhttp%3A%2F%2Fehall.jxust.edu.cn%2Fnew%2Findex.html', '', '');
        preg_match_all("/(Set-Cookie: )([^\r\n]+)()/i", $page, $matches);
        // echo $page;exit;
        // var_dump($matches);exit;
        if(strpos($page, "http://webvpn.jxust.edu.cn:80/vpn_key/update")){
            // return $this -> firstLogin($student, $password);
            // 不要这样递归，容易出事
            throw new MissException(['code' => 503, 'msg' => '办事大厅的cookie无法获取', 'errorCode' => 999]);
        }
        $cookieEha = $matches[2][0] . ';' . $matches[2][1] . ';' . 'org.springframework.web.servlet.i18n.CookieLocaleResolver.LOCALE=zh_CN;';
        $page = (new Utils())->curl('http://authserver.webvpn.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fehall.jxust.edu.cn%2Flogin%3Fservice%3Dhttp%3A%2F%2Fehall.jxust.edu.cn%2Fnew%2Findex.html', '', $cookieEha);
        // echo $cookieEha;exit;
        if(strpos($page, 'IP被冻结')){
            throw new IpStop();
        }
        $inputValue = [];
        preg_match_all('/<input.*?>(.*?)/ism', $page, $inputValue);
        preg_match_all('/(value=")([^\r\n]+)(")/i', $inputValue[0][0], $ltSource);
        preg_match_all('/(value=")([^\r\n]+)(")/i', $inputValue[0][3], $executionSource);
        // var_dump($inputValue);
        $lt = $ltSource[2][0];
        $execution = $executionSource[2][0];
        $sql = "SELECT * FROM `source1_1`";
        $source = Db::query($sql);
        $captchaResponse = (new GetVcode())->getHec('image.jpg', 1, $source, $cookieEha);
        if (!$captchaResponse) {
            throw new MissException(['code' => 502, 'msg' => '验证码识别异常', 'errorCode' => 999]);
        }
        // echo $captchaResponse;
        // exit;
        $data = [
            'username' => $student,
            'password' => $password,
            'captchaResponse' => $captchaResponse,
            'rememberMe' => 'on',
            'lt' => $lt,
            'dllt' => 'userNamePasswordLogin',
            'execution' => $execution,
            '_eventId' => 'submit',
            'rmShown' => '1'
        ];
        $data = http_build_query($data);
        $page = (new Utils())->curl('http://authserver.webvpn.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fehall.jxust.edu.cn%2Flogin%3Fservice%3Dhttp%3A%2F%2Fehall.jxust.edu.cn%2Fnew%2Findex.html', $data, $cookieEha);
        // echo($page);
        // exit;
        if (strpos($page, '您提供的用户名或者密码有误')) {
            throw new IDPasswordException();
        }
        $urlSource = (new Utils())->getLocation($page);
        if (count($urlSource) < 2) {
            throw new MissException(['code' => 503, 'msg' => '办事大厅登陆异常', 'errorCode' => 999]);
        }

        // 如果要调用办事大厅的数据就得用这个url,获得ticket，调用curlEhall函数
        // $url = $urlSource[2][0];
        $cookieEha = (new GetCookie())->curlCookie($page) . $cookieEha;
        if(strpos($urlSource[2][0], "ticket")){
            return $this->curlJW($cookieEha, $student, $password, false);
        }else{
            if(strpos($urlSource[2][0], "improveInfo")) {
                throw new FirstLoginException();
            }else{
                throw new MissException(['code' => 503, 'msg' => '办事大厅登陆未知错误', 'errorCode' => 999]);
            }
        }
    }
    // 抓取办事大厅的数据，暂时没有是用，如果要是用可通过ticket抓取
    public function curlEhall($url)
    {
        // sso登陆
        $cookieEhallSource = (new GetCookie())->newGetCookie('http://ehall.webvpn.jxust.edu.cn/jsonp/serviceCenterData.json?containLabels=true&searchKey=&_=1566200716748', 1);
        // echo($url);
        // exit;
        $cookieEhall = $cookieEhallSource . 'amp.locale=undefined;';
        $page = (new Utils())->curl($url, '', $cookieEhall);
        $urlSource = (new Utils())->getLocation($page);
        $cookieEhallSource = (new GetCookie())->curlCookie($page);
        $cookieEhall = $cookieEhall . $cookieEhallSource;
        $url = $urlSource[2][0];
        // echo $cookieEhall;
        // exit;
        $page = (new Utils())->curlOther($url, '', $cookieEhall);
        $urlSource = (new Utils())->getLocation($page);
        $cookieEhallSource = (new GetCookie())->curlCookie($page);
        // echo $page;
        // var_dump($cookieEhallSource);
        // var_dump($urlSource);
        $url = $urlSource[2][0];
        $cookieEhall = $cookieEhall . $cookieEhallSource;
        $page = (new Utils())->curlOther($url, '', $cookieEhall);
        return $page;
    }
    // 登陆教务系统
    public function curlJW($cookieEha, $student, $password, $falg)
    {
        // $cookievpn = (new Loginvpn())->vpnLogin($student, $password);
        // if(!$cookievpn){
        //     $cookievpn = (new Loginvpn())->vpnLogin($student, $password);
        // }
        // if(!$cookievpn) {
        //     throw new MissException(['code' => 505, 'msg' => 'vpn登陆异常', 'errorCode' => 999]);
        // }
        // 此刻开始是sso登陆教务系统
        $page = (new Utils())->curlLoginJW('http://jw.webvpn.jxust.edu.cn/sso.jsp', '', '');
        // var_dump($page);exit;
        $cookieJw = (new GetCookie())->getNewJwCookie($page);
        // var_dump($page);exit;
        $urlSso = 'http://authserver.webvpn.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fjw.jxust.edu.cn%2Fjsxsd%2F';
        $page = (new Utils())->curlOther($urlSso, '', $cookieEha);
        $urlJwLoginSource = (new Utils())->getLocation($page);
        if (empty($urlJwLoginSource[2])) {
            if($falg){
                return $this->firstLogin($student, $password);
            }else{
                throw new MissException(['code' => 504, 'msg' => '教务系统登陆异常', 'errorCode' => 999]);
            }
        } else {
            $page = (new Utils())->curlLoginJw($urlJwLoginSource[2][0], '', $cookieJw);
            preg_match_all("/(Set-Cookie: )([^\r\n]+)(;)/i", $page, $cookieJwSource);
            // var_dump($cookieJwSource);exit;
            $urlJwLoginSource = (new Utils())->getLocation($page);
            $cookieJw = $cookieJw . $cookieJwSource[2][0];
            $page = (new Utils())->curlLoginJw($urlJwLoginSource[2][0], '', $cookieJw);
            $urlJwLoginSource = (new Utils())->getLocation($page);
            // $page = (new Utils())->curlLoginJw($urlJwLoginSource[2][0], '', $cookieJw);
            // echo($page);exit;
            // $urlJwLoginSource = (new Utils())->getLocation($page);
            // $cookieJw = (new GetCookie())->curlCookie($page) . $cookieJw;
            $page = (new Utils())->curlLoginJw($urlJwLoginSource[2][0], '', $cookieJw);
            // echo $page; exit;
            // ini_set('date.timezone', 'Asia/Shanghai');
            // $time = date('Y-m-d G:i:s');
            // Db::table('login_info')->where('studentID', $student)->update(['cookieEhall' => str_replace($cookievpn, '', $cookieEha), 'cookievpn' => $cookievpn, 'time' => $time]);
            // var_dump($urlJwLoginSource);
            // preg_match_all(
            //     '/<span class="glyphicon-class">([^<>\n]+)/',
            //     $page,
            //     $name
            // );
            // if (empty($name[1][0])) {
            //     preg_match_all('/<li class="input_li" id="showMsg" style="color: red; margin-bottom: 0;">
			// 				\&nbsp;([^<>\n]+)/', $page, $msg);
            //     if (empty($msg[1])) {
                    // echo $result;
                    // preg_match_all('/<font color="blue"><b>([^<>\n]+)/', $result, $msg);
                    if (strpos($page, 'value="修改密码')) {
                        throw new PasswordEasyException();
                    }
            //     } else {
            //         throw new IDPasswordException();
            //     }
            // }
            //得到同学的名字
            if(strpos($page, '您确定退出系统')){
                // 更新数据库中的时间
                // ini_set('date.timezone', 'Asia/Shanghai');
                // $time = date('Y-m-d G:i:s');
                // Db::table('login_info')->where('studentID', $student)->update(['time' => $time]);
                $moreMsg = (new Utils())->getMoreMsg($cookieJw);
            }else{
                throw new MissException(['code' => 505, 'msg' => '基础信息获取异常', 'errorCode' => 999]);
            }
            $moreMsg['cookieEha'] = $cookieEha;
            return $moreMsg;
        }
    }
}
