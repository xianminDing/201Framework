<?php

class Db_Photo extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_photo_read',
			'database' => 'photo',
		 ),
		'slave' => array(
			'host' => 'dbserver_photo_read',
			'database' => 'photo',
			
		 ),        
	);      
}
