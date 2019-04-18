<?php
/**
* 所有的URL
* @author 仲伟涛 
* @editor 王浩镔
* @copyright (c) 2011-10-20
*/
class Libs_Global_Url
{
	/**
	* 软件库
	* @var ZOL_Db_Soft
	*/
	private static $_dbSoft;

    private static $dealerHost = 'http://dealer.zol.com.cn/';

	public static function getLogo($id)
	{
		return ZOL_Config::get('Pro_Ad', 'PIC_HOST') . 'detail_ad/' . ceil($id / 1000) . '/' . $id . '.jpg';
	}

	public static function getMer($merId)
	{
		return ZOL_Config::get('Pro_Ad', 'DEALER_HOST') . 'd_' . $merId . '/';
	}
    
	/**
	* 获取系列驱动下载链接
	*/
	public static function getDriveUrl($proId = 125888){
        self::$_dbSoft       = Db_Soft::instance();
        
        $sql = 'select z_product_id from z_soft_to_product where z_product_id = ' . $proId;
        $res = self::$_dbSoft->getRow($sql);
		
		$url = '';
		if ($res) {
			$url = 'http://driver.zol.com.cn/series/' . $proId . '.html';
		}
		return $url;
	}
	
	/**
	 * 攒机页链接
	 * @param unknown $paramArr
	 */
	public static function getDiyUrl($paramArr){
		$options = array(
				'mainId'    => '',	#攒机订单Id
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		return "http://zj.zol.com.cn/diy/detail/{$mainId}.html";
	}
	
	/**
	 * 攒机列表页Url
	 * @param unknown $paramArr
	 */
	public static function getZjListUrl($paramArr){
		$options = array(
				'cateId'    => '',	#cateIdId
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		return "http://zj.zol.com.cn/list_c{$cateId}_l1_1_1.html";
	}
	
	/**
	* 获取BLOG链接
	*/
	public static function getBlogUrl($paramArr){
        $options = array(
            'userId'    => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		$userId = trim($userId);
		$url = '';
		if ($userId) {
			$url = 'http://blog.zol.com.cn/'. $userId . '/';
		}
		return $url;
	}

	/**
	* 个人中心链接
	*/
	public static function getMyUrl($paramArr){
        $options = array(
            'userId'    => '',
            'type'      => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$url = 'http://my.zol.com.cn/';
		$url .= $userId ? ($userId .'/') : '';
		$url .= $type ? ($type .'/') : '';
		return $url;
	}


	/**
	* 获取品牌专区列表
	*/
	public static function getManuSpecAreaUrl($paramArr){
        $options = array(
            'hostName'       => '',#主机名
            'manuId'         => 0, #品牌ID
            'subcateId'      => 0, #该品牌对应的子类
            'mainSubcateId'  => 0, #该文章频道关联的主子类
            'specialManuUrl' => '',#品牌专区的地址
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		#这些频道下的品牌专区,都是在频道根目录下
		$multiSub = array('mouse.zol.com.cn','net.zol.com.cn','power.zol.com.cn');
		if($manuId){
            if(!$specialManuUrl){
                if($mainSubcateId && $mainSubcateId != $subcateId && !in_array($hostName,$multiSub) ){
                    $url = "http://{$hostName}/{$subcateId}/manu_{$manuId}.shtml";
                }else{
                    $url = "http://{$hostName}/manu_{$manuId}.shtml";
                }
            }else{
                $url= "http://".str_replace("http://","", $specialManuUrl)."/manu_{$manuId}.shtml";
            }
		}else{
			$url = "http://{$hostName}/";
		}
		return $url;
	}

	/**
	 * 获得pk的最终页URL
	 */
	public static function getPKUrl($paramArr){
        $options = array(
            'proId'       => 0, #本产品
            'pkProId'     => 0, #pk的产品
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
	    if (empty($proId) || empty($pkProId) || $proId==$pkProId) {
	        return false;
	    }

        if($pkProId  <  $proId){
            $tmp     = $pkProId;
            $pkProId = $proId;
            $proId   = $tmp;
        }
	    return '/pk/' . $proId. '_' . $pkProId. '.shtml';
    }

	/**
	* 获取产品最终页链接
	*/
	public static function getProUrl($paramArr){
        $options = array(
            'proId'             => 0,  #本产品
            'subcateEnName'     => '', #子类英文名
            'type'              => 'default', #最终页类型
            'param'             => '',        #附加参数
            'rewrite'           => true,      #是否进行伪静态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (!$rewrite) {
			return '/index.php?c=Detail_' . ucfirst($type) . '&proId=' . $proId . $param;
		}
        
        $type = strtolower($type);
		if ($type == 'index' || $type == 'default') {
			$url = '/' . $subcateEnName . '/index' . $proId . '.shtml';
		}elseif('grouppic' == $type){#组图列表 /{proId}/grouppic_{picClassId}_{groupId}_{page}.shtml
			$url = '/' . $proId . '/grouppic' . $param . '.shtml';
        } else {
			$type = $type == 'picture' ? 'pic' : $type;
			$url = '/' . ceil($proId / 1000) . '/' . $proId . '/' . $type . $param . '.shtml';
		}

//		if($subcateEnName && ($subcateEnName=='china')){
//			$url = "http://product.xgo.com.cn".$url;
//		}
		return $url;
	}
    
    /**
     * 获取口碑内页URL
     */
    public static function getKoubeiUrl($paramArr)
    {
        $options = array(
            'proId'  => 0,  #产品ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        return '/koubei/'.$proId.'.shtml';
    }

    /**
	 * 获得产品图片链接
	 */
	public static function getPicUrl($paramArr){
        $options = array(
            'picId'             => 0,     #图片ID
            'proId'             => 0,     #产品ID
            'type'              => 'PRO', #类型
            'param'             => '',    #附加的参数
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
      
		$PicUrl = '';
		switch ($type) {
           case 'PRO':
			default:
                $proUrl= ''; 
                if ($proId) {
                    $proUrl = (!$param?"_0":'').'_p'.$proId;
                }
				$proRelPath = "/picture_index_" . ceil($picId/10000) ."/";
				$PicUrl = $proRelPath . 'index' . $picId .$param.$proUrl.".shtml";
				break;
            case 'GROUP':#组图，此时$param为groupId
				$proRelPath = "/picture_index_" . ceil($picId/10000) ."/";
				$PicUrl = $proRelPath . 'group' . $picId .$param.".shtml";
                break;
			case 'DCBBS':
			case 'SJBBS':
			case 'PHOTO':
            case 'EXHIBIT':
                $str = "";
                $param = trim($param, "_");
                if(!empty($param)){$str = "_".$param;}
                $picTypeArr = strtolower($type);  
				$proRelPath = "/picture_index_" . ceil($picId/10000) . "/";
                $PicUrl = $proRelPath . $picTypeArr . $picId . $str . ".shtml";
//				$PicUrl = '/index.php?c=Pic_Sample&picId='.$picId.'&type='.$type.'&picClassId='.$param;
                break;
		}
       
		return $PicUrl;
	}
/**
	* 获得排行的更多链接
	*/
	public static function getTopUrl($paramArr){
        $options = array(
            'subcateId'          => 0,
            'subcateEnName'      => '',
            'secondSubcateId'    => 0,
            'secondSubcateEnName' => '',
            'range'              => 1, #价格段、参数链接时需要 1、2、3、4、5
            'needId'             => 0,
            'featureParam'       => '',#特性参数id,如:"x11"
            'cateId'             => 0, #大类id，大类页用 /compositor/cate_64.html
            'type'               => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		$baseUrl = 'http://'.TOP_HOST.'/compositor/';
        #特殊子类英文名处理
        if($secondSubcateEnName=='ultrabook') { $secondSubcateEnName = 'Ultrabook'; }
		if($type != '') {
			switch($type){
				case 'manu':
					$moreUrl = $baseUrl . $subcateId . '/manu_attention.html';
                    if($secondSubcateId) {
                        $moreUrl = $baseUrl . $secondSubcateId . '/'.$secondSubcateEnName.'_manu_attention.html';
                    }
					break;
				case 'manuPro':
					$moreUrl = $baseUrl . $subcateId . '/manu_' . $needId . '.html';
                    if($secondSubcateId) {
                        $moreUrl = $baseUrl . $secondSubcateId . '/manu_' . $needId . '.html';
                    }
					break;
				case 'subcate':
					$moreUrl = $baseUrl . $subcateId . '/' . $subcateEnName . '.html';
                    if($secondSubcateId) {
                        $moreUrl    =   $baseUrl . $secondSubcateId . '/'.$subcateEnName.'_' . $secondSubcateEnName . '.html';
                    }
					break;
				case 'price':
					$moreUrl = $baseUrl . $subcateId . "/price_{$range}.html";
					break;
				case 'param':
					$moreUrl = $baseUrl . $subcateId . '/param_' . $needId . "_{$range}.html";
					break;
				case 'featureParam':
					$moreUrl = $baseUrl . $subcateId . '/feature_param_' . $featureParam . ".html";
					break;
				case 'series':
					if($subcateId && $needId){
						$moreUrl = $baseUrl . $subcateId . "/series_".$needId.".html";
					}else if($subcateId){
						$moreUrl = $baseUrl . $subcateId . "/series_attention.html";
					}
                    break;
                case 'upQuick':
                    $moreUrl = $baseUrl . $subcateId . '/hit_wave.html';
                    break;
                case 'cate':
                    $moreUrl = $baseUrl . "cate_{$cateId}.html";
                    break;
                case 'subcateAll':
                    $moreUrl = $baseUrl . "subcateAll.html";
                    break;
                case 'trend':
                    $moreUrl = $baseUrl . "trend_{$subcateId}.html";
                    break;
                case 'shop':
                    $moreUrl = 'http://'.TOP_HOST . "/hot/{$subcateEnName}.html";
                    break;
				default :
					break;
			}
		} else {
			$moreUrl = $baseUrl . $subcateEnName . '.html';
		}
		return $moreUrl;
	}
    
    /**
	* 获得慧聪排行的更多链接
	*/
	public static function getHcTopUrl($paramArr){
        $options = array(
            'subcateId'          => 0,
            'subcateEnName'      => '',
            'secondSubcateId'    => 0,
            'secondSubcateEnName' => '',
            'range'              => 1, #价格段、参数链接时需要 1、2、3、4、5
            'needId'             => 0,
            'featureParam'       => '',#特性参数id,如:"x11"
            'cateId'             => 0, #大类id，大类页用 /compositor/cate_64.html
            'type'               => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		$baseUrl = 'http://'.TOP_HOST.'/hc/';
        #特殊子类英文名处理
        if($secondSubcateEnName=='ultrabook') { $secondSubcateEnName = 'Ultrabook'; }
		if($type != '') {
			switch($type){
				case 'manu':
					$moreUrl = $baseUrl . $subcateId . '/manu_attention.html';
                    if($secondSubcateId) {
                        $moreUrl = $baseUrl . $secondSubcateId . '/'.$secondSubcateEnName.'_manu_attention.html';
                    }
					break;
				case 'manuPro':
					$moreUrl = $baseUrl . $subcateId . '/manu_' . $needId . '.html';
                    if($secondSubcateId) {
                        $moreUrl = $baseUrl . $secondSubcateId . '/manu_' . $needId . '.html';
                    }
					break;
				case 'subcate':
					$moreUrl = $baseUrl . $subcateId . '/' . $subcateEnName . '.html';
                    if($secondSubcateId) {
                        $moreUrl    =   $baseUrl . $secondSubcateId . '/'.$subcateEnName.'_' . $secondSubcateEnName . '.html';
                    }
					break;
				case 'price':
					$moreUrl = $baseUrl . $subcateId . "/price_{$range}.html";
					break;
				case 'param':
					$moreUrl = $baseUrl . $subcateId . '/param_' . $needId . "_{$range}.html";
					break;
				case 'featureParam':
					$moreUrl = $baseUrl . $subcateId . '/feature_param_' . $featureParam . ".html";
					break;
				case 'series':
					if($subcateId && $needId){
						$moreUrl = $baseUrl . $subcateId . "/series_".$needId.".html";
					}else if($subcateId){
						$moreUrl = $baseUrl . $subcateId . "/series_attention.html";
					}
                    break;
                case 'upQuick':
                    $moreUrl = $baseUrl . $subcateId . '/hit_wave.html';
                    break;
                case 'hitNew':
                    $moreUrl = $baseUrl . $subcateId . '/hit_new.html';
                    break;
                case 'cate':
                    $moreUrl = $baseUrl . "cate_{$cateId}.html";
                    break;
                case 'subcateAll':
                    $moreUrl = $baseUrl . "subcateAll.html";
                    break;
                case 'trend':
                    $moreUrl = $baseUrl . "trend_{$subcateId}.html";
                    break;
                case 'shop':
                    $moreUrl = 'http://'.TOP_HOST . "/hot/{$subcateEnName}.html";
                    break;
				default :
					break;
			}
		} else {
			$moreUrl = $baseUrl . $subcateEnName . '.html';
		}
		return $moreUrl;
	}

	/**
	* 获得经销商产品页URL
	*/
	public static function getMerchantProUrl($paramArr){
        $options = array(
            'merId'             => 0,     #图片ID
            'proId'             => 0,     #产品ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		$dearleBuyUrl = "http://dealer.zol.com.cn/detail/" . ceil($merId/100) . "/{$merId}_{$proId}.html";
		return $dearleBuyUrl;
	}
    
    /**
	* 新获得经销商产品页URL
	*/
	public static function getNewMerchantProUrl($paramArr){
        $options = array(
            'goodsId'             => 0,     #物品ID
            'subcateEnName'        => '',    #子类英文名
            'manuEnName'           => '',    #品牌英文名
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        
        if (empty($goodsId) || empty($subcateEnName) || empty($manuEnName)) {
            return '';
        }

        $newMerUrl = "http://www.zol.com/detail/" . $subcateEnName . "/" . $manuEnName . "/" . $goodsId . ".html";
		return $newMerUrl;
	}

	/**
	* 获得大类列表页的URL
	* wolf 加入
	* @param integer $cateId 大类ID
	* @return 链接字符串
	*/
	public static function getProCateUrl($paramArr){
        $options = array(
            'cateId'         => 0,     #大类ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if(!$cateId)return '';

		return "/price_cate_{$cateId}.html";
	}

	/**
	* 获取产品列表页链接
	* 例:Libs_Global_Url::getProListUrl(array('subcateId'=>$subcateId,'subcateEnName'=>$subcateEnName,'appendParam'=>array('paramVal' => array($v['paramId']))))
	*/
	public static function getProListUrl($paramArr){
        $options = array(
            'subcateId'         => 0,     #产品子类ID
            'manuId'            => 0,     #品牌ID
            'subcateEnName'     => '',     #子类英文名
            'appendParam'       => array(),  #扩展参数
            'rewrite'           => true,     #是否伪静态
            'isProduct'         => 0,     #是否伪静态
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		$url = '';
		$appendParam = (array)$appendParam;
		if(!$rewrite){
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';
			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} elseif ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		}

		if(isset($appendParam['isHistory']) && $appendParam['isHistory']){
		   $url =  self::getHistoryListUrl($appendParam);
		   return $url;
		}
		if($subcateEnName){
			if($manuId || !empty($appendParam['manuId'])){
				$urlManu = "_".((int)$manuId?$manuId:$appendParam['manuId']);
			}else{
				$urlManu = "_0";
			}
			$urlPrice = '';
			$urlParam = '';
			$urlStyle = '';
			$urlQueryType='';
			$urlLocation = '';
			$urlPage = '';
			$urlZolLevel = '';
			$urlEditorLevel = '';
			$paramArr = array();

			if(is_array($appendParam)){
				$appendParam['priceId'] = (!isset($appendParam['priceId']) || $appendParam['priceId'] === '' || $appendParam['priceId'] === 'noPrice')
										? 'noPrice' : $appendParam['priceId'];

				if ($appendParam['priceId'] === 'noPrice') {
					$urlPrice = "_1";
				} else {
                    $priceId = $appendParam['priceId'] === '{PRICEID}' ? '{PRICEID}' : $appendParam['priceId'];
					$urlPrice = "_" . $priceId;
				}
				$appendParam["paramVal"] = empty($appendParam["paramVal"]) ? array() : $appendParam["paramVal"];
				if(count($appendParam["paramVal"]) >= 1){
					foreach($appendParam["paramVal"] as $paramKey=>$paramValue){
						$paramArr[] = $paramValue;
					}
					if(count($paramArr)==1){
						$urlParam = "_".$paramArr[0];
					}else{
						$urlParam = "_".implode("-",$paramArr);
					}
				}else{
					$urlParam = "_0";
				}
			}

			$urlQueryType	= '_'.(!empty($appendParam['queryType']) ? $appendParam['queryType'] : 1);
			$urlStyle		= '_'.(!empty($appendParam['style']) ? $appendParam['style'] : 1);
			$urlLocation 	= "_".(!empty($appendParam['locationId']) ? $appendParam['locationId'] : 1);
			if(isset($appendParam['isTrueLocationId']) && $appendParam['isTrueLocationId']=='nolocationId'){
				$urlLocation = "_0";
			}
			$urlPage = "_".(!empty($appendParam['page'])? $appendParam['page'] : 1);
			if($isProduct){#产品大全的url
				$urlZolLevel = "_".(!empty($appendParam['zolUserLevel'])? (int)$appendParam['zolUserLevel'] :0);
				$urlEditorLevel = "_".(!empty($appendParam['zolEditorLevel'])? (int)$appendParam['zolEditorLevel'] :0);
			}
			$pageType ='index';
			$url = '/'.$subcateEnName.'_'.$pageType.'/subcate'.$subcateId.$urlManu."_list".$urlPrice.$urlParam.$urlQueryType.$urlStyle.$urlLocation.$urlPage.".html";
			if (!empty($appendParam['longUrl'])) {
				return $url;
			}

			if($isProduct){#产品大全的url
				$url = '/pro_sub_manu/'.$subcateEnName.$urlManu.$urlPrice.$urlParam.$urlZolLevel.$urlEditorLevel.$urlPage.".html";

				if($urlPrice=='_1' && $urlParam=="_0" && $urlZolLevel=='_0' && $urlEditorLevel=='_0'){
						if($urlPage=='_1'){
							$url = '/pro_sub_manu/'.$subcateEnName.$urlManu.".html";
						}
				}
				return $url;
			}

			if(isset($appendParam["paramVal"]) && count($appendParam["paramVal"])<=1 && ($urlLocation=="_1" || $urlLocation=='_0') && $urlQueryType=='_1' && $urlStyle=='_1'){
				if($urlManu=='_0'){
					$urlManu = '';
				}

				if($urlPrice!='_1' && count($appendParam["paramVal"])==0){
					$url = '/'.$subcateEnName.'_'.$pageType.'/subcate'.$subcateId.$urlManu."_list".$urlPrice.$urlPage.".html";
				}else if($urlPrice=='_1'){
					if(count($appendParam["paramVal"])==0){
						$url = '/'.$subcateEnName.'_'.$pageType.'/subcate'.$subcateId.$urlManu.'_list'.$urlPage.'.html';
					}else{
						if($urlParam==''){
							$url = '/'.$subcateEnName.'_'.$pageType.'/subcate'.$subcateId.$urlManu."_list".$urlPage.".html";
						}else{
							$url = '/'.$subcateEnName.'_'.$pageType.'/subcate'.$subcateId.$urlManu."_list".$urlParam.$urlPage.".html";
						}
					}
				}
			}
		}

		return $url;
	}


	/**
	* 获取文章链接
	*/
	public static function getDocUrl($paramArr){
        $options = array(
            'docId'             => 0,     #文章ID
            'classUrl'          => '',    #频道url
            'date'              => '',    #文章时间,ID在205000下有用
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);


		if ($docId >= 205000) {
			$docUrl = $classUrl . '/' . floor($docId/10000) . '/' . $docId . '.html';
		} else {#旧链接
			list($year, $month, $day) = explode('-', substr($date, 0, 10));
			$docUrl = sprintf($classUrl . '/%04d/%02d%02d/' . $docId . '.shtml', $year, $month, $day);
		}

		return $docUrl;
	}

	/**
	* 获取文章频道首页链接
	* @param integer $classId 文章类别ID
	* @param boolean $full 是否全路径
	*/
	public static function getDocClassUrl($paramArr)
	{
        $options = array(
            'classId'   => 0,  #文章类别ID
            'full'    => 0,    #是否全路径
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (!empty(self::$_cache['docClass'][$classId]['classUrl'])) {
			return self::$_cache['docClass'][$classId]['classUrl'];
		}

		$classArr = self::getDocClassArr(0);

		if (empty($classArr[$classId]['hostName'])) {
			return false;
		}

		$hostName = $classArr[$classId]['hostName'];
		$url = $classArr[$classId]['url'];
		$classUrl = $full ?  ('http://' . $hostName . $url) :  $url;

		if (113 == $classId) {
			$classUrl = 'http://dealer.zol.com.cn/dealer_article';
		}

		if ($classUrl && $classUrl != 'http://') {
			self::$_cache['docClass'][$classId]['classUrl'] = $classUrl;
			return $classUrl;
		} else {
			return false;
		}
	}

    /**
	* 获取文章更多路径
	* @param integer $subclassId 子类ID
	* @param intger|string $classId 大类ID 不填返回相对路径
	*/
	public static function getDocMorePath($paramArr)
	{
        $options = array(
            'subclassId' => 0,  #子类ID
            'classId'    => 0,  #文章类别ID
            'full'       => 0,  #是否全路径
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (is_numeric($classId)) {
			$classUrl = self::getDocClassUrl(array('classId'=>$classId));
		} elseif (is_string($classId)) {
			$classUrl = $classId;
		} else {
			$classUrl = '';
		}

		return $classUrl . '/more/2_' . $subclassId . '.shtml';
	}


	/**
	 * 获取系列最终页链接
	 */
	public static function getSeriesUrl($paramArr){
        $options = array(
            'subcateId'         => 0,
            'seriesId'          => 0,
            'manuId'            => 0,
            'type'              => 'default',
            'param'             => array(),
            'rewrite'           => true,
            'showType'          => 0,
            'orderType'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		

		if (empty($subcateId) || empty($seriesId)) {
			return false;
		}
		$type = strtolower($type);

		$url = '';
		switch ($type) {
			case 'default':
			case 'detail':
				if ($rewrite == true) {
					$url = '/series/' .$subcateId. '/' .$seriesId. '_1.html';
				} else {
					$url = '/index.php?c=Series&a=Detail&seriesId=' .$seriesId;
				}
				break;
			case 'param':
				$paramStr = $showStr = '';
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = '_'.$param;
					}
					if (!empty($showType)) {
						$showStr = '_' . $orderType.'_'.$showType;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_'.$seriesId.$paramStr.$showStr.'.html';
				} else {
					if (!empty($param)) {
						$paramStr = '&paramVal=' . implode('-', $param);
					}
					if (!empty($showType)) {
						$paramShowType = $showType;
						$showStr = '&paramShowType=' . $paramShowType;
					}
					$url = '/index.php?c=Series&a=Param&seriesId=' .$seriesId. $paramStr. $showStr;
				}
			   break;
			   case 'price':
                    $paramStr = is_string($param) ? $param :  '';
                    if ($rewrite == true) {
                        $url = '/series/' . $subcateId . '/' . $manuId . '/price_' . $seriesId . $paramStr . '.html';
                    } else {
                        $url = '/index.php?c=Series&a=Param&subcateId=' . $subcateId . '&manuId=' . $manuId . '&seriesId=' . $seriesId . '&locationId=' . (int)$param;
                    }
			   break;
			   case 'param_comp':
				$paramStr = '';
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = !is_string($param) ? '_' . implode('-', $param) : $param;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_comp_'.$seriesId.$paramStr.'.html';
				} else {
					if (!empty($param)) {
						$paramStr = '&paramVal=' . implode('-', $param);
					}
					$url = '/index.php?c=Series&a=Param&compType=comp&seriesId=' .$seriesId. $paramStr;
				}
				break;
			   case 'param_comp_other':
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = !is_string($param) ? '_' . implode('-', $param) : $param;
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_comp_other_'.$seriesId.$paramStr.'.html';
				}
				break;
				case 'param_all':
				if ($rewrite == true) {
					if (!empty($param)) {
						$paramStr = '_' . implode('-', $param);
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/param_all_'.$seriesId.$paramStr.'.html';
				}
				break;
			case 'review':
				if ($rewrite == true) {
					if ($param && is_array($param)) {
						$param = '_' . join('_', $param);
					}
					if (!$param) {
						$param = '';
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/review_'.$seriesId. $param . '.html';
				} else {
					$url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId;
				}
				break;
			case 'picture':
				$paramStr = '';
				if ($rewrite == true) {
					if(!$param)$param='';
					$url = '/series/'.$subcateId.'/'.$manuId.'/pic_'.$seriesId.$param.'.html';
				} else {
					if (is_array($param) && !empty($param)) {
						foreach ($param as $key => $val) {
							$paramStr .= '&' . $key .'='. $val;
						}
					}
					$url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
				}
				break;
            case 'video':
                $paramStr = '';
                if ($rewrite == true) {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $val) {
                            $paramStr .= '_' . $val;
                        }
                    }
                    $url = '/series/'.$subcateId.'/'.$manuId.'/video_'.$seriesId.$paramStr.'.html';
                } else {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $key => $val) {
                            $paramStr .= '&' . $key .'='. $val;
                        }
                    }
                    $url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
                }
                break;
             case 'article':
                $paramStr = '';
                if ($rewrite == true) {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $val) {
                            $paramStr .= '_' . $val;
                        }
                    }
                    $url = '/series/'.$subcateId.'/'.$manuId.'/article_'.$seriesId.$paramStr.'.html';
                } else {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $key => $val) {
                            $paramStr .= '&' . $key .'='. $val;
                        }
                    }
                    $url = '/index.php?c=Series&a=' .$type. '&seriesId=' .$seriesId .$paramStr;
                }
                break;
             case 'fitting':
                $paramStr = '';
                if ($rewrite == true) {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $val) {
                            $paramStr .= '_' . $val;
                        }
                    }
                    $url = '/series/'.$subcateId.'/'.$manuId.'/fitting_'.$seriesId.$paramStr.'.html';
                } else {
                    if (is_array($param) && !empty($param)) {
                        foreach ($param as $key => $val) {
                            $paramStr .= '&' . $key .'='. $val;
                        }
                    }
                    $url = '/index.php?c=Series&a=' .$type. '&fitting_=' .$seriesId .$paramStr;
                }
                break;
             case 'sechand':
				if ($rewrite == true) {
					if ($param && is_array($param)) {
						$param = '_' . join('_', $param);
					}
					if (!$param) {
						$param = '';
					}
					$url = '/series/'.$subcateId.'/'.$manuId.'/ershou_'.$seriesId. $param . '.html';
				} else {
					$url = '/index.php?c=Series_' .$type. '&seriesId=' .$seriesId;
				}
				break;
		}
		return $url;
	}


	/**
	* 获取软件URL
	*/
	public static function getDriverUrl($paramArr){
		$driverId = (int)$paramArr['driverId'];
		$url = 'http://driver.zol.com.cn/detail/' . ceil($driverId / 10000) . '/' . $driverId . '.shtml';
		return $url;
	}




	/**
	* 获取论坛链接
	*/
	public static function getBbsUrl($paramArr){
        $options = array(
			'baseUrl'   => '',
			'subcateId' => 0,
			'manuId'    => 0,
			'proId'     => 0,
			'seriesId'  => 0,
			'isSpec'    => false,
			'boardId'   => 0,
			'isNormal'  => false,#是否有单独域名 在BbsInfo中
			'bbsProId'  => 0,    #这个数据是proInfo或者seriesInfo缓存中
			'bookType'  => 0,
			'rewrite'   => true, #是否伪静态,基本可以废弃
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        
		if ($baseUrl) {
			$baseUrl .= substr($baseUrl, -1) !== '/' ? '/' : '';
		} else {
			$baseUrl = 'http://group.zol.com.cn/';
		}

		$url = $baseUrl;
//                $bbsUrlInfo = ZOL_Api::run("Bbsv2.Book.getBbsInfo" , array(
//                    'subcatid'   => $subcateId,
//                    'manuid'     => $manuId,
//                    'productid'  => $proId,
//                    'clearCache' => 0
//                ));
//                if($bbsUrlInfo) {
//                    if(isset($bbsUrlInfo['url'])) {
//                        $url = $bbsUrlInfo['url'];
//                        return $url;
//                    }
//                }
		#子类品牌
		if ($subcateId && $manuId) {
			if (($subcateId == 223) || (!$boardId && !in_array($subcateId, array(15, 16, 57) ))  || $isSpec == true) {
				$url = $baseUrl . 'manu_index_' . $subcateId . '_' . $manuId . '.html';
			} else {
				$url = $isNormal ? ($baseUrl . 'subcate_list_' . $boardId . '.html') : $baseUrl;
			}
			#系列页
			if ($seriesId) {
				$url = $baseUrl . 'xilie_list_' . $subcateId . '_' . $manuId. '_' . $seriesId . '.html';
			}
		}else{
			if($subcateId){
                if($boardId){
                     $url = $isNormal ? ($baseUrl . 'subcate_list_' . $boardId . '.html') : $baseUrl;
                }
			}
		}
		#产品页
		if ($proId) {
			if (!$rewrite) {
				$url = $baseUrl . 'comment.php?productid=' . $proId . '&type=' . $bookType;
			} else {
				$url = $baseUrl . 'comment_' . ($bookType ? "type_{$bookType}_" : '') . $proId . '.html';
			}
		}

		if (($proId && $manuId && $subcateId) || $seriesId ){
			if ($seriesId) {
				$url = $baseUrl.'xilie_list_'.$subcateId.'_'.$manuId.'_'.$seriesId.'.html';
			} else {
				$url =$baseUrl.'comment_' . ($bookType ? "type_{$bookType}_" : '') . $proId . '.html';
			}
			if (!$rewrite) {
				$url = $baseUrl . 'comment.php?productid=' . $proId;

			}

            if ($seriesId && $manuId && $subcateId && ($bookType || !$rewrite)) {
                $url = $baseUrl . "xilie_list.php?subcatid={$subcateId}&manuid={$manuId}&xilieid={$seriesId}&type={$bookType}";
            }
		}
		if($bbsProId){
			$url =$baseUrl.'comment_' . ($bookType ? "type_{$bookType}_" : '') . $bbsProId . '.html';
		}
		return $url;
	}
	/**
	* 获取论坛帖子地址
	*/
	public static function getBookUrl($paramArr){
        $options = array(
			'baseUrl'    => '',
			'bookId'     => 0,
			'boardId'    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if($baseUrl == '')return '';

		$baseUrl .= substr($baseUrl, -1) == '/' ? '' : '/';
		$dir      = ceil($bookId/10000);
		return $baseUrl . $dir . '/' . $boardId . '_' . $bookId . '.html';
	}
	/**
	 * 获得参数的术语链接
	 */
	public static function getParamIntroLink($paramArr){
        $options = array(
            'linkId'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		if (!$linkId) return false;
		return '/product_param/index' . $linkId . '.html';
	}

	/**
	* 获取装备产品列表链接
	*/
	public static function getEquipProUrl($paramArr)
	{
        $options = array(
            'proId'         => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if (!$proId) return false;
		return 'http://zb.zol.com.cn/product/' . $proId . '/1/';
	}

	/**
	* 作者文章列表
	*/
	public static function getEditorUrl($paramArr)
	{
        $options = array(
            'userName'         => '',
            'classId'          => '',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		return 'http://service.zol.com.cn/doclist_'.$classId.'_3_1_'.  urlencode($userName).'.html';
		
	}

	/**
	 * 获得手机软件的地址
	 */
	public static function getMobileSoftUrl($paramArr)
	{
		$msId = (int)$paramArr['msId'];
		return 'http://sj.zol.com.cn/detail/'.ceil($msId/1000).'/'.$msId.'.shtml';
	}

	/**
	* 获得经销商URL
	*/
	public static function getMerchantUrl($paramArr)
	{
		$merId = (int)$paramArr['merId'];
        $merUrl = ZOL_Api::run("Shop.Merchant.getShopUrl" , array(
            'merchantId'     => $merId,           #经销商ID
        ));
        $page = isset($paramArr['page'])?$paramArr['page']:"";
        if (!empty($paramArr['type']) && ('shop' == $paramArr['type'])) {
            return $merUrl;
        } else {
            return $merUrl.$page;
        }
		
	}
	/**
	* 获取促销信息链接
	*/
	public static function getPromotionInfoUrl($paramArr)
	{
        $options = array(
            'promId'         => 0,
            'merId'          => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		
		//$url = 'http://dealer.zol.com.cn/d_' . $merId . '/market_' . $promId . '.html';
        //$url = 'http://www.zol.com/shop_' . $merId . '/market_'.$promId.'.html';
        $url = 'http://s.zol.com.cn/shop_' . $merId . '/market_'.$promId.'.html';
		return $url;
	}

    /**
	* 获取趋势链接
	*/
	public static function getTrendUrl($paramArr)
	{
        $proId = (int)$paramArr['proId'];
		return '/'.ceil($proId/1000).'/'.$proId.'/pro_hit.shtml';
	}

	/**
	* 获得问答堂专家的url
	*/
	public static function getAskExpertUrl($paramArr)
	{
        $editorId = $paramArr['uid'];
		return  'http://ask.zol.com.cn/editor/' . $editorId . '/';
	}
    
    /**
	* 获得问答堂关于某产品的url
	*/
	public static function getProAskListUrl($paramArr = array())
	{
        $options = array(
            'proId' => 0
        );
        if($paramArr){$options = array_merge($options,$paramArr);}
        extract($options);
        if(empty($proId)){return ;}
		return  'http://ask.zol.com.cn/product_' . $proId . '.html';
	}
    
     /**
	* 获得问答堂关于某个问答的url
	*/
	public static function getAskDetailUrl($paramArr = array())
	{
        $options = array(
            'askId' => 0
        );
        if($paramArr){$options = array_merge($options,$paramArr);}
        extract($options);
        if(empty($askId)){return ;}
		return  'http://ask.zol.com.cn/q/' . $askId . '.html';
	}

    /**
	 * 通过图片ID获取图片Src
	 */
	public static function getOnePicSrc($picId, $size = '_80x60', $dir = 'product', $extName = '')
	{
        $PIC_HOST = 'http://2.zol-img.com.cn';
		if ((int)$picId < 1) {
			return false;
		}

		if('' != trim($size)) {
			$size = $size[0] != '_' ? ('_' . $size) : $size;
		}

		$picUrl = $PIC_HOST . '/product/no.jpg';

		if ($size == '_100x75') {
			$picUrl = $PIC_HOST . '/product/no100x75.jpg';
		}


		$subDir   = floor($picId / 100000);
		$grandDir = floor($picId % 1000);

		$table = $dir . '_' . floor($picId / 10000);
		if (empty($extName)) {
			self::init();
			$sql = "SELECT ext_name FROM {$table} WHERE sid='{$picId}'";
			$extName = self::$_dbPicture->getOne($sql);
		}

		if ($extName && $extName!='txt') {
			$cryptName = crypt($picId, 'ceshi');
			$cryptName = str_replace(array('.', '/'), array('', ''), $cryptName);

			$cryptName = $cryptName . '.' . $extName;
			$picPath = $dir . '/' . $subDir . $size . '/' .  $grandDir . '/' . $cryptName;
			$picUrl = 'http://2' . chr($picId%6+97) . '.zol-img.com.cn/' . $picPath;
		}

		unset($picId, $dir, $size, $subDir, $grandDir, $extName, $cryptName);
		return $picUrl;
	}

    /**
     * 获得对比页面的链接地址
     * @param string $type 页面类别，pk：整体对比（PK),param：参数对比，pic：外观对比，review：评价对比
     * @param array $proIdArr
     */
    public static function getProductCompUrl($type,$proIdArr)
    {
        if(empty($proIdArr) || !is_array($proIdArr)){
            return '';
        }
        $url = '';
        //对ID，进行排序，保证小的ID在前面
        sort($proIdArr);
        if('pk' == $type){
            if(count($proIdArr) >= 2){
                $url = '/pk/' . $proIdArr[0] . '_' . $proIdArr[1] . '.shtml';
            }
        }else{
            $url = '/ProductComp_' . $type . '_' . implode('-',$proIdArr) . '.html';
        }
        return $url;
    }
    
	/**
	 * 获取经销商促销链接
	 * @param array $param 参数
	 * <pre>
	 * 	@param int $param['merId'] 经销商ID
	 * 	@param int $param['kindId'] 信息类型
	 * 	@param int $param['promoId'] 信息ID
	 * </pre>
	 * @return string 经销商促销链接
	 */
	public static function getMerPromo(array $param = array())
	{
		$merId     = isset($param['merId']) ? $param['merId'] : 0;
		$kindId    = isset($param['kindId']) ? $param['kindId'] : 0;
		$promoId   = isset($param['promoId']) ? $param['promoId'] : 0;

        $url = ZOL_Api::run("Shop.Merchant.getShopUrl" , array(
            'merchantId' => $merId,           #经销商ID
        ));
		//$url = self::getMer($merId);
		$url .= $kindId ? 'market_bulletin.php?infoKind=' . $kindId : '';
		$url .= $promoId ? 'market_' . $promoId . '.html' : '';
		return $url;
	}
    
    /**
	* 获取列表页链接
	* @param array 数组参数
	*/
	public static function getListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'style'         => 0,    #显示样式
            'locationId'    => 0,    #地区
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isHot'         => 0,    #主板推荐链接特殊处理
            'isLong'        => 0,    #是否启用长链接
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
            'oldUrl'        => 0,    #旧链接
            'isDN'          => 0,    #是否带域名
        );
        if (empty($paramArr['subcateEnName']) && $paramArr['subcateId']) {
            $Db_Product = Db_Product::instance();
            $sql = "select brief from subcategory_extra_info where subcategory_id={$paramArr['subcateId']}";
            $paramArr['subcateEnName'] = $Db_Product->getOne($sql);
        }
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        
        $tabSubArr = array(57,16,15);
        if (!in_array($subcateId, $tabSubArr) && $subcateEnName && !$oldUrl) return self::getListShortUrl($options);
        $subEnName = $isHistory ? 'history' : $subcateEnName.'_index';

		if (!$rewrite) {
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            //ZOL_Debugger::dump($priceId);
            $urlcate        = $subcateId ? $subcateId : '';                 #子类
            $urlManu        = $manuId ? "_" . $manuId : '';                 #品牌
            $urlPrice       = 'noPrice'!==$priceId ? "_" . $priceId : '';   #价格
            $urlParam       = $paramVal ? "_" . $paramVal : '';             #复合参数
            $urlQuery       = $queryType ? "_" . $queryType : '_1';         #排序
//            if($queryType == 99){    
//                $urlQuery   = '_0';
//            }
            $urlStyle       = $style ? "_" . $style : '_1';                 #列表显示形式
            $urlLocation    = $locationId ? "_" . $locationId : '_0';       #地区
            $urlHot         = $isHot ? "_hot"  : '';                        #主板推荐链接特殊处理
            $urlPage        = $page ? "_" . $page : '_1';                   #页码

            #关键字分页替换用，不需转换
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlParam .= "-k" . $keyword;   #关键字
            } else if (!$paramVal && $keyword) {
                $urlParam .= "_k" . $keyword;   #关键字
            }

            if (('noPrice'!==$priceId && $paramVal)) {
                $isLong = 1;
            }
            
            //ZOL_Debugger::dump($urlParam.$urlQuery.$urlStyle.$urlLocation);
            if ($queryType > 1 || $style == 1 || $locationId || $isLong || $keyword) {
                $urlManu  = $urlManu ? $urlManu : '_0';
                $urlPrice = $urlPrice ? $urlPrice : '_1';
                $urlParam = $urlParam ? $urlParam : '_0';
                $url = '/'.$subEnName.'/subcate'.$urlcate.$urlManu."_list".$urlPrice.$urlParam.$urlQuery.$urlStyle.$urlLocation.$urlPage.".html";
            } else {
                $url = '/'.$subEnName.'/subcate'.$urlcate.$urlManu."_list".$urlPrice.$urlParam.$urlHot.$urlPage.".html";
            }

        }
        $url = $isDN ? 'http://detail.zol.com.cn'.$url : $url;
		return $url;
	}
    
    /**
	* 获取列表页链接
	* @param array 数组参数
	*/
	public static function getListShortUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'enManu'        => '',
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'enQuery'       => '',   #排序
            'enStyle'       => '',   #显示样式
            'locationId'    => 0,    #地区ID
            'enLocation'    => '',   #地区
            'keyword'       => '',   #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
            'isDN'          => 0,    #是否带域名
            'isHot'         => '',   #主板推荐链接特殊处理
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        $subcateEnName = strtolower($subcateEnName);
        $enManu = str_replace(chr(32), '',$enManu);
        if (!$enManu && $manuId) {
            #查询顺序 $enManuArr > helper > 数据库
            static $enManuArr = array();
            if (!$enManuArr) {
                $enManuArr = Helper_List::getManuArr(array('subcateId'=>$subcateId));
            }
            if (!isset($enManuArr[$manuId])) {
                $Db_Product = Db_Product::instance();
                $sql = "select en_name from manufacturer where id={$manuId}";
                $enManuArr[$manuId]['enManu'] = $Db_Product->getOne($sql);
            }
            $enManu = str_replace(chr(32), '',$enManuArr[$manuId]['enManu']);
        }
        if (!$enLocation && $locationId) {
            #查询顺序 $enLocationArr > helper > 数据库
            static $enLocationArr = array();
            if (!isset($enLocationArr[$locationId])) {
                $arr = Helper_Area::getLocationInfo(array('locationId'=>$locationId));
                if (isset($arr['enName']) && $arr['enName']) {
                    $enLocationArr[$locationId] = $arr['enName'];
                } else {
                    $Db_Product = Db_Product::instance();
                    $sql = "select en_name from merchant_recommend_channel where base_url={$locationId}";
                    $enLocationArr[$locationId] = $Db_Product->getOne($sql);
                }
            }
            $enLocation = $enLocationArr[$locationId];
        }
        
        $subEnName = $isHistory ? $subcateEnName.'/history/' : $subcateEnName.'/';
        $enQuery = $isHot ? 'hot' : $enQuery; #主板推荐链接特殊处理
		if (!$rewrite) { #未改
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            $urlManu        = $enManu ? strtolower($enManu).'/' : '';                                   #品牌
            $urlPrice       = 'noPrice' !==$priceId ? ($paramVal ? $priceId.'_' : $priceId.'/') : '';   #价格
            $urlParam       = $paramVal ? str_replace('-', '_', $paramVal).'/' : '';                    #复合参数
            $urlQuery       = $enQuery ? $enQuery : '';                                                 #排序
            $urlStyle       = $enStyle ? ($enQuery ? '_'.$enStyle : $enStyle) : '';                     #列表显示形式
            $urlLocation    = $enLocation ? $enLocation.'/' : '';                                       #地区
            $urlPage        = $page != 1 ? ($enQuery || $enStyle ? '_'.$page : $page) : '';             #页码
            $urlkword       = '{keyword}' != $keyword ? str_replace('%', '@', ZOL_String::escape($keyword)) : $keyword; #关键字
            
            $url = '/'.$subEnName.$urlManu.$urlPrice.$urlParam.$urlLocation.$urlQuery.$urlStyle.$urlPage;
            if ($urlQuery || $urlStyle || $urlPage) $url .= '.html';
            if ($urlkword) $url .= "?k=$urlkword";

        }
        $url = $isDN ? 'http://detail.zol.com.cn'.$url : $url;
		return $url;
	}

    /**
	* 获取历史列表页链接 
	* @param array 数组参数
	*/
	public static function getHistoryListUrl($paramArr)
	{
        $options = array(
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 1,    #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
		if (!$rewrite) {
			$url = '/index.php?c=List&subcateId=' . $subcateId;
			$url .= $manuId ? '&manuId=' . $manuId : '';

			if (is_array($appendParam)) {
				$url .= '&' . http_build_query($appendParam);
			} else if ($appendParam) {
				$url .= '&' . $appendParam;
			}
			return $url;
		} else {
            $urlPrice = "noPrice" == $priceId ? 1 : $priceId;
            $manuId = (int)$manuId;
            $urlParam = $paramVal ? "_" . $paramVal : '_0'; #复合参数
            #关键字分页替换用，不需转换
            if ($keyword && '{keyword}' != $keyword) {
                $keyword = ZOL_String::escape($keyword);
                $keyword = str_replace('%', '@', $keyword);
            }
            if ($paramVal && $keyword) {
                $urlKeyword = "-k" . $keyword;   #关键字
            } else if (!$paramVal && $keyword) {
                $urlKeyword = "_k" . $keyword;   #关键字
            } else {
                $urlKeyword = '';
            }

            $url = '/history/subcate'.$subcateId.'_'.$manuId.'_'.$urlPrice.$urlParam.$urlKeyword.'_'.$queryType.'_'.$page.".html";
        }
		return $url;
	}
    
    /**
	* 获得排行的更多链接
	*/
	public static function getEvaPicUrl($paramArr){
        $options = array(
            'proId'      => 0,
            'picId'      => '',
            'picType'    => 0,
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        $url = "/".$picType."/eva_".$proId."_".$picId.".html";
        return $url;
    }

    /**
	* 获取排行榜更多页链接
	*/
	public static function getTopMoreUrl($subcateId,$subcateEnName,$manuId)
	{
		$baseUrl = 'http://top.zol.com.cn/compositor/';
		if (!$manuId && $subcateEnName) {
			$url = $baseUrl . $subcateId . '/' . $subcateEnName . '.html';
		}

		if ($manuId) {
			$url = $baseUrl . $subcateId . '/manu_' . $manuId . '.html';
		}
		return $url;
	}

    /**
	 * 得到系列榜更多页链接
	 */
	public static function getSeriesRankUrl($subcateId,$manuId){
		if($subcateId && $manuId){
			$url = "http://top.zol.com.cn/compositor/".$subcateId."/series_".$manuId.".html";
		}else if($subcateId){
			$url = "http://top.zol.com.cn/compositor/".$subcateId."/series_attention.html";
		}
		return $url;
	}

    /**
	* 获取产品库调查链接
	*/
	public static function getIndaUrl($subcateId, $result = FALSE)
	{
        if ($subcateId && $result) {
            $url = '/indagate_result_' . $subcateId  . '.html';
        } elseif ($subcateId) {
            $url = '/indagate_' . $subcateId  . '.html';
        }
		return $url;
	}

	/**
	 * 得到wap的连接
	 * @param type $paramArr
	 * @return string
	 */
	public static function getWapUrl($paramArr){
		$options = array(
			'cateId'   => 0,
			'subcateId'=> 0,
			'manuId'   => 0,
			'proId'    => 0,
			'propriId' => 0,    #报价ID 需要报价ID的页面使用
			'paramType'  => '', 	#参数类型 manu pricerange param
			'paramValue'  => '',	#参数值 品牌ID 价格，参数区间ID
			'isSeries' => 0,	#排行榜页用到
			'seriesId'  => 0,
			'reviewId' => 0,    #点评ID
			'docId'    => 0,    #文章ID
			'pageType' =>'',
			'param'     =>'',
			'wapExt' => 'html',
			'full'   => 1,  #绝对地址
			'newRule' => 0,  #新规则
			'longReviewId' => '',    # 好说ID，好说最终页用到。
		);
		if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
		$wapDir = '';
		$oriWapExt = $wapExt;
		if($wapExt=='html5') {
			$wapExt = 'html';
			$wapDir = "";
		}
		$wapUrl = 1==$full ? "http://wap.zol.com.cn" : "";
		$wapUrl .= $wapDir."/";
		$url = '';

		switch ($pageType) {
			case 'list':
				$manuStr = '';
				if($manuId) {$manuStr = "_".$manuId;}
				$url = $wapUrl."list/".$subcateId.$manuStr.".".$wapExt;
				break;
			case 'index':
				$url = $wapUrl;
				break;
			case 'detail':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/index.".$wapExt;
				break;
			case 'seriesdetail':
				$url = $wapUrl.ceil($proId/1000).'/'.$proId.'/extraprice_'.$propriId.'.'.$wapExt;
				break;
			case 'select':
			case 'price':
				$paramStr = '';
				if($param) {
					$paramStr = "_".implode("_", $param);
				}
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/".$pageType.$paramStr.".".$wapExt;
				break;
			case 'merchant':
				$url = "/shop_".$merchantId."/";
				break;
			case 'param':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/param.".$wapExt;
				break;
			case 'option':
				$url = $wapUrl.'index.php?c=Compare_Option&proIdStr='.$proId;
				break;
			case 'pic':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/pic.".$wapExt;
				break;
			case 'sample':
				$tempStr = $cateId ? '_'.$cateId : '';
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/sample{$tempStr}.".$wapExt;
				break;
			case 'video':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/video.".$wapExt;
				break;
			case 'bbs' :
				#只有三个产品线有论坛 考虑私有云查询目录名太麻烦，还要查结果 然后匹配目录名 这里写个对应
				$subcateToBbs = array(
					'57' => 'sjbbs',    #手机论坛
					'16' => 'nbbbs',    #笔记本论坛
					'15' => 'dcbbs',    #摄影论坛
				);
				#这里怕没有以上对应的情况 加了个默认 现在只有这三个产品线有论坛
				$dirName = isset($subcateToBbs[$subcateId]) ? $subcateToBbs[$subcateId] : 'sjbbs';
				//$url = "http://m.zol.com.cn/{$dirName}/x{$proId}.".$wapExt;
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/bbs.".$wapExt;
				break;
			case 'article':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/article.".$wapExt;
				break;
			case 'review':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/review.".$wapExt;
				break;
			case 'reviewdetail':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/reply_{$reviewId}_1_1.".$wapExt;
				break;
			case 'series':
				if($proId) {
					$url = $wapUrl.ceil($proId/1000)."/".$proId."/index.".$wapExt;
				} else {
					$url = $wapUrl."slist/".$subcateId."_".$manuId."_".$seriesId."_1_0_.".$wapExt;
				}
				break;
			case 'articleEval':
				$url = $wapUrl.ceil($proId/1000).'/'.$proId.'/article_eva_'.$cateId.'_'.$subcateId.'.'.$wapExt;
				break;
			case 'docArticle' :
				$url = 'http://3g.zol.com.cn/touch/article/'.ceil($docId/1000).'/'.$docId.'.html';
				if('html5'==$oriWapExt) {
					$url = 'http://m.zol.com.cn/article/'.$docId.'.html';
				}
				break;
			case 'TopHome':
				$url = $wapUrl . 'top/';
				break;
			case 'TopSubcate': #全不分类子页
				$tempStr = $cateId ? $cateId : 'all';
				$url = $wapUrl . 'top/'.$tempStr.'.html';
				break;
			case 'Subcate':     #产品线总榜
				$url = $wapUrl . 'top/' . $subcateEnName . '/';
				break;
			case 'TopTop':
				$url = $wapUrl . 'top/' . $subcateEnName . '/';
				break;
			case 'TopProList':
				$url = $wapUrl . 'top/';
				if($isSeries){
					$url .= $subcateEnName . '/series.html';
				}else{
					if($paramValue){
						if($paramType == 'manu') {
							$url .= $subcateEnName . '/'.$paramValue.'/';
						}else if($paramType == 'pricerange'){
							$url .= $subcateEnName . '/price_' . $paramValue . '.html';
						}else if($paramType == 'param'){
							$url .= $subcateEnName . '/param_' . $paramValue . '.html';
							//品牌排行榜价格筛选
						} else if ($paramType == 'manuPrice') {
							$url .= $subcateEnName . '/brand/price_' . $paramValue . '.html';
							//品牌排行榜参数筛选
						} else if ($paramType == 'manuParam') {
							$url .= $subcateEnName . '/brand/param_' . $paramValue . '.html';
						}
					}else{
						if($paramType == 'manu') {
							$url .= $subcateEnName . '/brand/';
						}else{
							$url .= $subcateEnName . '/hot.html';
						}
					}
				}
				break;
			case 'TopDetail':
				$url = $wapUrl . 'top/';
				if($isSeries){
					$url .= $subcateEnName . '/series.html';
				}else{
					if($paramValue){
						if($paramType == 'manu') {
							$url .= $subcateEnName . '/'.$paramValue.'/';
						}else if($paramType == 'pricerange'){
							$url .= $subcateEnName . '/price_' . $paramValue . '.html';
						}else if($paramType == 'param'){
							$url .= $subcateEnName . '/param_' . $paramValue . '.html';
							//品牌排行榜价格筛选
						} else if ($paramType == 'manuPrice') {
							$url .= $subcateEnName . '/brand/price_' . $paramValue . '.html';
							//品牌排行榜参数筛选
						} else if ($paramType == 'manuParam') {
							$url .= $subcateEnName . '/brand/param_' . $paramValue . '.html';
						}
					}else{
						if($paramType == 'manu') {
							$url .= $subcateEnName . '/brand/';
						}else{
							$url .= $subcateEnName . '/hot.html';
						}
					}
				}
				break;
			case 'TopManuList': #品牌排行终榜
				$url = $wapUrl . 'top/';
				$url .= $subcateEnName . '/brand/';
				break;
			case 'TopShopList': #沸点最终榜
				$url = $wapUrl . 'top/';
				$url .= $subcateEnName . '/index.html';
				break;
			case 'UpQuick': #上升最快
				$url = $wapUrl . 'top/';
				$url .= $subcateEnName . '/hit_wave.html';
				break;
			case 'ask' :
				$url = '/'.ceil($proId/1000).'/'.$proId.'/ask.html';
				break;
			case 'askDetail' :
				$url = '/ask/'.$param.'.html';
				break;
			case 'fitting':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/fitting.".$wapExt;
				break;
			case 'sale':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/sale.".$wapExt;
				break;
			case 'articleMore':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/article_more.".$wapExt;
				break;
			case 'pkPrice':
				$url = $wapUrl.ceil($proId/1000)."/".$proId."/pk_price.".$wapExt;
				break;
			case 'HaoshuoView':
				$url = 'https://wap.zol.com.cn/'.ceil($proId/1000).'/'.$proId.'/evaluation_'. $longReviewId .'.html';
				break;
			default:
				break;
		}
		return $url;
	}

    /**
     * 得到慧聪wap的连接
     * @param type $paramArr
     * @return string 
     */
    public static function getHcWapUrl($paramArr){
        $options = array(
            'cateId'   => 0,
            'subcateId'=> 0,
            'manuId'   => 0,
            'proId'    => 0,
            'propriId' => 0,    #报价ID 需要报价ID的页面使用
            'paramType'  => '', 	#参数类型 manu pricerange param
        	'paramValue'  => '',	#参数值 品牌ID 价格，参数区间ID
        	'isSeries' => 0,	#排行榜页用到
            'seriesId'  => 0,
            'reviewId' => 0,    #点评ID
            'docId'    => 0,    #文章ID
            'pageType' =>'',
            'param'     =>'',
            'wapExt' => 'html',
            'full'   => 1,  #绝对地址
            'newRule' => 0  #新规则
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $wapDir = '';
        $oriWapExt = $wapExt;
        if($wapExt=='html5') {
            $wapExt = 'html';
            $wapDir = "";
        }
        $wapUrl = 1==$full ? "http://hc.wap.zol.com.cn" : "";
		$wapUrl .= $wapDir."/";
        $url = '';
        
        switch ($pageType) {
            case 'list':
                $manuStr = '';
                if($manuId) {$manuStr = "_".$manuId;}
                $url = $wapUrl."list/".$subcateId.$manuStr.".".$wapExt;
                break;
            case 'index':
                $url = $wapUrl;
                break;
            case 'detail':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/index.".$wapExt;
                break;
            case 'seriesdetail':
                $url = $wapUrl.ceil($proId/1000).'/'.$proId.'/extraprice_'.$propriId.'.'.$wapExt;
                break;
            case 'select':
            case 'price':
                $paramStr = '';
                if($param) {
                    $paramStr = "_".implode("_", $param);
                }
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/".$pageType.$paramStr.".".$wapExt;
                break;
            case 'merchant':
            	$url = "/shop_".$merchantId."/";	
            	break;
            case 'param':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/param.".$wapExt;
                break;
            case 'option':
                $url = $wapUrl.'index.php?c=Compare_Option&proIdStr='.$proId;
                break;
            case 'pic':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/pic.".$wapExt;
                break;
            case 'sample': 
                $tempStr = $cateId ? '_'.$cateId : '';
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/sample{$tempStr}.".$wapExt;
                break;
            case 'video':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/video.".$wapExt;
                break;
            case 'bbs' :
                #只有三个产品线有论坛 考虑私有云查询目录名太麻烦，还要查结果 然后匹配目录名 这里写个对应
                $subcateToBbs = array(
                    '57' => 'sjbbs',    #手机论坛
                    '16' => 'nbbbs',    #笔记本论坛
                    '15' => 'dcbbs',    #摄影论坛
                );
                #这里怕没有以上对应的情况 加了个默认 现在只有这三个产品线有论坛
                $dirName = isset($subcateToBbs[$subcateId]) ? $subcateToBbs[$subcateId] : 'sjbbs';
                //$url = "http://m.zol.com.cn/{$dirName}/x{$proId}.".$wapExt;
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/bbs.".$wapExt;
                break;
            case 'article':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/article.".$wapExt;
                break;
            case 'review':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/review.".$wapExt;
                break;  
            case 'reviewdetail':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/reply_{$reviewId}_1_1.".$wapExt;
                break;
            case 'series':
                if($proId) {
                    $url = $wapUrl.ceil($proId/1000)."/".$proId."/index.".$wapExt;
                } else {
                    $url = $wapUrl."slist/".$subcateId."_".$manuId."_".$seriesId."_1_0_.".$wapExt;
                }
                break;
            case 'articleEval':
                $url = $wapUrl.ceil($proId/1000).'/'.$proId.'/article_eva_'.$cateId.'_'.$subcateId.'.'.$wapExt;
                break;
            case 'docArticle' :
                $url = 'http://3g.zol.com.cn/touch/article/'.ceil($docId/1000).'/'.$docId.'.html';
                if('html5'==$oriWapExt) {
                    $url = 'http://m.zol.com.cn/article/'.$docId.'.html';
                }
                break; 
            case 'TopHome':
            	$url = $wapUrl . 'top/';
            	break;
            case 'TopSubcate':
            	$tempStr = $cateId ? $cateId : 'all';
            	$url = $wapUrl . 'top/'.$tempStr.'.html';
            	break;
            case 'TopTop':
            	$url = $wapUrl . 'top/' . $subcateEnName . '/';
            	break;
            case 'TopDetail':
            	$url = $wapUrl . 'top/';
            	if($isSeries){
            		$url .= $subcateEnName . '/series.html';
            	}else{
            		if($paramValue){
            			if($paramType == 'manu') {
            				$url .= $subcateEnName . '/'.$paramValue.'/';
            			}else if($paramType == 'pricerange'){
            				$url .= $subcateEnName . '/price_' . $paramValue . '.html';
            			}else if($paramType == 'param'){
            				$url .= $subcateEnName . '/param_' . $paramValue . '.html';
                        //品牌排行榜价格筛选
            			} else if ($paramType == 'manuPrice') {
                            $url .= $subcateEnName . '/brand/price_' . $paramValue . '.html';
                        //品牌排行榜参数筛选
                        } else if ($paramType == 'manuParam') {
                            $url .= $subcateEnName . '/brand/param_' . $paramValue . '.html';
                        }
                    }else{
            			if($paramType == 'manu') {
            				$url .= $subcateEnName . '/brand/';
            			}else{
            				$url .= $subcateEnName . '/hot.html';
            			}
            		}
            	}
            	break;
            case 'ask' :
                $url = '/'.ceil($proId/1000).'/'.$proId.'/ask.html';
                break;
            case 'askDetail' :
                $url = '/ask/'.$param.'.html';
                break;
            case 'fitting':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/fitting.".$wapExt;
                break;
            case 'sale':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/sale.".$wapExt;
                break;
            case 'articleMore':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/article_more.".$wapExt;
                break;
            case 'pkPrice':
                $url = $wapUrl.ceil($proId/1000)."/".$proId."/pk_price.".$wapExt;
                break;
            default:
                break;
        }
        return $url;
    }
    
    
    /**
	* 获取对比页链接
	*/
	public static function getCompareUrl($paramArr)
	{
        $options = array(
            'proIdStr'      => '',
            'pkProIdStr'  => '',
            'pageType' =>'',
            'param'     =>'',
            'wapExt' => 'html',
            'full'   => 1,  #绝对地址
            'newRule' => 0  #新规则
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        
        $wapDir = '';
        $wapExt = 'html';
        $wapDir = "";

        $wapUrl = 1==$full ? "http://wap.zol.com.cn" : "";
		$wapUrl .= $wapDir."/";
        $url = '';
        
        //获取默认版本
        $proIdExtral = $proIdStr;
        $proPkIdExtral = $pkProIdStr;
        if ($proIdStr && !$pkProIdStr) {
            $url = $wapUrl.'pk/'.$proIdStr.'.'.$wapExt;
        } else if($pkProIdStr && !$proIdStr){
            $url = $wapUrl.'pk/'.$pkProIdStr.'.'.$wapExt;
        }else{
            $dataArr = ZOL_Api::run("Pro.Product.getDefaultExtraId" , array(
                            'proId'          => $proIdStr,          #产品ID
                        ));
            if(isset($dataArr['extraId'])){
                $proIdExtral = $proIdStr."-".$dataArr['extraId'];
            }
            $dataArr = ZOL_Api::run("Pro.Product.getDefaultExtraId" , array(
                            'proId'          => $proPkIdExtral,          #产品ID
                        ));
            if(isset($dataArr['extraId'])){
                $proPkIdExtral = $pkProIdStr."-".$dataArr['extraId'];
            }
            
            $url = $wapUrl.'pk/'.$proIdExtral.'_'.$proPkIdExtral.'.'.$wapExt;
        }
		return $url;
	}
    
    /**
	* 获取配件页链接
	*/
	public static function getFittingUrl($paramArr)
	{
        $options = array(
            'proId'      => 0,
            'subcateId'  => 0
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

        if ($subcateId) {
            $url = '/'.ceil($proId/1000).'/'.$proId.'/fitting_'.$subcateId.'.shtml';
        } else {
            $url = '/'.ceil($proId/1000).'/'.$proId.'/fitting.shtml';
        }
		return $url;
	}

    /**
	* 获取系列配件页链接
	*/
	public static function getSeriesFittingUrl($paramArr)
	{
        $options = array(
            'subcateId' => 0,
            'manuId'    => 0,
            'seriesId'  => 0,
            'subType'   => 0
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

        if ($subType) {
            $url = '/series/'.$subcateId.'/'.$manuId.'/fitting_'.$seriesId.'_'.$subType.'.html';
        } else {
            $url = '/series/'.$subcateId.'/'.$manuId.'/fitting_'.$seriesId.'.html';
        }
		return $url;
	}
    
    /** 
	* 获取列表页链接
	* @param array 数组参数
     * @jiebl 2013-12-14
	*/
	public static function getWapListUrl($paramArr)
	{
        $options = array(
            'a'             => '',   #控制器
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'style'         => 0,    #显示样式
            'locationId'    => 0,    #地区
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isLong'        => 0,    #是否启用长链接
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
            'oldUrl'        => 0,    #旧链接
            'isJs'          => 0,
            'wapExt'        => 'html5',
            'full'          => 0,  #绝对地址，#是否带域名
            'newRule'       => 0  #新规则
        );
        if (empty($paramArr['subcateEnName']) && $paramArr['subcateId']) {
            $Db_Product = Db_Product::instance();
            $sql = "select brief from subcategory_extra_info where subcategory_id={$paramArr['subcateId']}";
            $paramArr['subcateEnName'] = $Db_Product->getOne($sql);
        }
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        $wapDir = '';
        $oriWapExt = $wapExt;
        if($wapExt=='html') {
           $wapExt .= '?j=simple';
        }
        if($wapExt=='html5') {
            $wapExt = 'html';
            $wapDir = "";
        }
        $wapUrl = 1==$full ? "http://wap.zol.com.cn" : "";
		$wapUrl .= $wapDir."/";
        $url = '';
        $subEnName = $isHistory ? 'history' : $subcateEnName.'_index';
        $urlC = '/index.php?c=List_List';
        $rewriteUrl=''; 
        
        $urlA = $a ? '&a='.$a:'';

        //$rewriteUrlSubcate = $subcateId ? '/'.$subEnName.'/subcate'.$subcateId :'';
        $rewriteUrlSubcate = $subcateId ? '/list/'.$subcateId :'';
        $urlSubcate = $subcateId ? '&subcateId='.$subcateId :'';
        
        $rewriteUrlManu = $manuId ? "_" . $manuId :'';
        $urlManu = $manuId ? '&manuId='.$manuId :'';

        //$rewriteUrl .="_list";
        $rewriteUrlPrice = 'noPrice'!==$priceId ?"_" . $priceId :'';
        $urlPrice = 'noPrice'!==$priceId ?'&priceId='.$priceId :'';
        
        $rewriteUrlParam = $paramVal ? "_" . $paramVal :'';
        $urlParam = $paramVal ? '&paramVal='.$paramVal :'';

        if($keyword){
            $rewriteUrlParam =$paramVal ? $paramVal.'-k'.$keyword:"_" .$keyword;
        }
        $urlKeyword = $keyword ?'&keyword='.$keyword:'';
        
        $rewriteUrlPage = $page ? "_" . $page :'_1';
        $urlPage = $page ? '&page='.$page :'&page=1';
        
        $urlJs = $isJs ? '&isJs=1' :'';

        if ($queryType > 1) {
            $rewriteUrlQueryType = $queryType ? "_" . $queryType :'';
            $urlQueryType = $queryType ?'&queryType='.$queryType :'&queryType=1';
            
            $rewriteUrlManu  = $rewriteUrlManu ? $rewriteUrlManu : '_0';
            $rewriteUrlPrice = $rewriteUrlPrice ? $rewriteUrlPrice : '_1';
            $rewriteUrlParam = $rewriteUrlParam ? $rewriteUrlParam : '_0';
            
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu.'_v'.$rewriteUrlPrice.$rewriteUrlParam.$rewriteUrlQueryType;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlQueryType.$urlPage.$urlJs;
        }else if('noPrice'!==$priceId || $paramVal){
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu.'_v'.$rewriteUrlPrice.$rewriteUrlParam;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlPage.$urlJs;
        }else{
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlPage.$urlJs;
        }
        
        $rewriteUrl .= '.'.$wapExt ;
        $url = $full ? 'http://wap.zol.com.cn'.$url : $url;
        $rewriteUrl = $full ? 'http://wap.zol.com.cn'.$rewriteUrl : $rewriteUrl;
        if ($rewrite) {
            return $rewriteUrl;
        }else{
            return $url;
        }
	}
    
    /** 
	* 获取慧聪列表页链接
	* @param array 数组参数
     * @jiebl 2013-12-14
	*/
	public static function getHcWapListUrl($paramArr)
	{
        $options = array(
            'a'             => '',   #控制器
            'subcateId'     => 0,    #子类ID
            'subcateEnName' => 0,    #子类英文名
            'manuId'        => 0,    #品牌ID
            'priceId'       => 'noPrice', #价格
            'paramVal'      => '',   #复合参数
            'queryType'     => 0,    #排序
            'style'         => 0,    #显示样式
            'locationId'    => 0,    #地区
            'keyword'       => 0,    #关键字
            'page'          => 1,    #页码
            'rewrite'       => 1,    #是否伪静态
            'isLong'        => 0,    #是否启用长链接
            'isHistory'     => 0,    #是否取历史列表
            'appendParam'   => 0,    #兼容旧代码参数
            'oldUrl'        => 0,    #旧链接
            'isJs'          => 0,
            'wapExt'        => 'html5',
            'full'          => 0,  #绝对地址，#是否带域名
            'newRule'       => 0  #新规则
        );
        if (empty($paramArr['subcateEnName']) && $paramArr['subcateId']) {
            $Db_Product = Db_Product::instance();
            $sql = "select brief from subcategory_extra_info where subcategory_id={$paramArr['subcateId']}";
            $paramArr['subcateEnName'] = $Db_Product->getOne($sql);
        }
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
        if ($appendParam && is_array($appendParam)) { #兼容旧代码参数处理，相关文件较多怕有遗漏所以程序处理
            extract($appendParam);
            $paramVal = is_array($paramVal) ? implode('-', $paramVal) : $paramVal;
        }
        $wapDir = '';
        $oriWapExt = $wapExt;
        if($wapExt=='html') {
           $wapExt .= '?j=simple';
        }
        if($wapExt=='html5') {
            $wapExt = 'html';
            $wapDir = "";
        }
        $wapUrl = 1==$full ? "http://hc.wap.zol.com.cn" : "";
		$wapUrl .= $wapDir."/";
        $url = '';
        $subEnName = $isHistory ? 'history' : $subcateEnName.'_index';
        $urlC = '/index.php?c=List_List';
        $rewriteUrl=''; 
        
        $urlA = $a ? '&a='.$a:'';

        //$rewriteUrlSubcate = $subcateId ? '/'.$subEnName.'/subcate'.$subcateId :'';
        $rewriteUrlSubcate = $subcateId ? '/list/'.$subcateId :'';
        $urlSubcate = $subcateId ? '&subcateId='.$subcateId :'';
        
        $rewriteUrlManu = $manuId ? "_" . $manuId :'';
        $urlManu = $manuId ? '&manuId='.$manuId :'';

        //$rewriteUrl .="_list";
        $rewriteUrlPrice = 'noPrice'!==$priceId ?"_" . $priceId :'';
        $urlPrice = 'noPrice'!==$priceId ?'&priceId='.$priceId :'';
        
        $rewriteUrlParam = $paramVal ? "_" . $paramVal :'';
        $urlParam = $paramVal ? '&paramVal='.$paramVal :'';

        if($keyword){
            $rewriteUrlParam =$paramVal ? $paramVal.'-k'.$keyword:"_" .$keyword;
        }
        $urlKeyword = $keyword ?'&keyword='.$keyword:'';
        
        $rewriteUrlPage = $page ? "_" . $page :'_1';
        $urlPage = $page ? '&page='.$page :'&page=1';
        
        $urlJs = $isJs ? '&isJs=1' :'';

        if ($queryType > 1) {
            $rewriteUrlQueryType = $queryType ? "_" . $queryType :'';
            $urlQueryType = $queryType ?'&queryType='.$queryType :'&queryType=1';
            
            $rewriteUrlManu  = $rewriteUrlManu ? $rewriteUrlManu : '_0';
            $rewriteUrlPrice = $rewriteUrlPrice ? $rewriteUrlPrice : '_1';
            $rewriteUrlParam = $rewriteUrlParam ? $rewriteUrlParam : '_0';
            
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu.'_v'.$rewriteUrlPrice.$rewriteUrlParam.$rewriteUrlQueryType;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlQueryType.$urlPage.$urlJs;
        }else if('noPrice'!==$priceId || $paramVal){
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu.'_v'.$rewriteUrlPrice.$rewriteUrlParam;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlPage.$urlJs;
        }else{
            $rewriteUrl = $rewriteUrlSubcate.$rewriteUrlManu;
            $url = $urlC.$urlA.$urlSubcate.$urlManu.$urlPrice.$urlParam.$urlKeyword.$urlParam.$urlPage.$urlJs;
        }
        
        $rewriteUrl .= '.'.$wapExt ;
        $url = $full ? 'http://hc.wap.zol.com.cn'.$url : $url;
        $rewriteUrl = $full ? 'http://hc.wap.zol.com.cn'.$rewriteUrl : $rewriteUrl;
        if ($rewrite) {
            return $rewriteUrl;
        }else{
            return $url;
        }
	}
    
     /**
	 * 组合新版点评的筛选URL,生成url伪静态
     * 14-4-16 下午5:27 jiebl
	 */
	public static function getFilterRewriteUrl($paramArr)
	{
        $options = array(
            'subPageType' => 'review',
            'type'  => '',
            'proId'=> 0,
            'level'  => 0,
            'order'  => 2,
            'revId'  => 0,
            'page'  => 1
        );
        if($paramArr){ $options = array_merge($options,$paramArr);}
        extract($options);
        $param = sprintf('%s_%d_%d_%d_%d', $type ? '_' . $type : '', $level, $order, $revId, $page);
        $url = '/' . ceil($proId / 1000) . '/' . $proId . '/review' . $param . '.html';
        return $url;
    }
    /**
     *获取厂商频道 品牌等 相关 url 
     */
    public static function getManuFacturerUrl($paramArr){
        $options = array(
            'urlParam'    => array(), #伪静态链接参数数组,id,page 啥的往里扔
            'rewrite'     => true,    #是否伪静态
            'type'        => '',      #url类型
            'splitPage'   =>false,    #是否分页
            'page'        =>false,
        );
        
        if($paramArr){ $options = array_merge($options,$paramArr);}
        extract($options);
        
        $url = ''; 
        if($splitPage){  #分页的带PAGE替换参数链接
            switch ($type) {
                case 'home':
                    $url = $rewrite ? '?c=Manu_Facturer/'.$urlParam[0].'_{PAGE}.html' : '?c=Manu_Facturer&a=home&manuId='.$urlParam[0];
                    break;
                case 'intro':
                    $url = $rewrite ? '?c=Manu_Facturer/'.$urlParam[0].'.html' : '?c=Manu_Facturer&a=home&type=intro&manuId='.$urlParam[0];
                    break;
                case 'search':
                    $url = '?c=Manu_Facturer&a=list&page={PAGE}&keyword='.$urlParam[0];
                    break;
                case 'new':
                    $url = $rewrite ? '/manufacturer/new_p{PAGE}.html' : '?c=Manu_Facturer&a=list&dataType=new&page={PAGE}';
                    break;
                case 'cate':
                    $url = $rewrite ? 'Manu_Facturer/list_'.$urlParam[0].'_{PAGE}.html' : '?c=Manu_Facturer&a=list&page={PAGE}&dataType=cate&id='.$urlParam[0];
                    break;
                case 'subcate':
                    $url = $rewrite ? '/manufacturer/'.$urlParam[0].'_p{PAGE}.html' : '?c=Manu_Facturer&a=list&page={PAGE}&dataType=subcate&id='.$urlParam[0];
                    break;
                case 'spell':
                    $spell = strtolower($urlParam[0]);
                    $url = $rewrite ? '/manufacturer/'.$spell.'_p{PAGE}.html' : '?c=Manu_Facturer&a=list&page={PAGE}&dataType=spell&id='.$spell;
                    break;
                case 'all':
//                  
                    $url = $rewrite ? '/manufacturer/all_p{PAGE}.html' : '?c=Manu_Facturer&a=list&page={PAGE}&dataType=all';
                    break;
                default:
                    $url = $rewrite ? 'Manu_Facturer/list_'.$urlParam[0].'_{PAGE}.html' : '?c=Manu_Facturer&page={PAGE}&a=list&dataType=subcate&id='.$urlParam[0];
                
                    
                    break;
            }
        }else{
            switch ($type) {
                case 'home':
                    $url = $rewrite ? '/manufacturer/index'.$urlParam[0].'.html' : '?c=Manu_Facturer&a=home&manuId='.$urlParam[0];
                    break;
                case 'intro':
                    $url = $rewrite ? '/manufacturer/index'.$urlParam[0].'_about.html' : '?c=Manu_Facturer&a=home&type=intro&manuId='.$urlParam[0];
                    break;
                case 'search':
                    $url = '?c=Manu_Facturer&a=list&keyword='.$urlParam[0];
                    break;
                case 'index':
                    $url = $rewrite ? '/manufacturer/' : 'index.php?c=Manu_Facturer';
                    break;
                case 'new':
                     $url = $rewrite ? '/manufacturer/new.html' : '?c=Manu_Facturer&a=list&dataType=new';
                    break;
                case 'subcate':
                    $url = $rewrite ? '/manufacturer/'.$urlParam[0].'.html' : '?c=Manu_Facturer&a=list&dataType=subcate&id='.$urlParam[0];
                    break;
                case 'cate':
                    $url = $rewrite ? 'Manu_Facturer/list_'.$urlParam[0].'.html' : '?c=Manu_Facturer&a=list&dataType=cate&id='.$urlParam[0];
                    break;
                case 'spell':
                    $spell = strtolower($urlParam[0]);
                    $url = $rewrite ? '/manufacturer/'.$spell.'.html' : '?c=Manu_Facturer&a=list&dataType=spell&id='.$spell;
                    break;
                case 'all':
                    $url = $rewrite ? '/manufacturer/all.html' : '?c=Manu_Facturer&a=list&dataType=all';
                    break;
                default:
                    $url = $rewrite ? 'Manu_Facturer/list_'.$urlParam[0].'.html' : '?c=Manu_Facturer&a=list&dataType=subcate&id='.$urlParam[0];
                    break;
            }
        }
        
                
        return $url;
    }
    
    /**
     * 获得跑分url
     */
    public  static  function  getPaoFenUrl(){       
          return  "/paofen/";
    }
    
    /*
     * desc : 获取品牌或者产品的帖子列表页地址
     *        如果该产品下没有帖子，返回品牌的url地址
     * date : 2014-12-31
     */
    public static function getBbsMoreUrl($paramArr)
    {
        $options = array(
            'manuId'    => 0,
            'proId'     => 0,
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        $result = ZOL_Api::run("Bbsv2.Urls.getProManuListUrl" , array(
            'manuid'    => $manuId,
            'productid' => $proId,
        ));
        return $result;
    }
    
    /*
     * 获取 帖子 品牌列表页地址
     * date : 2015-01-06
     */
    static  $otherbbsRewrite = array(    //其他论坛url重写关系表根据子类
                'gpsbbs'  => 15,
                'techbbs' => 16,
                'gqbbs'   => 17,
                'oabbs'   => 18,
                'jdbbs'   => 19,
                'gamebbs' => 20,
                'softbbs' => 21,
                'cdbbs'   => 22,
                'babybbs' => 23,
                'jiaoyi'  => 3,
                'huodong' => 2,
    );
    public static function  getBbsManuUrl($paramArr)
    {
        $options = array(
            'manuId'    => 0,
            'proId'     => 0,
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        
        $proInfo = Helper_Product::getBaseInfo(array('proId'=>$proId));
        
        $bbsInfo = ZOL_Api::run("Bbsv2.Book.getBbsInfo", array('manuid'=>$manuId,'productid'=>$proId));
        
	    $bbsid   = $bbsInfo['bbsid'];
        $boardid = $bbsInfo['boardid'];
	    $subid   = $bbsInfo['subid'];
	    
	    $productid  = $proInfo['id'];
	    $manuid     = $proInfo['manuId'];

	    $bbsCfg = ZOL_Api::run("Bbsv2.Book.getDbInfo", array('bbsid'=>$bbsid));
        if(!empty($bbsCfg)){
            $bbs    = $bbsCfg->bbs;
        }else{ 
              //产品库新增品牌,对应创建论坛版块
              $createResult = '';
              $createResult = ZOL_Api::run("Bbsv2.Global.addManuBbs" , array('subcatid'=>$proInfo['subcateId'],'manuid'=> $proInfo['manuId']));    
              if($createResult){
                   $bbsCfg = ZOL_Api::run("Bbsv2.Book.getDbInfo", array('bbsid'=>$bbsid));
                   $bbs    = $bbsCfg->bbs;
              }  
        }
	    
	    $speBbs = false;
	    if($bbsid == 6){
	        $speBbs = array_search($boardid, self::$otherbbsRewrite);
	    }
	    $bbs = $speBbs ? $speBbs : $bbs;
	    $url = "http://bbs.zol.com.cn/{$bbs}/d{$boardid}.html";
        
        return $url;
    }
    
    /**
     * 获取口碑相关url
     * 目前只有手机产品线有口碑
     * @param type $paramArr
     */
    public static function getKoubeiDetailUrl($paramArr)
    {
        $options = array(
            'subcateId' => '57',
            'manuId'    => '',
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        
        if($manuId){
            $url = 'http://detail.zol.com.cn/koubei/'.$subcateId.'/manu_'.$manuId.'.shtml';
        }else{
            $url = 'http://detail.zol.com.cn/koubei/';
        }
        return $url;
    }
    
    /**
     * 获取商城相关url
     * @param type $paramArr
     */
    public static function getShopListUrl($paramArr)
    {
        $options = array(
            'cateId' => '',
            'subcateId' => '',
            'manuId'    => '',
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);
        
        #获取子类的大类
        $rtnColsArr = ZOL_Api::run("Pro.Cate.getSubCate" , array(
            'rtnCols'        => '*(cateId,subcateEnName)',     
        ));
        $subcateEnName = isset($rtnColsArr[$subcateId])?$rtnColsArr[$subcateId]['subcateEnName']:'';
        if(!$cateId){
            $cateId = isset($rtnColsArr[$subcateId])?$rtnColsArr[$subcateId]['cateId']:'';
        }
        $url = $subUrl = $manuUrl = $cateUrl = '';
        if($subcateId){ $subUrl = '_s'.$subcateId; }
        if($manuId){ $manuUrl = '_m'.$manuId; }
        if($cateId){ $cateUrl = 'c'.$cateId; }
        
        $url = 'http://www.zol.com/'.$subcateEnName.'/list/'.$cateUrl.$subUrl.$manuUrl.'.html';
        return $url;
    }
    
    /**
     * 产生指定的url地址的二维码
     * 域名限制在 在线，商城，蜂鸟
     * @author chai.jiawei <chai.jiawei@zol.com.cn>
     * @date 2015-01-28
     * @return string $qrcodeUrl 二维码的地址
    */
    public static function getQrcode($params=array()) {
        //预定义变量
        
        $options    = array(
            'sizeNum'   => 2,//sizeNum  1-6 ，对应6中尺寸的 二维码 （75，150，258，344，430，860）
            'color'     => '000000',//二维码的颜色(16进制)，默认为黑色
            'logotype'  => 'default',//logo图案样式 default|pure
            'url'       => ''//生成的地址
        );
        
        //初始化参数
        if(is_array($params) && !empty($params)) {
            $options = array_merge($options, $params);
        }
        extract($options);
        
        $qrcodeUrl  = '';//返回的二维码地址
        $urlArr     = array();//组装URL参数
        //检测参数
        if('' == $url) {
            return $qrcodeUrl;
        }
        
        //产生二维码地址
        $origUrl    = 'http://qr.fd.zol-img.com.cn/qrcode/qrcodegen.php?';
        $salt       = "zolapiqrcode";
        $urlArr[]   = 'token=' . substr(md5($url.$salt),1,10 );
        $urlArr[]   = 'url=' . urlencode($url);
        $urlArr[]   = 'sizeNum=' . $sizeNum;
        $urlArr[]   = 'color=' . $color;
        $urlArr[]   = 'logotype=' . $logotype;
        if(count($urlArr) > 0) {
            $urlStr = implode('&', $urlArr);
        }
        $qrcodeUrl  = $origUrl . $urlStr;
        
        //返回数据
        return $qrcodeUrl;
    }

    /**
	 * 百度超级频道产品pk 相关 url （开启rewrite，需要写rewrite规则）
     * @param $paramArr
     * @return string
     */
    public static function getPkForBaiduWapUrl($paramArr)
    {
        $options = array(
            'proId'     => 0,     #本产品
            'pkProId'   => 0,     #pk的产品
			'subcateId' => 0,     #产品线
			'param'     => '',    #参数
        	'pageType'  => "",    #页面类型
            'rewrite'   => 1,     #是否伪静态
        );
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);

        $url = "";
        switch ($pageType) {
            case 'index': #首页
            	if(!$rewrite){
					$url = "/index.php?c=CompareForBaidu";
				}else{
                    $url = "/bdsuper/pkbd/";
				}
                break;
			case "choose": #产品选择页面
				if($subcateId) {
                    if (!$rewrite) {
                        $url = "/index.php?c=CompareForBaidu&subcateId={$subcateId}";
                    } else {
                        $url = "/bdsuper/pkbd/ProductComp_{$subcateId}.html";
                    }
                    #rewrite需要开启QSA
                    if($param && $url){
                        $url .= (strpos($url,"?") > 0 ? "&" : "?")."param=".$param;
                    }
                }
                break;
            case "pk": #对比详情页
                if($subcateId && $proId && $pkProId) {
                    if (!$rewrite) {
                        $url = "/index.php?c=CompareForBaidu&subcateId={$subcateId}&proIdStr={$proId}_{$pkProId}";
                    } else {
                        $url = "/bdsuper/pkbd/{$proId}_{$pkProId}.html";
                    }
                }
                break;
			default:
				break;
        }
        return $url;
    }
}
?>
