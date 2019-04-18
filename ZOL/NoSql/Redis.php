<?php
/**
 * Redis类
 * @author 仲伟涛
 * 2011-7
 */
class ZOL_NoSql_Redis extends ZOL_NoSql_Abstraction
{

    protected static $redisArr  = array();#创建的redis对象的集合
    protected static $redis     = false;  #当前方法操作的redis对象
    protected static $dalCfg    = false;

    protected static function init($key){

        if (!isset(self::$redisArr[$key])){
            if (class_exists("Redis")) {
                #获得链接信息
                $dal            = new DAL_Redis();
                $keyInfo        = $dal->getKeyInfo($key);
                if($keyInfo){
                    $hostInfo   = $keyInfo['server'];
                    self::$dalCfg[$key]   = $keyInfo;
                    #连接redis
                    self::$redis    = new Redis();
                    self::$redis->connect($hostInfo['host'],$hostInfo['port']);
                    self::$redisArr[$key] = self::$redis;
                }
            } else {
                die("Redis接口模块不可用");
            }
        }else{
            self::$redis = self::$redisArr[$key];
        }

    }

    /**
     * 数据压缩格式
     */
    private static function compress($value){
        $value = array('D' => $value);
        return serialize($value);
    }

    /**
     * 数据压缩格式
     */
    private static function unCompress($value){
        $unValue = unserialize($value);
        return $unValue && isset($unValue['D']) || $unValue['D']==null ? $unValue['D'] : $value;
    }

    /*--------------------------------------------------------------------
                            key-value类型
    ---------------------------------------------------------------------*/
    /**
     * key-value 写
     */
    public static function set($key,$subKey,$value,$expire=0){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];

        $key = $keyInfo['key'] . ':' . $subKey;
        $value  = self::compress($value);

        $time   = $expire ? $expire : $keyInfo['time'];
        $time   = max($time,0);
        if ($time > 0) {
            return self::$redis->setex($key,$time,$value);
        } else {
            return self::$redis->set($key,$value);
        }
    }

    /**
     * key-value 读
     */
    public static function get($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;

        $keyInfo = self::$dalCfg[$key];
        $key = $keyInfo['key'] . ':' . $subKey;
        return self::unCompress(self::$redis->get($key));
    }


    /**
     * 设置多值
     */
    public function setMulti($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        $keyArr = array();
        if (is_array($subKey)) {
            foreach ($subKey as $k => $v) {
                $key            = $keyInfo['key'] . ':' . $k;
                $keyArr[$key]   = self::compress($v);
            }
        }
        return self::$redis->mset($keyArr);
    }

    /**
     * 获取多值
     * @param subKey 传入的是数组
     */
    public function getMulti($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        #key的处理
        $keyArr = array();
        if($subKey){

        }

        $arr = $this -> obj -> mget($keyArr);
        if (is_array($arr)) {
            foreach ($arr as $key=>$row) {
                $arr[$key]  = $this -> unCompress($row);
            }
        }
    }

    /**
     * 删除元素
     */
    public static function delete($key, $subKey){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->delete($key);
    }


    /*--------------------------------------------------------------------
                                   hash类型
    ---------------------------------------------------------------------*/
    /**
     * 存单键值
     */
    public static function hashSet($key, $subKey, $value,$snId=0)
    {
        self::init($snId);
        $key = self::$dalCfg->getKey('HASH',$key);
        if($key){
            $re = self::$redis->hSet($key, $subKey, self::compress($value));
            if ($time > 0) {
                self::$redis->expire($key, $time);
            }
            return $re;
        }
    }

    /**
     * 取单键值
     */
    public static function hashGet($key, $subKey,$snId=0){
        self::init($snId);
        $key = self::$dalCfg->getKey('HASH',$key);
        return self::unCompress(self::$redis->hGet($key, $subKey));
    }

    /*--------------------------------------------------------------------
                                   set类型
    ---------------------------------------------------------------------*/
    /**
     * 增加集合元素
     */
    public static function sAdd($key,$subKey,$value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        $re     = self::$redis->sAdd($key, $value);#认为set的处理是不压缩的
        $time   = $keyInfo['time'];
        if ($time > 0) {
            self::$redis->expire($key, $time);
        }
        return $re;
    }

    /**
     * 删除一个指定的元素
     */
    public static function sDelete($key , $subKey , $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sRemove($key, $value);
    }

    /**
     * 移动元素
     *
     * @param 要移动涉及的key $fromKey
     * @param 移动到的key $toKey
     * @param 元素 $value
     */
    public static function sMove($key ,$fromKey, $toKey, $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $fromKey = $keyInfo['key'] . ':' . $fromKey;
        $toKey   = $keyInfo['key'] . ':' . $toKey;

        $value  = self::compress($value);
        return self::$redis->sMove($fromKey, $toKey, $value);
    }

    /**
     * 统计元素个数
     */
    public static function sSize($key,$subKey){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sSize($key);
    }

    /**
     * 判断元素是否属于某个key
     */
    public static function sIsMember($key,$subKey, $value){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;
        return self::$redis->sIsMember($key, $value);
    }

    /**
     * 求交集
     *
     * @param key集合 $keyArr
     */
    public static function sInter($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sInter($keyArr);
        }
    }

    /**
     * 求交集并存储到另外的key中
     *
     * @param key集合 $keyArr 'output', 'key1', 'key2', 'key3'
     */
    public static function sInterStore($key,$ouput,$keyArr){
        self::init($key);
        #转换为真实的KEY
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        array_unshift($keyArr,$ouput);  #插入到数组的开头
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
        }
        return call_user_func_array(array(self::$redis, "sInterStore"), $keyArr);
    }

    /**
     * 求并集
     *
     * @param key集合 $keyArr
     */
    public static function sUnion($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];
        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sUnion($keyArr);
        }
    }

    /**
     * 求差集 A-B的操作
     *
     * @param key集合 $keyArr
     */
    public static function sDiff($key,$keyArr = array()){
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        $keyInfo = self::$dalCfg[$key];

        if($keyArr){
            foreach ($keyArr as $k => $v){
                $keyArr[$k] = $keyInfo['key'] . ':' . $v;
            }
            return self::$redis->sDiff($keyArr);
        }
    }
    /**
     * 获取当前key下的所有元素
     *
     * @param key集合 $key
     */
    public static function sMembers($key,$subKey){
        self::init($key);
        if(!self::$dalCfg[$key] || !$subKey)return false;
        $keyInfo = self::$dalCfg[$key];
        $key     = $keyInfo['key'] . ':' . $subKey;

        return self::$redis->sMembers($key);
    }


    public static function sMultiHset($paramArr)
    {
        $option = array(
            'key' => 'AladdinData', #这方法只能在SSDB用,所以默认是AladdinData
            'hashname' => '',
            'hashvalue' => array()
        );
        if(is_array($paramArr))$option = array_merge ($option,$paramArr);
        extract($option);
        
        if(empty($hashvalue) || empty($hashname)){
            return false;
        }
        
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        
        if(!empty($hashvalue) && is_array($hashvalue)){
            return self::$redis->hMSet($hashname,$hashvalue);
        }else{
            return false;
        }
    }
    
    public static function sMultiHget($paramArr)
    {
        $option = array(
            'key' => 'AladdinData', #这方法只能在SSDB用,所以默认是AladdinData
            'hashname' => '',
            'hashkeys' => array()
        );
        if(is_array($paramArr))$option = array_merge ($option,$paramArr);
        extract($option);
        
        if(empty($hashkeys) || empty($hashname)){
            return false;
        }
        
        self::init($key);
        if(!self::$dalCfg[$key])return false;
        if(!empty($hashkeys) && is_array($hashkeys)){
            return self::$redis->hMGet($hashname,$hashkeys);
        }else{
            return false;
        }
    }

	public static function setIfNotExist($key,$subKey,$value,$expire=0){
		self::init($key);
		if(!self::$dalCfg[$key] || !$subKey)return false;
		$keyInfo = self::$dalCfg[$key];

		$key = $keyInfo['key'] . ':' . $subKey;
		$value  = self::compress($value);


		if (self::$redis->setnx($key,$value)) {
			$time   = $expire ? $expire : $keyInfo['time'];
			$time   = max($time,0);

			if ($time !== false) {
				self::$redis->expire($key,$time);
			}

			return true;
		}

		return false;
	}

	public static function incr($key,$subKey){
		self::init($key);
		if(!self::$dalCfg[$key] || !$subKey)return false;
		$keyInfo = self::$dalCfg[$key];

		$key = $keyInfo['key'] . ':' . $subKey;

		return self::$redis->incr($key);
	}

	public static function decr($key,$subKey){
		self::init($key);
		if(!self::$dalCfg[$key] || !$subKey)return false;
		$keyInfo = self::$dalCfg[$key];

		$key = $keyInfo['key'] . ':' . $subKey;

		return self::$redis->decr($key);
	}
}

