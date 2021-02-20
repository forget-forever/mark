<?php
/**
 * Created by PhpStorm.
 * User: CHUNLAI
 * Date: 2018/10/13
 * Time: 9:29
 */

namespace app\api\model;


class GetCookie
{
    public static function getCookie($url = "http://jw.webvpn.jxust.edu.cn")
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // 获取头部信息
        curl_setopt($ch, CURLOPT_HEADER, 1);
        // 返回原生的（Raw）输出
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, 1);
        curl_setopt($ch,CURLOPT_TIMEOUT,10);
        $content = curl_exec($ch);
        curl_close($ch);
        list($header, $body) = explode("\r\n\r\n", $content);
        preg_match_all("/(set\-cookie: )([^\r\n]*)(;)/i", $header, $matches);
        $cookie = $matches[2][0] . ";" . $matches[2][1] . ";";
        return $cookie;
    }
    public function newGetCookie($url){
        $content = get_headers( $url ,  1 );
        // $cookie = $content['Set-Cookie'][0] . ';' . $content['Set-Cookie'][1] . ';';
        // var_dump($content);
        // exit;
        return implode(';',$content['Set-Cookie']) . ';';
    }
    public function curlCookie($str){
        preg_match_all("/(Set-Cookie: )([^\r\n]+)(;)/i", $str, $matches);
        $cookieSource = explode(";", $matches[2][0]);
        $cookie = $cookieSource[0] . ";";
        // echo $cookie;
        // exit;
        return $cookie;
    }
    public function getMoreCookie($str){
        preg_match_all("/(Set-Cookie: )([^\r\n]+)(;)/i", $str, $matches);
        // var_dump($matches);exit;
        return $matches[2][0] . ';' . $matches[2][1];
    }
    public function getNewJwCookie($str){
        preg_match_all("/(Set-Cookie: )([^\r\n]+)(;)/i", $str, $matches);
        // echo $str;
        // var_dump($matches);exit;
        return $matches[2][0] . ';' . $matches[2][1] . ';';
    }
}