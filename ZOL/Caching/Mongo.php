<?php

class ZOL_Caching_Mongo extends ZOL_Caching_Abstraction
{
//    protected static $server   = 'mongodb://localhost:27017';
    protected static $server   = 'localhost';
    protected static $wserver   = 'localhost';
    protected static $eserver = 'mongo_server_zoldb';//均衡服务器
    protected static $mongoDbArr = array(
        'memCache'=>array(
            'server'=>'localhost:11200',
            'wserver'=>'mongo_zpro_memcache_w:11200',
            'eserver'=>'mongo_zpro_memcache_r',
        ),
        'API'=>array(
            'server'=>'localhost:11200',
            'wserver'=>'mongo_zpro_memcache_w:11200',
            'eserver'=>'mongo_zpro_memcache_r', #负载均衡
        ),
    );
    protected static $mongo=array();
    protected static $_mongoServerKey = '';
    protected static $timeout = 1000; // 设置monogodb超时时间，正常情况1s已经很长，设置时间长了，一直等待，会堵死服务器
    protected static function init($mongoServerKey=0,$isMongoWrite=0)
    {
        static $reconn = 0;
        static $faildServer = null;
        $mongoKey = self::$_mongoServerKey.$mongoServerKey.$isMongoWrite;
        if (empty(self::$mongo[$mongoKey]))
        {
            defined('MONGOCACHE_CONF_KEY') || define('MONGOCACHE_CONF_KEY', 'Mongo');
            $memConf = ZOL_Config::get(MONGOCACHE_CONF_KEY);
            if ($memConf) {
                self::$server = $memConf;
            }
            try {
                if($mongoServerKey){
                    if(self::$mongoDbArr[$mongoServerKey]){
                        if($isMongoWrite){
                            self::$server = self::$mongoDbArr[$mongoServerKey]['wserver'];
                            self::$eserver = self::$mongoDbArr[$mongoServerKey]['wserver'];
                        }else{
                            self::$server = self::$mongoDbArr[$mongoServerKey]['server'];
                            self::$eserver = self::$mongoDbArr[$mongoServerKey]['eserver'];
                        }
                    }
                }else{
                        self::$server  = 'mongo_server_zoldb';
                        self::$eserver = 'mongo_server_zoldb';//均衡服务器
                }
                if ($reconn == 0) {
                    $server = self::$server;
                } else {
                    $server = self::$eserver;
                }
                $server = 'mongodb://' . $server;
                self::$mongo[$mongoKey] = new Mongo($server, array('timeout' => self::$timeout, 'persist' => 'Product'));
                //self::$mongo[$mongoKey] = new MongoClient($server, array('connectTimeoutMS' => self::$timeout));
            } catch (MongoException $e) {
                if (self::$mongo[$mongoKey]) {
                    self::$mongo[$mongoKey]->close();
                }
                self::$mongo[$mongoKey] = null;
                if ($reconn < 2) {
                    ++$reconn;
                    return self::init($mongoServerKey,$isMongoWrite);
                }
                ZOL_Log::write('异常1：'.$e->getMessage(), ZOL_Log::TYPE_ERROR);
                Plugin_Expires::setExpires(0);
                ZOL_Http::sendHeader(404);
                trigger_error($e->getMessage(), E_USER_WARNING);
                exit;
            }
        }
        $reconn = 0;
    }
    
    protected  static function getMod($modName,$mongoDBKey='',$dbName='localhost')
    {
        //defined('APP_NAME') || define('APP_NAME', 'Product');
        //$appName = APP_NAME;
        return self::$mongo[$mongoDBKey]->$dbName->$modName;
    }


    public static function get($modName, $key, $autoDel = false,$mongoDBKey=0,$isMongoWrite=0,$dbName='Product')
    {
        try {
            self::init($mongoDBKey,$isMongoWrite);
            $mongoDBKey = self::$_mongoServerKey.$mongoDBKey.$isMongoWrite;
            $mod = self::getMod($modName,$mongoDBKey,$dbName);
            $data = $mod->findOne(array('_id' => $key));
        }  catch (MongoException $e) {
              //ZOL_Log::write('异常2'.$e->getMessage(), ZOL_Log::TYPE_ERROR);
        }

//        catch (MongoConnectionException $e) {
//            return self::get($modName, $key, $autoDel);
//        }
//        if (!empty($data['_expire']) && $data['_expire'] <= SYSTEM_TIME) {
//            if ($autoDel) {
//                self::delete($modName, $key);
//            }
//            return false;
//        }
        return isset($data['data']) ? $data['data'] : null;
    }
    
    public static function delete($modName, $key,$mongoDBKey=0,$isMongoWrite=0,$dbName='Product')
    {
        self::init($mongoDBKey,$isMongoWrite);
        $mongoDBKey = self::$_mongoServerKey.$mongoDBKey.$isMongoWrite;
        return self::getMod($modName,$mongoDBKey,$dbName)->remove(array('_id' => $key));
    }
    public static function set($modName, $key = '', $var = '', $expire = 0,$mongoDBKey=0,$isMongoWrite=0,$dbName='Product',$ttl=false)
    {
        self::init($mongoDBKey,$isMongoWrite);   
        $expire = $expire ? (SYSTEM_TIME + $expire) : 0;
        $data = array('_id' => $key, 'data' => $var, '_expire' => $expire);
        //超时时间的设置 对应MongoDb的TTL
        if($ttl){
            $data["ttldt"] = new MongoDate(SYSTEM_TIME);
        }
        $mongoDBKey = self::$_mongoServerKey.$mongoDBKey.$isMongoWrite;
        return self::getMod($modName,$mongoDBKey,$dbName)->save($data);
    }
    public static function add($modName, $key = '', $var = '', $expire = 0,$mongoDBKey=0,$isMongoWrite=0,$dbName='Product')
    {
        self::init($mongoDBKey,$isMongoWrite);
        $expire = $expire ? (SYSTEM_TIME + $expire) : 0;
        $data = array('_id' => $key, 'data' => $var, '_expire' => $expire);
        $mongoDBKey = self::$_mongoServerKey.$mongoDBKey.$isMongoWrite;
        return self::getMod($modName,$mongoDBKey,$dbName)->insert($data);
    }
}

