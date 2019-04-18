<?php

class ZOL_Caching_Memcache extends ZOL_Caching_Abstraction
{
	protected static $servers   = array(
        '10.19.36.26:11211',
        '10.19.36.27:11211',
        '10.19.36.28:11211',
        '10.19.36.29:11211',
        '10.19.36.30:11211',
	);
	
	protected static $mem;

	protected static function init()
	{
		if (empty(self::$mem))
		{
			self::$mem = new Memcache;
            defined('MEMCACHE_CONF_KEY') || define('MEMCACHE_CONF_KEY', 'Memcache');
            $memConf = ZOL_Config::get(MEMCACHE_CONF_KEY);
            if ($memConf) {
                self::$servers = $memConf;
            }
			foreach (self::$servers as $val)
			{
				$exp = explode(':', $val);
				self::$mem->addServer($exp[0], $exp[1]);
			}
		}
	}
	public static function flush()
	{
		self::init();

		return self::$mem->flush();
	}
	public static function get($key)
	{
		self::init();

		return self::$mem->get($key);
	}
	public static function delete($key)
	{
		self::init();

		return self::$mem->delete($key);
	}
	public static function set($key = '', $var = '', $expire = 3600)
	{
		self::init();

		return self::$mem->set($key, $var, 0, $expire);
	}
	public static function add($key = '', $var = '', $expire = 3600)
	{
		self::init();

		return self::$mem->add($key, $var, 0, $expire);
	}
}

