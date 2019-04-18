<?php

class Helper_FilterService extends Helper_Abstract
{
    private static $service_host = "10.19.35.51";
    private static $service_port = "6799";
    
    public static function getDataList($paramArr){
        
        $options = array(
            'type'          => 'book', #模块类型: book帖子, reply回复, zj攒机单
            'db'            => 'all',  #查询指定库的名字,一般取all
            'qf'            => '',     #指定搜索的字段的名称列表,使用逗号连接多个字段名称
            'rf'            => '',     #指定返回的字段的名称列表,使用逗号连接多个字段名称
            'dist'          => '2',    #指定距离参数(编辑距离*),默认2
            'num'           => '100',  #指定返回的结果集数量,默认100
            'key'           => '',     #指定要搜索的关键词的列表,使用逗号连接多个关键词.任意匹配到关键词的数据,都会被返回.
            'sf'            => '',     #指定用于排序的字段, 排序的字段必须为数字类型, 排序将会倒序
            'start'         => '',     #开始位置
            'debug'         => 0,      #调试模式只输出查询语句
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        $sql = self::getSql($options);
        
        extract($options);
        if($debug){
            echo $sql;exit;
        }
        if(empty($rf)){
            $rf = "lucnpk,title";
        }
        $attrArr = explode(',', $rf); #title,nickname.....
        $data = self::doQuery(array('sql'=>$sql,'returnCol'=>$attrArr));
        
        if (!empty($data['data'])) {
            foreach ($data['data'] as &$dataItem){
                if(!empty($dataItem['lucnpk'])){
                    $lucnpkArr = explode(':', $dataItem['lucnpk']);
                    $dataItem['dbName'] = isset($lucnpkArr[0]) ? $lucnpkArr[0] : '';
                    $dataItem['boardId'] = isset($lucnpkArr[1]) ? $lucnpkArr[1] : 0;
                    $dataItem['bookId'] = isset($lucnpkArr[2]) ? $lucnpkArr[2] : 0;
                }
            }
        }
        return $data;
    }
    
    public static function getSql($paramArr){
        
        $options = array(
            'type'          => 'book', #模块类型: book帖子, reply回复, zj攒机单
            'db'            => 'all',  #查询指定库的名字,一般取all
            'qf'            => '',     #指定搜索的字段的名称列表,使用逗号连接多个字段名称
            'rf'            => '',     #指定返回的字段的名称列表,使用逗号连接多个字段名称
            'dist'          => '2',    #指定距离参数(编辑距离*),默认2
            'num'           => '100',  #指定返回的结果集数量,默认100
            'key'           => '',     #指定要搜索的关键词的列表,使用逗号连接多个关键词.任意匹配到关键词的数据,都会被返回.
            'sf'            => '',     #指定用于排序的字段, 排序的字段必须为数字类型, 排序将会倒序
            'start'         => '',     #开始位置
            
            #编辑距离(Edit distance)* : 关键词和匹配词之间需要通过几步的操作可以变成完全一样, 比如"冰毒"和"冰消毒",只需要去掉一个"消"就可以完全一样,即编辑距离为1
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        $key = trim($key);
        if(!$key)return false;
        
        $sql = " -type ".$type." -db ".$db;
        
        if($qf){
            $sql .= " -qf ".$qf;
        }
        if($rf){
            $sql .= " -rf ".$rf;
        }
        if($key){
            $sql .= " -key ".$key;
        }
        if($sf){
            $sql .= " -sf ".$sf;
        }
        if($start){
            $sql .= " -start ".$start;
        }
        if($dist){
            $sql .= " -dist ".$dist;
        }
        if($num){
            $sql .= " -num ".$num;
        }
        return $sql;
    }
    
    public static function doQuery($paramArr){
        $options = array(
            'sql'           => '',   #要执行的SQL
            'returnCol'     => false,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        if(!$returnCol)return false;
		#获得搜索的host
		$host = self::$service_host;
        $port = self::$service_port;
		#数据请求
        $content = '';
		$timeout = 10; #socket超时设置为10秒
        ini_set('default_socket_timeout',2);
		$fp = fsockopen($host, $port,$errno,$errstr,$timeout);
        if (!$fp) {
            for($i=0; $i<4; $i++) {
                //service报错重试
                $fp = fsockopen($host, $port,$errno,$errstr,$timeout);
                if ($fp) break;
            }
        }
        if($fp){
            stream_set_timeout($fp, $timeout);
            fwrite($fp,$sql."\n");
            while (!feof($fp)) {
                $content .= fgets($fp, 2048);
            }
            fclose($fp);
        }
        if(!$content){
            
            $fp = fsockopen($host, $port,$errno,$errstr,$timeout);
            if (!$fp) {
                for($i=0; $i<4; $i++) {
                    $fp = fsockopen($host, $port,$errno,$errstr,$timeout);
                    if ($fp) break;
                }
            }
            if($fp){
                stream_set_timeout($fp, $timeout);
                fwrite($fp,$sql."\n");
                while (!feof($fp)) {
                    $content .= fgets($fp, 2048);
                }
                fclose($fp);
            }
        }
		#数据解析
		$content = trim ( $content );
        $content = iconv ( "GBK", 'UTF-8//IGNORE', $content ); #转成utf8才能解析
		$parser = xml_parser_create ();
		xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct ( $parser, $content, $dataArr, $indexArr );
		xml_parser_free ( $parser );
        
        $returnArr = array();
        if($indexArr){
            $tmp = $returnCol[0];
            if(isset($indexArr[$tmp])){
                foreach($indexArr[$tmp] as $k => $v){
                    foreach($returnCol as $colKey){
                        $idx = $indexArr[$colKey][$k];
                        if(!isset($dataArr[$idx]) || !isset($dataArr[$idx]['value']))continue;
                        $returnArr[$k][$colKey] = iconv ( "UTF-8", 'GBK', trim($dataArr[$idx]['value']));;
                    }
                }
            }
        }
		#结果封装
		return array(
            'index' => isset($dataArr[0]['attributes']) ? $dataArr[0]['attributes'] : array() ,
            'data'  => $returnArr,
        );
	}
}