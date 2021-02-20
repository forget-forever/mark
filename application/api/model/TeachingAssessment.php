<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-14 13:06:09 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2019-09-14 13:06:09 
 */

namespace app\api\model;


use app\api\lib\Exception\MissException;
use app\api\lib\Exception\EvaluatedException;
use app\api\lib\Exception\EvaluationFailException;
use app\api\lib\Exception\EvaluationNull;

class TeachingAssessment
{
    public function teachingAssessment($cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        $page = $this->getplanpage($cookie);
        preg_match_all('/<TR.*?>(.*?)<\/TR>/ism', $page, $class_type);
        if (!$class_type) {
            throw new MissException();
        }
        //echo $page;
        $j = 0;
        //var_dump($class_type);
        $classes = [];
        for ($i = 0; $i < count($class_type[0]); $i++) {
            //var_dump($class_type[0][$i]);
            preg_match_all('/<td.*?>(.*?)<\/td>/ism', $class_type[0][$i], $class);
            //var_dump($class[1]); //class[0]是取的是td标签，class[1]才是纯文本数组
            if ($class[1]) {
                preg_match_all('/<input.*?>(.*?)/ism', $class[0][0], $classNum);
                // var_dump($classNum);
                $classes[$j]['courseNumber'] = $this->cut($classNum[0][1],'value="','"  />');
                $classes[$j]['courseName'] = $class[1][2];
                $classes[$j]['teacherName'] = $class[1][3];
                $j++;
            }
        }
        // var_dump($classes);
        if($classes == []){
            throw new EvaluationNull();
        }
        return $classes;
    }
    public function doTeachingEvaluation ($cookie, $grade){
        $classes = $this -> teachingAssessment($cookie);
        $firsrGrade = 'pj0502id=7742F097319046A0AF3333AD6EADE4C3&xnxq01id=&pj01id=';
        $secondGrade = 'cj0701id=&pj0502id=7742F097319046A0AF3333AD6EADE4C3&xnxq01id=&pj01id=&operate=2';
        $url_1 = 'http://jw.webvpn.jxust.edu.cn/jsxsd/xspj/xspj_zt_save.do';
        $url_2 = 'http://jw.webvpn.jxust.edu.cn/jsxsd/xspj/xspj_zpf_save.do';
        $zpf = [];
        $level = [];
        $radio = [];
        $grades = explode(',' , $grade);
        for($i = 0; $i<count($classes); $i++){
            if($grades[$i]>=1&&$grades[$i]<=59){
                $level[$i] = '544C064F1E734991937A0D34545DC407';
            }else if($grades[$i]>=60&&$grades[$i]<=79){
                $level[$i] = '57857B4BB3424E7FB12E3638D8CB4ED8';
            }else if($grades[$i]>=80&&$grades[$i]<=89){
                $level[$i] = 'E402E07E5F204974B37751EE4E110068';
            }else if($grades[$i]>=90&&$grades[$i]<=95){
                $level[$i] = '91C2E12B939141008702252ECEE9E889';
            }
            $radio[$i] = 'radio_pjdj_' . $classes[$i]['courseNumber'];
            $zpf[$i] = 'zpf_' . $classes[$i]['courseNumber'];
        }
        // 整理表单数据
        for($i = 0; $i<count($classes); $i++){
            $firsrGrade = $firsrGrade . '&jx02id=' . $classes[$i]['courseNumber'] . '&jg0101id' . '&' . $radio[$i] . '=' . $level[$i];
        }
        $firsrGrade = $firsrGrade . '&pageIndex=1';
        // 提交第一张表单
        $result = $this -> doevaluat($url_1, $firsrGrade, $cookie);
        if(!strstr($result, '保存成功')){
            throw new EvaluationFailException();
        }
        // 拼接第二个表单的数据
        for($i = 0; $i<count($classes); $i++){
            $secondGrade = $secondGrade . '&jx02id=' . $classes[$i]['courseNumber'] . '&jg0101id=&sum=' . $zpf[$i] . '&' . $zpf[$i] . '=' . $grades[$i];
        }
        $secondGrade = $secondGrade . '&pageIndex=1';
        $result = $this -> doevaluat($url_2, $secondGrade, $cookie);
        if(!strstr($result, '提交成功')){
            throw new EvaluationFailException();
        }
        // if(strstr($result, '提交成功'))
        return 'succeed';
    }
    private function doevaluat($url,$msg,$cookie)
    {
        $res = (new Login())->loginCurl($url, $msg, $cookie);
        return $res;
    }
    private function getplanpage($cookie)
    {
        $url = 'http://jw.webvpn.jxust.edu.cn/jsxsd/xspj/xspj_zt_list.do?pj0502id=7742F097319046A0AF3333AD6EADE4C3&xnxq01id=2020-2021-1&pj01id=';
        $page = (new Login())->loginCurl($url, '', $cookie);
        return $page;
    }
    private function cut($input,$start,$end){
        $substr = substr($input, strlen($start)+strpos($input, $start),(strlen($input) - strpos($input, $end))*(-1));  
        return $substr;
    }
}
