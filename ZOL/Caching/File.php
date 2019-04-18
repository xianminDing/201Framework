<?php

class ZOL_Caching_File
{
    protected static $key;
    protected static $dir;
    protected static $path;
    protected static $separator = "\n";
    protected static $ext = ".cache";

    protected static function init($key)
    {
        self::path(self::encrypt($key));
    }
    public static function flush()
    {
        self::init($key);

        return ZOL_File::rm(self::$dir);;
    }
    public static function get($key)
    {
        self::init($key);

        $data = ZOL_File::get(self::$path);
        if (!empty($data))
        {
            $data = explode("\n", $data, 2);
            if ($data[0] > time() && !empty($data[1]))
            {
                return unserialize($data[1]);
            }
            else
            {
                ZOL_File::rm(self::$path);

                return false;
            }
        }
        else
        {
            return false;
        }
    }
    public static function delete($key)
    {
        self::init($key);

        return ZOL_File::rm(self::$path);
    }
    public static function set($key = '', $var = '', $expire = 3600)
    {
        if (empty($var) || empty($key))
        {
            return false;
        }

        self::init($key);
        $expire = intval($expire);
        if ($expire <= 0)
        {
            return false;
        }
        $expire = ($expire > $_SERVER['REQUEST_TIME']) ? $expire : ( $expire + $_SERVER['REQUEST_TIME']);
        $content = $expire . self::$separator . serialize($var);

        if (false == ZOL_File::write($content, self::$path))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    public static function add($key = '', $var = '', $expire = 3600)
    {
        if (empty($var) || empty($key))
        {
            return false;
        }
        $expire = intval($expire);
        if ($expire <= 0)
        {
            return false;
        }
        $expire = ($expire > $_SERVER['REQUEST_TIME']) ? $expire : ( $expire + $_SERVER['REQUEST_TIME']);
        $content = $expire . self::$separator . serialize($var);

        if ( ZOL_File::exists(self::$path))
        {
            // add if caching was expired
            // no write yet
            if (false == ZOL_File::write($content, self::$path))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        else
        {
            return false;
        }
    }

    protected static function path($key)
    {

        $path = (self::dir()) . '/' . chunk_split(substr($key, 0, 6), 2, '/') . $key . self::$ext;

        return self::$path = $path;
    }
    protected static function dir()
    {
        $dir = ZOL_Config::get('app.var')
            ? ZOL_Config::get('app.var')
            : ZOL_Config::get('default.var');
        return self::$dir = $dir . '/cache' ;
    }
    protected static function encrypt($key)
    {
        if (empty($key))
        {
            return false;
        }
        return self::$key = md5($key);
    }
    public static function lock($key)
    {
        $cacheFile = self::path($key);
        $cacheDir = dirname($cacheFile);

        if (! is_dir($cacheDir)) {
          if (! ZOL_File::mkdir($cacheDir, 0755, true)) {
            // make sure the failure isn't because of a concurency issue
            if (! is_dir($cacheDir)) {
              throw new ZOL_Exception("Could not create cache directory");
            }
          }
        }
        @touch($cacheFile . '.lock');
    }

    public static function unlock($key) {
        // suppress all warnings, if some other process removed it that's ok too
        $cacheFile = self::path($key);
        @unlink($cacheFile . '.lock');
    }
}

