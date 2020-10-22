<?php
/**
 * 加密解密
 */

namespace Tools;


class Encoder
{
    private static function _passKey($string, $key)
    {
        $key = md5($key);

        $ctr = 0;
        $tmp = '';
        $len_s = strlen($string);
        $len_k = strlen($key);

        for ($i = 0; $i < $len_s; $i++) {
            $ctr = $ctr == $len_k ? 0 : $ctr;
            $tmp .= $string[$i] ^ $key[$ctr++];
        }

        return $tmp;
    }

    /**
     * 加密
     *
     * @param $string
     * @param $key
     *
     * @return string
     */
    public static function encode($string, $key)
    {
        $encrypt_key = md5(microtime());

        // 变量初始化
        $ctr = 0;
        $tmp = '';
        $len_s = strlen($string);

        for ($i = 0; $i < $len_s; $i++) {
            $ctr = $ctr == 32 ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($string[$i] ^ $encrypt_key[$ctr++]);
        }

        return base64_encode(self::_passKey($tmp, $key));
    }

    /**
     * 解密
     *
     * @param $string
     * @param $key
     *
     * @return string
     */
    public static function decode($string, $key)
    {
        $string = self::_passKey(base64_decode($string), $key);

        $tmp = '';
        $len_s = strlen($string);

        for ($i = 0; $i < $len_s; $i++) {
            $tmp .= $string[$i] ^ $string[++$i];
        }

        return $tmp;
    }
}