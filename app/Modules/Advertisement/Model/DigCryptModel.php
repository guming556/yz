<?php

namespace App\Modules\Advertisement\Model;

use Illuminate\Database\Eloquent\Model;

class DigCryptModel extends Model {
    private $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";
    private $key, $length, $codelen, $codenums, $codeext;

    public function __construct($length = 9, $key = 2543.5415412812) {
        $this->key      = $key;
        $this->length   = $length;
        $this->codelen  = substr($this->strbase, 0, $this->length);
        $this->codenums = substr($this->strbase, $this->length, 10);
        $this->codeext  = substr($this->strbase, $this->length + 10);
    }

    /**
     * @param $nums
     * @return string
     * 加密
     */
    public function en($nums) {
        $rtn     = "";
        $numslen = strlen($nums);
        //密文第一位标记数字的长度
        $begin = substr($this->codelen, $numslen - 1, 1);
        //密文的扩展位
        $extlen     = $this->length - $numslen - 1;
        $temp       = str_replace('.', '', $nums / $this->key);
        $temp       = substr($temp, -$extlen);
        $arrextTemp = str_split($this->codeext);
        $arrext     = str_split($temp);
        foreach ($arrext as $v) {
            $rtn .= $arrextTemp[$v];
        }
        $arrnumsTemp = str_split($this->codenums);
        $arrnums     = str_split($nums);
        foreach ($arrnums as $v) {
            $rtn .= $arrnumsTemp[$v];
        }
        return $begin . $rtn;
    }

    /**
     * @param $code
     * @return bool|string
     * 解密
     */
    public function de($code) {
        if (!$code) {
            return false;
        }
        $begin = substr($code, 0, 1);
        $rtn   = '';
        $len   = strpos($this->codelen, $begin);
        if ($len !== false) {
            $len++;
            $arrnums = str_split(substr($code, -$len));
            foreach ($arrnums as $v) {
                $rtn .= strpos($this->codenums, $v);
            }
        }

        return $rtn;
    }

    /**
     * @param $string
     * @param $operation E表示加密 D表示解密
     * @param string $key
     * @return mixed|string
     */

    public function encrypt($string, $operation, $key = '') {
        $key           = md5($key);
        $key_length    = strlen($key);
        $string        = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key), 0, 8) . $string;
        $string_length = strlen($string);
        $rndkey        = $box = array();
        $result        = '';
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($key[$i % $key_length]);
            $box[$i]    = $i;
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j       = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp     = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a       = ($a + 1) % 256;
            $j       = ($j + $box[$a]) % 256;
            $tmp     = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'D') {
            if (substr($result, 0, 8) == substr(md5(substr($result, 8) . $key), 0, 8)) {
                return substr($result, 8);
            } else {
                return '';
            }
        } else {
            return str_replace('=', '', base64_encode($result));
        }
    }
}
