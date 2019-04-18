<?php
/**
* KMS DB
* @author zhongwt <zhong.weitao@zol.com.cn>
* @copyright (c) 2009-08-24
*/
class Db_Analyze extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host'     => 'dbserver_analyze',
			'database' => 'kms6',
			'username' => 'qa_team',
			'password' => 'c879bc4a',
		 ),
		 'slave' => array(
			'host'     => 'dbserver_analyze',
			'database' => 'kms6',
			'username' => 'qa_team',
			'password' => 'c879bc4a',
		 ),
	);    	
}
