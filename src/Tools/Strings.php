<?php
/**
 * 字符串类库
 */

namespace Tools;

class Strings
{
    /**
     * 截取字符串
     *
     * @param string $str 需要截取的字符串
     * @param int $start 开始位置
     * @param bool/int $length 截取长度, 当为false的时候，是截取到最后
     * @param string $encoding 编码形式
     * @return bool|string
     */
    public static function subStr($str, $start, $length = false, $encoding = 'utf-8')
    {
        if (is_array($str) || is_object($str)) {
            return false;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($str, intval($start), ($length === false ? self::strLen($str) : intval($length)), $encoding);
        }

        return substr($str, $start, ($length === false ? self::strLen($str) : intval($length)));
    }

    /**
     * 计算字符串长度
     *
     * @param string $str 需要计算的字符串
     * @param string $encoding 编码形式
     * @return bool|int
     */
    public static function strLen($str, $encoding = 'UTF-8')
    {
        if (is_array($str) || is_object($str))
            return false;
        $str = html_entity_decode($str, ENT_COMPAT, 'UTF-8');
        if (function_exists('mb_strlen')) {
            return mb_strlen($str, $encoding);
        }

        return strlen($str);
    }

    /**
     * 截取指定字符中间的字符串
     *
     * @param string $string 需要截取的字符串
     * @param string $start 开始的字符
     * @param string $end 结束的字符
     * @return string
     */
    public static function strCut($string, $start, $end)
    {
        $one = explode($start, $string);
        $one = explode($end, $one[1]);

        return $one[0];
    }

    /**
     * 转换成大写字符串
     *
     * @param string $str 需要转换的字符串
     * @return bool|string
     */
    public static function strToUpper($str)
    {
        if (is_array($str)) {
            return false;
        }

        if (function_exists('mb_strtoupper')) {
            return mb_strtoupper($str, 'utf-8');
        }

        return strtoupper($str);
    }

    /**
     * 转换成小写字符，支持中文
     *
     * @param $str
     * @return bool|string
     */
    public static function strToLower($str)
    {
        if (is_array($str)) {
            return false;
        }

        if (function_exists('mb_strtolower')) {
            return mb_strtolower($str, 'utf-8');
        }

        return strtolower($str);
    }

    /**
     * 首字母大写
     *
     * @param string $str 需要转换的字符串
     * @return string
     */
    public static function ucFirst($str = '')
    {
        if ($str == '') {
            return $str;
        } else {
            return self::strToUpper(self::subStr($str, 0, 1)) . self::subStr($str, 1);
        }
    }

    /**
     * 截取字符串，支持中文
     *
     * @static
     * @access public
     * @param string $str 需要转换的字符串
     * @param int $start 开始位置
     * @param string $length 截取长度
     * @param string $charset 编码格式
     * @param bool $suffix 截断显示字符
     * @return string
     */
    public static function truncate($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
    {
        if (function_exists("mb_substr"))
            $slice = mb_substr($str, $start, $length, $charset);
        elseif (function_exists('iconv_substr')) {
            $slice = iconv_substr($str, $start, $length, $charset);
            if (false === $slice) {
                $slice = '';
            }
        } else {
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
        }
        return $suffix ? $slice . '...' : $slice;
    }

    /**
     * 获取字符串重音
     * @param $str
     * @return string|string[]|null
     */
    public static function replaceAccentedChars($str)
    {
        $patterns = array(
            /* Lowercase */
            '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
            '/[\x{00E7}\x{010D}\x{0107}]/u',
            '/[\x{010F}]/u',
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
            '/[\x{0142}\x{013E}\x{013A}]/u',
            '/[\x{00F1}\x{0148}]/u',
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
            '/[\x{0159}\x{0155}]/u',
            '/[\x{015B}\x{0161}]/u',
            '/[\x{00DF}]/u',
            '/[\x{0165}]/u',
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
            '/[\x{00FD}\x{00FF}]/u',
            '/[\x{017C}\x{017A}\x{017E}]/u',
            '/[\x{00E6}]/u',
            '/[\x{0153}]/u',
            /* Uppercase */
            '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
            '/[\x{00C7}\x{010C}\x{0106}]/u',
            '/[\x{010E}]/u',
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
            '/[\x{0141}\x{013D}\x{0139}]/u',
            '/[\x{00D1}\x{0147}]/u',
            '/[\x{00D3}]/u',
            '/[\x{0158}\x{0154}]/u',
            '/[\x{015A}\x{0160}]/u',
            '/[\x{0164}]/u',
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
            '/[\x{017B}\x{0179}\x{017D}]/u',
            '/[\x{00C6}]/u',
            '/[\x{0152}]/u'
        );

        $replacements = array(
            'a',
            'c',
            'd',
            'e',
            'i',
            'l',
            'n',
            'o',
            'r',
            's',
            'ss',
            't',
            'u',
            'y',
            'z',
            'ae',
            'oe',
            'A',
            'C',
            'D',
            'E',
            'L',
            'N',
            'O',
            'R',
            'S',
            'T',
            'U',
            'Z',
            'AE',
            'OE'
        );

        return preg_replace($patterns, $replacements, $str);
    }
}