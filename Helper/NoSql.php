<?php
/**
 * NoSql数据访问Helper
 * @author 仲伟涛
 * @copyright (c) 2012-04-23
 */
class Helper_NoSql extends Helper_Abstract {


	/**
	 * 获得Key-Value型数据
	 */
	public static function getKeyVal($paramArr) {
		$options = array(
			'type'          =>  'REDIS', #Nosql数据库类型，
			'key'           =>  '', #必须与DAL_相关配置关联
			'subKey'        =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $value = false;
        switch ($type) {
            case 'REDIS':
                $value = self::getReidsKeyVal($options);
                break;
            default:
                break;
        }
        return $value;

	}
	/**
	 * 设置Key-Value型数据
	 */
	public static function setKeyVal($paramArr) {
		$options = array(
			'type'          =>  'REDIS', #Nosql数据库类型，
			'key'           =>  '', #
			'subKey'        =>  '',
			'value'         =>  '', #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        switch ($type) {
            case 'REDIS':
                $value = self::setReidsKeyVal($options);
                break;
            default:
                break;
        }

	}

	/**
	 * 从redis获得Key-Value型数据
	 */
    public static function getReidsKeyVal($paramArr){
		$options = array(
			'key'           =>  '', #多个数据之间用逗号分隔
			'subKey'        =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::get($key,$subKey);
    }


	/**
	 * redis设置Key-Value型数据
	 */
    public static function setReidsKeyVal($paramArr){
		$options = array(
			'key'           =>  '', #多个数据之间用逗号分隔
			'subKey'        =>  '',
			'value'         =>  '', #
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::set($key,$subKey,$value);
    }

    /**
     * 删除Redis的Key
     */
    public static function redisDelKey($paramArr){
		$options = array(
			'key'           =>  '',
			'subKey'        =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::delete($key,$subKey);
    }



    /**
     * 集合类数据结构 - 添加
     */
    public static function setAdd($paramArr){
		$options = array(
			'key'           =>  '',
			'subKey'        =>  '',
			'value'         =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::sAdd($key,$subKey,$value);
    }

    /**
     * 集合类数据结构 - 删除一个value
     */
    public static function setDel($paramArr){
		$options = array(
			'key'           =>  '',
			'subKey'        =>  '',
			'value'         =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::sDelete($key,$subKey,$value);
    }

    /**
     * 集合类数据结构 - 获得所有元素
     */
    public static function setGet($paramArr){
		$options = array(
			'key'           =>  '',
			'subKey'        =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::sMembers($key,$subKey);
    }


    /**
     * 集合类数据结构 - 取交集
     */
    public static function setInter($paramArr){
		$options = array(
			'key'           =>  '',
			'subKeys'       =>  '',
			'storToKey'     =>  false, #将交集结果存储哪个key
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $subKeyArr = explode(",",$subKeys);
        if(count($subKeyArr) < 2)return false;

        if($storToKey){
            return ZOL_NoSql_Redis::sInterStore($key,$storToKey,$subKeyArr);

        }else{
            return ZOL_NoSql_Redis::sInter($key,$subKeyArr);

        }
    }

    /**
     * 集合类数据结构 - 取并集
     */
    public static function setUnion($paramArr){
		$options = array(
			'key'           =>  '',
			'subKeys'       =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $subKeyArr = explode(",",$subKeys);
        if(count($subKeyArr) < 2)return false;

        return ZOL_NoSql_Redis::sUnion($key,$subKeyArr);
    }

    /**
     * 集合类数据结构 - 取差集
     */
    public static function setDiff($paramArr){
		$options = array(
			'key'           =>  '',
			'subKeys'       =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        $subKeyArr = explode(",",$subKeys);
        if(count($subKeyArr) < 2)return false;

        return ZOL_NoSql_Redis::sDiff($key,$subKeyArr);
    }
    /**
     * 集合类数据结构 - 获得个数
     */
    public static function setSize($paramArr){
		$options = array(
			'key'           =>  '',
			'subKey'       =>  '',
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return ZOL_NoSql_Redis::sSize($key,$subKey);
    }

}
?>