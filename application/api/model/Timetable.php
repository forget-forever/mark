<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2020-06-21 10:15:14 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2020-06-21 10:15:14 
 */

/**
 * Created by PhpStorm.
 * User: CHUNLAI
 * Date: 2018/10/14
 * Time: 18:24
 */

namespace app\api\model;


class Timetable
{
    /**
     * 将处理的所有数据返回，得到课表
     * @param $semester
     * @param $studentID
     * @param $password
     * @param $cookie
     * @return array
     */
    public function getTimetable($semester, $cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        // $result = $this->dataHandle($semester, $cookie);
        // echo($gradePage);exit;
        // return $result;
        $result = [];
        for($i = 0; $i < 31; $i++){
            $result[$i] = [];
        }
        $gradePage = $this->getTimetablePage($semester, $cookie);
        preg_match_all('/<TD.*?>(.*?)<\/TD>/ism', $gradePage, $source);
        // var_dump($source);exit;
        for($i = 0; $i < 35; $i++){
            // echo $source[1][$i];exit;
            // echo '第' . ($i+1) . '个框:<br>';
            preg_match_all('/<div.*?>(.*?)<\/div>/ism', $source[1][$i], $table);
            // echo($table[1][0]);
            $class = explode('----------------', $table[1][1]);
            for($j = 0; $j < count($class); $j++){
                if(!strpos($class[$j], '<font')) continue;
                $class[$j] = str_replace(" ", '', $class[$j]);
                $class[$j] = str_replace("<span><fontcolor='red'>&nbspP</font></span>", '', $class[$j]);
                $class[$j] = str_replace("<span><fontcolor='red'>&nbspO</font></span>", '', $class[$j]);
                // $class[$j] = str_replace('P</font>', '', $class_0[$j]);
                $class[$j] = $this->delHeadByte($class[$j]);
                $oneClass = explode('<br/>', $class[$j]);
                // var_dump($oneClass);exit;
                $courseName = $oneClass[0];
                $oneClass =array_splice($oneClass, 1);
                // var_dump($oneClass);exit;
                preg_match_all('/<font.*?>(.*?)<\/font>/ism', implode('',$oneClass), $classMsg);
                $whichCourse = intval($i / 7) + 1;
                $courseAddress = $classMsg[1][2];
                $courseWeekSource = explode('(周)', $classMsg[1][1]);
                // $courseWeek = str_replace('(周)', '', $classMsg[1][0]);
                $courseWeek = $courseWeekSource[0];
                $whichDay = $i % 7 + 1;
                $result = $this -> addCourse($whichDay, $whichCourse, $courseName, $courseWeek, $courseAddress, $result);
                // var_dump($classMsg);
                // echo 'whichDay:' . $whichDay;
                // echo '<br>whichCourse:' . $whichCourse;
                // echo '<br>courseName:' . $courseName;
                // echo '<br>courseWeek:' . $courseWeek;
                // echo '<br>courseAddress:' . $courseAddress;
                // echo '<br><br><br>';
            }
            // var_dump($class);
            //  echo '<br><br>';
        }
        // exit;
        return $result;
    }
    public function addCourse ($whichDay, $whichCourse, $courseName, $courseWeek, $courseAddress, $result) {
        $week = explode(',', $courseWeek);
        // var_dump($week);
        for($i = 0; $i < count($week); $i++){
            // echo $i;
            $whichWeek = explode('-', $week[$i]);
            // echo $whichWeek[0];
            for($j = $whichWeek[0]; $j <= $whichWeek[(count($whichWeek) - 1)]; $j++){
                $num = count($result[$j - 1]);
                $result[$j - 1][$num]['whichDay'] = $whichDay;
                $result[$j - 1][$num]['whichCourse'] = $whichCourse;
                $result[$j - 1][$num]['courseName'] = $courseName;
                $result[$j - 1][$num]['courseWeek'] = $courseWeek . '(周)';
                $result[$j - 1][$num]['courseAddress'] = $courseAddress;
            }
        }
        return $result;
    }
    public function selectWchichCourse($str){
        switch($str) {
            case '01-02节' : return 1;
            case '03-04节' : return 2;
            case '05-06节' : return 3;
            case '07-08节' : return 4;
            case '09-10节' : return 5;
        }
    }
    // 去除字符串最前面的非汉字
    public function delHeadByte($str) {
        for($i = 0; $i < strlen($str); $i++){
            $s = substr($str, $i, 1);
            if(preg_match("/^[".chr(0xa1)."-".chr(0xff)."]+$/", $s)){
                break;
            }else if(($s != '-') && ($s != '<') && ($s != '>') && ($s != 'b') && ($s != 'r')){
                break;
            }
        }
        $str = substr($str, $i);
        // $str = substr($str, $i, 0);
        return $str;
    }
    // 切割字符串
    public function cutStr($str,$start,$end){
        $str_0 = substr($str, strlen($start)+strpos($str, $start),(strlen($str) - strpos($str, $end))*(-1));
        return $str_0;
    }
    // 获取数据
    public function getTimetablePage($semester, $cookie)
    {
        $url = "http://jw.jxust.edu.cn/jsxsd/xskb/xskb_list.do?xnxq01id=" . $semester;
        $gradePage = (new Login())->loginCurl($url, "", $cookie);
        return $gradePage;
    }




    // *@next functions were stoped at 2019-11-30
    /**
     * 遍历处理整个学期的课程数据
     * @param $semester
     * @param $cookie
     * @return array
     */
    public function dataHandle($semester, $cookie)
    {
        $result = [];
        for ($i = 0; $i < 30; $i++) {
            $result[$i] = $this->getOneWeekData($semester, $i + 1, $cookie);
            // if()
        }
        return $result;
    }

    /**
     * 处理一周的课程数据
     * @param $semester
     * @param $whichWeek
     * @param $cookie
     * @return array
     */
    public function getOneWeekData($semester, $whichWeek, $cookie)
    {
        $gradePage = $this->getTimetablePage($semester, $whichWeek, $cookie);
        //获取表格
        preg_match('/<table id="kbtable" [\w\W]*?>([\d\D]*?)<\/table>/',
            $gradePage, $result);
        preg_match_all('/<div [\w\W]*?[style="" class="kbcontent"]>([\d\D]*?)<\/div>/',
            $result[1], $matches);
        //获取筛选后的课程表
        $courseTable[] = [];
        for ($i = 0, $k = 0; $i < 5; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $courseTable[$i][$j] = $matches[1][$k];
                $k += 2;
            }
        }
        //开始处理筛选后的课程表的数据
        $courseName = [];//课程名
        $courseWeek = [];//上课周数
        $courseAddress = [];//上课地点
        $whichDay = [];//星期几
        $whichCourse = [];//第几课
        for ($i = 0, $m = 0; $i < 5; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($courseTable[$i][$j] == "&nbsp;") {
                    continue;
                }
                preg_match('/([^<>]*?)<br\/>/', $courseTable[$i][$j], $matches);
                $courseName[$m] = $matches[1];
                preg_match_all('/<font [\w\W]*?>([^<>]*?)<\/font>/',
                    $courseTable[$i][$j], $matches);
                $courseWeek[$m] = $matches[1][0];
                //如果上课地点为空则''
                if (count($matches[1]) == 2) {
                    $courseAddress[$m] = $matches[1][1];
                } else {
                    $courseAddress[$m] = '';
                }
                $whichDay[$m] = $j + 1;
                $whichCourse[$m] = $i + 1;
                $m++;
            }
        }
        //一周的课表
        $oneWeek = [];
        for ($i = 0; $i < count($courseName); $i++) {
            $oneWeek[$i] = [
                'whichDay' => $whichDay[$i],
                'whichCourse' => $whichCourse[$i],
                'courseName' => $courseName[$i],
                'courseWeek' => $courseWeek[$i],
                'courseAddress' => $courseAddress[$i],
            ];
        }
        return $oneWeek;
    }

    /**
     * 获取一周的页面
     * @param $semester
     * @param $whichWeek
     * @param $cookie
     * @return mixed
     */
    
}