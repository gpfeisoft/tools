<?php
/**
 * 数组相关类库
 */

namespace Tools;


class Arrays
{
    /**
     * 把二维数组中的指定key组成一个新的一维数组
     *
     * @param $array
     * @param $key
     * @return array|null
     */
    public static function getArrayByKey($array, $key)
    {
        if (!empty($array) && is_array($array)) {
            $result = array();
            foreach ($array as $k => $item) {
                $result[$k] = $item[$key];
            }

            return $result;
        }

        return null;
    }

    /**
     * 对象转数组
     * @param $object
     * @return mixed
     */
    public static function object2array(&$object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * 遍历数组
     *
     * @param      $array
     * @param      $function
     * @param bool $keys
     */
    public static function walkArray(&$array, $function, $keys = false)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::walkArray($array[$key], $function, $keys);
            } elseif (is_string($value)) {
                $array[$key] = $function($value);
            }

            if ($keys && is_string($key)) {
                $newkey = $function($key);
                if ($newkey != $key) {
                    $array[$newkey] = $array[$key];
                    unset($array[$key]);
                }
            }
        }
    }

    /**
     * 移除数组中重复的值
     * @param $array
     * @return array
     */
    public static function arrayUnique($array)
    {
        if (version_compare(phpversion(), '5.2.9', '<')) {
            return array_unique($array);
        } else {
            return array_unique($array, SORT_REGULAR);
        }
    }

    /**
     * 移除数组中重复的值 二维数组
     * @param $array
     * @param bool $keepkeys
     * @return array
     */
    public static function arrayUnique2d($array, $keepkeys = true)
    {
        $output = array();
        if (!empty($array) && is_array($array)) {
            $stArr = array_keys($array);
            $ndArr = array_keys(end($array));

            $tmp = array();
            foreach ($array as $i) {
                $i = join("¤", $i);
                $tmp[] = $i;
            }

            $tmp = array_unique($tmp);

            foreach ($tmp as $k => $v) {
                if ($keepkeys)
                    $k = $stArr[$k];
                if ($keepkeys) {
                    $tmpArr = explode("¤", $v);
                    foreach ($tmpArr as $ndk => $ndv) {
                        $output[$k][$ndArr[$ndk]] = $ndv;
                    }
                } else {
                    $output[$k] = explode("¤", $v);
                }
            }
        }

        return $output;
    }
}