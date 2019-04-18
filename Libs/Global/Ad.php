<?php
/**
* AD广告插件
* @example   ..... 
* <?php
* //统一设置广告参数
* Plugin_Ad::setParam(array('subcateId' => $subcateId, 'manuId' => $manuId, 'proId' => $proId));
* Plugin_Ad::setParam($subcateId, $manuId, $proId);
* ?>
* <?=Plugin_Ad::getAd('detail', 'title_under', $subcateId, $manuId, $proId)?>
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c) 2009-9-30
*/

class Libs_Global_Ad
{
	/**
	* 广告文件目录
	*/
	const AD_FILE_DIR   = APP_AD_DIR;
	
	/**
	* 广告容器CLASS名
	*/
	const AD_CON_CLASS  = 'adSpace';
	
	/**
	* 一组广告时所用的标签
	*/
	const AD_GROUP_TAG  = 'ul';
	
	/**
	* 一组广告内每个广告的容器标签
	*/
	const AD_ITEM_TAG   = 'li';
	
	/**
	* 单个广告时的用的标签
	*/
	const AD_SINGLE_TAG = 'div';
	
    protected static $_subcateId = 0;
	protected static $_manuId    = 0;
	protected static $_proId     = 0;
	protected static $_locationId= 0;
	protected static $_filterAdCon = true;
	
	
	protected static $_processAdData = false;
    
	
	
	/**
	* 获取广告位数据
	* @param int $subcateId 产品子类
	* @return array
	*/
	public static function getAdData($subcateId = 0)
	{
		if (self::$_processAdData) {
			return self::$_adData;
		}
		
		$data = self::$_adData;
		self::$_processAdData = true;
		return $data;
	}
	
	/**
	* 设置广告参数
	*/
	public static function setParam($subcateId = 0, $manuId = 0, $proId = 0, $locationId = 0, $filterAdCon = true)
	{
		if (is_array($subcateId)) {  #如果是传过来了一个参数数组，执行下列操作
			$subcateId   = empty($param['subcateId']) ? 0 : (int)$param['subcateId'];
			$manuId      = empty($param['manuId']) ? 0 : (int)$param['manuId'];
			$proId       = empty($param['proId']) ? 0 : (int)$param['proId'];
			$locationId  = empty($param['locationId']) ? 0 : (int)$param['locationId'];
			$filterAdCon = (isset($param['filterAdCon']) && (bool)$param['filterAdCon']) ? true : false;
		}
		
		self::$_subcateId   = $subcateId;
		self::$_manuId      = $manuId;
		self::$_proId       = $proId;
		self::$_locationId  = $locationId;
		self::$_filterAdCon = $filterAdCon;
	}
	
	/**
	* 获取广告位
	* @param string  $pageType  页面类型
	* @param string  $adName    广告名
	* @param integer $subcateId 子类ID
	* @param integer $manuId    品牌ID
	* @param integer $proId     产品ID
	* @param integer $locatioinId 地区ID
	* @param integer $type 类别
	* @return string html
	*/
	public static function getAd($pageType, $adName, $subcateId = 0, $manuId = 0, $proId = 0, 
									$locationId=0, $type=0, $filterAdCon = null)
	{
		if (is_array($pageType)) {
			$option = $pageType;
			$options = array(
				'pageType'    => '',
				'adName'      => '',
				'subcateId'   => 0,
				'manuId'      => 0,
				'proId'       => 0,
				'locationId'  => 0,
				'type'        => 0,
				'filterAdCon' => null,
			);
			$options = array_merge($options, $option);
			extract($options);
			unset($option);
		}
		
		$subcateId  = $subcateId ? $subcateId : self::$_subcateId;
		$manuId     = $manuId ? $manuId : self::$_manuId;
		$proId      = $proId ? $proId : self::$_proId;
		$locationId = $locationId ? $locationId : self::$_locationId;
		
		$adData = self::getAdData($subcateId);
		if (empty($adData[$pageType][$adName])) {
		echo $adName."x";exit;
			return false;
		}
		
		$adArr = $adData[$pageType][$adName];
		

		
		$filterAdCon = $filterAdCon !== null ? (bool)$filterAdCon : self::$_filterAdCon;
		
		$adNum = count($adArr);
		$itemStartTag = $itemEndTag = '';
		if ($adNum > 1) {
			$isGroup = true;
			$id = self::getAdId($pageType, $adName);
			$groupStartTag = '<' . self::AD_GROUP_TAG . ' class="' . self::AD_CON_CLASS . '" id="' . $id . '">';
			$groupEndTag   = '</' . self::AD_GROUP_TAG . '>';
			
			$itemStartTag = '<' . self::AD_ITEM_TAG . ' {ID} {STYLE}>';
			$itemEndTag   = '</' . self::AD_ITEM_TAG . '>';
		} else {
			$isGroup = false;
			$id = self::getAdId($pageType, $adName);
			//$startTag = '<' . self::AD_SINGLE_TAG . ' class="' . self::AD_CON_CLASS . '">';
			//$endTag   = '</' . self::AD_SINGLE_TAG . '>';	
			$itemStartTag = '<' . self::AD_SINGLE_TAG . ' class="' . self::AD_CON_CLASS . '" id="' . $id . '" {STYLE}>';
			$itemEndTag   = '</' . self::AD_SINGLE_TAG . '>';
		}
		
		$item = '';
		$haveContent=$haveAd = false;
		
		foreach ($adArr as $pos => $adFiles) {
			
			$adId      = self::getAdId($pageType, $adName, $pos);
			
			$adContent = '';
			
			foreach ($adFiles as $adFile) {
				
				$type =$adFile[0];
				$_content = self::getAdContent($adFile, $subcateId, $manuId, $proId, $locationId, $type);
				$adContent .= $_content;
				if(($type==4 || $type==5) && $_content) {
					break;
				}
			}
			$displayStyle= '';
			if ($adContent || !$filterAdCon) {
				if($adContent){
					$haveAd = true;#只要有一个广告就要显示，一个也没有那就不显示了
				}else{
					if(!$filterAdCon){
						$displayStyle = 'style="display:none;"';
					}
				}
				$haveContent = true;
				$startTag = str_replace(array('{ID}','{STYLE}'), 
										array('id="' . $adId . '"',$displayStyle), $itemStartTag);
				$endTag   = $itemEndTag;
				$item .= $startTag . $adContent . $endTag;
			}
		}
		
		if (($haveContent && $haveAd) || !$filterAdCon) {
			$adStr = $isGroup ? ($groupStartTag . $item . $groupEndTag) : $item;
		} else {
			$adStr = false;
		}
        var_dump($adStr);
		return $adStr;
	}
	
	/**
	* 获取广告容器ID
	*/
	private static function getAdId($pageType, $adName, $pos = '')
	{
		$id = $pageType;
		$id .= $adName ? '_' . $adName : '';
		$id .= $pos ? '_' . $pos : '';
		
		return $id;
	}
	
	/**
	* 获取广告文件内容
	* @param string $adFile     广告文件名
	* @param integer $subcateId 子类ID
	* @param integer $manuId    品牌ID
	* @param integer $proId     产品ID
	* @param enum   $type       广告文件目录类型 1 带有子目录 2 AD根目录
	*/
	private static function getAdContent($adFile, $subcateId = 0, $manuId = 0, $proId = 0, $locationId=0, $type = 1)
	{
		if (is_numeric($adFile{0})) {
			$type = (int)$adFile{0};
			$adFile = substr($adFile, 1);
		}
		
		$adFile = str_replace(
						array('{SUBCATEID}', '{MANUID}', '{PROID}','{LOCATIONID}'), 
						array($subcateId, $manuId, $proId,$locationId),
						$adFile
					);
		$adFilePath='';
		if (1 === $type) {
			if ($subcateId && $manuId) {
				$adFilePath = self::AD_FILE_DIR . $subcateId . '/' . $manuId . '/' . $adFile;
			}
//			 else {
//				$adFilePath = self::AD_FILE_DIR . $adFile;
//			}
			
		}else if(2===$type){ 
			if ($subcateId && $manuId) {
				$adFilePath = self::AD_FILE_DIR . $adFile;
			}
		}else if(3===$type){
			$adFilePath = self::AD_FILE_DIR . $adFile;
		}else if($type==4){
			$adFilePath = self::AD_FILE_DIR . $adFile;
		}else if($type==5){
			if ($subcateId && $manuId) {
				$adFilePath = self::AD_FILE_DIR . $subcateId . '/0/' . $adFile;
			}
		}else{
			if(!$manuId || $proId){
			$adFilePath = self::AD_FILE_DIR . $adFile;
			}
		}
//		{debug} 
//		echo $adFilePath."<br>";
		$data = false;
		if ($adFilePath) {
			$data = file_get_contents($adFilePath);
			//$data = self::filterAd($data);//清除无用的广告JS
		}
		
		$data = strpos($data, 'http://detail.zol.com.cn:8088/') 
				? str_replace('http://detail.zol.com.cn:8088/', '/', $data) 
				: $data;
		$data = str_replace('http://detail.zol.com.cn/', '/', $data);
		if (strpos($data, '/ad_')) {
			$data = self::mergDetailNewAd($data);
		}
	
		return $data;
	}
	
	
	/**
	* 过滤广告内容
	* @param string $content 广告内容
	* @return mixed adhtml
	*/
	private static function filterAd($content)
	{
		$preg = array(
			"/<script.+swfobject\.js\".*><\/script>/isU",
			"/<\/?center>/isU",
			"/<\/?div.*>/isU",
			"/[\r\n]/isU",
		);
		$rePreg = array('', '', '', '');
		$content = preg_replace($preg, $rePreg, $content);
		
		//过滤没用的HTML
		if (strip_tags($content, '<script>') == '') {
			return false;
		}
		
		return $content;
	}
	
	/**
	* 获取AJAX请求的广告内容，以JSON数据返回 只取type=4的广告
	* @return json
	*/
	public static function getAjaxAd($pageType, array $param = array())
	{
		$adData = self::getAdData($subcateId);
		$adFileArr = !empty($adData[$pageType]) ? $adData[$pageType] : '';
		
		if (!$adFileArr) {
			return false;
		}
		
		$subcateId  = $param['subcateId'] ? $param['subcateId'] : self::$_subcateId;
		$manuId     = $param['manuId'] ? $param['manuId'] : self::$_manuId;
		$proId      = $param['proId'] ? $param['proId'] : self::$_proId;
		$locationId = $param['locationId'] ? $param['locationId'] : self::$_locationId;

		$adCodeArr = array();
		foreach ($adFileArr as $adName => $adArr) {
			
			$groupAdNum = count($adArr);
			if ($groupAdNum == 1) {
				$adId = self::getAdId($pageType, $adName);
			}
			foreach ($adArr as $pos => $adFiles) {
				$adId = !$adId ? self::getAdId($pageType, $adName, $pos) : $adId;
				$content = '';
				$codeType = '';
				foreach ($adFiles as $adFile) {
					$type =$adFile[0];
					
					if ($type != 4) {
						continue;
					}
					
					$content = self::getAdContent($adFile, $subcateId, $manuId, $proId, $locationId, $type);#获取广告广告内容
//					if($adName=='list_prolist_7'){
//							echo $content."<br><br>";
//						}
					if ($content) {
						break;
					}
				}
				if ($content) {
					$noScriptCode = preg_replace("/<script.*>.*<\/script>/", '', $content);
					#判断是HTML还是JS
					if ($noScriptCode == '') {#纯JS
						$code='';
						preg_match_all("/src=\"(.+)\"/isU", $content, $match);
						$src = $match[1];
						if ($src) {
							foreach ($src as $s) {
								$code .= file_get_contents($s);
							}
						}
						
						$code = addslashes(stripcslashes(str_replace(array("\r","\n"), array('',''), $code)));
						$codeType = 'JS';
					} elseif ($noScriptCode == $content) {#纯HTML
						$code = $content;
						$codeType = 'HTML';
					} else {#混合代码
						$code = preg_replace("/<script.+src=\"(.+)\"><\/script>/isU", "'<script>' . file_get_contents(\\1) . '</script>'", $content);
						$codeType = 'MIXED';
					}
					$code = str_replace(array("\r", "\n"), array('', ''), $code);
					
					$adCodeArr[$adId] = "{ID:'{$adId}', TYPE:'{$codeType}', CODE:'{$code}'}";
				}
			}
		}
		return '[' . join(',', $adCodeArr) . ']';
	}
	
	public static function getJsContent($jsFile)
	{
		
	}
	
	
	/**
	 * 合并detail页新形式广告代码为一个
	 */
	private static function mergDetailNewAd($adStr)
	{
		$srcReg = '\/ad(_\d+)+\.js';
		$reg = "/(<script\s+type=\"text\/javascript\"\s+src\=\"{$srcReg}\"\s*>\<\/script\>\s*){2,}/is";
		preg_match_all($reg, $adStr, $matches);
		if ($matches && !empty($matches[0][0])) {
			$adSearch = $matches[0][0];
			preg_match_all("/{$srcReg}/is", $adSearch, $srcMatch);
			$adId = join($srcMatch[1]);
			$reStr = '<script type="text/javascript" src="/ad' . $adId . '.js"></script>';
//			$reStr = '<script type="text/javascript" src="/index.php?c=Ad&adId=' . $adId . '"></script>';
			$adStr = str_replace($matches[0][0], $reStr, $adStr);
		}
		return $adStr;
	}
}
