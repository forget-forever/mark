<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/4/25
 * Time: 09:44
 */

namespace app\api\controller\v1;

use app\api\model\CheckParams;
use app\api\model\GetCookie;
use app\api\model\Eduplan as plan;
use think\Controller;

/**
 * Class LevelGrade 专业培养计划
 * @package app\api\controller\v1
 */
class Eduplan extends Controller
{
    public function eduplan($cookie)
    {
        // $cookie = GetCookie::getCookie();
        // CheckParams::checkParams($studentID, $password);
        $result = (new plan()) -> eduplan($cookie);
        return $result;
    }
}