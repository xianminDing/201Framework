<?php
/**
* 微动态写库
* @author wang.haobin@zol.com.cn
* @copyright (c) 2013年07月09日
*/
class Db_DataFlowRead extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
        'username' => 'userdata',
		'password' => '43f59a7e5d',
		'master' => array(
			'host' => 'dbserver_dataflow_read',
			'database' => 'dataflow',
		 ),
		 'slave' => array(
			'host' => 'dbserver_dataflow_read',
			'database' => 'dataflow',
		 ),
	);    	
}

