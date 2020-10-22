<?php
/**
 * 数学函数类库
 */

namespace Tools;


class Math
{

    /**
     * 转换为int类型
     *
     * @param $val
     * @return int
     */
    public static function intVal($val)
    {
        if (is_int($val)) {
            return $val;
        }

        if (is_string($val)) {
            return (int)$val;
        }

        return (int)(string)$val;
    }

    public static function ceilf($value, $precision = 0)
    {
        $precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precisionFactor;
        $tmp2 = (string)$tmp;
        // If the current value has already the desired precision
        if (strpos($tmp2, '.') === false)
            return ($value);
        if ($tmp2[strlen($tmp2) - 1] == 0)
            return $value;

        return ceil($tmp) / $precisionFactor;
    }

    public static function floorf($value, $precision = 0)
    {
        $precisionFactor = $precision == 0 ? 1 : pow(10, $precision);
        $tmp = $value * $precisionFactor;
        $tmp2 = (string)$tmp;

        if (strpos($tmp2, '.') === false)
            return ($value);
        if ($tmp2[strlen($tmp2) - 1] == 0)
            return $value;

        return floor($tmp) / $precisionFactor;
    }

}