<?php
/**
 * Created by PhpStorm.
 * User: CHUNLAI
 * Date: 2018/10/17
 * Time: 1:32
 */

namespace app\api\controller\v1;


use app\api\model\ResetPassword as ResetPasswordModel;
use app\api\lib\Exception\Illegal;
use think\Controller;
use think\Db;

class ResetPassword extends Controller
{
    public function resetPassword($studentID, $IDCard, $openid, $bind)
    {
        $result = ResetPasswordModel::doResetPassword($studentID, $IDCard);
        return $result;
        if($bind == 1){

            $result = ResetPasswordModel::doResetPassword($studentID, $IDCard);
            ini_set('date.timezone','Asia/Shanghai');
            $time=date('Y-m-d G:i:s');
            $data = ['studentID' => $studentID, 'qqID' => $openid, 'time' => $time];
            Db::table('id_info')->insert($data);
        }else{
            $res = Db::table("id_info")->where("qqID", $openid)->select();
            if(count($res) > 0){
                if($res[0]["studentID"] == $studentID){
                    $result = ResetPasswordModel::doResetPassword($studentID, $IDCard);
                }else {
                    throw new Illegal();
                }
            }else{
                // ini_set('date.timezone','Asia/Shanghai');
                // $time=date('Y-m-d G:i:s');
                // $data = ['studentID' => $studentID, 'qqID' => $openid, 'time' => $time];
                // Db::table('id_info')->insert($data);
                $result = "successful";
            }
            // $result = substr($result,0,strlen($result)-2); 
        }
        
        return $result;
    }
}