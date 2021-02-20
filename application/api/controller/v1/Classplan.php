<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/6/2
 * Time: 17:43
 */

namespace app\api\controller\v1;

use app\api\model\CheckParams;
use app\api\model\GetCookie;
use app\api\model\Classplan as plan;
use think\Controller;

/**
 * Class LevelGrade 专业培养计划
 * @package app\api\controller\v1
 */
class Classplan extends Controller
{
    public function classplan($cookie)
    {
        // $cookie = GetCookie::getCookie();
        // CheckParams::checkParams($studentID, $password);
        $result = (new plan()) -> classplan($cookie);
        return $result;
    }
}