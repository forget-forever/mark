<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/8/20
 * Time: 22:21
 */

namespace app\api\controller\v1;

use app\api\model\CheckCookie;
use think\Controller;

/**
 * Class Grade 查询在校成绩
 * @package app\api\controller\v1
 */
class IsLogin extends Controller
{
    public function isLogin($cookie)
    {
        $result = (new CheckCookie())->checkCookie($cookie);
        return $result;
    }
}