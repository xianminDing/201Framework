<?php
/**
 * 缓存发布基础类
 * 单例模式调用，防止部分调用重复new导致资源占用过高，用$instance静态属性来存储已经实例化的类
 * User: chenjt
 * Date: 2019-02-18
 */


class Modules_Abstract
{
	private static $instance;
	//单例模式调用
	public static function instance(){
		if (self::$instance == null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function refresh(){
	
	}
	
	public static function show($moduleName,$param){
	
	}
}