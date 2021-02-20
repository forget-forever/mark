<?php
/**
 * Created by visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/6/19
 * Time: 11:03
 */

namespace app\api\controller\v1;

use app\api\model\CheckParams;
use app\api\model\GetCookie;
// use app\api\model\TeacherEvaluat;
use app\api\model\TeachingAssessment;
use think\Controller;

class TeacherEva extends Controller
{
    public function teacherEva($cookie, $read, $grades)
    {
        // $cookie = GetCookie::getCookie();
        // CheckParams::checkParams($studentID, $password);
        if ($read == 0) {
            $result = (new TeachingAssessment())->doTeachingEvaluation($cookie, $grades);
            return $result;
        } else if ($read == 1) {
            $result = (new TeachingAssessment())->teachingAssessment($cookie);
            return $result;
        }
    }
}