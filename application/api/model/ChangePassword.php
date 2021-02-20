<?php
/**
 * rebuild by visual studio code.
 * User: ZHou meilei
 * Date: 2019/7/7
 * Time: 11:34
 */

namespace app\api\model;


use app\api\lib\Exception\OldPasswordException;
use app\api\lib\Exception\ServerException;
use app\api\lib\Exception\IDPasswordException;
use think\Exception;

class ChangePassword
{
    public function changePassword($studentID, $oldPassword, $newPassword)
    {
        // $page = (new Utils())->curlLoginJW('http://jw.webvpn.jxust.edu.cn', '', '');
        // $cookie = (new GetCookie())->getNewJwCookie($page);
        $cookie = $this -> firstLogin($studentID, $oldPassword);
        $page = (new Utils())->curl("http://jw.jxust.edu.cn/framework/userInfo_edit.jsp", '', $cookie);
        $encoded = (new Login) -> getEncoded($oldPassword, $newPassword, $cookie);
        $data = http_build_query(['id' => '', "encoded" => $encoded]);
        $page = (new Utils()) -> curl("http://jw.jxust.edu.cn/yhxigl.do?method=changMyUserInfo", $data, $cookie);
        if(strpos($page, '修改密码成功')){
            return "success";
        }else{
            throw new ServerException();
        }
    }

    public function firstLogin($studentID, $password)
    {
        $page = (new Utils())->curlLoginJW('http://jw.webvpn.jxust.edu.cn', '', '');
        // var_dump($page);exit;
        $cookieJw = (new GetCookie())->getNewJwCookie($page);
        // echo $cookieJw;exit;
        $url = "http://jw.webvpn.jxust.edu.cn/Logon.do?method=logon";
        // $this->picCurl("http://jw.jxust.edu.cn/verifycode.servlet", $cookie);
        // var_dump($photos);
        $RANDOMCODE = '';
        $RANDOMCODE = (new Login)->getHec('http://jw.jxust.edu.cn/verifycode.servlet', 0, $cookieJw);
        $encodedSource = (new Login)->getEncoded($studentID, $password, $cookieJw);
        $encoded = $encodedSource[0];
        $cookieJw = $cookieJw . $encodedSource[1];
        // echo $RANDOMCODE;exit;
        // var_dump($RANDOMCODE);
        $post = [
            'userAccount' => $studentID,
            'userPassword' => '',
            'RANDOMCODE' => $RANDOMCODE,
            'encoded' => $encoded
        ];
        //将数组转换成字符串
        $post = http_build_query($post);
        // echo $post;
        // echo "<br>";
        // $page = $this->loginCurl($url, $post, $cookieJw);
        $page = (new Utils())->curl($url, $post, $cookieJw);
        if (strpos($page, "密码错误")) {
            throw new IDPasswordException();
        }
        $url = (new Utils())->getLocation($page);
        $page = (new Utils())->curlOther($url[2][0], '', $cookieJw);
        if (strpos($page, 'value="修改密码')) {
            return $cookieJw;
        }
        // echo($page);exit;
        $url = (new Utils())->getLocation($page);
        $cookieJw = $cookieJw . (new GetCookie())->curlCookie($page);
        $page = (new Utils())->curlOther($url[2][0], '', $cookieJw);
        preg_match_all(
            '/<span class="glyphicon-class">([^<>\n]+)/',
            $page,
            $name
        );
        if (empty($name[1][0])) {
            //正则匹配——用户名或密码错误
            preg_match_all('/<li class="input_li" id="showMsg" style="color: red; margin-bottom: 0;">
                        \&nbsp;([^<>\n]+)/', $page, $msg);
            if (empty($msg[1])) {
                // echo $result;
                // preg_match_all('/<font color="blue"><b>([^<>\n]+)/', $result, $msg);
                if (strpos($page, 'value="修改密码')) {
                    return $cookieJw;
                }
            } else {
                throw new OldPasswordException();
            }
        }
        
    }


    /**
     * 首次登录后修改密码
     * @param $oldPassword
     * @param $newPassword
     * @param $cookie
     * @return array
     * @throws ServerException
     */
}