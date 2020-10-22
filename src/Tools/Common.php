<?php
/**
 * 通用函数类库
 */

namespace Tools;


class Common
{
    /**
     * 随机生成密码
     *
     * @param int $length 长度
     * @param int $flag 类型：1：数字 2：字母 3：数字+字母
     * @return string
     */
    public static function genPassword($length = 8, $flag = 1)
    {
        switch ($flag) {
            case 1:
                $str = '0123456789';
                break;
            case 2:
                $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            default:
                $str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
        }

        for ($i = 0, $password = ''; $i < $length; $i++) {
            $password .= Strings::subStr($str, mt_rand(0, Strings::strLen($str) - 1), 1);
        }

        return $password;
    }

    /**
     * 获取随机数
     *
     * @param int $min 起始数
     * @param int $max 结束数
     * @return int
     */
    public static function getRandom($min, $max)
    {
        srand((double)microtime() * 1000000);

        return rand($min, $max);
    }

    /**
     * 替换第一次出现的字符串
     *
     * @param string $search 需要搜索的字符
     * @param string $replace 替换后的字符
     * @param string $subject 待搜索的字符串
     * @param int $cur 规定在何处开始搜索
     * @return mixed
     */
    public static function strReplaceFirst($search, $replace, $subject, $cur = 0)
    {
        return (strpos($subject, $search, $cur)) ? substr_replace($subject, $replace, (int)strpos($subject, $search, $cur), strlen($search)) : $subject;
    }

    /**
     * 跳转
     *
     * @param string $url
     * @param null $headers
     */
    public static function redirect($url, $headers = null)
    {
        if (!empty($url)) {
            if ($headers) {
                if (!is_array($headers))
                    $headers = array($headers);

                foreach ($headers as $header)
                    header($header);
            }

            header('Location: ' . $url);
            exit;
        }
    }

    /**
     * 跳转
     *
     * @param string $link
     */
    public static function redirectTo($link)
    {
        if (strpos($link, 'http') !== false) {
            header('Location: ' . $link);
        } else {
            header('Location: ' . Http::getHttpHost(true) . '/' . $link);
        }
        exit;
    }

    /**
     * 时间辍转换为字符串
     * @param int $timestamp 需要转换的时间辍
     * @param int $current_time 对比的时间辍
     * @return false|string
     */
    public static function timestampToHuman($timestamp, $current_time = 0)
    {
        if (!$current_time) $current_time = time();
        $span = $current_time - $timestamp;
        if ($span < 60) {
            return "刚刚";
        } else if ($span < 3600) {
            return intval($span / 60) . "分钟前";
        } else if ($span < 24 * 3600) {
            return intval($span / 3600) . "小时前";
        } else if ($span < (7 * 24 * 3600)) {
            return intval($span / (24 * 3600)) . "天前";
        } else {
            return date('Y-m-d', $timestamp);
        }
    }

    public static function cleanNonUnicodeSupport($pattern)
    {
        if (!defined('PREG_BAD_UTF8_OFFSET'))
            return $pattern;

        return preg_replace('/\\\[px]\{[a-z]\}{1,2}|(\/[a-z]*)u([a-z]*)$/i', "$1$2", $pattern);
    }

    /**
     * nl2br
     * @param $str
     * @return string|string[]|null
     */
    public static function nl2br($str)
    {
        return preg_replace("/((<br ?\/?>)+)/i", "<br />", str_replace(array("\r\n", "\r", "\n"), "<br />", $str));
    }

    /**
     * br2nl
     * @param $str
     * @return mixed
     */
    public static function br2nl($str)
    {
        return str_replace("<br />", "\n", $str);
    }

    /**
     * 判断是否64位架构
     *
     * @return bool
     */
    public static function isX86_64arch()
    {
        return (PHP_INT_MAX == '9223372036854775807');
    }

    /**
     * HackNews热度计算公式
     *
     * @param $time 时间
     * @param $viewCount 浏览量
     *
     * @return float|int
     */
    public static function getGravity($time, $viewCount)
    {
        $timeGap = ($_SERVER['REQUEST_TIME'] - strtotime($time)) / 3600;
        if ($timeGap <= 24) {
            return 999999;
        }

        return round((pow($viewCount, 0.8) / pow(($timeGap + 24), 1.2)), 3) * 1000;
    }

}