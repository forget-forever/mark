<?php

/**
 * Created by Visual studio code.
 * User: ZHOU MEILEI
 * Date: 2019/8/18
 * Time: 8:40
 */

namespace app\api\model;

class GetVcode
{
    public function getHec($imagePath, $num, $source, $cookie)
    {
        $this -> getPic($cookie);
        $pic_1 = [];
        $res = imagecreatefromjpeg($imagePath);
        $k = 0;
        $pic = [];
        $codes = [];
        $pic_1[0] = '';
        $pic_1[1] = '';
        $pic_1[2] = '';
        $pic_1[3] = '';
        for ($i = 1, $m = 0; $i < 33; $i++) {
            for ($j = 1; $j < 91; $j++) {
                $rgb = imagecolorat($res, $j, $i);
                $rgbarray = imagecolorsforindex($res, $rgb);
                if ($rgbarray['red'] < 100 || $rgbarray['green'] < 100 || $rgbarray['blue'] < 100) {
                    // echo "0";
                    $pic[$m][$k++] = '0';
                } else {
                    // echo "1";
                    $pic[$m][$k++] = '1';
                }
            }
            $m++;
            $k = 0;
            // echo "<br>";
        }
        // $coeds = [];
        for ($i = 0; $i < count($pic[0]); $i++) {
            for ($j = 0; $j < count($pic); $j++) {
                if (($pic[$j][$i] == '0') && ($i != '0') && ($j != 0) && ($i != (count($pic[0]) - 1)) && ($j != (count($pic) - 1))) {
                    if (($pic[$j + 1][$i] == '0') || ($pic[$j - 1][$i] == '0') || ($pic[$j][$i + 1] == '0') || ($pic[$j][$i - 1] == '0')) {
                        $pic[$j][$i] = '0';
                    } else {
                        $pic[$j][$i] = '1';
                    }
                }
                $codes[$i][$j] = $pic[$j][$i];
            }
        }
        for ($i = 1, $falg = 0, $row = 0; $i < count($codes) - 1; $i++) {
            if ($row > 3) {
                break;
            }
            if ($falg && (!in_array('0', $codes[$i]))) {
                $row++;
                $falg = 0;
                continue;
            }
            for ($j = 0; $j < count($codes[$i]); $j++) {
                if ($j == count($codes[$i]) - 1) {
                    if ($falg) $pic_1[$row] = $pic_1[$row] . '1';
                    continue;
                }
                if ($codes[$i][$j] == '0' && ($i != 0) && ($j != 0)) {
                    if (($codes[$i + 1][$j] == '0') || ($codes[$i - 1][$j] == '0') || ($codes[$i][$j + 1] == '0') || ($codes[$i][$j - 1] == '0')) {
                        $pic_1[$row] = $pic_1[$row] . '0';
                        $falg = 1;
                    } else {
                        if ($falg)  $pic_1[$row] = $pic_1[$row] . '1';
                        // $pic_1[$row] = $pic_1[$row] . '1';
                    }
                } else {
                    if ($falg)  $pic_1[$row] = $pic_1[$row] . '1';
                }
                // echo $pic[$j][$i];
            }
            // echo "<br>";
        }

        for ($i = 0; $i < count($pic_1); $i++) {
            $pic_1[$i] = $this -> cutString($pic_1[$i], '1');
        }
        $true_codes = [];
        $parcent = [];
        // $sourceID = [];
        for ($i = 0; $i < count($pic_1); $i++) {
            $true_codes[$i] = '';
            if ($pic_1[3] == '') {
                break;
            }
            for ($j = 0; $j < count($source); $j++) {
                if (similar_text($pic_1[$i], $source[$j]['code'], $parcent[$i])) {
                    if ($parcent[$i] >= 90) {
                        $true_codes[$i] = $source[$j]['value'];
                        // $sourceID[$i] = $source[$j]['id'];
                        break;
                    }
                }
            }
            if ($true_codes[$i] == '') {
                break;
            }
        }
        if ($true_codes[0] && $true_codes[1] && $true_codes[2] && $true_codes[3]) {
            return implode('',$true_codes);
        } else {
            if ($num <= 15) {
                $num++;
                return $this -> getHec($imagePath, $num, $source, $cookie);
            } else {
                return false;
            }
        }
    }

    public function getPic($cookie)
    {
        $filename = "image.jpg";
        # 文件下载 BEGIN #
        // 打开临时文件，用于写入（w),b二进制文件
        // $resource = fopen($tmpFile, 'wb');
        // $resource = '';
        $fp = fopen($filename, 'wb');
        $curl = curl_init();
        // 设置输出文件为刚打开的
        curl_setopt($curl, CURLOPT_URL, 'http://authserver.jxust.edu.cn/authserver/captcha.html?ts=89');
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)
         AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
        curl_setopt($curl, CURLOPT_FILE, $fp);
        // 不需要请求头文件
        // curl_setopt($curl, CURLOPT_REFERER, 'http://authserver.jxust.edu.cn');
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        // 执行
        curl_exec($curl);
        // 关闭curl
        curl_close($curl);
        // return $res;
        // var_dump($fp);
        fclose($fp);
    }

    public function cutString($str, $s){
        if(substr($str, -1, 1) == $s){
            $str = substr($str, 0, -1);
            return $this -> cutString($str, $s);
        }else{
            return $str;
        }
    }
}
