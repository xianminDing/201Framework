<?php
/** 
* 知识链接类
* @author wolf <lang.feng@zol.com.cn>
* @copyright (c) 2009-10-29
*/
class Db_Knowledge  extends ZOL_Abstract_Pdo 
{
	protected $servers   = array(
		'master' => array(
			'host' => 'dbserver_knowledge_read',
			'database' => 'knowledge',
		 ),
		 'slave' => array(
			'host' => 'dbserver_knowledge_read',
			'database' => 'knowledge',
		 ),
	);    	
}
