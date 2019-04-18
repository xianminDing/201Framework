<?php
/**
 * Memcache协议的消息队列
 * @author 仲伟涛
 * 2011-7
 */
class ZOL_Queue_Memcache extends ZOL_Queue_Abstraction
{
    protected static $serverIp   = 'ca_redis_3'; #消息队列的服务器的相关信息
    protected static $serverPort = 11213;
    protected static $mem;

    protected static function init()
    {
        if (empty(self::$mem))
        {
            self::$mem = new Memcache;
            self::$mem->connect(self::$serverIp, self::$serverPort,1);//第三个参数是超时时间(秒),好奇怪为什么秒为单位呢
        }
    }
    /**
     * 获得一条队列中的内容
     * @param $key 多个key可以
     * @return 
     */
    public static function get($key,$limit=1)
    {
        self::init();
        $key = $key . '-' . $limit;
        return self::$mem->get($key);
    }
    /**
     * 向队列中添加一条内容
     * @param $key 队列KEY
     * @param $var 内容
     */
    public static function set($key = '', $var = '')
    {
        self::init();
        return self::$mem->set($key, $var, 0, 0);
    }
}

