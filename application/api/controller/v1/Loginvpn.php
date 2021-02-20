<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-07 13:46:32 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-10-11 20:59:00
 */

namespace app\api\controller\v1;

header('Access-Control-Allow-Origin:*');

use think\Controller;
use app\api\model\Loginvpn as loginvpnModel;

class Loginvpn extends Controller {
    public function loginvpn ($studentID, $password){
        $res = (new LoginvpnModel())->doLogin($studentID, $password);
        return $res;
    }
}

?>