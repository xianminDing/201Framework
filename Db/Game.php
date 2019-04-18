<?php
/** 
* 游戏库DB连接
* @author zhongwt <zhong.weitao@zol.com.cn>
* @copyright (c) 
*/
class Db_Game extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_flash_read',
			'database' => 'youxi',
		 ),
		 'slave' => array(
			'host' => 'dbserver_flash_read',
			'database' => 'youxi',
		 ),
	);    	
}
?>