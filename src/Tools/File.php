<?php
/**
 * 文件相关类库
 */

namespace Tools;


class File
{
    /**
     * 返回临时文件路径
     * @return array|bool|false|string|null
     */
    public static function getTempDir()
    {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }
        if ($temp = getenv('TMP')) {
            return $temp;
        }
        if ($temp = getenv('TEMP')) {
            return $temp;
        }
        if ($temp = getenv('TMPDIR')) {
            return $temp;
        }
        $temp = tempnam(__FILE__, '');
        if (file_exists($temp)) {
            unlink($temp);

            return dirname($temp);
        }

        return null;
    }

    /**
     * 下载文件保存到指定位置
     *
     * @param $url
     * @param $filepath
     *
     * @return bool
     */
    public static function saveFile($url, $filepath)
    {
        if (Validate::isAbsoluteUrl($url) && !empty($filepath)) {
            $file = self::file_get_contents($url);
            $fp = @fopen($filepath, 'w');
            if ($fp) {
                @fwrite($fp, $file);
                @fclose($fp);

                return $filepath;
            }
        }

        return false;
    }

    /**
     * 文件复制
     *
     * @param $source
     * @param $dest
     *
     * @return bool
     */
    public static function copyFile($source, $dest)
    {
        if (file_exists($dest) || is_dir($dest)) {
            return false;
        }

        return copy($source, $dest);
    }

    /**
     * 遍历路径
     *
     * @param        $path
     * @param string $ext
     * @param string $dir
     * @param bool $recursive
     *
     * @return array
     */
    public static function scandir($path, $ext = 'php', $dir = '', $recursive = false)
    {
        $path = rtrim(rtrim($path, '\\'), '/') . '/';
        $real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
        $files = scandir($real_path);
        if (!$files)
            return array();

        $filtered_files = array();

        $real_ext = false;
        if (!empty($ext))
            $real_ext = '.' . $ext;
        $real_ext_length = strlen($real_ext);

        $subdir = ($dir) ? $dir . '/' : '';
        foreach ($files as $file) {
            if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length)))
                $filtered_files[] = $subdir . $file;

            if ($recursive && $file[0] != '.' && is_dir($real_path . $file))
                foreach (self::scandir($path, $ext, $subdir . $file, $recursive) as $subfile)
                    $filtered_files[] = $subfile;
        }

        return $filtered_files;
    }

    /**
     * 删除文件夹
     *
     * @param      $dirname
     * @param bool $delete_self
     */
    public static function deleteDirectory($dirname, $delete_self = true)
    {
        $dirname = rtrim($dirname, '/') . '/';
        if (is_dir($dirname)) {
            $files = scandir($dirname);
            foreach ($files as $file)
                if ($file != '.' && $file != '..' && $file != '.svn') {
                    if (is_dir($dirname . $file))
                        self::deleteDirectory($dirname . $file, true);
                    elseif (file_exists($dirname . $file))
                        unlink($dirname . $file);
                }
            if ($delete_self)
                rmdir($dirname);
        }
    }

    /**
     * 获取服务器配置允许最大上传文件大小
     *
     * @param int $max_size
     * @return mixed
     */
    public static function getMaxUploadSize($max_size = 0)
    {
        $post_max_size = self::convertBytes(ini_get('post_max_size'));
        $upload_max_filesize = self::convertBytes(ini_get('upload_max_filesize'));
        if ($max_size > 0)
            $result = min($post_max_size, $upload_max_filesize, $max_size);
        else
            $result = min($post_max_size, $upload_max_filesize);

        return $result;
    }

    /**
     * 转换byte
     * @param $value
     * @return int
     */
    public static function convertBytes($value)
    {
        if (is_numeric($value))
            return $value;
        else {
            $value_length = strlen($value);
            $qty = (int)substr($value, 0, $value_length - 1);
            $unit = strtolower(substr($value, $value_length - 1));
            switch ($unit) {
                case 'k':
                    $qty *= 1024;
                    break;
                case 'm':
                    $qty *= 1048576;
                    break;
                case 'g':
                    $qty *= 1073741824;
                    break;
            }

            return $qty;
        }
    }

    /**
     * 获取内存限制
     *
     * @return int
     */
    public static function getMemoryLimit()
    {
        $memory_limit = @ini_get('memory_limit');

        return self::getOctets($memory_limit);
    }

    public static function getOctets($option)
    {
        if (preg_match('/[0-9]+k/i', $option))
            return 1024 * (int)$option;

        if (preg_match('/[0-9]+m/i', $option))
            return 1024 * 1024 * (int)$option;

        if (preg_match('/[0-9]+g/i', $option))
            return 1024 * 1024 * 1024 * (int)$option;

        return $option;
    }

    /**
     * 获取文件扩展名
     *
     * @param $file 上传的文件
     * @return mixed|string
     */
    public static function getFileExtension($file)
    {
        if (is_uploaded_file($file)) {
            return "unknown";
        }

        return pathinfo($file, PATHINFO_EXTENSION);
    }
}