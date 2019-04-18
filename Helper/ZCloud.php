<?php
/**
 * 方便私有云内部调用产品库的一个特殊处理类
 * @author 仲伟涛
 * @copyright (c) 2013-07-2
 */
class Helper_ZCloud extends Helper_Abstract {

	/**
	 * 私有云直接调用这个方法，获得产品库的mongodb缓存，而不是curl请求私有云服务器
	 */
	public static function loadMongoCache($paramArr) {
		$options = array(
			'moduleName,'  =>  0, #缓存模块
			'param'        => array(), #缓存参数
			'num'          =>  0, #缓存数量
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        return self::loadCache($moduleName, $param, $num);
                
	}
    
}
?>