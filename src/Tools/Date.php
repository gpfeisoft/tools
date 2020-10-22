<?php
/**
 * 日期相关类库
 */

namespace Tools;


class Date
{
    /**
     * 生成指定范围的年份列表
     * @param int $start
     * @param int $end
     * @return array
     */
    public static function dateYears($start = 0, $end = 0)
    {
        $tab = [];

        $start = $start == 0 ? 1900 : $start;
        $end = $end == 0 ? date('Y') : $end;

        for ($i = $end; $i >= $start; $i--)
            $tab[] = $i;

        return $tab;
    }

    /**
     * 生成月列表
     * @param bool $english 是否显示英文月份
     * @return array
     */
    public static function dateMonths($english = true)
    {
        $tab = [];

        for ($i = 1; $i != 13; $i++) {
            if ($english == true) {
                $tab[$i] = date('F', mktime(0, 0, 0, $i, date('m'), date('Y')));
            } else {
                $tab[$i] = $i;
            }
        }

        return $tab;
    }

    /**
     * 生成日列表
     * @return array
     */
    public static function dateDays()
    {
        $tab = [];

        for ($i = 1; $i != 32; $i++)
            $tab[] = $i;

        return $tab;
    }

    /**
     * 根据时分秒生成时间字符串
     *
     * @param $hours
     * @param $minutes
     * @param $seconds
     * @return string
     */
    public static function hourGenerate($hours, $minutes, $seconds)
    {
        return implode(':', array($hours, $minutes, $seconds));
    }

    /**
     * 得到指定日期的开始时间
     *
     * @param $date
     * @return string
     */
    public static function dateFrom($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . self::hourGenerate(0, 0, 0);

        return $date;
    }

    /**
     * 得到指定日期的结束时间
     *
     * @param $date
     * @return string
     */
    public static function dateTo($date)
    {
        $tab = explode(' ', $date);
        if (!isset($tab[1]))
            $date .= ' ' . self::hourGenerate(23, 59, 59);

        return $date;
    }

    /**
     * 获取精准的时间
     *
     * @return int
     */
    public static function getExactTime()
    {
        return microtime(true);
    }

    /**
     * 得到精准的时间
     * @return array
     */
    public static function getMicroTime()
    {
        list($decimal, $integer) = explode(' ', microtime());
        return [
            'integer' => $integer,
            'decimal' => $decimal
        ];
    }

    /**
     * 得到精准的时间
     * @return float
     */
    public static function getMicroTimeSingle()
    {
        list($use, $sec) = explode(" ", microtime());

        return floor($sec + $use * 1000000);
    }

    /**
     * 获取日期
     *
     * @param null $timestamp
     * @return bool|string
     */
    public static function getSimpleDate($timestamp = null)
    {
        if ($timestamp == null) {
            return date('Y-m-d');
        } else {
            return date('Y-m-d', $timestamp);
        }
    }

    /**
     * 获取完整时间
     *
     * @param null $timestamp
     * @return bool|string
     */
    public static function getFullDate($timestamp = null)
    {
        if ($timestamp == null) {
            return date('Y-m-d H:i:s');
        } else {
            return date('Y-m-d H:i:s', $timestamp);
        }
    }


}