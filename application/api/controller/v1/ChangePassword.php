<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2020-02-16 13:49:34 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2020-02-16 13:49:34 
 */

namespace app\api\controller\v1;

use app\api\model\ChangePassword as ChangePasswordModel;
use app\api\model\GetCookie;
use app\api\validate\NewPassword;
use think\Controller;

/**
 * Class ChangePassword 改变密码
 * @package app\api\controller\v1
 */
class ChangePassword extends Controller
{
    public function changePassword($studentID, $oldPassword, $newPassword)
    {
        //校验新密码
//        $data = ['password' => $newPassword];
//        (new NewPassword())->goCheck1($data);
        $result = (new ChangePasswordModel())->changePassword($studentID, $oldPassword, $newPassword);
        return $result;
    }
}