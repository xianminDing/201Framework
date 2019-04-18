<?php

/**
 * 抽象类
 * Memcache缓存类别相关操作
 * @author wiki <wu.kun@zol.com.cn>
 * @copyright (c) 2009-6-23
 */
abstract class ZOL_DAL_MongoCacheModule extends ZOL_DAL_FileCacheModule {

    /**
     * 是否散列存储
     * @var bool 
     */
    protected $_hash = false;
    protected $_expire = 0;
    protected $_mongoServerKey = 0; #mongodb服务器key
    protected $_isMongoWrite = 0; #mongodb是否是写数据
    protected $_mongoDbName  = 'Product'; #db库名称    
    protected $_ttl = false; #是否支持TTL
    protected $_assignMongoModuleName = false;
    
    //强制指定缓存模块名
    public function assginMongoModuleName($moduleName){
        $this->_assignMongoModuleName = $moduleName;
    }
    /**
     * 初始化处理参数和模块名;
     */

    public function processParam($cacheParam = '') {
        
        $moduleName = $this->_assignMongoModuleName ? $this->_assignMongoModuleName : str_replace("Modules_", "",get_class($this));#为了与旧框架缓存系统兼容
        
        $this->_moduleName = $moduleName;
        if (!($cacheParam instanceof ZOL_DAL_ICacheKey)) {
			$keyMakerName = ZOL_DAL_Config::getKeyMakerName($moduleName, 'MONGO');
            $keyMaker = new $keyMakerName($this->_moduleName, (array) $cacheParam);
        } else {
            $keyMaker = &$cacheParam;
            $keyMaker->setModuleName($this->_moduleName);
        }
        $this->_cacheParam = $keyMaker->getCacheParam();
        $this->_cacheKey = $keyMaker->getCacheKey();
        return $this;
    }

    /**
     * 获取MemCache缓存
     * 可被重写
     * @return mixed
     */
    public function get($cacheParam = null) {
        
        $fileCheckDieFile = "/www/mongo-memcache-check.lock";#检查mongodb服务器是不是有问题
        if ($cacheParam !== null && $this->_cacheParam !== $cacheParam) {
            $this->processParam($cacheParam);
        }
        #返回缓存数据
        if (!empty($this->_cachePool[$this->_cacheKey])) {
            return $this->_cachePool[$this->_cacheKey];
        }
        $modName = $this->_moduleName;
        $data = ZOL_Caching_Mongo::get($modName, $this->_cacheKey, false, $this->_mongoServerKey,0,$this->_mongoDbName);
        #没有拿到数据,可能是这个数据是散列存储的,导致模块名称不对.
        #$this->_hash是缓存模块程序(这个类的子类)里面set的一个bool值,所以远程调用只能尝试变更这个属性再次获取
        if(!$data){
            $modName .= '.'.$this->_cacheKey[0];
            $data = ZOL_Caching_Mongo::get($modName, $this->_cacheKey, false, $this->_mongoServerKey,0,$this->_mongoDbName);
        }
		#添加日志功能
		if(IS_DEBUGGING){
			$nowTime    = date("H:i:s");
			$nowUrl     = str_replace("_check_mongo_read=", "", $_SERVER["REQUEST_URI"]);
			$logContent = "{$nowUrl} [{$nowTime}] CacheRead:{$modName} Param:".json_encode($cacheParam) . "\n";
			ZOL_Log::checkUriAndWrite(array('message'=>$logContent , 'paramName'=>'_check_mongo_read'));
		}

        #调试用的，打开之后自动刷新的缓存会直接刷新
//        if ($this->_autoRefresh) {
//            $data = '';
//        }
        #尝试解决经销商缓存读写比过高问题
        #如果缓存是自动刷新的，在非正常访问的情况下，强制变成非自动更新
        #如果是阿拉丁触发(IS_ALADDIN_CRAW == true),一定要强制刷新缓存.mod by chenjt @ 2015年12月30日
        if($this->_autoRefresh){
            if(ZOL_Http::isCrawler() || !ZOL_HTTP::isRealPerson()){
                $this->_autoRefresh = FALSE;
            }
            if(defined('IS_ALADDIN_CRAW') && IS_ALADDIN_CRAW){
                $this->_autoRefresh = 1;
            }
        }
        $dataTemp = '';
        if ((isset($data['date']) && isset($data['exprieTime']) || $this->_autoRefresh)) {
            $nowTime   = SYSTEM_DATE;
            $publishDate = date("Y-m-d H:i:s", SYSTEM_TIME-3600);
            if(isset($data['date'])){
                $publishDate = $data['date'];
            }
            $exprieCacheTime = 3600;
            if(isset($data['exprieTime'])) {
               $exprieCacheTime  = $data['exprieTime'];
            }

            $expreTime = SYSTEM_TIME - strtotime($publishDate);
            if ($this->_autoRefresh && $expreTime >= $exprieCacheTime) {
                if(!ZOL_File::exists($fileCheckDieFile)) {
                    $dataTemp = $data;  #临时存一份旧数据，在获得锁的时候返回旧数据
                    $data = false;
                }
            }
        }
        if(!$data && $this->_autoRefresh){
            #自动刷新就加个锁，如果有锁就不刷新了，并且返回旧数据
            $locked = ZOL_Api::run("Kv.Redis.stringGet" , array(
                'serverName'     => 'ZCloud1',          #服务器名
                'key'            => $this->_cacheKey,   #获得数据的Key
            ));
            if($locked == 1){
                $data = $dataTemp;
            }else{
                unset($dataTemp);
            }
        }
        if (!$data && $this->_autoRefresh) {#自动更新缓存
            #自动刷新就加个锁，如果有锁就不刷新了
            $lockResult = ZOL_Api::run("Kv.Redis.stringSet" , array(
                'serverName'     => 'ZCloud1',       #服务器名
                'key'            => $this->_cacheKey,#Key
                'value'          => 1,               #value
                'life'           => 1800,            #生命期（秒）
            ));
            $this->refresh($this->_cacheParam);
            $data = $this->_content;
        }
        #释放内存
        if ($this->_cachePool && count($this->_cachePool) > self::CACHE_SIZE) {
            array_pop($this->_cachePool);
        }
        $this->_cachePool[$this->_cacheKey] = $data;
        return $data;
    }

    /**
     * 设置MemCache缓存
     */
    public function set($cacheParam = null, $content = '') {
    
        if (isset($cacheParam) && $this->_cacheParam != $cacheParam) {
            $this->processParam($cacheParam);
        }
        //$this->getRandExpire();

        if ($content && is_array($content)) {
            $content = self::arrayFilter($content);
        }
       
        $this->_content = $content;
        
        if (empty($this->_cacheKey)) {
            return false;
        }

        if (empty($this->_content)) {
            $this->rm();
            return false;
        }
        
        $modName = $this->_moduleName;
        $modName .= $this->_hash ? ('.' . $this->_cacheKey[0]) : '';
       
        $expire = $this->_isDuly ? ($this->_expire - (SYSTEM_TIME % $this->_expire)) : $this->_expire;
        
        return ZOL_Caching_Mongo::set($modName, $this->_cacheKey, $this->_content, $expire, $this->_mongoServerKey, $this->_isMongoWrite,$this->_mongoDbName,$this->_ttl);
    }

    /**
     * 删除MemCache缓存
     */
    public function rm($cacheParam = null) {
        if (isset($cacheParam) && $this->_cacheParam != $cacheParam) {
            $this->processParam($cacheParam);
        }

        if (empty($this->_cacheKey)) {
            return false;
        }

        $modName = $this->_moduleName;
        $modName .= $this->_hash ? ('.' . $this->_cacheKey[0]) : '';
        return ZOL_Caching_Mongo::delete($modName, $this->_cacheKey,$this->_mongoServerKey,1,$this->_mongoDbName);
    }

}
