<?php

namespace Liucg1995\Uploader\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Upload extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * 针对数字的加密解密
     * 加密：alpha_id(36731) >> fmQJjeWjk
     * 解密：alpha_id(fmQJjeWjk, true) >> 36731
     *
     * @param string $in 输入字符
     * @param string $to_num 是否转为数字
     * @param number $pad_up 密文长度
     * @param real $pass_key salt
     * @return mixed|string
     */
   public static function alpha_id($in, $to_num = false, $pad_up = 9, $pass_key = 2543.5415412812)
    {
        $strbase = "Flpvf70CsakVjqgeWUPXQxSyJizmNH6B1u3b8cAEKwTd54nRtZOMDhoG2YLrI";

        $codelen = substr($strbase, 0, $pad_up);

        $codenums = substr($strbase, $pad_up, 10);

        $codeext = substr($strbase, $pad_up + 10);

        if ($to_num) {
            $begin = substr($in, 0, 1);

            $rtn = '';

            $len = strpos($codelen, $begin);

            if ($len !== false) {
                $len++;

                $arrnums = str_split(substr($in, -$len));

                foreach ($arrnums as $v) {
                    $rtn .= strpos($codenums, $v);
                }
            }

            return $rtn;
        } else {
            $rtn = "";

            $numslen = strlen($in);

            // 密文第一位标记数字的长度

            $begin = substr($codelen, $numslen - 1, 1);

            // 密文的扩展位

            $extlen = $pad_up - $numslen - 1;

            $temp = str_replace('.', '', $in / $pass_key);

            $temp = substr($temp, -$extlen);

            $arrextTemp = str_split($codeext);

            $arrext = str_split($temp);

            foreach ($arrext as $v) {
                $rtn .= $arrextTemp[$v];
            }

            $arrnumsTemp = str_split($codenums);

            $arrnums = str_split($in);

            foreach ($arrnums as $v) {
                $rtn .= $arrnumsTemp[$v];
            }

            return $begin . $rtn;
        }
    }
}
