<?php
/**
 * 产品分类的助手类
 * @author zhongwt
 * @copyright (c) 2011-11-14
 */
class Helper_Cate extends Helper_Abstract
{
	/**
	 * 获得产品的顶级分类
	 */
	public static function getTopCate()
	{
		return array(
			1  => array('name' => '笔记本整机', 'orderSort' => 21, 'showNum' => 1),
			2  => array('name' => '手机', 'orderSort' => 20, 'showNum' => 1),
			3  => array('name' => '相机', 'orderSort' => 19, 'showNum' => 1),
			11 => array('name' => '数码', 'orderSort' => 17, 'showNum' => 1),
			4  => array('name' => 'DIY硬件', 'orderSort' => 16, 'showNum' => 1),
			5  => array('name' => '家电', 'orderSort' => 15, 'showNum' => 1),
			6  => array('name' => '办公投影', 'orderSort' => 14, 'showNum' => 1),
			7  => array('name' => '游戏机', 'orderSort' => 13, 'showNum' => 1),
			8  => array('name' => '软件', 'orderSort' => 12, 'showNum' => 1),
			9  => array('name' => '网络', 'orderSort' => 11, 'showNum' => 1),
			10 => array('name' => '安防', 'orderSort' => 10, 'showNum' => 1),
			13 => array('name' => '汽车用品', 'orderSort' => 9, 'showNum' => 1),
			14 => array('name' => '智能生活', 'orderSort' => 8, 'showNum' => 1),
			15 => array('name' => '户外装备', 'orderSort' => 7, 'showNum' => 1),
			12 => array('name' => 'LED', 'orderSort' => 6, 'showNum' => 1),
			16 => array('name' => '母婴玩具', 'orderSort' => 5, 'showNum' => 1),
			//17  => array('name' => '保健器械', 'orderSort'=> 0,'showNum' => 1),
			18 => array('name' => '五金建材', 'orderSort' => 0, 'showNum' => 1),
			19 => array('name' => 'HIFI', 'orderSort' => 4, 'showNum' => 1),
			20 => array('name' => '暖通', 'orderSort' => 3, 'showNum' => 1),
			21 => array('name' => '广电设备', 'orderSort' => 22, 'showNum' => 1),
			22 => array('name' => '矿机', 'orderSort' => 23, 'showNum' => 1),
			23 => array('name' => '酒店用品', 'orderSort' => 24, 'showNum' => 1),
			24 => array('name' => '人体工程学', 'orderSort' => 25, 'showNum' => 1),
		);
	}

	/**
	 * 获得产品的大类
	 */
	public static function getCate($paramArr)
	{
		$options = array(
			'groupByTop' => 0,   #是否按照顶级分类进行分组
			'topCateId'  => 0,   #顶级分类ID
			'getUrl'     => 0,   #是否获得URL
			'type'       => '',  #类型

		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
		$cacheParam = '';
		if ($type) {
			$cacheParam = array('type' => $type);
		}

		if ($cacheParam) {
			$cateArr = self::loadCache('Cate', $cacheParam);
			return $cateArr;
		} else {
			$cateArr = self::loadCache('Cate');
		}
		#取得URL
		if ($getUrl && $cateArr) {
			foreach ($cateArr as $k => $v) {
				$cateArr[$k]['url'] = Libs_Global_Url::getProCateUrl(array('cateId' => $k));
			}
		}
		#按照顶级分类进行汇总 或者指定了顶级分类ID
		if (($groupByTop || $topCateId) && $cateArr) {

			$outArr = array();
			foreach ($cateArr as $k => $v) {
				if (isset($v['cateFid'])) {
					$outArr[$v['cateFid']][] = $v;
				}
			}

			if ($topCateId && isset($outArr[$topCateId])) {
				$cateArr = $outArr[$topCateId];
			} else {
				$cateArr = $outArr;
			}

		}
		return $cateArr;

	}

	/**
	 * 获得产品子类
	 */
	public static function getSubCate($paramArr)
	{
		$options = array(
			'cateId'      => 0,   #大类ID,如果不指定就是获得所有子类
			'groupByCate' => 0,   #按照大类的分类输出
			'subcateId'   => 0,   #子类ID,只获得指定的子类ID
			'locationId'  => 0,   #地区id

		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
		if ($cateId) {
			$cacheParam = array('cateId' => $cateId);
		} else {
			$cacheParam = array();
		}

		$data = self::loadCache('Subcate', $cacheParam);
		if (!$data) return false;

		if ($subcateId) {#如果指定了子类,只留下子类信息
			if (empty($data[$subcateId])) return false;
			$tmp = $data[$subcateId];
			unset($data);
			$data[$subcateId] = $tmp;
		}
		#循环获得URL
		foreach ($data as $k => $v) {
			#如果只指定一个大类,将其他大类信息删除掉
			if ($cateId && $cateId != $v['cateId']) {
				unset($data[$k]);
				continue;
			}

			if (isset($v['subcateEnName'])) {
				$data[$k]['url'] = Libs_Global_Url::getListUrl(array(
					'subcateId'     => $k,
					'subcateEnName' => $v['subcateEnName'],
					'appendParam'   => array('locationId' => $locationId),
				));
			}
		}

		if ($groupByCate) {
			$outArr = array();
			if ($data) {
				foreach ($data as $k => $v) {
					$outArr[$v['cateId']][] = $v;
				}
				$data = $outArr;
			}
		}
		return $data;
	}

	/**
	 * 获得产品大类的相关子类
	 */
	public static function getCateRelation($paramArr)
	{
		$options = array(
			'cateId' => 0,   #大类ID

		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		$data = self::loadCache('SubcateRepeat', array('cateId' => $cateId));

		return $data;
	}

	/**
	 * 获得产品子类的相关子类
	 */
	public static function getSubCateRelation($paramArr)
	{
		$options = array(
			'subcateId'  => 0,
			'locationId' => 0,
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		$data = self::loadCache('RelSubcate', array('subcateId' => $subcateId));
		if ($data) {
			foreach ($data as $k => $v) {
				$curSubInfo = self::loadCache('Subcate', array('subcateId' => $k));
				$data[$k]['cateId'] = $curSubInfo['cateId'];
				$data[$k]['url'] = Libs_Global_Url::getListUrl(array(
					'subcateId'     => $k,
					'subcateEnName' => $v['enName'],
					'locationId'    => $locationId,
				));
			}
		}

		return $data;
	}

	/**
	 * 用首字母区分子类
	 */
	public static function getSubcateGroupBySpell($paramArr)
	{
		$options = array(
			'spell'      => 0,
			'locationId' => 0,   #地区id
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		$data = self::loadCache('Subcate', array());
		if (!$data) return false;

		$outArr = array();
		if ($data) {
			foreach ($data as $k => $v) {
				if (!isset($v['proNum']) || !isset($v['subcateEnName']) || !isset($v['spell'])) continue;
				if ($spell && $spell != $v['spell']) continue;

				$v['url'] = Libs_Global_Url::getListUrl(array(
					'subcateId'     => $k,
					'subcateEnName' => $v['subcateEnName'],
					'locationId'    => $locationId,
				));
				$outArr[$v['spell']][] = $v;
			}
		}

		return $spell && isset($outArr[$spell]) ? $outArr[$spell] : $outArr;
	}

	/**
	 * 按照子类获得相关类别关联
	 */
	public static function getCateBySubcate($paramArr)
	{
		$options = array(
			'subcateId' => 0,   #子类ID
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		$topCate = self::getTopCate();
		$subArr = self::getSubCate(array('subcateId' => $subcateId));
		$cateLink = self::getCate(array());
		$cateFid = isset($cateLink[$subArr[$subcateId]['cateId']]['cateFid']) ? $cateLink[$subArr[$subcateId]['cateId']]['cateFid'] : 1;

		$cateRelArr = self::getCate(array('groupByTop' => 1, 'topCateId' => $cateFid, 'getUrl' => 1));//大类相关子类
		foreach ($cateRelArr as $key => $row) {
			$_subArr[$row['name']]['id'] = $row['id'];
			$_subArr[$row['name']]['url'] = $row['url'];
			$_subArr[$row['name']]['array'] = Helper_Cate::getSubCate(array('cateId' => $row['id']));
			$_subArr[$row['name'] . '相关子类']['url'] = $row['url'];
			$_subArr[$row['name'] . '相关子类']['array'] = Helper_Cate::getCateRelation(array('cateId' => $row['id']));
		}
		$relSubArr['name'] = $topCate[$cateFid]['name'];
		$relSubArr['list'] = $_subArr;

		return $relSubArr;
	}
    
    /*
     * 获得子类的select筛选
     */
    public static function getSubcateSelect($paramArr){
        $options = array(
            'subcateId' => 0
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
		$dbProduct = Db_Product::instance();
		$sql = "SELECT s.`id`, s.`categoryid` cateId, s.`name`
				FROM `subcategory` s, `category` c
				WHERE c.id=s.categoryid AND s.categoryid>0 AND s.categoryid>0 and c.property in(0,1)
				ORDER BY c.order_sort DESC, s.order_sort DESC";
		$arr = $dbProduct->getAll($sql);
		$subcatArr = array();
		if ($arr) {
			foreach($arr as $t){
				$subcatArr[$t['id']] = $t['name'];
			}
		}
        
		$outstr = '<option value="0">选择子类</option>';
		#字母排序
        $classArr = array();
		foreach($subcatArr as $key => $value) {
			//进行字母索引
			$f_letter = ZOL_String::getFirstLetter($value);
			$akey = ord($f_letter);
			$classArr[$akey][$key]=$f_letter."_".$value;
		}
        ksort($classArr);
		foreach ($classArr as $letterkey => $larr) {
			foreach ($larr as $cid => $cname) {
				$outstr .= "<option value='".$cid."'";
				if($subcateId>0 && $cid == $subcateId) $outstr .= " selected='selected' ";
				$outstr .= ">".$cname."</option>";
			}
		}
		return $outstr;
	}
}
?>