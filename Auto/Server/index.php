<?php
/**
* Admin自动运行程序入口
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-22
* @version v1.0
*/


//产品配置
define('IN_PRODUCTION', true);
define('PRODUCTION_ROOT', dirname(dirname(dirname(__FILE__))));
define('SYSTEM_VAR', PRODUCTION_ROOT . '/var/');

//应用配置
define('APP_NAME', 'Auto_Server'); // 配置是哪个实例
define('APP_PATH', PRODUCTION_ROOT . '/Auto/' . APP_NAME); // 配置实例的APP路径
//数据库配置
define('DB_USERNAME','3g');
define('DB_PASSWORD','0f822e08');

//DAL配置
define('DAL_CACHE_DIR', SYSTEM_VAR .'cache_data/');#缓存目录
define('DAL_LOCALMEM_CACHE_DIR', DAL_CACHE_DIR .'tmpfs/');#内存缓存目录
define('DAL_CACHE_MODULES_DIR', PRODUCTION_ROOT . '/Modules');#数据模块目录
define('DAL_DIR', PRODUCTION_ROOT . '/DAL');#DAL实例目录
define('DAL_CACHE_SAVE_TYPE', 'SERIALIZE');#缓存类型


//一些常用的配置
define('SYSTEM_URL', 'https://apicloud.zol.com.cn');
define('DETAIL_URL', 'http://detail.zol.com.cn');#产品库的url
// 调试模式
define('IS_DEBUGGING', false);
if(!IS_DEBUGGING)error_reporting(0);             #如果没有打开报错，关闭报错
// 生产状态
define('IS_PRODUCTION', false);
define('ZOL_API_ISFW', true);#应用ZOL框架的项目
require_once('/www/zdata/Api.php'); #私有云

require_once(PRODUCTION_ROOT . '/init.php');

ZOL::setNameSpace(PRODUCTION_ROOT . '/Libs'); // 对这个实例需要调取的命名空间做映射
ZOL::setNameSpace(PRODUCTION_ROOT . '/Modules');
ZOL::setNameSpace(PRODUCTION_ROOT . '/Db');
ZOL::setNameSpace(PRODUCTION_ROOT . '/DAL');
ZOL::setNameSpace(PRODUCTION_ROOT . '/Auto');
ZOL::setNameSpace(PRODUCTION_ROOT . '/Helper');#注册Helper实例

ZOL_Controller_Front::run();

