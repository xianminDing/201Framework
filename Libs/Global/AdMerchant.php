<?php
/**
* 经销商类库
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2010-08-09
* @version v1.0
*/

class Libs_Global_AdMerchant
{
	/**
	* 当前数据库链接
	* 
	* @var ZOL_Db_Product
	*/
	protected static $_db;

	/**
	* 加载数据库
	* @return void
	*/
	public static function loadDb()
	{
		self::$_db = Db_Product::instance();
	}
    
	/**
	* 获取经销商信息
	* @param array $param 参数
	* <pre>
	*	integer $param['id']
	*	string  $param['conditions'] 附加条件
	*	string  $param['orderBy'] 排序
	*	integer $param['num'] 数量
	*	integer $param['page'] 页码
	* </pre>
	* @todo 只做了单ID功能，后继可增加其它条件
	* @return array
	*/
	public static function getMerchant(array $param = array())
	{
		$options = array(
			'merId'      => 0,
			'cols'       => '',
			'conditions' => '',
			'orderBy'    => '',
			'num'        => 0,
			'page'       => 0,
		);
		
		$options = array_merge($options, $param); #初始化变量数组
		extract($options);                        #将数组元素转换成单个变量
		unset($options);                          #销毁原数组
        
        if (!$cols) {                             #如果没有传入列名，给$cols赋默认值
            $cols = 'SQL_CALC_FOUND_ROWS merchantid id, name, Fullname fullName,
				     telephone tel, mobile, merchant_credit credit,province_id provinceId,city_id cityId';
        }

		$tables      = 'merchant_search_list';                                                       #表名
		$conditions .= $merId ? ' AND merchantid=' . $merId : '';                                    #条件
		$orderBy     = $orderBy ? ' ORDER BY ' . $orderBy : ' ORDER BY merchantid DESC';             #排序

        #分页查询
		if ($page > 0) {
			$offset = ($page - 1) * $num;
		} else {
			$offset = 0;
		}
		
        #数量限制
		$limit = '';
		if (!$merId && $num) {
			$limit = " LIMIT {$offset}, {$num}";
		}
		
		$sql = "SELECT {$cols} 
				FROM {$tables} 
				WHERE 1 {$conditions} {$orderBy}
				{$limit}";
		
		$method = $merId ? 'getRow' : 'getAll';   #如果有经销商ID，读取一行数据
		self::loadDb();                           #链接数据库
		$data = self::$_db->$method($sql);        #从数据库里取得结果
		
		return $data;
	}
	
	/**
	* 获取经销商评介数
	* 
	* @param int $merId 经销商ID
	* @return int
	*/
	public static function getAppraiseNum($merId)
	{
		$sql = "SELECT COUNT('X') 
				FROM user_appraise_merchant 
				WHERE merchant_id='{$merId}' AND is_del=1";
		
		return (int)self::$_db->getOne($sql);
	}
	
	/**
	* 在售商品数量
	* @param int $merId 经销商ID
	* @return int
	*/
	public static function getSalesNum($merId)
	{
        $priceTable = 'zs_goods_' . ceil($merId / 1000);
		$sql = "SELECT COUNT('X')
                FROM {$priceTable}
                WHERE mer_id='{$merId}' and  is_del=0  and goods_type!=1";
		return (int)Db_ShopGoods::instance()->getOne($sql);
	}
	
	/**
	* 获取经销商促销信息
	* @param array $param 参数
	* <pre>
	*	integer $param['merId'] 经销商ID
	*	string  $param['conditions'] 附加条件
	*	string  $param['orderBy'] 排序
	*	integer $param['num'] 数量
	*	integer $param['page'] 页码
	* </pre>
	*/
	public static function getPromotion(array $param = array())
	{
		$options = array(
			'merId'      => 0,
			'cols'       => '',
			'conditions' => '',
			'orderBy'    => '',
			'num'        => 0,
			'page'       => 0,
		);
		
		$options = array_merge($options, $param);
		extract($options);
		unset($options);
		
		$now = SYSTEM_DATE;
		
		$cols = $cols 
				? $cols 
				: 'SQL_CALC_FOUND_ROWS sid id, title';
				
		
		$tables = 'merchant_info';
		$conditions .= " AND is_del=0 AND end_time>'{$now}'" ;
		$conditions .= $merId ? ' AND merchant_id=' . $merId : '';
		$orderBy = $orderBy ? ' ORDER BY ' . $orderBy : ' ORDER BY start_time DESC';
		
		if ($page > 0) {
			$offset = ($page - 1) * $num;
		} else {
			$offset = 0;
		}
		
		$limit = '';
		if ($num) {
			$limit = " LIMIT {$offset}, {$num}";
		}
		
		$sql = "SELECT {$cols} 
				FROM {$tables} 
				WHERE 1 {$conditions} {$orderBy}
				{$limit}";
		self::loadDb();
		$data = self::$_db->getAll($sql);
		
		return $data;
	}
	
	/**
	* 获取主营类别
	* @param int $merId 参数
	*/
	public static function getMainCate($merId)
	{
		if (isset(self::$_cache['mainCate'][$merId])) {
			return self::$_cache['mainCate'][$merId];
		}
		
		$unSubIdArr = ZOL_Product_Lib_Cate::getSubcate(array('cateId' => 49));
		foreach ($unSubIdArr as $row) {
			$unSubId[] = $row['id'];
		}
		
		$_exceptId = join(',', $unSubId);
		$sql = "SELECT subcatid subcateId, manuid manuId
				FROM manu_product WHERE merchantid='{$merId}' 
					AND is_hidden=0  AND subcatid NOT IN($_exceptId)
				ORDER BY FLOOR(sequence/1000), subcatid ASC ";
		self::loadDb();
		$_data = self::$_db->getAll($sql);
		if (!$_data) {
			return false;
		}

		$_firstSubcateId = $_data[0]['subcateId'];
		$_lastSubcateId  = $_data[count($_data)-1]['subcateId'];
		$tag = $_firstSubcateId != $_lastSubcateId ? 'subcate' : 'manu';
		self::_mainCateIter($_data, $tag, $merId);
		return self::$_cache['mainCate'][$merId];
	}
	
	private static function _mainCateIter($data, $tag, $merId)
	{
		foreach ($data as $row) {
			$_keyName = $tag . 'Id';
			$keyId = $row[$_keyName];
			if (!isset(self::$_cache['mainCate'][$merId][$keyId])) {
				$method = 'get' . ucfirst($tag);
				$param = array(
					$_keyName => $keyId,
					'cols' => 'id ' . $_keyName . ', name',
				);
				$info = ZOL_Product_Lib_Cate::$method($param);
				$info['subcateId'] = $row['subcateId'];
				self::$_cache['mainCate'][$merId][$keyId] = $info;
			}
		}

	}
}