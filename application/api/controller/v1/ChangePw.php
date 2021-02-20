<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-08-29 15:59:54 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-08-29 16:11:07
 */

namespace app\api\controller\v1;

use think\Controller;
use app\api\model\ChangePw as Change;

class ChangePw extends Controller{
    public function changePw($studentID, $phone, $code, $newPassword, $cookie, $do){
        if($do == 1){
            $result = (new Change())->getCookie($studentID, $phone); 
        }
        return $result;
    }
}


?>