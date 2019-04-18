<?php
/**
* 主要供搜索推荐接口使用
* @author 冉彪 <ran.biao@zol.com.cn>
* @copyright (c)
*/
class Db_Search2 extends Db_Search
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_search_log_read',
			'database' => 'search_log',
		 ),
		 'slave' => array(
			'host' => 'dbserver_search_log_read',
			'database' => 'search_log',
		 ),
	);
}
