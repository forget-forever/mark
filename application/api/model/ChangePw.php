<?php 
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-08-28 11:11:01 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2020-08-19 14:26:46
 */
namespace app\api\model;

use app\api\lib\Exception\PhoneError;
use think\Db;
use app\api\lib\exception\MissException;

class ChangePw{
    public function getCookie($studentID, $phone) {
        $cookie = (new GetCookie())->newGetCookie("http://authserver.jxust.edu.cn/authserver/login?service=http%3A%2F%2Fauthserver.jxust.edu.cn%2Fauthserver%2Fservices%2Fj_spring_cas_security_check") . 'org.springframework.web.servlet.i18n.CookieLocaleResolver.LOCALE=zh_CN;';
        // echo($cookie);
        preg_match_all("/(JSESSIONID=)([^\r\n]+)(; path)/i", $cookie, $matches);
        // var_dump($matches);
        // exit;
        $url = "http://authserver.jxust.edu.cn/authserver/getBackPasswordByMobile.do?service=http%3A%2F%2Fauthserver.jxust.edu.cn%2Fauthserver%2Fservices%2Fj_spring_cas_security_check%3Bjsessionid%3D" . $matches[2][0];
        // echo $url;exit;
        $sql = "SELECT * FROM `source1_1`";
        $source = Db::query($sql);
        $captchaResponse = (new GetVcode())->getCode('image.jpg', 1, $source, $cookie, $url);
        $data = ["map['uid']" => $studentID, 
            "map['mobile']" => $phone, 
            "map['captchaResponse']" => $captchaResponse, 
            "_target1" => ''
        ];
        if (!$captchaResponse) {
            throw new MissException(['code' => 502, 'msg' => '验证码识别异常', 'errorCode' => 999]);
        }
        
        $data = http_build_query($data);
        $page = (new Utils()) -> curlExtra($url, $data, $cookie);
        if (strpos($page, '输入的手机号码不正确')) {
            throw new PhoneError();
        }
        if(strpos($page, "验证码已发送")){
            echo 'success';exit;
        }
        var_dump($page);
        exit;
    }
}
?>