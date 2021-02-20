<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-09-23 15:54:11 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2019-09-23 15:58:17
 */

namespace app\api\controller\v1;

use think\Controller;
use app\api\model\UpdateVpn as update;

class UpdateVpn extends Controller {
    public function updateVpn (){
        $res = (new update())->updateVpn();
        return $res;
    }
}

?>