<?php
/**
 * Created by visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/6/19
 * Time: 14:00
 */

namespace app\api\lib\Exception;


class EvaluationNull extends BaseException
{
    public $code = 400;
    public $msg = '暂无评教';
    public $errorCode = 40004;
}