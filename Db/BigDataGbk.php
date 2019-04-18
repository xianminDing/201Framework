<?php
/** 
* @author dingxm
* @copyright (c) 2016-12-20
*/
class Db_BigDataGbk extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',
		 ),
		 'slave' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',
		 ),
	    'charset'   => 'gbk', 
	    'username' =>'pro_admin',
        'password' =>'3c2d4c41',
	);	
}
