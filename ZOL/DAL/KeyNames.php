<?php
/**
* 
* @author wiki<wu.kun@zol.com.cn>
* @copyright (c) $(date)
* @version v1.0
*/
abstract class ZOL_DAL_KeyNames implements ZOL_DAL_IKeyNames
{
	protected $_keyNames = array();
	
	public function getKeyNames()
	{
		return $this->_keyNames;
	}
	public function getKeyCnNames()
	{
		return $this->_keyCnNames;
	}
}


