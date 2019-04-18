<?php
/**
* 产品Lucene搜索帮助类
* @author 仲伟涛
* @copyright (c) 2012-01-17
*/
class Helper_Lucene extends Helper_Abstract
{
	private static $_lucence_host = "product.lucene.zol.com.cn.";
	private static $dbSearch;


    /**
	 * 获得产品的搜索提示
	 */
	public static function getProSuggest($paramArr){
        $options = array(
            'keyword'       => '',   #关键字
            'num'           => 10,   #获得个数
            'getCate'       => 0,    #是否获关键词的类别信息
            'cateNum'       => 10,   #产品个数

        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$sqlArr = array(#产品提示
			'pro'  => "select name,url from pnote where name={$keyword} or pname={$keyword} or sname={$keyword} limit {$num}",
		);
		if($getCate){#类别提示
			$sqlArr['cate'] = "select name,url from detail_cate_left where name={$keyword} limit {$cateNum}";
		}

        $return = array();
        foreach ($sqlArr as $type => $sql) {
            $return[$type] = self::doQuery(array(
				'sql'       => $sql,
				'returnCol' => array("name","url"),#值返回的列
			));

        }
        return $return;
    }


    /**
	 * 获得关键字的相关搜索,比如5230的相关搜索:三星S5230 诺基亚5230
	 */
	public static function getRelKeyword($paramArr){
        $options = array(
            'keyword'       => '',   #关键字
            'num'           => 10,   #获得个数
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$num++;#因为返回的数据中包含keyword本身,所以需要多取一个

		$sql = "select kword from relation where kword={$keyword} limit {$num}";

		$data = self::doQuery(array(
			'sql'       => $sql,
			'returnCol' => array("kword"),#值返回的列
		));
		#排除keyword自身
		if($data){
			foreach($data as $k => $v){
				if($v['kword'] == $keyword){
					unset($data[$k]);
					break;
				}
			}
		}
        return $data;
    }

    /**
	 * 获得获得昵称列表
	 */
	public static function getNickNameList($paramArr){
        $options = array(
            'table'         => 'nickname', #可选项  nickname(edtdst=2),nickname.dang(edtdst=10)
            'cols'          => '*',
            'keyword'       => '',   #关键字
            'offset'        => 0,
            'num'           => 10,   #获得个数
            'debug'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if(!$cols || !$keyword){
            return false;
        }
        
	    $sql  = "select {$cols} from {$table} where nickname={$keyword} or userid={$keyword} limit {$offset}, {$num}";
        
        if($debug){
            echo $sql;
            exit;
        }
        $return = array();
        
        $return = self::doQuery(array(
            'sql'       => $sql,
            'getAttr'   => true,
            'returnCol' => explode(',', $cols),#值返回的列
        ));

        return $return;
    }
    
    /**
	 * 获得产品列表
	 */
	public static function getProList($paramArr){
        $options = array(
            'table'            => 'product',          #表名,product.mtall匹配度高，结果少些[列表页用] product  匹配度低些，结果多些
            'keyword'          => '',                 #关键字
            'cols'             => '',                 #列名
            'noJd'             => 0,                  #是否排除京东导入 1.排除京东导入
            'noStop'           => 0,                  #是否排除停产 1.排除停产产品
            'seriesId'         => 0,                  #系列ID
            'subcateId'        => 0,                  #子类ID
            'manuId'           => 0,                  #品牌ID
            'manuType'         => '',                 #品牌类型，国际品牌 自主品牌
            'proIds'           => 0,                  #产品IDs
            'priceId'          => 'noPrice',          #报价 不限价格是:noPrice 0:是第一个价格段,有价格限制的
            'locationId'       => 1,                  #地区
            'parentLocId'      => 1,                  #父级地区
            'paramVal'         => false,              #参数部分
            'minPrice'         => 0,                  #最低价格 price >= x
            'maxPrice'         => 0,                  #最高价格 price <= x
            'prices'           => array(),            #价格多选
            'minLevel'         => 0,                  #最低Level Level >= x
            'maxLevel'         => 0,                  #最高Level Level <= x
            'marketTime'       => 0,                  #上市时间.格式:201503
            'orderBy'          => 0,                  #0:默认排序 1:最热门 2:最冷门 3:最便宜 4:最贵 5:评论数最多 6:评论数最少 7:评分最高 8:评分最低 9:最新
            'offset'           => 0,                  #offset
            'num'              => 10,                 #获得个数
            'getRelCate'       => 0,                  #获得匹配子类和品牌
            'random'           => 0,                  #分段随机处理 每3个是一段,然后每段随机. 如:1-3，4-6，6-9，10-12，13-15，前25名分五段，每段随机排序
            'getProInfo'       => 0,                  #获得产品信息
            'getMainParamNum'  => 0,                  #获得主要参数个数
            'hasPingce'        => 0,                  #是否有评测文章
            'isGroup'          => 0,                  #是否分组取总数
            'subSecond'        => 0,                  #是否是第二子类
            'showSeries'       => 0,                  #是否显示系列
            'debug'            => 0,                  #调试用
            'offerSql'         => '',                 #指定SQL(功率计算器需求)
            'isHistory'        => 0,                  #历史列表(停产和等级小于10的产品)
            'isLog'            => 0,                  #是否记录报错日志并发送邮件
            'conditions'        =>  '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		#数据列的处理
        if(''==$cols) {
            $cols = 'product_id,name';
            if ($locationId) {
                $cols .= ",price_".$locationId;
            }
        }

        #报价多选
        if (!empty($prices) || '0' === $prices) {
            $prices = explode('-', $prices);
        } else {
            $prices = array();
        }

		#获得sql
		$sql = self::getSql(array(
            'table'      => $table,             #表名 product 或者 product.mtall product.mtall匹配度高，结果少些 product  匹配度低些，结果多些
            'cols'       => $cols,              #列名
            'seriesId'   => $seriesId,          #系列ID
            'subcateId'  => $subcateId,         #子类ID
            'manuId'     => $manuId,            #品牌ID
            'manuType'   => $manuType,          #品牌类型，国际品牌 自主品牌
            'proIds'     => $proIds,                  #产品IDs
            'priceId'    => $priceId,           #报价
            'locationId' => $locationId,        #地区
            'paramVal'   => $paramVal,          #参数部分
            'conditions' => $conditions,                 #指定参数
            'keyword'    => $keyword,           #关键字
            'noJd'       => $noJd,              #是否就京东导入 1.排除京东导入 0.不排除
            'noStop'     => $noStop,            #是否排除停产 1.排除停产产品 0.不排除
            'minPrice'   => $minPrice,          #最低价格 price >= x
            'maxPrice'   => $maxPrice,          #最高价格 price <= x
            'prices'     => $prices,            #价格多选
            'minLevel'   => $minLevel,          #最低Level Level >= x
            'maxLevel'   => $maxLevel,          #最高Level Level <= x
            'hasPingce'  => $hasPingce,         #是否有评测文章
            'marketTime' => $marketTime,        #上市时间.格式:201503
            'orderBy'    => $orderBy,           #排序
            'offset'     => $offset,            #offset
            'num'        => $num,               #获得个数
            'isGroup'    => $isGroup,           #是否分组取总数
            'subSecond'  => $subSecond,         #是否是第二子类
            'showSeries' => $showSeries,        #是否显示系列
            'isHistory'  => $isHistory,         #历史列表(停产和等级小于10的产品)
		));
        if($offerSql){
            $sql = $offerSql;
        }
        
        if($debug){
			echo $sql;
			exit;
		}
		#数据查询
		$data = self::doQuery(array(
			'sql'       => $sql,
			'returnCol' => !$isGroup ? explode(",",$cols) : '',#值返回的列
			'getAttr'   => $isGroup ? false : true,#获得这次查询返回的属性值,比如共有多少产品 关联哪些子类和品牌
            'isLog'     => $isLog
		));
		$proList = $data['data'];

        #group分组结果(各参数命中数)
        if ($isGroup && $proList) {
            $groupRsArr = $groupValArr = array();
            foreach(array('row','term','count') as $type) {
                $k=0;
                if (isset($data['index'][$type])) {
                    foreach ($data['index'][$type] as $key) {
                        if ($data['data'][$key]["type"] == 'close') continue;
                        $k++;
                        if ('row' == $type) {
                            $vals = $data['data'][$key]["attributes"]['col'];
                        } else {
                            $vals = iconv ("UTF-8", 'GBK', trim($data['data'][$key]["value"]));
                        }
                        $groupValArr[$k][] = $vals;
                    }
                }
            }
            if ($groupValArr) {
                foreach ($groupValArr as $val) {
                    $pid = str_replace(array('param_','_mt'), '', $val[0]);
                    if ('colorid' ==$val[0]) {
                        $pid = 'colorArr';
                    } else if ('activity_type' ==$val[0]) {
                        $pid = 'promotionArr';
                    } else if ('price' ==$val[0]) {
                        $val[1] = (int)$val[1];
                    }

                    $groupRsArr[$pid][$val[1]] = $val[2];
                }
            }
            return $groupRsArr;
        }

		if ($proList) {
			#进行切分随机处理 比如:1-3，4-6，6-9，10-12，13-15，分五段，每段随机排序
			if($random){
				$tmpList = array();
				$numArr = range(0, count($proList) - 1);
				$numArr = array_chunk($numArr, 3); #每三个分为一段
				foreach($numArr as $subNum){
					shuffle($subNum);
					foreach($subNum as $k){
						if(!isset($proList[$k]))continue;
						$tmpList[] = $proList[$k];
					}
				}
				$proList = $tmpList;
			}

			#获得产品信息
			if ($getProInfo) {
				foreach($proList as $k => $v) {
                    #不获取论坛信息
                    $baseArr = array('proId'=>$v['proId'], 'getSimpleBbsInfo' => 1);
                    if ($locationId >1 && isset($v["price_{$locationId}"])) $baseArr['locPrice'] = $v["price_{$locationId}"]; //地区价格lucene
					$tmpProInfo =  Helper_Product::getProductInfo($baseArr);
                    $proList[$k] = $tmpProInfo;
                    
                    
                    #手机系列设置过按系列显示的在列表页不露出单品-只是产品库lucene调整私有云不需要
                    #客户端是全部放开的，所以通过$showSeries判断就行了, 客户端用 2 == $showSeries 判断
                    if (((1 == $showSeries) && 'noPrice' == $priceId && !$paramVal ) || ((2 == $showSeries) && ('noPrice' == $priceId) && !$paramVal && ($proList[$k]['seriesId'] > 0))) {
                        $setInfo = Helper_Series::getSeriesInfo(array('seriesId'=>$proList[$k]['seriesId'],'getSimpleBbsInfo'=>1));
                        if($setInfo){
                            $proList[$k]['name'] = $setInfo['name'];
                            $proList[$k]['compId'] = 's'.$setInfo['mainId']; #系列对比标识
                            if (isset($setInfo['priceNote'])) { #价格处理
                                $proList[$k]['priceShow']['price'] = $setInfo['priceNote'];
                                $proList[$k]['priceShow']['mark'] = "";
                                $proList[$k]['priceShow']['hideNote'] = 1;  #这个变量是控制报价说明隐藏的，为1表示隐藏
                            } else if ($setInfo['lowPrice'] == $setInfo['highPrice']) {
                                $proList[$k]['priceShow']['price'] = $setInfo['lowPrice'] > 10000 ? (round($setInfo['lowPrice']/10000,2)."万") : $setInfo['lowPrice'] ;
                                #20140910 防止系列列表出现￥颜色和价格颜色不一致的情况 tanghs
                                $proList[$k]['priceShow']['mark']=='' && $proList[$k]['priceShow']['style2']='price-normal';
                                $proList[$k]['priceShow']['mark'] = "￥";
                            } else {
                                $setInfo['lowPrice']=$setInfo['lowPrice'] > 10000 ? (round($setInfo['lowPrice']/10000,2)."万") : $setInfo['lowPrice'] ;
                                $setInfo['highPrice']=$setInfo['highPrice'] > 10000 ? (round($setInfo['highPrice']/10000,2)."万") : $setInfo['highPrice'] ;
                                $proList[$k]['priceShow']['price'] = $setInfo['lowPrice'].'-'.$setInfo['highPrice'];
                                $proList[$k]['priceShow']['style2']='price-normal';
                                $proList[$k]['priceShow']['mark'] = "￥";
                                $proList[$k]['priceShow']['hideNote'] = 1;
                            }
                        }
                    }
                    if(isset($v['price_name']) && isset($v['price'])){
                        $proList[$k]['price_name'] =  $v['price_name'];
                        $proList[$k]['price'] =  $v['price'];
                        $proList[$k]['price_id'] =  isset($v['price_id']) ?$v['price_id'] :9999;
                    }

                    if($tmpProInfo['level']==10) {
                        $proList[$k]['secondInfo'] = Helper_Product::getProSecondInfo(array('proId'=>$v['proId'],'infoNum'=>5));
                    }
                    
				}
			}

			#获得产品参数
			if ($getMainParamNum) {
				foreach($proList as $k => $v) {
					$proList[$k]['mainParam'] = Helper_Product::getSortParam(array('proId'=>$v['id'],'onlyMain'=>1,'num'=>$getMainParamNum));
				}
			}

		}

		$outArr =  array(
			 'allNum'  => isset($data['attr']['hits']) ? $data['attr']['hits'] : 0,
			 'data'    => $proList,
		 );
		#相关子类的处理
		if($getRelCate && isset($data['attr']['occur'])){
			$outArr['relCate'] = self::getRelCate(array('occurStr'=>$data['attr']['occur']));
		}
		return $outArr;
	}


	/**
	 * 根据参数拼装SQL
     * 表名,列名,子类,品牌,价格,复合参数,关键字,显示停产,是否评测,排序,分组,limit
	 */
	public static function getSql($paramArr)
	{
        $options = array(
            'table'      => 'product',          #表名
            'cols'       => 'product_id,name',  #列名
            'seriesId'   => 0,                  #系列ID
            'subcateId'  => 0,                  #子类ID
            'manuId'     => 0,                  #品牌ID
            'proIds'     => 0,                  #产品IDs
            'manuType'   => '',                 #品牌类型，国际品牌 自主品牌
            'priceId'    => 'noPrice',          #报价
            'prices'     => array(),            #价格多选
            'locationId' => 1,                  #地区
            'paramVal'   => false,              #参数部分
            'conditions' => '',                 #指定参数
            'keyword'    => '',                 #关键字
            'noJd'       => 0,                  #是否京东导入 1.排除京东导入
            'noStop'     => 0,                  #是否排除停产 1.排除停产产品
            'minPrice'   => 0,                  #最低价格 price >= x
            'maxPrice'   => 0,                  #最高价格 price <= x
            'prices'     => array(),            #价格多选
            'minLevel'   => 0,                  #最低Level Level >= x
            'maxLevel'   => 0,                  #最高Level Level <= x
            'hasPingce'  => 0,                  #是否有评测文章
            'marketTime' => 0,                  #上市时间.格式:201503
            'orderBy'    => 0,                  #排序 #0:默认排序 1:最热门 2:最冷门 3:最便宜 4:最贵 5:评论数最多 6:评论数最少 7:评分最高 8:评分最低 9:最新,11：销量 15:新品排序.
            'offset'     => 0,                  #offset
            'num'        => 10,                 #获得个数
            'isGroup'    => 0,                  #是否分组取总数
            'subSecond'  => 0,                  #是否是第二子类
            'showSeries' => 0,                  #是否显示系列
            'isHistory'  => 0,                  #历史列表(停产和等级小于10的产品)
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$wherePrice = $locationId ? 'price_'.$locationId : 'price'; #比较用价格字段

		if ($conditions != '') $conditions .= " and";

        #子类-包含第二子类判断
        if (is_array($subcateId)) {
            foreach ($subcateId as $key => $value) {
                $conditions .= $key > 0 ? " or sub_id={$value}" : " and (sub_id={$value}";
            }
            $conditions .= ')';
        } else if ($subcateId) {
                $conditions .= $subSecond ? " second_sub_id={$subcateId}" : " sub_id={$subcateId}";
        }

        #系列
        if ($seriesId && strpos($seriesId, '-')) {
            foreach (explode('-', $seriesId) as $key => $sid) {
                $conditions .= $key > 0 ? " or series_id={$sid}" : " and (series_id={$sid}";
            }
            $conditions .= ')';
        } elseif ($seriesId) {
            $conditions .= " and series_id={$seriesId}";
        }

		#品牌
        if ($manuId) {
            if (strpos($manuId, '-')) {
                $manuArr = explode('-', $manuId); #品牌多选
                foreach ($manuArr as $K => $v) {
                    $conditions .= $K > 0 ? " or (manu_id={$v} or second_manu_id = {$v} )" : " and ( (manu_id={$v} or second_manu_id = {$v})";
                }
                $conditions .= ')';
            } else {
                $conditions .= " and ( manu_id={$manuId} or second_manu_id = {$manuId} )";
            }
        }
        #产品
        if ($proIds) {
            if ($conditions == '') $conditions .= " product_id > 0";
            if (strpos($proIds, '-')) {
                $proArr = explode('-', $proIds); #品牌多选
                foreach ($proArr as $K => $v) {
                    $conditions .= $K > 0 ? " or product_id={$v}" : " and (product_id={$v}";
                }
                $conditions .= ')';
            } else {
                $conditions .= " and product_id={$proIds}";
            }
        }
		#品牌类型
        if($manuType) $conditions .= " and manu_type={$manuType}";
		#地区
        if($locationId) $conditions .= " and show_{$locationId}=1";
        #上市时间
        if($marketTime)$conditions .= " and market_time={$marketTime}";
        if($noJd)$conditions .= " and (data_from=0 or data_from=3)";
        #价格
        if($priceId !== '' && $priceId !== 'noPrice') {
			$conditions .= self::getPriceCondition(array("priceId"=>$priceId,"subcateId"=>$subcateId,"locationId"=>$locationId));
            #显示等级在20以上的有报价的产品
            if (!empty($conditions)) {
                $conditions .= ' and level >= 20 ';
            }
        }

        if($minPrice) $conditions .= " and {$wherePrice}>=$minPrice";
        if($maxPrice) $conditions .= " and {$wherePrice}<=$maxPrice";
		if($minPrice || $maxPrice) $conditions .= " and level>=20";

        #价格多选
        if(!empty($prices)) {
            $pricesCondition = '';
            foreach($prices as $key => $value) {
                $pricesCondition .= self::getPriceCondition(array("priceId"=>$value,"subcateId"=>$subcateId,"locationId"=>$locationId, 'type' => 'or'));
            }
        }

        #显示等级在20以上的有报价的产品
        if (!empty($pricesCondition)) {
            $conditions = $conditions . ' and (' . substr($pricesCondition, 3) . ') and level >= 20  ';
        }

		#level
        if($minLevel) $conditions .= " and level>=$minLevel";
        if($maxLevel) $conditions .= " and level<=$maxLevel";

		if($paramVal && !is_array($paramVal)){#参数可以是这种形式:s859-s2192-p18684-s2325
			$paramVal = explode('-',$paramVal);
		}
        #参数
        if($paramVal && $subcateId) {
            $conditions.= self::getParamCondition(array("paramVal" => $paramVal, "subcateId" => $subcateId));
        }
        
		/*是否显示后台隐藏的产品*/##################### 不知道哪里用
        if(isset($hidenPro) && $hidenPro) {
            $conditions .= " and show_{$locationId}=1";
        }

		#筛选有评测文章的产品
		if ($hasPingce)$conditions .= " and ispingce=1";

        #是否排除停产 1.排除停产产品
		if (!in_array($orderBy,array(3,4))) {#如果最便宜,最贵排序的时候限制了level和价格,无需这个判断
            $timeLine = strtotime('-3 MONTH');
			if (!$isHistory && $noStop && $priceId === 'noPrice' && $minPrice == 0 && $maxPrice == 0) { #如果指定了价格的时候,会在价格的地方加上level>20的限制
				$conditions .= " and (level>10 or (level=10 and editdate>={$timeLine}))";
			}
            if ($isHistory) {
                $conditions .= " and level=10 and editdate<{$timeLine}";
            }
            if($orderBy==15){
                //按照上市时间排序,排除概念产品.
                $conditions .= " and market_time>1 and level<>12";
            }
            if($orderBy==7){
                $conditions .= " and review_num>=100";
            }
		} else {
			#历史遗留问题 3为慧聪产品
			$conditions .= " and level>=20 and (data_from=0 or data_from=3)";
			if($locationId){
				$conditions .= " and price_{$locationId}>1";
			}
            
		}

        //分站不提取停产产品
        if ($keyword == '' && $locationId !=1) {
			$conditions .= " and stop=0";
		}
        
		#指定关键字keyword
		if ($keyword) {
            $_keyword = self::keywordFileter($keyword);
            if(strpos($_keyword, '@@@') !== false){
                $_temKwdArr = explode ('@@@', $_keyword);
                $_keyword = $_temKwdArr[1];
            }
            $conditions .= ' and ' . $_keyword;
        }

        #排序
		$orderByStr = $groupStr = '';
        if ($orderBy) {
			#排序数组
			$orderArr = array (
				"0" => "",
				"1" => " order by stop,ip_count desc",            #最热门
				"2" => " order by stop,ip_count asc",                           #最冷门
				"3" => " order by {$wherePrice} asc",                           #最便宜
				"4" => " order by {$wherePrice} desc",                          #最贵
				"5" => " order by review_num desc",                             #点评数最多
				"6" => " order by review_num asc",                              #点评数最少
				"7" => " order by user_score desc,level desc,ip_count desc",    #评分最高
				"8" => " order by user_score asc",                              #评分最低
				"9"	=> " order by product_id desc",                             #最新
                "10"=> " order by is_commend desc,product_id desc",             #阿亮加的
                "11"=> " order by sellNum desc",                                #销量
                "12"=> " order by param_17218 desc",                            #月消费高(手机套餐子类)
                "13"=> " order by param_17218 asc",                             #月消费低(手机套餐子类)
                "14"=> " order by commend_date desc,commend desc,product_id desc",#推荐最新
                "15"=> " order by market_time desc,ip_count desc",
			);
            $orderByStr = $orderArr[$orderBy];
        }

        #攒机筛选的默认处理
        if (isset($pgType) && 'zj' == $pgType) {
            $conditions .= " and level>10 and price>1";
        }

        #手机产品线,默认页和品牌搜索时露出设置过系列
        #点评数和销量排序前10名都是小米 所以改成按系列显示 商皛需求 20141223 mtx 
        if ($showSeries || in_array($orderBy, array(5,11))) {
            $conditions .= " and lucene_show=1";
//            $orderByStr  = str_replace('ip_count','series_ipcount',$orderByStr);
        }

		$limitStr = " limit {$offset}, {$num}";

        #是否分组
        if ($isGroup) {
            $groupStr = self::getParamCondition(array(
                "paramVal"  => $paramVal,
                "subcateId" => $subcateId,
                "manuId"    => $manuId,
                "priceId"   => $priceId,
                'isGroup'   => $isGroup
            ));
            $limitStr = '';
        }

		$sql = "select {$cols} from {$table} where {$conditions}{$groupStr}{$orderByStr}{$limitStr}";
        //echo $sql;exit;
		return $sql;
	}


	 /**
     * 搜索关键词替换，提交数据的时候return带0，查询结果时return带1
     */
    public static function replaceKeyword($str,$return=0) {
        $str = preg_replace('#([\s]+)#sU','#',$str);
        $str = preg_replace('#&(amp;)+#s','&',$str);
        if (!$return) {
            $rep_arr = array('价格','参数','报价','图片','下载','壁纸','新品','港行版','限量版','关键字');
            $str = str_replace($rep_arr,'',$str);
        }

        $preg_match_arr = array('wcdma','td-scdma','cdma2000','P&E','B&W','WI-FI','ev-do','WM','Symbian^3');
        $preg_replace_arr = array('联通3G','移动3G','电信3G','PandE','BandW','WIFI','evdo','windows mobile','symbian3');
        return str_ireplace($preg_match_arr,$preg_replace_arr,$str);
    }

    /**
	 * 查询参数的特殊过滤
	 */
	public static function getFilterQuery()
	{
        return array(
					'移动3G（TD-SCDMA）' => '移动3G',
					'电信3G（CDMA2000）' => '电信3G',
					'联通3G（WCDMA）'    => '联通3G',
					'TD-SCDMA'          => '移动3G',
					'WCDMA'             => '联通3g',
					'CDMA2000'          => '电信3g',
					'是'                => 'luceneyes',
					'有'                => 'luceneyes',
					'无'                => 'luceneno',
					'否'                => 'luceneno',
					'Symbian^3'         => 'symbian3',
                    'Wi-Fi'             => 'WiFi'
					);
	}

	/**
	* 关键字过滤处理
	*/
	public static function keywordFileter($kword){

        $s_price = $noteWord = '';
        $sub_id = $manu_id = 0;
        $add_subcate_arr = array('gphone'=>'57','单反'=>'15','单反相机'=>'15');    #固定关键词指定类别
        $match_subcate_arr = array('镜头'=>268);   #匹配关键词指定类别

        //判断编码处理
        $str_code = mb_detect_encoding($kword);
        if ($str_code == 'UTF-8') {
            $newstr = mb_convert_encoding($kword,'GBK',$str_code);
            $k_en = preg_replace('#[a-z0-9\.,/\+]#isU','',$kword);
            $s_en = preg_replace('#[a-z0-9\.,/\+]#isU','',$newstr);
            if (trim($s_en) && strlen($s_en)*3 >= strlen($k_en)*2) {
                $kword = $newstr;
            }
        }

        $kword = trim($kword,'=/.');
        #关键词处理
        if (!$kword) {################################ 要不要跳转
            header('location:http://search.zol.com.cn/s/');
            exit();
        } else {
            if (preg_match("/^[a-zA-z]*$/", $kword)) {
                $kword = strtolower(ZOL_String::htmlSpecialChars($kword));
            }else{
                $kword = ZOL_String::htmlSpecialChars($kword);
            }
        }

        #价格区间处理
        if (preg_match('#(\d+)(元|块)?(到|至|\-)(\d+)(元|块)#sU',$kword,$match)) {
            $s_price = $match[1].'BBB'.$match[4];
            $kword = str_replace($match[1].$match[2].$match[3].$match[4].$match[5],'',$kword);
        } elseif (preg_match('#(\d+)(元|块)?以(下|内)#sU',$kword,$match)) {
            $s_price = '0BBB'.$match[1];
            $kword = str_replace($match[1].$match[2].'以'.$match[3],'',$kword);
        } elseif (preg_match('#(\d+)(元|块)?以上#sU',$kword,$match)) {
            $s_price = $match[1].'BBB0';
            $kword = str_replace($match[1].$match[2].'以上','',$kword);
        } elseif (preg_match('#(\d+)(元|块)#sU',$kword,$match)) {
            $s_price = intval($match[1]*0.9).'BBB'.intval($match[1]*1.1);
            $kword = str_replace($match[1].$match[2],'',$kword);
            $kword = str_replace('左右','',$kword);
        }

        if (isset($add_subcate_arr[strtolower($kword)])) {      #固定词指定类别处理
            $sub_id = $add_subcate_arr[strtolower($kword)];
        }
        foreach ($match_subcate_arr as $mk=>$subcatid) {        #匹配词指定类别，仅限一个
            if (strpos($kword,$mk) !== false) {
                $sub_id = $subcatid;
                break;
            }
        }

        $seKword = self::replaceKeyword($kword,1);


        #判断初步纠错
        $sql = "select kword,type_id From note.catefirst Where kword={$seKword} limit 1";
		$noteRows = self::doQuery(array('sql'=>$sql));


        if(isset($noteRows['data'][0]) && isset($noteRows['data'][0]['attributes']) && $noteRows['data'][0]['attributes']['hits'] >= 1){
			$idx = $noteRows['index']['kword'][0];
			$val = $noteRows['data'][$idx]['value'];

			$pgStr = preg_replace('#([\s]+)#sU','#',strtolower($val));
            if ($pgStr != strtolower($seKword)) {
                $noteWord = iconv("UTF-8","GBK",$val);
            }

		}else{
            $sword = str_replace(array('（','）','【','】','：','？','，'),' ',$kword);
            $sword = preg_replace('#([\s\(\)\[\]\*\?\,]+)#',' ',$sword);
            $sword = preg_replace('#([A-Z]+)#','strtolower("\\1")',trim($sword));
            $sql = "select replace_word from z_keyword_list Where keyword='{$sword}'";
			self::$dbSearch = Db_Search::instance("Db_Search");
            $noteWord = self::$dbSearch->getOne($sql);
		}


        $add_cond = '';
        $cond = '(title='.$seKword.' or keyword='.$seKword.' or other_name='.$seKword.')';

        if ($manu_id) {     #品牌
            $add_cond .= " And manu_id=".$manu_id;
        }
        if ($sub_id) {      #子类
            $add_cond .= " And sub_id=".$sub_id;
        }
        if (strstr($s_price,'BBB')) {   #价格区间搜索
            $pa = explode('BBB',$s_price);
            if ($pa[1] == 0) {
                $add_cond.= " And price>=".$pa[0]." ";
            } else if ($pa[0] == 0) {
                $add_cond .= " And price>0 And price<=".$pa[1];
            } else {
                sort($pa);
                $add_cond .= " And price>=".$pa[0]." And price<=".$pa[1];
            }
        }
        if ($s_price=='t') {
            $add_cond .= " And level>15 And price>0 order by price asc ";
        } else if ($s_price=='o') {
            $add_cond .= " And price=0 ";
        }

        #搜索提示
        $sql = "select id From product Where ".$cond.$add_cond.' limit 0';
		$rows = self::doQuery(array('sql'=>$sql));

		#获得note的typeid
		$noteTypeVal = 0;
		if(isset($noteRows['index']['type_id'])){
			$noteTypeIdx = $noteRows['index']['type_id'][0];
			$noteTypeVal = $noteRows['data'][$noteTypeIdx]['value'];
		}

        if (isset($rows['data'][0]['attributes']['hits']) && $rows['data'][0]['attributes']['hits'] < 1 && $noteWord) {

            return $noteWord.'@@@(title='.$noteWord.' or keyword='.$noteWord.')'.$add_cond;

        } else if (isset($rows['data'][0]['attributes']) && $noteRows['data'][0]['attributes']['hits'] > 1
                && isset($noteRows['data'][0]) && isset($noteRows['index']['type_id']) && $noteTypeVal < 4) {

            return $noteWord.'@@@'.$cond.$add_cond;

        } else {

            return $cond.$add_cond;
        }
    }


    /**
	 * 得到价格参数查询条件
	 */
	private static function getPriceCondition($paramArr){
        $options = array(
            'priceId'       => 'noPrice',   #price Id
            'subcateId'     => 0,    #子类
            'locationId'    => 1,    #地区
            'type'          => 'and'
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $priceCon = '';
        if ($priceId === 'noPrice') {
            return $priceCon;
        }
        $priceRangeArr = self::loadCache('PriceRange', array('subcateId' => $subcateId));
        if (strpos($priceId,'-')) {
            $_priceArr = explode("-",$priceId);
            $lowPrice  = (int)$_priceArr[0];
			$highPrice = (int)$_priceArr[1];
        } else if (isset($priceRangeArr[$priceId]) && $priceId !== 'noPrice') {
			$lowPrice = $priceId;
			$highPrice = (int)$priceRangeArr[$priceId];
		}
        if (isset($lowPrice) ) {
            $lowPrice = $lowPrice ? $lowPrice : 1;//避免级别状态不同步导致的价格查询问题
            $priceCon .= $locationId > 1 ? ' ' . $type . " ( price_".$locationId." >= {$lowPrice}" : ' ' . $type . " ( price >= {$lowPrice}";
		}
		if (isset($highPrice) && (int)$highPrice) {
            $priceCon .= $locationId > 1 ? " and price_".$locationId." <= {$highPrice} ) " : " and price <={$highPrice} ) ";
        } elseif (!empty ($priceCon)) {
            $priceCon .= ' ) ';
        }

        return $priceCon;
    }


	/**
	 * 得到复合参数查询条件
	 * @param array $paramVal
	 */
	public static function getParamCondition($paramArr) {
        $options = array(
            'paramVal'      => false, #参数数组
            'subcateId'     => 0,     #子类
            'isGroup'       => 0,     #是否分组
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        $paramCon = '';
        if ($paramVal) {
            $paramArr      = array();
            $listParamArr  = self::loadCache('ListParam', array('subcateId' => $subcateId, 'type'=>'ALL'));
            $FilterQuery   = self::getFilterQuery(); #获得参数的替换词
            foreach ($listParamArr as $paramId => $paramValue) {
//                if ($paramId == 'featuresArr') continue;
                if (isset($paramValue['sourceArr'])) {
                    foreach ($paramValue['sourceArr'] as $opId => $opValue) {
                        #有unit的去掉$opValue中的unit内容，比如$opValue的内容为“3.5英寸”，unit的内容为“英寸”， 则去掉“3.5英寸”中的“英寸”，得到3.5
                        #lucene中现在是用3.5这个值进行搜索的
                        $opValue  = isset($paramValue['unit']) ? str_replace($paramValue['unit'], '', $opValue) : $opValue;
                        $feValue  = isset($FilterQuery[$opValue]) ? strtr($opValue, $FilterQuery) : $opValue;
                        $feValue  = str_replace('GB', '', $feValue); #功率计算页特殊数据处理
                        $key = 'p' . $opId;
                        $paramArr[$key] =array($feValue, $paramId, 'equal');
                    }
                }

                if (isset($paramValue['queryValArr'])) {
                    foreach ($paramValue['queryValArr'] as $opId => $opValue) {
                        if (!isset($paramValue['commandArr']) || !isset($paramValue['isOption'])) continue;
                        $feValue = isset($FilterQuery[$opValue]) ? strtr($opValue, $FilterQuery) : $opValue;
                        $key = ($paramValue['isOption'] == 2) ? ('s' . $opId) : ('p' . $opId);
                        #此处是针对运营商合作的特殊处理jiebl@2013.12.9
                        if ('16776' == $paramId && isset($paramValue['specialTabPixArr'][$opId])) {
                            $key = $paramValue['specialTabPixArr'][$opId]['pix'].$opId;
                            $feValue = $opId;
                            $specialTabName = $paramValue['specialTabPixArr'][$opId]['name'];
                            $isRange = isset($paramValue['isRange']) ? $paramValue['isRange'] : 0;
                            $paramArr[$key] =array($feValue, $paramId, $paramValue['commandArr'][$opId], $paramValue['isOption'], $isRange,$specialTabName);
                        }else{
                            $isRange = isset($paramValue['isRange']) ? $paramValue['isRange'] : 0;
                            if(isset($paramValue['commandArr'][$opId])){
                                $paramArr[$key] =array($feValue, $paramId, $paramValue['commandArr'][$opId], $paramValue['isOption'], $isRange);
                            }
                        }
                        if (isset($paramValue['linkArr'][$opId])) array_push($paramArr[$key],$paramValue['linkArr'][$opId]);
                    }
                }

                if ($paramId == 'colorArr') {
                    foreach ($paramValue['valArr'] as $opId => $val) {
                        $key = 'c'.$opId;
                        $paramArr[$key] =array($opId,'colorArr','color');
                    }
                }
                
                if ($paramId == 'telecomArr') {
                    foreach ($paramValue['valArr'] as $opId => $val) {
                        $key = 't'.$opId;
                        $paramArr[$key] =array($opId,'telecomArr','telecom');
                    }
                }
                
                if ($paramId == 'promotionArr') {
                    foreach ($paramValue['valArr'] as $opId => $val) {
                        $key = 'a'.$opId;
                        $paramArr[$key] =array($opId,'promotionArr','promotion');
                    }
                }
                if ($paramId == 'featuresArr' && isset($paramValue['valArr'])) {
                    foreach ($paramValue['valArr'] as $opId => $val) {
                        $key = 'x'.$opId;
                        $paramArr[$key] =array($opId,'featuresArr','features');
                    }
                }
            }
            foreach ($paramVal as $key => $value) {
                $paramValue     = isset($paramArr[$value][0]) ? $paramArr[$value][0] : '';
                $paramId 		= isset($paramArr[$value][1]) ? $paramArr[$value][1] : '';
                $paramCommand 	= isset($paramArr[$value][2]) ? $paramArr[$value][2] : '';
                $isOption       = isset($paramArr[$value][3]) ? $paramArr[$value][3] : '';
                $isRange        = isset($paramArr[$value][4]) ? $paramArr[$value][4] : '';
                if (isset($paramArr[$value][5])) { #合并子类新增元素
                    $_paramArr[$paramId][] = array($paramValue,$paramCommand,$isOption,$isRange,$paramArr[$value][5]);
                } else {
                    $_paramArr[$paramId][] = array($paramValue,$paramCommand,$isOption,$isRange);
                }
                if ($paramId == '' || $paramValue == '') {
                    return $paramCon;
                }
            }
            foreach ($_paramArr as $_key => $_value) {
                $paramAnd  = '';
                $_isOption = $_value[0][2];
                $_parCom   = $_value[0][1];
                $_parVal   = $_value[0][0];
                $_isRange  = $_value[0][3];
                $_pmcon    = ($_key == 'colorArr') ? "colorid" : "param_{$_key}"; #颜色
                $_pmcon    = ($_key == 'promotionArr') ? "activity_type" : $_pmcon; #促销类型
                $_pmcon    = ($_key == 'telecomArr') ? "purchase_type" : $_pmcon; #合约机
                $_pmcon    = ($_key == 'featuresArr') ? "special_x" : $_pmcon; #特性
                if (count($_value) == 1) {
                    if ($_key == 4951) $_parVal= str_replace('<', '', $_parVal);
                    $mt = ('equal' == $_parCom && $_isOption == 2) ? "_mt" : "";
                    if('special' == $_parCom){
                        if(isset($_value[0][4])) $paramAnd .=  $_value[0][4]."=".$_parVal;
                    }else if ('between' == $_parCom) {
                        $_parVal   = self::getBetweenValue ($_pmcon, $_parVal,$_isRange);
                        $paramAnd .= $_parVal;
                    } else if (preg_match('/###/',$_parVal)) {
                        $_parVal = str_replace ('###', ' or ' . $_pmcon . $mt . ' = ', $_parVal);
                        $paramAnd .= $_pmcon . $mt . ' = ' . $_parVal;
                    } else if (isset($_value[0][4])) { #多子类联合搜索
                        $linkArr = explode('-', $_value[0][4]);
                        foreach ($linkArr as $k => $v) {
                            $paramAnd .= $k ? " or param_{$v}$mt=$_parVal" : "param_{$v}$mt=$_parVal";
                        }
                    } else {
                        $paramAnd .= $_pmcon . $mt . ' = ' . $_parVal;
                    }
                } else {
                    foreach ($_value as $_pmKey => $_var) {
                        $_temVal = $_var[0];
                        $mt = ('equal' == $_var[1] && $_isOption == 2) ? "_mt" : "";
                        if ('between' == $_var[1]) {
                            $_temVal = self::getBetweenValue ($_pmcon, $_temVal, $isRange);
                            $paramAnd ? $paramAnd .= ' or (' . $_temVal . ')' : $paramAnd .= '(' . $_temVal . ')';
                        } else if (preg_match('/###/',$_temVal)) {
                            $_temVal = str_replace ('###', ' or ' . $_pmcon . $mt . ' = ', $_temVal);
                            $paramAnd ? $paramAnd .= ' or ' . $_pmcon . $mt . ' = ' . $_temVal : $paramAnd .= $_pmcon . ' = ' . $_temVal;
                        } else if (isset($_var[4])) { #多子类联合搜索
                            $linkArr = explode('-', $_var[4]);
                            foreach ($linkArr as $k => $v) {
                                $paramAnd .= $paramAnd ? " or param_{$v}$mt=$_temVal" : "param_{$v}$mt=$_temVal";
                            }
                        } else {
                            $paramAnd ? $paramAnd .= ' or ' . $_pmcon . $mt . ' = ' . $_temVal : $paramAnd .= $_pmcon . ' = ' . $_temVal;
                        }
                    }
                }
                $paramCon .= ' and (' . $paramAnd. ')';
            }
        }

        if ($isGroup && ($manuId || 'noPrice' != $priceId || $paramVal)) {
            $groupCon = '';
            if (!$manuId) {
                $ln = 0;
                $manuArr = Helper_List::getManuArr(array('subcateId' => $subcateId));
                $groupCon .= 'manu_id{';
                foreach ($manuArr as $k => $v) {
                    $groupCon .= $ln++ ? '|'.$k : $k;
                }
                $groupCon .= '}';
                //echo $groupCon;exit;
            }
            if ('noPrice' == $priceId) {
                $ln = 0;
                $ot = $groupCon ? '&' : '';
                $priceArr = self::loadCache('PriceRange', array('subcateId' => $subcateId));
                $groupCon .= "{$ot}price{";
                foreach ($priceArr as $k => $v) {
                    $groupCon .= $ln++ ? '|'.$k.'###'.(int)$v : $k.'###'.$v;
                }
                $groupCon .= '}';
                //echo $groupCon;exit;
            }

            if ($manuId || 'noPrice' != $priceId || $paramVal) {
                $valParamArr = Helper_List::getParamArr(array(
                    'subcateId'      => $subcateId,     #子类ID
                    'custom'         => 0,              #自定义参数(超级本处理)
                ));
                foreach ($valParamArr as $pamId => $pam) {
                    if ($pamId == 'featuresArr') continue;
                    $paramF = 'colorArr' == $pamId ? 'colorid' : 'param_'.$pamId;
                    $paramF = 'promotionArr' == $pamId ? 'activity_type' : $paramF;
                    $ln = 0;
                    $ot = $groupCon ? '&' : '';
                    $groupCon .= $pam['isMt'] ? "{$ot}{$paramF}_mt{" : "{$ot}{$paramF}{";
                    foreach ($pam['valArr'] as $val) {
                        if (preg_match('/###/',$val['query'])) {
                            $_temVal = str_replace ('###', '|' , $val['query']);
                        } else {
                            $_temVal = $val['query'];
                        }
                        $groupCon .= $ln++ ? '|'.$_temVal : $_temVal;
                    }
                    $groupCon .= '}';
                }
            }
            return ' group by '.$groupCon;
        }

		return $paramCon;
	}

	/**
	 * 对区间的参数值生成条件
	 *
	 * @param 选项 $option
	 * @param 选项值 $value
	 * @return 搜索的条件
	 */
	public static function getBetweenValue($option, $value, $isRange){
		$betweenArr = explode ( '###', $value );
		$condition = '';
        #取区间或相似范围的参数，如果起始参数为0则取大于反之取大于等于
        if ($isRange) {
            $condition.="({$option}_max>={$betweenArr[0]} And {$option}_max<={$betweenArr[1]})";
        } else {
            if ($betweenArr [0]) {
                $condition .= " and " . $option . ">={$betweenArr[0]}";
            } else {
                $condition .= " and " . $option . ">{$betweenArr[0]}";
            }
            if (!empty($betweenArr [1]) && 0 < $betweenArr [1]) {
                $condition .= " and " . $option . "<=".$betweenArr[1];
            }
        }
		return $condition;
	}

    /**
	 * 得到命中的相关子类和品牌等信息
	 * occurStr形如:sub_id:49_2,57_11,499_1,;manu_id:98_7,212_2,32221_1,1189_2,;
	 */
	private static function getRelCate($paramArr){
         $options = array(
            'occurStr'      => '',   #lucene返回xml中的occur属性值
         );
         if (is_array($paramArr))$options = array_merge($options, $paramArr);
		 extract($options);

		 if(!$occurStr)return false;

		 $relCate = array();
		 $relOccurArr = explode(';', $occurStr);
		 if($relOccurArr && is_array($relOccurArr)){ #相关子类和品牌的混合
			foreach($relOccurArr as $relOccurStr){
				if(empty($relOccurStr))continue;
				#将品牌和子类截取出来 sub_id:49_2,57_11,499_1, 分解
				$relSubOccur = explode(':',$relOccurStr);
				if(empty($relSubOccur))continue;

				#49_2,57_11,499_1,  分解
				$relGrandOccur = explode(',',$relSubOccur[1]);
				$tmpArr = array();
				if($relGrandOccur){
					foreach($relGrandOccur as $v){#57_11 分解
						if(!$v)continue;
						$v = explode("_", $v);
						$tmpArr[] = array(
							'id'  => $v[0],
							'num' => $v[1],
						);
					}
				}

				if($relSubOccur[0] == 'sub_id'){
					#子类信息的完善
					if($tmpArr){
						$subcateInfo = self::loadCache('Subcate', array());
						foreach($tmpArr as $k => $v){
							$tmpArr[$k]['name'] = $subcateInfo[$v['id']]['name'];
							$tmpArr[$k]['subcateEnName'] = $subcateInfo[$v['id']]['subcateEnName'];
						}
					}
					$relCate['subcate'] = $tmpArr;

				}elseif($relSubOccur[0] == 'manu_id'){
					#品牌信息的完善
					if($tmpArr){
						$i = 0;
						foreach($tmpArr as $k => $v){

							if($i++ > 50){#避免品牌数量过大
								unset($tmpArr[$k]);
								continue;
							}

							$manuInfo = self::loadCache('Manu', array ('manuId' => $v['id']));
							if(!$manuInfo){
								unset($tmpArr[$k]);
								continue;
							}

							$tmpArr[$k]['name']   = $manuInfo['name'];
							$tmpArr[$k]['cnName'] = $manuInfo['cnName'];
							$tmpArr[$k]['enName'] = isset($manuInfo['enName']) ? $manuInfo['enName'] : '';
						}
					}
					$relCate['manu'] = $tmpArr;
				}
			}
			return $relCate;
		}


	}


	 /**
	 * 执行查询,类似mysql的query
	 */
	public static function doQuery($paramArr){
        $options = array(
            'sql'           => '',   #要执行的SQL
            'returnCol'     => false,#关心的返回字段,如果指定了返回字段,则值返回指定的返回字段的数据,否则返回index和data
            'getAttr'       => false,#是否获得属性值,因为返回xml中的attributes中函数很多有用信息,可以用这个
            'isLog'         => 0,    #是否开启日志邮件
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		#获得搜索的host,从sql中提取,product模块就去product.lucene.zol.com.cn product.mtall也是product
		$host = self::$_lucence_host;
	    if(preg_match("/from ([a-z\.]+) where/isU", $sql,$tblMatch)){
			$host = trim($tblMatch[1]);
			if($pos = strpos($host, ".")){#取点之前的
				$host = substr($host, 0,$pos);
			}
			$host .= ".lucene.zol.com.cn.";
		}

		#数据请求
        $content = '';
		$timeout = 100; #socket超时设置为1秒
        ini_set('default_socket_timeout',2);
		$fp = fsockopen($host, 6036,$errno,$errstr,$timeout);
        if (!$fp) {
            for($i=0; $i<4; $i++) {
                //Lucene报错记录
                if($isLog){
                    if($errno==0){
                        self::saveLog(array(
                            'errNum' => 0,
                            'errDec' => "初始化套接字报错: host:{$host}, 描述:{$errstr},设置的超时时间100秒.定义的常量default_socket_timeout值为2秒",
                            'stamp'  => SYSTEM_TIME,
                        ));
                    }else{
                        self::saveLog(array(
                            'errNum' => $errno,
                            'errDec' => "fscokopen函数报错: host:{$host}, 描述:{$errstr},错误数量{$errno}.",
                            'stamp'  => SYSTEM_TIME,
                        ));
                    }
                }
                $fp = fsockopen($host, 6036,$errno,$errstr,$timeout);
                if ($fp) break;
            }
        }
        if ($fp) {
            stream_set_timeout($fp, $timeout);
            fwrite($fp,$sql."\n");
            while (!feof($fp)) {
                $content .= fgets($fp, 2048);
            }
            fclose($fp);
        }
        if(!$content){
            #如果第一次取回数据为空,记录日志重来一次
            if($isLog){
                self::saveLog(array(
                    'errNum' => 1,
                    'errDec' => "通过fgets方法取回的内容为空,sql语句为{$sql}.",
                    'stamp'  => SYSTEM_TIME,
                ));
            }
            
            $fp = fsockopen($host, 6036,$errno,$errstr,$timeout);
            if (!$fp) {
                for($i=0; $i<4; $i++) {
                    $fp = fsockopen($host, 6036,$errno,$errstr,$timeout);
                    if ($fp) break;
                }
            }
            if($fp){
                stream_set_timeout($fp, $timeout);
                fwrite($fp,$sql."\n");
                while (!feof($fp)) {
                    $content .= fgets($fp, 2048);
                }
                fclose($fp);
            }
        }

		#数据解析
		$content = trim ( $content );
        $content = iconv ( "GBK", 'UTF-8', $content ); #转成utf8才能解析
		$parser = xml_parser_create ();
		xml_parser_set_option ( $parser, XML_OPTION_CASE_FOLDING, 0 );
		xml_parser_set_option ( $parser, XML_OPTION_SKIP_WHITE, 1 );
		xml_parse_into_struct ( $parser, $content, $dataArr, $indexArr );
		xml_parser_free ( $parser );

		#结果封装
		if($returnCol){#指定了返回列
			 $keyMap = array(#lucene有些变量不符合产品库规范,进行转换
				 'product_id' => 'proId'
			 );
			 $returnArr = array();
			 if($indexArr){
				 $tmp = $returnCol[0];
				 if(isset($indexArr[$tmp])){
					 foreach($indexArr[$tmp] as $k => $v){
						 foreach($returnCol as $colKey){
							 $idx = $indexArr[$colKey][$k];
							 if(!isset($dataArr[$idx]) || !isset($dataArr[$idx]['value']))continue;
							 #将key编码规范化
							 $arrKey = isset($keyMap[$colKey]) ? $keyMap[$colKey] : $colKey;
							 $returnArr[$k][$arrKey] = iconv ( "UTF-8", 'GBK', trim($dataArr[$idx]['value']));;
						 }
					 }
				 }
			 }
			 if($getAttr){#获得属性
				return array(
					'data'  => $returnArr,
					'attr'  => isset($dataArr[0]['attributes']) ? $dataArr[0]['attributes'] : array() ,
				);
			 }else{
				return $returnArr;
			 }
		}else{
			return array(
				'index' => $indexArr,
				'data'  => $dataArr,
			);
		}
	}



    private static function saveLog($paramArr){
        
        $option = array(
            'errNum' => 0,
            'errDec' => '',
            'stamp'  => SYSTEM_TIME,
        );
        
        if(is_array($paramArr)) $options = array_merge($option,$paramArr);
        extract($options);
        
        $mailContent = "";
        $mailContent .= "lucene接口报错:\r\n";
        $mailContent .= "报错数量:{$errNum}\r\n";
        $mailContent .= "时间:{$stamp}\r\n";
        $mailContent .= "内容:{$errDec}\r\n";
        ZOL_Api::run("Service.Message.sendMail" , array(
            'mailto'         => 'chen.jingtao@zol.com.cn',   #收件人地址
            'subject'        => 'Helper_Lucene Err',      #标题
            'fromname'       => 'Lucene接口日志',          #发件人名
            'content'        => $mailContent,   #消息内容
        ));
        return true;
    }

}
