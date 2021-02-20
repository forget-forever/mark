<?php
/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/8/24
 * Time: 10:27
 */

namespace app\api\lib\Exception;


class IpStop extends BaseException
{
    public $code = 505;
    public $msg = 'IP已冻结';
    public $errorCode = 999;
}