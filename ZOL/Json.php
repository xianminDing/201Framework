<?php
/*
 * 框架的Json类
 * 因为框架是GBK编码,使用这个类可以转化成UTF-8编码然后转为JSON字符串输出.
 * 
 * 如果引入了私有云,可以直接用api_json_encode和api_json_decode函数
 * 
 */
class ZOL_Json
{
    
    public static function Encode($string){
        array_walk_recursive($string, "api_json_convert_encoding_g2u");
        return json_encode($string);
    }
    
    public static function Decode($string, $assoc = true){
        $string = json_decode($string,$assoc);
        array_walk_recursive($string, "api_json_convert_encoding_u2g");
        return $string;
    }
    
    public static function api_json_convert_encoding_g2u(&$value, &$key){
        $value = mb_convert_encoding($value, "UTF-8", "GBK");
    }
    
    public static function api_json_convert_encoding_u2g(&$value, &$key){
        $value = mb_convert_encoding($value, "GBK", "UTF-8");
    }
    
}