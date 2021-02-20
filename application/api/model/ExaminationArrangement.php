<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-14 13:06:37 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-10-22 13:33:59
 */


namespace app\api\model;


use app\api\lib\Exception\MissException;

class ExaminationArrangement
{
    public function getExaminationArrangement($cookie)
    {
        // (new Login())->doLogin($studentID, $password, $cookie);
        $extraEXam = [];
        $page = $this->getExaminationArrangementPage($cookie);
        $result = $this->dataHandle($page);
        $extraEXam = $this->getMoreExam($cookie);
        $result = array_merge($result, $extraEXam);
        if(count($result) < 1){
            throw new MissException(['msg'=>'暂无考试安排', 'errorCode'=>40004]);
        }
        return $result;
    }

    public function getMoreEXam($cookie){
        $url = 'http://jw.webvpn.jxust.edu.cn/jsxsd/xsks/xsstk_list';
        $page = (new Login())->loginCurl($url, "xnxqid=2020-2021-1&xqlb=&xqlbmc=", $cookie);
        preg_match_all('/<TR.*?>(.*?)<\/TR>/ism', $page, $matchesNum);
        preg_match_all('/<TD.*?>(.*?)<\/TD>/ism', $page, $matches);
        // var_dump($matchesNum);
        // var_dump($matches);exit;
        if (count($matchesNum[1])<2) {
            return [];
        }
        // $result = $this->dataHandle($page);
        // var_dump($matches);exit;
        $result[] = [];
        if(strpos($matches[1][0], '到数据')){
            $result = [];
        }else{
            for ($i = 0; $i < (count($matchesNum[1]) - 1); $i++) {
                $result[$i] = [
                    'course' => $matches[1][10 * $i + 2],
                    'time' => substr_replace($matches[1][10 * $i + 8], '~', '-6' , '-5'),
                    'address' => $matches[1][10 * $i + 7],
                    'seatNum' => ''
                ];
            }
        }
        return $result;
    }

    private function getExaminationArrangementPage($cookie)
    {
        $url = "http://jw.webvpn.jxust.edu.cn/jsxsd/xsks/xsksap_list";
        $examinationArrangementPage = (new Login())->loginCurl($url, "xnxqid=2020-2021-1&xqlb=", $cookie);
        return $examinationArrangementPage;
    }

    private function dataHandle($page)
    {
        // return $page;
        // preg_match_all('/<td align="left">([^<>\n]*)<\/td>/', $page, $matches_course);
        // preg_match_all('/<td>([^<>\n]*)<\/td>/', $page, $matches_other);
        preg_match_all('/<TR.*?>(.*?)<\/TR>/ism', $page, $matchesNum);
        preg_match_all('/<TD.*?>(.*?)<\/TD>/ism', $page, $matches);
        if (count($matchesNum[1])<2) {
            // throw new MissException(['msg'=>'暂无考试安排', 'errorCode'=>40004]);
            return [];
        }
        //考试数
        // var_dump($matchesNum[1]);
        // var_dump($matches);exit;
        if(strpos($matches[1][0], '到数据')){
            return [];
        }
        $result[] = [];
        for ($i = 0; $i < (count($matchesNum[1]) - 1); $i++) {
            $result[$i] = [
                'course' => $matches[1][12 * $i + 4],
                'time' => $matches[1][12 * $i + 6],
                'address' => $matches[1][12 * $i + 7],
                'seatNum' => $matches[1][12 * $i + 8]
            ];
        }
        return $result;
    }
}