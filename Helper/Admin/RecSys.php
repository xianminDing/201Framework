<?php

/**
 * 后台推荐用到的一些静态方法
 */
class Helper_Admin_RecSys extends Helper_Abstract
{
    
    /**
	 * 统一上线私有云二次包装
	 */
	public static function onlineItems($paramArr){
	   $options = array(
	        'source'         => 'person',           #资源来源 pgc外网抓取、person 个人资源
	        'type'           => 'question',         #资源类型 bbs为论坛、question为问答、form攒机单、talk好说
	        'ids'            => array(),   #资源id列表 ids列表数组
	    );
	   if (is_array($paramArr)) $options = array_merge($options, $paramArr);
	   $options['sourceType'] = "{$options['source']}_{$options['type']}";
	   unset($options['source'],$options['type']);
	   $res =  ZOL_Api::run("Recsys.Admin.onlineItems" ,$options);
	   return $res;
	}
	
	/**
	 * 刷新上线时间
	 */
	
	public static function refreshExpireTime($paramArr){
	    $options = array(
	        'source'         => 'person',           #资源来源 pgc外网抓取、person 个人资源
	        'type'           => 'question',         #资源类型 bbs为论坛、question为问答、form攒机单、talk好说
	        'ids'            => "",   #资源id列表 ids列表数组
	        'expire_day'     => 0
	    );
	    if (is_array($paramArr)) $options = array_merge($options, $paramArr);
	    $options['sourceType'] = "{$options['source']}_{$options['type']}";
	    
	    
	    $idsArr = explode(",", $options['ids']);
	    unset($options['source'],$options['type'],$options['ids']);
	    if($idsArr){
	        foreach ($idsArr as $id){
	            if($id){
	                $options['id'] = $id;
	                ZOL_Api::run("Recsys.Admin.refreshExpireTime" ,$options);
	            }
	        }
	    }
	}
	
	
	/**
	 * 统一下线私有云二次包装
	 */
	public static function offlineItems($paramArr){
	    $options = array(
	        'source'         => 'person',           #资源来源 pgc外网抓取、person 个人资源
	        'type'           => 'question',         #资源类型 bbs为论坛、question为问答、form攒机单、talk好说
	        'ids'            => array()  #资源id列表 ids列表数组
	    );
	    if (is_array($paramArr)) $options = array_merge($options, $paramArr);
	    $options['sourceType'] = "{$options['source']}_{$options['type']}";
	    unset($options['source'],$options['type']);
	    ZOL_Api::run("Recsys.Admin.offlineItems" ,$options);
	}
}