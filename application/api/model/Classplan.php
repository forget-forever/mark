<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/6/25
 * Time: 09:47
 */

namespace app\api\model;


use app\api\lib\Exception\MissException;

class Classplan
{
    public function classplan($cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        $page = $this->getplanpage($cookie);
        preg_match_all('/<TR.*?>(.*?)<\/TR>/ism', $page, $class_type);
        if(!$class_type){
            throw new MissException();
        }
        //echo $page;
        $j = 0;
        for ($i = 0; $i < count($class_type[1]); $i++){
            preg_match_all('/<td.*?>(.*?)<\/td>/ism', $class_type[1][$i], $class);
            if($class[1]){
                $classes[$j]['name'] = $class[1][3];
                $classes[$j]['type'] = $class[1][8];
                $classes[$j]['credit'] = $class[1][5];
                $classes[$j]['time'] = $class[1][6];
                $classes[$j]['start'] = $class[1][1];
                $j++;
            }
        }
        return $classes;
    }
    private function getplanpage($cookie)
    {
        $url = 'http://jw.webvpn.jxust.edu.cn/jsxsd/pyfa/pyfa_query';
        $page = (new Login())->loginCurl($url, '', $cookie);
        return $page;
    }
    //截取指定两个字符之间的字符串
    private function cut($input, $start, $end)
    {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
        return $substr;
    }
}
