<?php
/*
 * ZOL框架入口
 */
#产品配置
define('IN_PRODUCTION', true);                                  #只用于init.php判断入口程序是否正确
define('PRODUCTION_ROOT', dirname(dirname(dirname(__FILE__)))); #__FILE__ 当前的值为/www/rest/chenjt/Html/Api/index.php；
                                                                #PRODUCTION_ROOT的值为/www/rest/chenjt 为当前程序相对根目录的地址
define('SYSTEM_VAR', PRODUCTION_ROOT . '/var/');                #基本没用的一个常量，里边放了些字体，除此以外基本上什么也没有了，[好像是换成目录]

#应用配置
define('APP_NAME', 'Admin');                                      #配置是哪个实例 当前是Pro这个实例，
define('APP_PATH', PRODUCTION_ROOT . '/App/' . APP_NAME);       #配置实例的路径 各个实例都是在App下建的文件夹
define('APP_HTML_DIR', PRODUCTION_ROOT . '/Html/' . APP_NAME);  #配置HTML的实例路径 html里放的是js和css文件
define('APP_INCLUDE_DIR', APP_HTML_DIR . '/include/' );         #配置里的include的路径，在html下的APP_NAME文件夹下
define('APP_AD_DIR', APP_HTML_DIR . '/include/ad/');            #配置里的 一个莫名其妙的文件，好像也不是广告

#数据库配置
define('DB_USERNAME','zolbms');                                     #数据库用户名
define('DB_PASSWORD','2dfcd5b0');                               #数据库密码

#DAL配置
define('DAL_CACHE_DIR', SYSTEM_VAR .'cache_data/');             #缓存目录
define('DAL_LOCALMEM_CACHE_DIR', DAL_CACHE_DIR .'tmpfs/');      #内存缓存目录 
define('DAL_CACHE_MODULES_DIR', PRODUCTION_ROOT . '/Modules');  #缓存模块目录
define('DAL_DIR', PRODUCTION_ROOT . '/DAL');                    #DAL实例目录
define('DAL_CACHE_SAVE_TYPE', 'SERIALIZE');                     #缓存类型

#一些常用的配置
define('DETAIL_URL', 'http://detail.zol.com.cn');                        #产品库的url
defined('SYSTEM_HOST') || define('SYSTEM_HOST', 'admin.apicloud.zol.com.cn');  #如果没有定义SYSTEM_HOST常量，定义SYSTEM_HOST常量
define('APP_CLIENT_HOST', 'lib3.wap.zol.com.cn');                         #手机客户端程序的特殊判断
  
#调试模式，如果不是接口线上环境就启动调试模式.
define('IS_DEBUGGING' , $_SERVER['SERVER_NAME'] != SYSTEM_HOST);
#如果是8089的测试环境，打开二级报错
define('IS_DEBUGGING_L2' , (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 8089));
#如果是生产状态，关闭报错
define('IS_PRODUCTION', $_SERVER['SERVER_NAME'] == SYSTEM_HOST);

if(!IS_DEBUGGING)error_reporting(0);             #如果没有打开报错，关闭报错

define('ZOL_API_ISFW', true);                    #应用ZOL框架的项目
define('ZOL_API_LOGLEVEL', E_ALL ^ E_NOTICE );   #定义错误级别 ^ E_WARNING
require_once('/www/zdata/Api.php');              #引入私有云

require_once(PRODUCTION_ROOT . '/init.php');     #装载初始化文件

ZOL::setNameSpace(PRODUCTION_ROOT . '/Libs');    #注册类库
ZOL::setNameSpace(PRODUCTION_ROOT . '/Modules'); #注册缓存模块
ZOL::setNameSpace(APP_PATH);                     #注册应用
ZOL::setNameSpace(PRODUCTION_ROOT . '/Db');      #注册数据库链接类
ZOL::setNameSpace(PRODUCTION_ROOT . '/DAL');     #注册DAL实例
ZOL::setNameSpace(PRODUCTION_ROOT . '/Helper');  #注册Helper实例

#ZOL控制器对象调用
ZOL_Controller_Front::run();