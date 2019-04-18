<?php

/**
 * 除产品库外mongoDB发布与获取的助手方法
 * 产品库相关的绝对不能用这个!!!!
 * @author 陈景涛<chen.jingtao@zol.com.cn>
 * @date   2019年01月24日20:14:36
 */

class Helper_Mongo
{
    private static $_mongoServer = 'apicloud';     #移动端用的服务名称
    private static $_mongoTblName = 'appArticle';  #除产品库外,其余所有移动端使用的表都用这个名字,注意,这个TABLE的TTL为30天!
    
    private static $_cachekey = array();
    const CACHE_NUM = 100;
    
    //获取mongoDB的内容
    public static function get($paramArr){
        
        $options = array(
            'moduleName' => '',       #模块名称, 例如ArticleContent, ChannelList等
            'param'      => array(),  #参数列表,以数组形式传入,例如 array('docId'=>7701234)等
            'retry'      => true,
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        
        extract($options);
        
        if(!$moduleName || !is_array($param))return false;
        
        $param = self::arrayFilter($param);
        
        $mongoKey = $moduleName.'_'.http_build_query($param);
        
        //防止频繁读取某一个key消耗性能
        if(isset(self::$_cachekey[$mongoKey])){
            return self::$_cachekey[$mongoKey];
        }
        
        $mongoData = null;
        
        $mongoData = ZOL_Api::run("Kv.MongoCenter.get" , array(
            'module'    => self::$_mongoServer,             #业务模块名
            'tbl'       => self::$_mongoTblName,             #表名
            'key'       => $mongoKey, 
            'retry'     => $retry,    #是否尝试连接
            ));
        
        if(count(self::$_cachekey)<self::CACHE_NUM){
            self::$_cachekey[$mongoKey] = $mongoData;
        }else{
            array_pop(self::$_cachekey[$mongoKey]);
            self::$_cachekey[$mongoKey] = $mongoData;
        }
        return $mongoData;
    }
    
    /*
     * 写入mongoDB
     * moduleName:模块名称, 例如 ArticleContent, ChannelList等
     * key:   参数列表,以数组形式传入,例如 array('docId'=>7701234)等
     * data:  要发布的数据
     * NOTE:  不需要设置life,这个业务的数据30天失效。
     */
    public static function set($paramArr){
        
        $options = array(
            'moduleName' => '',       #模块名称
            'param'      => array(),  #参数列表
            'data'       => false,    #要发布的数据
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        
        extract($options);
        
        if(!$moduleName || !is_array($param))return false;
        
        $param = self::arrayFilter($param);
        
        //key把数组转化成字符串,再和modulename连在一起组合成唯一串
        $mongoKey = $moduleName.'_'.http_build_query($param);
        
        $mongoData = null;
        
        $mongoData = ZOL_Api::run("Kv.MongoCenter.set" , array(
            'module'    => self::$_mongoServer,             #业务模块名
            'tbl'       => self::$_mongoTblName,            #表名
            'key'       => $mongoKey, #key
            'data'      => $data,     #要发布的数据
            ));
        
        return $mongoData;
    }
    
    /**
	* 递归过滤数组值
	* @param mixed $array 传入的数组
	* @param mixed $callback 回调函数
	* @return array
	*/
	public static function arrayFilter(array $array, $callback = null)
	{
		foreach ($array as &$value) {
			if (is_array($value)) {
				$value = self::arrayFilter($value, $callback);
			}
		}
		return array_filter($array);
	}
}