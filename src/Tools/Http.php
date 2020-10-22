<?php
/**
 * Http相关类库
 */

namespace Tools;


class Http
{
    /**
     * 获取当前域名
     *
     * @param bool $http
     * @param bool $entities
     * @return string
     */
    public static function getHttpHost($http = false, $entities = false)
    {
        $host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
        if ($entities) {
            $host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
        }

        if ($http) {
            $host = self::getCurrentUrlProtocolPrefix() . $host;
        }

        return $host;
    }

    /**
     * 获取当前服务器名
     *
     * @return mixed
     */
    public static function getServerName()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_SERVER']) && $_SERVER['HTTP_X_FORWARDED_SERVER'])
            return $_SERVER['HTTP_X_FORWARDED_SERVER'];

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * 获取用户IP地址
     *
     * @return mixed
     */
    public static function getRemoteAddr()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR']) || preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                return $ips[0];
            } else
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 获取用户来源地址
     *
     * @return null
     */
    public static function getReferer()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            return null;
        }
    }

    /**
     * 判断是否使用了HTTPS
     *
     * @return bool
     */
    public static function usingSecureMode()
    {
        if (isset($_SERVER['HTTPS']))
            return ($_SERVER['HTTPS'] == 1 || strtolower($_SERVER['HTTPS']) == 'on');
        if (isset($_SERVER['SSL']))
            return ($_SERVER['SSL'] == 1 || strtolower($_SERVER['SSL']) == 'on');

        return false;
    }

    /**
     * 获取当前URL协议
     *
     * @return string
     */
    public static function getCurrentUrlProtocolPrefix()
    {
        if (self::usingSecureMode())
            return 'https://';
        else
            return 'http://';
    }

    /**
     * 判断是否本站链接
     *
     * @param string $referrer
     * @return string
     */
    public static function secureReferrer($referrer)
    {
        if (preg_match('/^http[s]?:\/\/' . Tools::getServerName() . '(:443)?\/.*$/Ui', $referrer))
            return $referrer;

        return '/';
    }

    /**
     * 获取POST或GET的指定字段内容
     *
     * @param string $key
     * @param bool $default
     * @return bool|string
     */
    public static function getInput($key, $default = false)
    {
        if (!isset($key) || empty($key) || !is_string($key)) {
            return false;
        }

        $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $default));

        if (is_string($ret) === true) {
            $ret = trim(urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret))));
        }

        return !is_string($ret) ? $ret : stripslashes($ret);
    }

    /**
     * 判断POST或GET中是否包含指定字段
     *
     * @param string $key
     * @return bool
     */
    public static function getInputIsset($key)
    {
        if (!isset($key) || empty($key) || !is_string($key))
            return false;

        return isset($_POST[$key]) ? true : (isset($_GET[$key]) ? true : false);
    }

    /**
     * 判断是否为Post
     *
     * @return bool
     */
    public static function isPost()
    {
        return isset($_SERVER["REQUEST_METHOD"]) ? strtolower($_SERVER["REQUEST_METHOD"]) == 'post' : false;
    }

    /**
     * 判断是否为Get
     *
     * @return bool
     */
    public static function isGet()
    {
        return isset($_SERVER["REQUEST_METHOD"]) ? strtolower($_SERVER["REQUEST_METHOD"]) == 'get' : false;
    }

    /**
     * 判断是否为Ajax
     * @return bool
     */
    public static function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    /**
     * 得到客户端的IP地址
     *
     * @return string
     */
    public static function getClientIp()
    {
        $unknown = 'unknown';
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        /**
         * 处理多层代理的情况
         * 或者使用正则方式：$ip = preg_match("/[\d\.]{7,15}/", $ip, $matches) ? $matches[0] : $unknown;
         */
        if (false !== strpos($ip, ',')) {
            $ip = reset(explode(',', $ip));
        }
        return $ip;
    }

    /**
     * 判断是否命令行执行
     *
     * @return bool
     */
    public static function isCli()
    {
        if (isset($_SERVER['SHELL']) && !isset($_SERVER['HTTP_HOST'])) {
            return true;
        }

        return false;
    }

    /**
     * 判断是否爬虫，范围略大
     *
     * @return bool
     */
    public static function isSpider()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
            $spiders = array('spider', 'bot');
            foreach ($spiders as $spider) {
                if (strpos($ua, $spider) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 把文件发送至浏览器
     * @param $file
     * @param bool $delaftersend
     * @param bool $exitaftersend
     */
    public static function sendToBrowser($file, $delaftersend = true, $exitaftersend = true)
    {
        if (file_exists($file) && is_readable($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename = ' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check = 0, pre-check = 0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            if ($delaftersend) {
                unlink($file);
            }
            if ($exitaftersend) {
                exit;
            }
        }
    }

    /**
     * 对POST内容进行处理
     *
     * @return array
     */
    public static function safePostVars()
    {
        if (!is_array($_POST))
            return array();
        $_POST = array_map(array('Security', 'htmlentitiesUTF8'), $_POST);
    }

    /**
     * 把URL中的空格换成中划线
     * @param $url
     * @return string
     */
    public static function replaceSpace($url)
    {
        return urlencode(strtolower(preg_replace('/[ ]+/', '-', trim($url, ' -/,.?'))));
    }

    /**
     * 返回json
     * @param $array
     */
    public static function returnAjaxJson($array)
    {
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=utf-8");
        }
        echo(json_encode($array));
        ob_end_flush();
        exit;
    }

    /**
     * file_get_contents操作，超时关闭
     *
     * @param      $url
     * @param bool $use_include_path
     * @param null $stream_context
     * @param int $curl_timeout
     *
     * @return bool|mixed|string
     */
    public static function file_get_contents($url, $use_include_path = false, $stream_context = null, $curl_timeout = 8)
    {
        if ($stream_context == null && preg_match('/^https?:\/\//', $url))
            $stream_context = @stream_context_create(array('http' => array('timeout' => $curl_timeout)));
        if (in_array(ini_get('allow_url_fopen'), array('On', 'on', '1')) || !preg_match('/^https?:\/\//', $url))
            return @file_get_contents($url, $use_include_path, $stream_context);
        elseif (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($curl, CURLOPT_TIMEOUT, $curl_timeout);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            $opts = stream_context_get_options($stream_context);
            if (isset($opts['http']['method']) && Tools::strtolower($opts['http']['method']) == 'post') {
                curl_setopt($curl, CURLOPT_POST, true);
                if (isset($opts['http']['content'])) {
                    parse_str($opts['http']['content'], $datas);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
                }
            }
            $content = curl_exec($curl);
            curl_close($curl);

            return $content;
        } else {
            return false;
        }
    }

    /**
     * 以固定格式将数据及状态码返回手机端
     *
     * @param      $code
     * @param      $data
     * @param bool $native
     */
    public static function returnMobileJson($code, $data, $native = false)
    {
        if (!headers_sent()) {
            header("Content-Type: application/json; charset=utf-8");
        }
        if (is_array($data) && $native) {
            Arrays::walkArray($data, 'urlencode', true);
            echo(urldecode(json_encode(array('code' => $code, 'data' => $data))));
        } elseif (is_string($data) && $native) {
            echo(urldecode(json_encode(array('code' => $code, 'data' => urlencode($data)))));
        } else {
            echo(json_encode(array('code' => $code, 'data' => $data)));
        }
        ob_end_flush();
        exit;
    }
}