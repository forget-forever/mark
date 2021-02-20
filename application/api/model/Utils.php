<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/8/15
 * Time: 9:09
 */

namespace app\api\model;


class Utils
{
    // 办事大厅的登陆
    public function curl($url, $post, $cookie)
    {
        $url = str_replace('.webvpn', '', $url);
        $url = str_replace('http:', 'https:', $url);
        // $url = str_replace('http://jw.jxust.edu.cn','http://jw.webvpn.jxust.edu.cn', $url);
        // $url = str_replace('http://jw.webvpn.jxust.edu.cn','http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.webvpn.jxust.edu.cn/','http://auth.zml123.top', $url);
        // $url = str_replace('http://jw.jxust.edu.cn', 'http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.jxust.edu.cn/','http://auth.zml123.top', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)
        //  AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        //不自动输出数据，要echo才行
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //重要，抓取跳转后数据
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        //重要，302跳转需要referer，可以在Request Headers找到
        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.96 Safari/537.36');
        // curl_setopt(
        //     $ch,
        //     CURLOPT_HTTPHEADER,
        //     [        
        //         'Referer: http://authserver.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fehall.jxust.edu.cn%2Flogin%3Fservice%3Dhttp%3A%2F%2Fehall.jxust.edu.cn%2Fnew%2Findex.html',
        //         'Content-Type: application/x-www-form-urlencoded',
        //     ]
        // );
        //TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种
        curl_setopt($ch, CURLOPT_POST, 1);
        //post提交数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    // 之后的302跳转的，必须为GET其他的传参方式会报错
    public function curlOther($url, $post, $cookie)
    {
        $url = str_replace('.webvpn', '', $url);
        $url = str_replace('http:', 'https:', $url);
        // $url = str_replace('http://jw.jxust.edu.cn','http://jw.webvpn.jxust.edu.cn', $url);
        // $url = str_replace('http://jw.webvpn.jxust.edu.cn','http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.webvpn.jxust.edu.cn/','http://auth.zml123.top', $url);
        // $url = str_replace('http://jw.jxust.edu.cn', 'http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.jxust.edu.cn/','http://auth.zml123.top', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)
        //  AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        //不自动输出数据，要echo才行
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //重要，抓取跳转后数据
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        //重要，302跳转需要referer，可以在Request Headers找到
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.96 Safari/537.36');
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [        
                'Referer: https://authserver.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fehall.jxust.edu.cn%2Flogin%3Fservice%3Dhttp%3A%2F%2Fehall.jxust.edu.cn%2Fnew%2Findex.html',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            ]
        );
        //TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种
        curl_setopt($ch, CURLOPT_POST, 0);
        //post提交数据
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    // sso登陆教务系统，Referer不同
    public function curlLoginJw($url, $post, $cookie)
    {
        $url = str_replace('.webvpn', '', $url);
        $url = str_replace('http:', 'https:', $url);
        // $url = str_replace('http://jw.webvpn.jxust.edu.cn','http://jw.putiyue.com', $url);
        // $url = str_replace('http://jw.webvpn.jxust.edu.cn','http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.webvpn.jxust.edu.cn/','http://auth.zml123.top', $url);
        // $url = str_replace('http://jw.jxust.edu.cn', 'http://jw.zhoumeilei.cn', $url);
        // $url = str_replace('http://authserver.jxust.edu.cn/','http://auth.zml123.top', $url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        //不自动输出数据，要echo才行
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //重要，抓取数据后不跟随，自己看Location
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        //重要，302跳转需要referer，可以在Request Headers找到
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.96 Safari/537.36');
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [        
                'Referer: https://jw.webvpn.jxust.edu.cn/sso.jsp',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
            ]
        );
        //TRUE 时会发送 POST 请求，类型为：application/x-www-form-urlencoded，是 HTML 表单提交时最常见的一种
        curl_setopt($ch, CURLOPT_POST, 0);
        //post提交数据
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    public function getLocation ($str) {
        preg_match_all("/(Location: )([^\r\n]+)()/i", $str, $matches);
        // var_dump($matches);
        // exit;
        // $locationSource = explode(";", $matches[2][0]);
        // $location = $locationSource[0] . ";";
        // echo $cookie;
        // exit;
        return $matches;
    }
    public function getMoreMsg($cookie)
    {
        $url = "https://jw.webvpn.jxust.edu.cn/jsxsd/framework/xsMain_new.jsp?t1=1";
        $result = $this->curlOther($url, "", $cookie);
        preg_match_all('/<div class="middletopdwxxcont">([^<>\n]+)/', $result, $msg);
        // echo($result);
        // exit;
        $msg = [
            "name" => $msg[1][1],
            "studentID" => $msg[1][2],
            "school" => $msg[1][3],
            "profession" => $msg[1][4],
            "class" => $msg[1][5],
            "cookie" => $cookie
        ];
        return $msg;
    }
    public function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array) object_to_array($v);
            }
        }
     
        return $obj;
    }
}