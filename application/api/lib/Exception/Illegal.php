<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2020-02-16 13:00:03 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2020-02-16 13:01:32
 */

namespace app\api\lib\Exception;


class Illegal extends BaseException
{
    public $code = 400;
    public $msg = 'illegal operation';
    public $errorCode = 60010;
}