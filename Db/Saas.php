<?php
/**
* 生意宝
* @author 钱志伟 <qian.zhiwei@zol.com.cn>
* @copyright (c)
*/
class Db_Saas extends ZOL_Abstract_Pdo
{
	protected $servers   = array(
		'master' => array(
			'host' => 'saas_new',
			'database' => 'saas_new',
		 ),
		'slave' => array(
			'host' => 'saas_new',
			'database' => 'saas_new',
		 ),        
	);
}