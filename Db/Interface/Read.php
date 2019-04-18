<?php

class Db_Interface_Read extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		//'engner' => 'mysql',
		'master' => array(
			'host' => 'dbserver_interface',
			'database' => 'z_ip_interface',
		 ),
		 'slave' => array(
			'host' => 'dbserver_interface_read',
			'database' => 'z_ip_interface',
		 ),
	);


}
