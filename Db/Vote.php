<?php
/** 
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 
*/
class Db_Vote extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_pro_vote',
			'database' => 'pro_vote',
		 ),
		 'slave' => array(
			'host' => 'dbserver_pro_vote',
			'database' => 'pro_vote',
		 ),
	);    	
}
