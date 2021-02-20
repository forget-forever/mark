<?php
/**
 * Created by visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/2/26
 * Time: 17:14
 */

namespace app\api\model;
use app\api\lib\Exception\StudentIDException;
use app\api\lib\Exception\MissException;



class ResetPassword
{
    
    public static function doResetPassword($studentID, $IDCard)
    {
        $url = "http://jw.jxust.edu.cn/findmm.jsp";
        $page = (new Utils())->curlLoginJW('http://jw.webvpn.jxust.edu.cn', '', '');
        $cookieJw = (new GetCookie())->getNewJwCookie($page);
        $page = (new Utils()) -> curl($url, "", $cookieJw);
        preg_match_all('/<input.*?>(.*?)/ism', $page, $inputValue);
        preg_match_all('/(value=")([^\r\n]+)(")/i', $inputValue[0][2], $yztokenSource);
        // $yztoken = $yztokenSource[1][0];
        $data = http_build_query([
                "method" => "showAccount", 
                "account" => $studentID,
                "yztoken" => $yztokenSource[2][0]
        ]);
        $page = (new Utils()) -> curl("http://jw.jxust.edu.cn/Logon.do", $data, $cookieJw);
        preg_match_all('/<input.*?>(.*?)/ism', $page, $inputValue);
        preg_match_all('/(value=")([^\r\n]+)(")/i', $inputValue[0][1], $yztokenSource);
        $data = http_build_query([
                "account" => $studentID,
                "accounttype" => $yztokenSource[2][0],
                "sfzjh" => $IDCard
        ]);
		$page = (new Utils()) -> curl("http://jw.jxust.edu.cn/Logon.do?method=resetPasswd", $data, $cookieJw);
		// echo $page;exit;
		if(strpos($page, "密码已重置")){
			return "success";
		}else if(strpos($page, "身份证件号输入错误")){
			throw new StudentIDException();
		}else{
			throw new MissException(['code' => 500, 'msg' => '密码重置异常', 'errorCode' => 999]);
		}



		// 以下代码弃用 2020-2-16
        $url = "http://jw.webvpn.jxust.edu.cn/Logon.do?method=showAccount&account=".$studentID;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // 获取头部信息
        curl_setopt($ch, CURLOPT_HEADER, 1);
        // 返回原生的（Raw）输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $content = curl_exec($ch);
        curl_close($ch);
//        try{
//            // 解析HTTP数据流
        list($header, $body) = explode("\r\n\r\n", $content);
//        }catch (Exception $e){
//            throw new ServerException();
//        };
        // 解析COOKIE
        echo $content;exit;
        preg_match_all("/(set\-cookie: )([^\r\n]*)(;)/i", $header, $matches);
        // 后面用CURL提交的时候可以直接使用
        //TODO:异常以后写
//        try{
        $cookie = $matches[2][0] . ";" . $matches[2][1] . ";";
        $url = '172.16.2.39/Logon.do?method=resetPasswd';

        //旧接口
        //拼接url
        // $url = $url.'&account='.$studentID.'&accounttype=2&sfzjh='.$IDCard;
        // $result = Action::curl_get($url);

        $data = 'account='.$studentID.'&accounttype=2&sfzjh='.$IDCard;
        // $data = array("account"=>$studentID,"accounttype"=>2,"sfzjh"=>$IDCard);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($curl);
        curl_close($curl);
        
        return $result;
    }
}