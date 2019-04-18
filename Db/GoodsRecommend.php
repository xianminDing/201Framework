<?php

class Db_GoodsRecommend extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'engner' => 'mysql',
        'username' => 'bigdata',
		'password' => '2694ce972b',
		'master' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',/*
            'username' => 'bigdata',
			'password' => '2694ce972b',*/
		 ),
		 'slave' => array(
			'host' => 'dbserver_bigdata',
			'database' => 'bigdata',/*
             'username' => 'bigdata',
			'password' => '2694ce972b',*/
		 ),        
	);    	
}
