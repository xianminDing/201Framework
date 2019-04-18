<?php
/** 
 * 经销商数据库连接类
 * @author wiki <charmfocus@gmail.com>
 * @copyright (c) 2010-11-15
 */
class Db_Dealer extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_dealer_read',
			'database' => 'dealer',
		 ),
		 'slave' => array(
			'host' => 'dbserver_dealer_read',
			'database' => 'dealer',
		 ),
	);    	
}
