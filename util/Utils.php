<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author Wang Xiu <wangxiu@dodoca.com>
 */
final class Utils {

    private function __construct() {
    }


    /**
     * Capitalize the first letter of the given string
     * @param string $string string to be capitalized
     * @return string capitalized string
     */
    public static function capitalize($string) {
        return ucfirst(mb_strtolower($string));
    }

    /**
     * 手机验证
     * @param type $status
     * @return boolean
     */
    public static function isMobile($status) {
        if (self::getstatus($status, 11) || self::getstatus($status, 12) || self::getstatus($status, 13)) {
            return true;
        }
        return false;
    }
    
    public static function getstatus($status, $position) {
        $t = $status & pow(2, $position - 1) ? 1 : 0;
        return $t;
    }
    
    /**
     * 统计几维数组
     * @param type $arr
     * @return type
     */
    public static function arrayLevel($arr) {
        $al = array(0);
        function aL($arr, &$al, $level = 0) {
            if (is_array($arr)) {
                $level++;
                $al[] = $level;
                foreach ($arr as $v) {
                    aL($v, $al, $level);
                }
            }
        }

        aL($arr, $al);
        return max($al);
    }
    
    /**
     * 根据数组键值进行排序
     * @param type $arr
     * @param type $keys
     * @param type $type
     * @return type
     */
    public static function array_sort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
    
    /**
     * 将指定Array内的元素复制到目标Array中, 如果具有相同key, 则会被覆盖.
     * */
    public static function copyArrayItems($source, $target) {
        foreach ($source as $sourceItemKey => $sourceItemValue) {
            $target[$sourceItemKey] = $sourceItemValue;
        }
        return $target;
    }

    /**
     * 移除$target数组中中带有 $source中元素相同key的元素.
     * @param $source
     * @param $target
     * @return unknown_type
     */
    public static function removeArrayItems($source, $target) {
        foreach ($source as $sourceKey => $sourseValue) {
            unset($target[$sourseValue]);
        }
        return $target;
    }
    
    /**
     * 树形
     * 
     * @param unknown $list
     * @param string $pk
     * @param string $pid
     * @param string $child
     * @param number $root
     * @return multitype:unknown
     */
    static function listToTree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array ();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array ();
            foreach($list as $key => $data) {
                $refer [$data [$pk]] = & $list [$key];
            }
            foreach($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data [$pid];
                if($root == $parentId) {
                    $tree [] = & $list [$key];
                } else {
                    if(isset($refer [$parentId])) {
                        $parent = & $refer [$parentId];
                        $parent [$child] [] = & $list [$key];
                    }
                }
            }
        }
        return $tree;
    }
    
    /**
     * html过滤
     * @param array|object $_date
     * @return string
     */
    static public function htmlString($_date) {
        if (is_array($_date)) {
            foreach ($_date as $_key => $_value) {
                $_string[$_key] = self::htmlString($_value);  //递归
            }
        } elseif (is_object($_date)) {
            foreach ($_date as $_key => $_value) {
                $_string->$_key = self::htmlString($_value);  //递归
            }
        } else {
            $_string = htmlspecialchars($_date);
        }
        return $_string;
    }
    
    /**
     * 数据库输入过滤
     * @param string $_data
     * @return string
     */
    static public function mysqlString($_data) {
        $_data = trim($_data);
        return !GPC ? addcslashes($_data) : $_data;
    }
    
    /**
     * 获得真实IP地址  
     * @return string  
     */
    static public function realIp() {
        static $realip = NULL;
        if ($realip !== NULL)
            return $realip;
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        preg_match('/[\d\.]{7,15}/', $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }
    
    /**
     * 下载文件
     * @param string $file_path 绝对路径
     */
    static public function downFile($file_path) {
        //判断文件是否存在
        $file_path = iconv('utf-8', 'gb2312', $file_path); //对可能出现的中文名称进行转码
        if (!file_exists($file_path)) {
            exit('文件不存在！');
        }
        $file_name = basename($file_path); //获取文件名称
        $file_size = filesize($file_path); //获取文件大小
        $fp = fopen($file_path, 'r'); //以只读的方式打开文件
        header("Content-type: application/octet-stream");
        header("Accept-Ranges: bytes");
        header("Accept-Length: {$file_size}");
        header("Content-Disposition: attachment;filename={$file_name}");
        $buffer = 1024;
        $file_count = 0;
        //判断文件是否结束
        while (!feof($fp) && ($file_size - $file_count > 0)) {
            $file_data = fread($fp, $buffer);
            $file_count += $buffer;
            echo $file_data;
        }
        fclose($fp); //关闭文件
    }
    
    
	/**
     * 图片等比例缩放
     * @param resource $im    新建图片资源(imagecreatefromjpeg/imagecreatefrompng/imagecreatefromgif)
     * @param int $maxwidth   生成图像宽
     * @param int $maxheight  生成图像高
     * @param string $name    生成图像名称
     * @param string $filetype文件类型(.jpg/.gif/.png)
     */
    static public function resizeImage($im, $maxwidth, $maxheight, $name, $filetype) {
        $pic_width = imagesx($im);
        $pic_height = imagesy($im);
        if (($maxwidth && $pic_width > $maxwidth) || ($maxheight && $pic_height > $maxheight)) {
            if ($maxwidth && $pic_width > $maxwidth) {
                $widthratio = $maxwidth / $pic_width;
                $resizewidth_tag = true;
            }
            if ($maxheight && $pic_height > $maxheight) {
                $heightratio = $maxheight / $pic_height;
                $resizeheight_tag = true;
            }
            if ($resizewidth_tag && $resizeheight_tag) {
                if ($widthratio < $heightratio)
                    $ratio = $widthratio;
                else
                    $ratio = $heightratio;
            }
            if ($resizewidth_tag && !$resizeheight_tag)
                $ratio = $widthratio;
            if ($resizeheight_tag && !$resizewidth_tag)
                $ratio = $heightratio;
            $newwidth = $pic_width * $ratio;
            $newheight = $pic_height * $ratio;
            if (function_exists("imagecopyresampled")) {
                $newim = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresampled($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            } else {
                $newim = imagecreate($newwidth, $newheight);
                imagecopyresized($newim, $im, 0, 0, 0, 0, $newwidth, $newheight, $pic_width, $pic_height);
            }
            $name = $name . $filetype;
            imagejpeg($newim, $name);
            imagedestroy($newim);
        } else {
            $name = $name . $filetype;
            imagejpeg($im, $name);
        }
    }
    
    /**
     * 给已经存在的图片添加水印
     * @param string $file_path
     * @return bool
     */
    static public function addMark($file_path) {
        if (file_exists($file_path) && file_exists(MARK)) {
            //求出上传图片的名称后缀
            $ext_name = strtolower(substr($file_path, strrpos($file_path, '.'), strlen($file_path)));
            //$new_name='jzy_' . time() . rand(1000,9999) . $ext_name ;
            $store_path = ROOT_PATH . UPDIR;
            //求上传图片高宽
            $imginfo = getimagesize($file_path);
            $width = $imginfo[0];
            $height = $imginfo[1];
            //添加图片水印             
            switch ($ext_name) {
                case '.gif':
                    $dst_im = imagecreatefromgif($file_path);
                    break;
                case '.jpg':
                    $dst_im = imagecreatefromjpeg($file_path);
                    break;
                case '.png':
                    $dst_im = imagecreatefrompng($file_path);
                    break;
            }
            $src_im = imagecreatefrompng(MARK);
            //求水印图片高宽
            $src_imginfo = getimagesize(MARK);
            $src_width = $src_imginfo[0];
            $src_height = $src_imginfo[1];
            //求出水印图片的实际生成位置
            $src_x = $width - $src_width - 10;
            $src_y = $height - $src_height - 10;
            //新建一个真彩色图像
            $nimage = imagecreatetruecolor($width, $height);
            //拷贝上传图片到真彩图像
            imagecopy($nimage, $dst_im, 0, 0, 0, 0, $width, $height);
            //按坐标位置拷贝水印图片到真彩图像上
            imagecopy($nimage, $src_im, $src_x, $src_y, 0, 0, $src_width, $src_height);
            //分情况输出生成后的水印图片
            switch ($ext_name) {
                case '.gif':
                    imagegif($nimage, $file_path);
                    break;
                case '.jpg':
                    imagejpeg($nimage, $file_path);
                    break;
                case '.png':
                    imagepng($nimage, $file_path);
                    break;
            }
            //释放资源 
            imagedestroy($dst_im);
            imagedestroy($src_im);
            unset($imginfo);
            unset($src_imginfo);
            //移动生成后的图片
            @move_uploaded_file($file_path, ROOT_PATH . UPDIR . $file_path);
        }
    }
    
    /**
     * 从数组中删除空白的元素（包括只有空白字符的元素）
     *
     * @param array $arr
     * @param boolean $trim
     */
    function array_remove_empty($arr, $trim = true) {
        foreach ($arr as $key =>$value) {
            if (is_array($value)) {
                array_remove_empty($arr[$key]);
            } else {
                $value = trim($value);
                if ($value == '') {
                    unset($arr[$key]);
                } elseif ($trim) {
                    $arr[$key] = $value;
                }
            }
        }
    }
    
    /**
     * 从一个二维数组中返回指定键的所有值
     *
     * @param array $arr
     * @param string $col
     *
     * @return array
     */
    function array_col_values($arr, $col) {
        $ret = array();
        foreach ($arr as $row) {
            if (isset($row[$col])) {
                $ret[] = $row[$col];
            }
        }
        return $ret;
    }
    
    /**
     * 将一个二维数组按照指定字段的值分组
     *
     * @param array $arr
     * @param string $keyField
     *
     * @return array
     */
    function array_group_by($arr, $keyField) {
        $ret = array();
        foreach ($arr as $row) {
            $key = $row[$keyField];
            $ret[$key][] = $row;
        }
        return $ret;
    }
    
    /**
     * 根据指定的键值对数组排序
     *
     * @param array $array 要排序的数组
     * @param string $keyname 键值名称
     * @param int $sortDirection 排序方向
     *
     * @return array
     */
    function array_column_sort($array, $keyname, $sortDirection = SORT_ASC) {
        return array_sortby_multifields($array, array($keyname =>$sortDirection));
    }
    
    /**
     * 对数据进行编码转换
     * @param array/string $data       数组
     * @param string $input     需要转换的编码
     * @param string $output    转换后的编码
     */
    function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
        if (!is_array($data)) {
            return iconv($input, $output, $data);
        } else {
            foreach ($data as $key =>$val) {
                if (is_array($val)) {
                    $data[$key] = array_iconv($val, $input, $output);
                } else {
                    $data[$key] = iconv($input, $output, $val);
                }
            }
            return $data;
        }
    }
    
    /**
     * 将字符串转换为数组
     *
     * @param	string	$data	字符串
     * @return	array	返回数组格式，如果，data为空，则返回空数组
     */
    function string2array($data) {
        if ($data == '')
            return array();
        eval(";\$array = $data;");
        return $array;
    }
    
    /**
     * 返回经stripslashes处理过的字符串或数组
     * @param $string 需要处理的字符串或数组
     * @return mixed
     */
    function new_stripslashes($string) {
        if (!is_array($string))
            return stripslashes($string);
        foreach($string as $key =>$val) $string[$key] = new_stripslashes($val);
        return $string;
    }
    
    

}

?>
