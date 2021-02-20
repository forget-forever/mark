<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-10-22 12:28:05 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-10-22 12:30:17
 */

namespace app\api\lib\Exception;


class FirstLoginException extends BaseException
{
    public $code = 400;
    public $msg = '首次登陆，需要绑定手机号';
    public $errorCode = 60005;
}
?>