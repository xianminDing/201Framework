<?php
/**
 * 产品库搜索2.0读取XML插件
 * add by wanghb on 2011-06-09
 */
class Libs_Global_ReadSearchXml
{
    //读xml函数
    ###################################################################################
    # XML_unserialize: takes raw XML as a parameter (a string)
    # and returns an equivalent PHP data structure
    ###################################################################################
    public static function XML_unserialize($xml) {
        $xml_parser = new ZOL_Xml();
        $data = $xml_parser->parse($xml);
        $xml_parser->destruct();
        return $data;
    }
    ###################################################################################
    # XML_serialize: serializes any PHP data structure into XML
    # Takes one parameter: the data to serialize. Must be an array.
    ###################################################################################
    public static function XML_serialize($data, $level = 0, $prior_key = NULL) {
        if($level == 0){ ob_start(); echo '<?xml version="1.0" encoding="GBK" ?>',"\n"; }
        while(list($key, $value) = each($data))
            if(!strpos($key, ' attr'))
                if(is_array($value) and array_key_exists(0, $value)){
                    self::XML_serialize($value, $level, $key);
                }else{
                    $tag = $prior_key ? $prior_key : $key;
                    echo str_repeat("\t", $level),'<',$tag;
                    if(array_key_exists("$key attr", $data)){ #if there's an attribute for this element
                        while(list($attr_name, $attr_value) = each($data["$key attr"])) {
                            echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"';
                        }
                        reset($data["$key attr"]);
                    }

                    if(is_null($value)) {
                        echo " />\n";
                    } elseif (!is_array($value)) {
                        echo '>',htmlspecialchars($value),"</$tag>\n";
                    } else {
                        echo ">\n",self::XML_serialize($value, $level+1),str_repeat("\t", $level),"</$tag>\n";
                    }
                }
        reset($data);
        if($level == 0){ $str = &ob_get_contents(); ob_end_clean(); return $str; }
    }

    public static function count_numeric_items(&$array){
        return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0;
    }

    /**
     * 把GBK的XML转成GBK的编码
     * @param 输入的xml字符串 $str
     * @return 转换后的字符串
     */
    public static function change_xml_to_UTF8($str) {
        $str = str_replace(' encoding="GBK"',' encoding="UTF-8"',$str);
        return mb_convert_encoding($str,'UTF-8','GBK');
    }

    /**
     * 把字符串转换成GBK
     */
    public static function UTF82GBK($str) {
        if (is_array($str)) {
            return array_map('UTF82GBK',$str);
        } else {
            return mb_convert_encoding($str,'GBK','UTF-8');
        }
    }

    /**
     * 咱们自己的搜索提取
     * @param $sql 查询的sql语句
     * @param $host 搜索服务器域名
     * @param $port 搜索端口
     * @return 数据数组
     */
    public static function readSearchXml($sql,$host='product.lucene.zol.com.cn',$port='6035') {
        if ($sql) {
            $sql = preg_replace('#[\r\n\t]+#s',' ',$sql);   //替换掉所有的空格和回车，使语句不会出错
			#获得搜索的host,从sql中提取,product模块就去product.lucene.zol.com.cn product.mtall也是product
			$host = "product.lucene.zol.com.cn.";
			if(preg_match("/from ([a-z\.]+) where/isU", $sql,$tblMatch)){
				$host = trim($tblMatch[1]);
				if($pos = strpos($host, ".")){#取点之前的
					$host = substr($host, 0,$pos);
				}
				$host .= ".lucene.zol.com.cn.";
			}
            $data = '';
            ini_set('default_socket_timeout',2);
            $fp = fsockopen($host,$port);
            if ($fp) {
                fputs($fp,$sql."\n");
                $start = NULL;
                while(!self::safe_feof($fp, $start)) {
                    $data.= fgetc($fp);
                }
            }
            fclose($fp);
        } else {
            return '';
        }
        
        if (preg_match('#<\?xml([^>]*) encoding="GBK"#i',$data)) {
            $data = self::change_xml_to_UTF8($data);
        }
        
        $arr = self::XML_unserialize($data);
        if (is_array($arr)) {
            $arr = ZOL_String::u8conv($arr);
        }
        return $arr;
    }
    public static function safe_feof($fp, &$start = NULL) {
        return feof($fp);
    }
}

?>
