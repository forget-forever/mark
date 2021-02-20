<?php
/*
 * @Author: ZHOU MEILEI
 * @CreateBy: Visual studio code 
 * @Date: 2019-11-07 22:01:14 
 * @Last Modified by:   ZHOU MEILEI 
 * @Last Modified time: 2019-11-07 22:01:14 
 */

namespace app\api\model;


use think\Db;

class UpdateVpn{
    public function UpdateVpn(){
        $res = Db::table('vpn_list')->select();
        $falg = [];
        $j = 0;
        ini_set('date.timezone', 'Asia/Shanghai');
        $time = date('Y-m-d G:i:s');
        $matches = [];
        for($i = 0; $i < count($res); $i++){
            $page = (new Utils())->curlOther('http://webvpn.jxust.edu.cn/', '', $res[$i]['value']);
            // echo $page;exit;
            if(strpos($page, '退出登录')){
                preg_match_all("/(Set-Cookie: )([^\r\n]+)(;)/i", $page, $matches);
                $cookievpn = implode(';', $matches[2]);
                // echo($cookievpn);exit;
                Db::table('vpn_list')->where('id', $res[$i]['id'])->update(['time' => $time, 'value' => $cookievpn]);
                continue;
            }else{
                
                Db::table('vpn_list')->where('id', $res[$i]['id'])->update(['extra' => ($res[$i]['extra'] + 1)]);
                $falg[$j++] = $res[$i]['id'];
                if($res[$i]['extra'] == '2'){
                    $url = 'http://api.zml.com/api/v1/mail?';
                    $data = [
                        'recipients' => '286248938@qq.com',
                        'content' => '<h1>cookie的错误次数已经达到三次，请注意查看</h1><br><h3>此次cookie值为：' . $res[$i]['value'] . '</h3>',
                        'name' => '小程序后台管理',
                        'subject' => 'cookie异常提醒'
                    ];
                    //将数组转换成字符串
                    $data = http_build_query($data);
                    $url = $url .  $data;
                    $res_0 = file_get_contents($url);
                }
            }
            // preg_match_all("/(Set-Cookie: )([^\r\n]+)()/i", $page, $matches);
        }
        if(count($falg) > 0){
            return $falg;
        }else{
            return false;
        }
    }
}


?>