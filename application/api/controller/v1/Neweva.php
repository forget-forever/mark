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
use app\api\model\Neweva;
use think\Controller;

/**
 * Class Neweva   新的评教功能
 * @package app\api\controller\v1
 */
class Classplan extends Controller
{
    public function classplan($studentID, $password)
    {
        $cookie = GetCookie::getCookie();
        CheckParams::checkParams($studentID, $password);
        $result = (new plan()) -> classplan($studentID, $password, $cookie);
        return $result;
    }
}