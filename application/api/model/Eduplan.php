<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-14 13:06:50 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2019-09-14 13:06:50 
 */

namespace app\api\model;


use app\api\lib\Exception\MissException;

class Eduplan
{
    public function eduplan($cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        $page = $this->getplanpage($cookie);
        preg_match_all('/<TR.*?>(.*?)<\/TR>/ism', $page, $class_type);
        if(!$class_type){
            throw new MissException();
        }
        $classes = [];
        $class_other = [];
        $class = [];
        $subject = '';
        //$class_type =  [];
        // echo($class_type[1][7]);
        for ($i = 0, $j = 0, $k=0; $i < count($class_type[1]); $i++) {
            preg_match_all('/<TD align=\"center\">(.*?)&nbsp;<\/TD>/', $class_type[1][$i], $class);
            if (count($class[1])!=0) {
                if($class[1][1]){
                    // 获取课程种类
                    $type = $this->cut($class_type[1][$i],"<TD align=\"center\" rowspan=\"",'&nbsp;</TD>');
                    if($type){
                        preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $type, $matches);
                        $subject_type = join('', $matches[0]);
                    }
                    if($subject_type){
                        $subject = str_replace('应修已修','',$subject_type);
                        $k=0;
                    }
                    //var_dump($class[1]);
                    // preg_match_all('/<TD align=\"center\">(.*?)&nbsp;<!-- <input type=\"text\" name=\"zxf\" style=\"border: 0px;width:100%\"/> --><\/TD>/', $class_type[1][$i], $class_time);
                    preg_match_all('/<TD align=\"center\" width=\"10%\">(.*?)&nbsp;<\/TD>/', $class_type[1][$i], $class_other);
                    preg_match_all('/<TD align="left">&nbsp;&nbsp;(.*?)&nbsp;<\/TD>/', $class_type[1][$i], $class_name);
                    //var_dump($class_name);
                    $classes[$subject][$k]['name'] = $class_name[1][0]; //课程名字
                    $classes[$subject][$k]['type'] = $class[1][$j + 3]; //课程类型
                    $classes[$subject][$k]['credit'] = $class[1][$j + 4]; //课程学分
                    //课程总学时
                    $classes[$subject][$k]['time'] = $class[1][$j + 5] + $class[1][$j + 6] + $class[1][$j + 7];
                    $classes[$subject][$k]['start'] = $class_other[1][0]; //课程开始学期
                    $k++;
                }
            }
        }
        return $classes;
    }
    private function getplanpage($cookie)
    {
        $url = 'http://jw.webvpn.jxust.edu.cn/jsxsd/pyfa/topyfamx';
        $page = (new Login())->loginCurl($url, '', $cookie);
        return $page;
    }
    //截取指定两个字符之间的字符串
    private function cut($input,$start,$end){
        $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));  
        return $substr;
    }
}
