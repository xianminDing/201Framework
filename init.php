<?php
/**
 *  @author wang.tao5@zol.com.cn
 *  @copyright 2012年7月10日11:07:15 添加注释
 */
if (!defined('IN_PRODUCTION')) { #如果不是生产状态退出
	die('Hacking attempt');
}
if (PHP_VERSION < '5.0.0') {     #如果php版本低于5.0 退出
	die ('The PHP version is ' . PHP_VERSION . '! Plz upgrade it to 5.0 or newer version!');
}

/*
|---------------------------------------------------------------
| Protect against register_globals 
| 防止污染全局变量 - 也就是怕用户命名同名的变量以后把这些变量复写了
|---------------------------------------------------------------
|  This must be done before any globals are set by the code
|  这段代码必须在所有全局变量在代码设置以前执行
|--------------------------------------------------------------- 
| http://www.php.net/manual/zh/security.globals.php 这个页面中有register_globals打开的危害，这个在PHP 5.3.0中已经是过时的设置，在PHP5.4.0中已经被移除
| http://www.cnblogs.com/agostop/archive/2012/02/17/2355829.html 这个里边有关于$_REQUEST ['GLOBALS']的进一步说明
*/

if (ini_get('register_globals') || 1) { #如果开启了全局变量设置
	if (isset($_REQUEST ['GLOBALS'])) {
		die ('<a href="http://www.hardened-php.net/index.76.html">$GLOBALS overwrite vulnerability</a>');
	}
    
    #销毁$GLOBALS， 如果有$verboten数组中的$GLOBALS变量 提示并终止程序运行
	$verboten = array ('GLOBALS', '_SERVER', 'HTTP_SERVER_VARS', '_GET', 'HTTP_GET_VARS', '_POST', 'HTTP_POST_VARS', '_COOKIE', 'HTTP_COOKIE_VARS', '_FILES', 'HTTP_POST_FILES', '_ENV', 'HTTP_ENV_VARS', '_REQUEST', '_SESSION', 'HTTP_SESSION_VARS' );
    foreach ($_REQUEST as $name => $value) {
		if (in_array ( $name, $verboten )) {
			header ("HTTP/1.x 500 Internal Server Error");
			echo "register_globals security paranoia: trying to overwrite superglobals, aborting.";
			die (- 1);
		}

		unset ($GLOBALS[$name]);   #只要变量一出现，就被存到了$GLOBALS中
	}
}

/**
 * 如果get_called_class函数不存在，用PHP模拟get_called_class函数
 * 作用：得到最近调用的静态的类名 比如 B继承了A 在B中打印，则是B名
 */
if (!function_exists('get_called_class')) {
	function get_called_class() {
		$bt = debug_backtrace();                                      #该函数返回一个调用函数的关联数组，数组内的元素按调用函数从内到外排序
		$lines = file($bt[1]['file']);                                #当前的文件中的内容作为一个数组返回，每行为一个数组
		preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/',      #这个是匹配调用类和静态方法的正则，
			   $lines[$bt[1]['line']-1],                              #静态类与静态方法所在的行 [个人觉得这个地方应该用个循环，否则，如果静态方法的调用不在上一行容易找不到]
			   $matches);
		return $matches[1];                                           #返回正则里的第一个小括号中匹配的 静态类名
	}
}

/*
|---------------------------------------------------------------
| catching the current resource usages 查看系统调用 - 应该是得到linux下的一些系统的参数
|---------------------------------------------------------------
| getrusage() does not exist on the Microsoft Windows platforms   这个函数在windows下不起作用
|
*/
if (function_exists ('getrusage')) {
	$sysRUstart = getrusage ();
} else {
	$sysRUstart = array ();
}

/*
|---------------------------------------------------------------
| For security  # 为了安全，为了安全起见
|                               allow_url_fopen 是 php.ini 里的一个设置选项 值为on的时候 表示 将远程文件看作是本地文件 ，为off的时候 表示禁止运行远程文件
|                               禁止了以后可以防止WEB变种攻击，但是当前服务器也就不能进行抓取操作了
|                               参考地址 http://blog.sina.com.cn/s/blog_6aa521e20100ldpq.html
|---------------------------------------------------------------
*/
@ini_set ( 'allow_url_fopen', 0 );

/*
|---------------------------------------------------------------
| Test for PHP bug which breaks PHP 5.0.x on 64-bit...
| As of 1.8 this breaks lots of common operations instead
| of just some rare ones like export.
| 为了防止5.0版本的php在64位下的bug
|---------------------------------------------------------------
*/
$borked = str_replace ('a', 'b', array (- 1 => - 1 ));
if (!isset($borked[-1])) { #如果这个bug存在的话数组中-1的值会消失，然后跳出
	echo "PHP 5.0.x is buggy on your 64-bit system; you must upgrade to PHP 5.1.x\n" . "or higher. ABORTING. (http://bugs.php.net/bug.php?id=34879 for details)\n";
	exit ();
}

/* 设置PHP的环境值 */
//@ini_set('memory_limit',          '1024M'); #memory_limit 最大单线程的独立内存使用量，
//@ini_set('magic_quotes_runtime',  0);       #是否给一些函数里的变量自动转义

date_default_timezone_set ( 'PRC' );          #函数设置用在脚本中所有日期/时间函数的默认时区 这个是设置成中国时区
define('SYSTEM_TIME', isset ( $_SERVER ['REQUEST_TIME'] ) ? $_SERVER ['REQUEST_TIME'] : time ());   #time()当前系统的时间戳 $_SERVER ['REQUEST_TIME']请求脚本的时间戳
define('SYSTEM_DATE', date ( 'Y-m-d H:i:s', SYSTEM_TIME ));   #当前服务器的时间

define('PAGE_REQUEST_TIME', microtime ( true ));              #当前服务器的UNIX时间戳和微秒数 - 脚本请求时间
define('SYSTEM_PATH', dirname(__FILE__));                     #系统当前目录，和PRODUCTION_ROOT的值一样，因为init.php 整个系统的根目录下边

define('SYSTEM_CHARSET', 'GBK');                              #系统编码常量
#定义系统首页
defined('SYSTEM_HOMEPAGE') || define('SYSTEM_HOMEPAGE', 'http://www.zol.com.cn/');   #如果没有定义系统首页常量，将系统首页常量定义为 http://www.zol.com.cn/


spl_autoload_register(array('ZOL', 'autoload'));


if (IS_DEBUGGING)
{
	ZOL_Exception::register();
	ZOL_Error::register();
}

class ZOL
{
	/**
	* 已加载的类
	* @var array
	*/
	private static $_loadedClass = array();
	
	private static $_namespace = array();

	/*
	|---------------------------------------------------------------
	|  Loads a class or interface file from the include_path.
	|---------------------------------------------------------------
	| @param string $name A ZOL (or other) class or interface name.
	| @return void
	*/
	public static function autoload($name)
	{
		if (trim($name) == '')
		{
			new ZOL_Exception('No class or interface named for loading');
		}

		//为了使用通用的ip库接口,必须这样特殊判断
		if(in_array($name,array("IpLocation"))  ){
			include '/www/zdata/intf/iplocation.php';
			return;
		}
		if( $name == "DB_Interface_Read"  ){
			include SYSTEM_PATH . '/Db/Interface/Read.php';
			return;
		}
		//<<


		if (class_exists($name, false) || interface_exists($name, false))
		{
			return;
		}

		$namespace = substr($name, 0, stripos($name, '_'));
		// 对ZOL一种处理
		if ($namespace == 'ZOL') 
		{
			$file = SYSTEM_PATH . '/' . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';
		}
		// 对个性的命名空间做处理
		elseif (array_key_exists($namespace, self::$_namespace)) 
		{
			$file = self::$_namespace[$namespace] . '/' . str_replace('_', DIRECTORY_SEPARATOR, $name) . '.php';			
		}
		// 其他情况全是有问题的
		else 
		{
			throw new ZOL_Exception("The namespace config have problem: '{$name}'");
		}
		
		if (! file_exists($file))
		{
			throw new ZOL_Exception("The file dose not exist: '{$file}'");
		}
		if (! is_readable($file))
		{
			throw new ZOL_Exception("The file can not read: '{$file}'");
		}
		
		include $file;

		if (! class_exists($name, false) && ! interface_exists($name, false))
		{
			throw new ZOL_Exception('Class or interface does not exist in loaded file');
		}
		if (empty(self::$_loadedClass[$name])) {
			self::$_loadedClass[$name] = 0;
		}
		self::$_loadedClass[$name] ++;
	}


	/**
	 * 使用namespace方法实现每个实例的命名空间映射
	 *
	 * @param string $path
	 */
	public static function setNameSpace($path)
	{
		if (empty($path)) {
			new ZOL_Exception('No class or interface named for loading');
		}
		$namespace = substr(strrchr($path, '/'), 1);
		$namespacePath = substr($path, 0, strlen($path) - strlen($namespace) - 1);
		if (!isset(self::$_namespace[$namespace]) || self::$_namespace[$namespace] != $namespacePath) {
			self::$_namespace[$namespace] = $namespacePath;
		} else {
			throw new ZOL_Exception('Class or interface does not exist in loaded file');
		}
	}
	
	public static function getLoadedClass()
	{
		return self::$_loadedClass;
	}
}

