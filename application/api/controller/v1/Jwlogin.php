<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-10-09 21:25:45 
 * @Last Modified by: ZHOU MEILEI
 * @Last Modified time: 2020-02-16 16:44:52
 */

namespace app\api\controller\v1;



use app\api\model\CheckParams;
use app\api\model\Login;
use app\api\model\Utils;
use think\Controller;
use app\api\lib\Exception\Illegal;
use think\Db;

class Jwlogin extends Controller
{
    public function jwlogin($studentID, $password, $code)
    {
        $url = 'https://api.q.qq.com/sns/jscode2session?';
        $data = [
            'appid' => '1109559711',
            'secret' => 'axciKYIwbFhc4qig',
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];
        //将数组转换成字符串
        $data = http_build_query($data);
        $url = $url .  $data;
        $id = file_get_contents($url);
        $id = (new Utils()) -> object_to_array(json_decode($id));
		// var_dump($id);exit;
        if($id["errcode"] != 0){
            // $data = http_build_query($data);
			// $url = $url .  $data;
			$id = file_get_contents($url);
			$id = (new Utils()) -> object_to_array(json_decode($id));
            if($id["errcode"] != 0){
                throw new Illegal();
            }
        }
        // var_dump($id);exit;
        CheckParams::checkParams($studentID, $password);
        $result = (new Login())->doLogin($studentID, $password);
        if(count($result) > 2){
            if(!Db::table('id_info')->where('qqID', $id["openid"])->find()){
                ini_set('date.timezone','Asia/Shanghai');
                $time=date('Y-m-d G:i:s');
                $data = ['studentID' => $studentID, 'qqID' => $id["openid"], 'time' => $time];
                Db::table('id_info')->insert($data);
            }
            // $msg = Db::table('id_info')->where('qqID', $openid)->select();
            // if($studentID == $msg[0]['studentID']){
            //     return 'success';
            // }
        }
        return $result;
    }

}


?>