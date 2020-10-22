<?php

/**
 * 安全类库
 */

namespace Tools;


class Security
{

    /**
     * @param string $content 需要过滤的内容
     * @param string $allow 保留的标签，用半角英文逗号间隔
     * @return string
     */
    public static function removeXss($content, $allow = "")
    {
        $danger = 'javascript,vbscript,expression,applet,meta,xml,blink,link,style,script,embed,object,iframe,frame,frameset,ilayer,layer,bgsound,title,base,';
        $event = 'onabort|onactivate|onafterprint|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|' .
            'onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onblur|onbounce|oncellchange|onchange|onclick|oncontextmenu|oncontrolselect|' .
            'oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondblclick|ondeactivate|ondrag|ondragend|ondragenter|ondragleave|' .
            'ondragover|ondragstart|ondrop|onerror|onerrorupdate|onfilterchange|onfinish|onfocus|onfocusin|onfocusout|onhelp|onkeydown|onkeypress|' .
            'onkeyup|onlayoutcomplete|onload|onlosecapture|onmousedown|onmouseenter|onmouseleave|onmousemove|onmouseout|onmouseover|onmouseup|' .
            'onmousewheel|onmove|onmoveend|onmovestart|onpaste|onpropertychange|onreadystatechange|onreset|onresize|onresizeend|onresizestart|' .
            'onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onselect|onselectionchange|onselectstart|onstart|onstop|onsubmit|onunload,';

        if (!empty($allow)) {
            foreach ($allow as $item) {
                $danger = str_replace($item . ',', '', $danger);
                $event = str_replace($item . '|', '', $event);
            }
        }

        //替换所有危险标签
        $content = preg_replace("/<\s*($danger)[^>]*>[^<]*(<\s*\/\s*\\1\s*>)?/is", '', $content);
        //替换所有危险的JS事件
        $content = preg_replace("/<([^>]*)($event)\s*\=([^>]*)>/is", "<\\1 \\3>", $content);

        return $content;
    }

    /**
     * XSS
     *
     * @param $str
     * @return mixed
     */
    public static function removeXSS2($str)
    {
        $str = str_replace('<!--  -->', '', $str);
        $str = preg_replace('~/\*[ ]+\*/~i', '', $str);
        $str = preg_replace('/\\\0{0,4}4[0-9a-f]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}5[0-9a]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}6[0-9a-f]/is', '', $str);
        $str = preg_replace('/\\\0{0,4}7[0-9a]/is', '', $str);
        $str = preg_replace('/&#x0{0,8}[0-9a-f]{2};/is', '', $str);
        $str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);
        $str = preg_replace('/&#0{0,8}[0-9]{2,3};/is', '', $str);

        $str = htmlspecialchars($str);
        // 非成对标签
        $lone_tags = array("img", "param", "br", "hr");
        foreach ($lone_tags as $key => $val) {
            $val = preg_quote($val);
            $str = preg_replace('/&lt;' . $val . '(.*)(\/?)&gt;/isU', '<' . $val . "\\1\\2>", $str);
            // $str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
            $str = preg_replace_callback('/<' . $val . '(.+?)>/i', function ($temp) {
                return str_replace("&quot;", "\"", $temp[0]);
            }, $str);


        }
        $str = preg_replace('/&amp;/i', '&', $str);

        // 成对标签
        $double_tags = array("table", "tr", "td", "font", "a", "object", "embed", "p", "strong", "em", "u", "ol", "ul", "li", "div", "tbody", "span", "blockquote", "pre", "b", "font");
        foreach ($double_tags as $key => $val) {
            $val = preg_quote($val);
            $str = preg_replace('/&lt;' . $val . '(.*)&gt;/isU', '<' . $val . "\\1>", $str);
            // $str = preg_replace_callback('/<' . $val . '(.+?)>/i', create_function('$temp', 'return str_replace("&quot;","\"",$temp[0]);'), $str);
            $str = preg_replace_callback('/<' . $val . '(.+?)>/i', function ($temp) {
                return str_replace("&quot;", "\"", $temp[0]);
            }, $str);
            $str = preg_replace('/&lt;\/' . $val . '&gt;/is', '</' . $val . ">", $str);
        }
        // 清理js
        $tags = Array(
            'javascript',
            'vbscript',
            'expression',
            'applet',
            'meta',
            'xml',
            'behaviour',
            'blink',
            'link',
            'style',
            'script',
            'embed',
            'object',
            'iframe',
            'frame',
            'frameset',
            'ilayer',
            'layer',
            'bgsound',
            'title',
            'base',
            'font'
        );

        foreach ($tags as $tag) {
            $tag = preg_quote($tag);
            $str = preg_replace('/' . $tag . '\(.*\)/isU', '\\1', $str);
            $str = preg_replace('/' . $tag . '\s*:/isU', $tag . '\:', $str);
        }

        $str = preg_replace('/[\s]+on[\w]+[\s]*=/is', '', $str);

        Return $str;
    }

    /**
     * 过滤SQL
     * @param string/array $string  需要过滤的内容
     * @param string $allow 保留的SQL语句，用半角英文逗号间隔
     * @return string/array
     */
    public static function filterSql($string, $allow = '')
    {
        if (is_array($string)) {
            foreach ($string as $key => $val) {
                $string[$key] = self::filter($val, $allow);
            }
        } else {
            $string = self::removeXss($string, $allow);
            $string = addslashes($string);
            //过滤sql注入
            $sqlStr = array("chr(", "'", "%27", "union", "left", "right", " and ", "%20and%20", "+and+", "version()", "select", "insert", "delete", "update", "create");

            foreach ($sqlStr as $item) {
                $string = str_replace($item, '', $string);
            }
        }

        return $string;
    }

    /**
     * 把字符转换为 HTML 实体。
     * @param $string
     * @param int $type
     * @return array|string
     */
    public static function htmlentitiesUTF8($string, $type = ENT_QUOTES)
    {
        if (is_array($string))
            return array_map(array('Security', 'htmlentitiesUTF8'), $string);

        return htmlentities((string)$string, $type, 'utf-8');
    }

    /**
     * 要把 HTML 实体转换回字符
     * @param $string
     * @return array|string
     */
    public static function htmlentitiesDecodeUTF8($string)
    {
        if (is_array($string))
            return array_map(array('Security', 'htmlentitiesDecodeUTF8'), $string);

        return html_entity_decode((string)$string, ENT_QUOTES, 'utf-8');
    }


}