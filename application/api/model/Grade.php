<?php
/**
 * Created by PhpStorm.
 * User: CHUNLAI
 * Date: 2018/10/13
 * Time: 17:42
 */

/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-10-11 19:46:09 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2019-10-11 19:46:09 
 */

namespace app\api\model;


use app\api\lib\Exception\MissException;
use app\api\lib\Exception\MissGradeException;
use app\api\lib\Exception\NeedEvaluationException;

class Grade
{
    public function getGrade($semester, $cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        $gradePage = $this->getGradePage($semester, $cookie);
        $result = $this->dataHandle($gradePage);
        return $result;
    }


    /**
     * @param $gradePage 成绩页面
     * @return array 成绩
     * @throws MissException 成绩不存在
     * @throws NeedEvaluationException 未评教
     */
    private function dataHandle($gradePage)
    {
        // echo $gradePage;exit;
        // preg_match_all('/请评教/', $gradePage, $isNeedEvaluate);
        if (strpos($gradePage, '评教未完成')) {
            throw new NeedEvaluationException();
        }
        preg_match('/未查询到数据/', $gradePage, $isNull);
        if (!empty($isNull[0])) {
            throw new MissException(['code' => '400', 'msg' => '请求的成绩不存在', 'errorCode' => '40000']);
        }
        //获取课程名，并保存到$courseName中
        // preg_match_all('/<td align="left">([^<>\n]*)/',
        //     $gradePage, $matchesName);
        //获取成绩 
        preg_match_all('/<td.*?>(.*?)<\/td>/ism',
            $gradePage, $matchesGrade);
            // var_dump($matchesGrade);
            // exit;
        //获取学分、绩点
        // preg_match_all('/<td>([^<>\n]*)/',
        //     $gradePage, $matchesOther);
        // echo $courseNum;
        $result[] = [];
        // echo($gradePage);
        //var_dump($matchesGrade[1][0]);
        
        //2019-4-01 解决教务系统修改了点东西
        //2019-4-11 为了修复挂了科的分数问题特写下此代码
        
        // $matchesGrade = [];
        // for($i = 0, $j = 0;$i<(count($matchesGrade_0[1])+count($matchesGrade_1[1]));$i++){
        //     if($matchesOther[1][5 + $i * 11]){
        //         preg_match('/\d+/',$matchesGrade_0[1][$i-$j],$matchesGrade[$i]);
        //     }else {
        //         preg_match('/\d+/',$matchesGrade_1[1][$j],$matchesGrade[$i]);
        //         $j++;
        //     }
        // }
        // var_dump($matchesGrade);
        //echo($matchesGrade[4][0]);
        $result = [];
        $j = 0;
        for ($i = 0; $i < count($matchesGrade[1]); $i = $i + 16) {
            $result[$j] = [
                'courseName' => $matchesGrade[1][$i + 3],
                'credit' => $matchesGrade[1][7 + $i],
                'coursePoint' => $matchesGrade[1][$i + 9]=='' ? '0' : $matchesGrade[1][$i + 9],
                'courseGrade' =>  str_replace(' ', '', str_replace("\n",'',str_replace("\t", '', $matchesGrade[1][$i + 5]))) 
            ];
            $j++;
        }
        return $result;
    }

    //获取成绩页面
    public function getGradePage($semester, $cookie)
    {
        $url = "http://jw.webvpn.jxust.edu.cn/jsxsd/kscj/cjcx_list?kksj=" . $semester . "&kcxz=&kcmc=&xsfs=all";
        $gradePage = (new Login())->loginCurl($url, "", $cookie);
        return $gradePage;
    }
}