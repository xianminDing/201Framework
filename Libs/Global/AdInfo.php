<?php
class Libs_Global_AdInfo
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
	* @得到广告信息数组
	* <pre>
	*	integer $param['id'] 广告ID
	*	string  $param['conditions'] 附加条件
	*	string  $param['orderBy'] 排序
	*	integer $param['num'] 数量
	*	integer $param['page'] 页码
	* </pre>
	*/
	public static function getAdInfo(array $param = array())
	{
		$options = array(
			'id'         => null,
			'cols'       => '',
			'conditions' => '',
			'orderBy'    => '',
			'num'        => 0,
		);
		#将$param里的值，赋值到$options里边
		$options = array_merge($options, $param);
		extract($options);
		unset($options);

        #如果没有传入cols,用默认的列值
        if (!$cols) {
            $cols = 'id, subcate_id subcateId, manu_id manuId,
                   path, pic_id picId, merchant_id merId, link, style, title, 
                   subtitle, tel, mobile, is_del isDel, addtime, offset';
        }

		$tables      = 'product_ad';                                                                 #表名
		$conditions .= isset($id) ? " AND id IN ($id)" : '';                                         #条件
		$orderBy     = is_string($id) ? " ORDER BY INSTR(',{$id},', CONCAT(',', id, ','))" : '';     #排序 伟成说这个有病，神经病
		$limit       = $num ? " LIMIT {$num}" : '';                                                  #默认提取数
		$sql         = "SELECT {$cols}
                        FROM {$tables} 
                        WHERE 1 {$conditions} {$orderBy} {$limit}"; 

        self::loadDb();                        #链接数据库
		if (is_numeric($id)) {
            $data = self::$_db->getRow($sql);
        }
        $data = self::$_db->getAll($sql);

		return $data;
	}
    
	/**
	* 得到经销商的信息
	*/
    public static function getAdMerInfo($merId)
    {
        //$merInfo = Libs_Global_AdMerchant::getMerchant(array('merId' => $merId));

        $merInfo = ZOL_Api::run("Shop.Merchant.getMerchantForProduct" , array(
            'merchantId'     => $merId,           #经销商ID
        ));

        $data = array(
            'title'       => $merInfo['name'],
            'tel'         => isset($merInfo['tels']) ? $merInfo['tels'][0] : '',
            'mobile'      => $merInfo['mobile'],
            'credit'      => $merInfo['credit'],
            'provinceId'  => $merInfo['province_id'],
            'cityId'      => $merInfo['city_id'],
            'salesNum'    => Libs_Global_AdMerchant::getSalesNum($merId),     #在售商品数量
            'appraiseNum' => $merInfo['commNum'],  #获取经销商评介数
            'shopListUrl' => $merInfo['shopListUrl'],
            'appraiseUrl' => $merInfo['appraiseUrl'],
        );

        return $data;
    }
	
}
