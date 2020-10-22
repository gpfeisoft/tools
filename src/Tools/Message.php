<?php


namespace Tools;


class Message
{

    /**
     * 显示错误信息
     *
     * @param string $string
     * @param array $error
     * @param bool $htmlentities
     *
     * @return mixed|string
     */
    public static function displayError($string = 'Fatal error', $error = array(), $htmlentities = true)
    {
        if (DEBUG_MODE) {
            if (!is_array($error) || empty($error))
                return str_replace('"', '&quot;', $string) . ('<pre>' . print_r(debug_backtrace(), true) . '</pre>');
            $key = md5(str_replace('\'', '\\\'', $string));
            $str = (isset($error) AND is_array($error) AND key_exists($key, $error)) ? ($htmlentities ? htmlentities($error[$key], ENT_COMPAT, 'UTF-8') : $error[$key]) : $string;

            return str_replace('"', '&quot;', stripslashes($str));
        } else {
            return str_replace('"', '&quot;', $string);
        }
    }

    /**
     * 打印出对象的内容
     *
     * @param      $object
     * @param bool $kill
     *
     * @return mixed
     */
    public static function displayObject($object, $kill = true)
    {
        echo '<pre style="text-align: left;">';
        print_r($object);
        echo '</pre><br />';
        if ($kill)
            die('END');

        return ($object);
    }

}