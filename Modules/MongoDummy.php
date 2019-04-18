<?php
/**
* MongoDb的傀儡类，主要用于在Module没有定义的类，提供通用的处理
* 仲伟涛
* 2014-9
*/

class Modules_MongoDummy extends ZOL_DAL_MongoCacheModule
{

	protected $_depend = array();
    
	public function __construct($cacheParam = '',$moduleName=''){
        
        parent::assginMongoModuleName($moduleName);
	}
	
    
	public function refresh(array $param = null){
        return false;
	}
}
