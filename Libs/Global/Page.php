<?php
/**
* 强大的分页类
* 具有模板功能
* @example
*	$page = new Page(array('total'=>500));
*	echo $page->display(1);
*	echo '<br/>' . $page->display('{PREV:[上一页]}{NEXT:[下一页]}');
* @author wiki
* @copyright (c) 2009-4-1
*/
class Libs_Global_Page {

	protected $pageKey = 'page';

    protected $placeHd = '{PAGE}';

	/**
	* 模板
	* @var string
	*/
	protected $Template = '';

	/**
	* 当前页
	* @var integer
	*/
	protected $Page = 1;

	/**
	* 每页显示的数目
	* @var integer
	*/
	protected $RowNum = 20;

	/**
	* 总数
	* @var integer
	*/
	protected $Total = 0;

	/**
	* 总页数
	*/
	protected $TotalPage = 0;

	/**
	* 有没有页码Bar
	*/
	protected $existBar = false;

	/**
	* 当前页码焦点偏移量
	* @var integer
	*/
	protected $OffsetNum = 0;

	/**
	* 当面页码条中间显示宽
	* @var integer
	*/
	protected $BarLength = 0;

	protected $Url = '';


	protected $target = '';
	protected $jsOnclick = '';

	/**
	* 系统标签
	*/
	protected $FirstTag   = 'FIRST';
	protected $PrevTag    = 'PREV';
	protected $PrevHdTag  = 'PREVHD';#上一页的引导符
	protected $BarTag     = 'BAR';
	protected $NextTag    = 'NEXT';
	protected $NextHdTag  = 'NEXTHD';#下一页的引导符
	protected $LasttTag   = 'LAST';
	protected $CurrentTag = 'CURRENT';
	protected $TotalTag   = 'TOTAL';

	/**
	* 页码标签
	*/
	protected $NumTag  = '[NUM]';

	protected $TagsVal = array();
	protected $Label   = array();
	/**
	* 标签分割符
	*
	*/
	protected $TagDelimiter = '|';

	/**
	* 属性-值分割符
	*/
	protected $AttrDelimiter = ':';

	/**
	* 处理模板表达式模板
	* {TAGS} = $this->Tag
	* {ATTRDELIMITER} = $this->AttrDelimiter
	*/
	protected $RegexTemplate = '{(({TAGS}){ATTRDELIMITER}([^}]+))}';

	protected  $Style = array(
					'FIRST'   => '',
					'PREV'    => '',
					'PREVHD'  => '',
					'BAR'     => '',
					'NEXT'    => '',
					'NEXTHD'  => '',
					'LAST'    => '',
					'CURRENT' => '',
					'TOTAL'   => '',
                    'MORE1'   => '',
                    'MORE2'   => '',
				);

	/**
	* 是否设置智能模式 自动处理各个标签显示
	*
	* @var mixed
	*/
	protected $SmartShow = array(
					'FIRST'   => true,
					'PREV'    => false,
					'PREVHD'  => true,
					'BAR'     => true,
					'NEXT'    => false,
					'NEXTHD'  => true,
					'LAST'    => true,
					'CURRENT' => true,
					'TOTAL'   => true,
				);

	#开始页码
	private $startPage;

	#结束页码
	private $endPage;

	private $startOver = true;#开头

	private $endOver = false;#结束

	private $startPageOver = true;#当前页是否是首页
	private $endPageOver   = false;#当前页是否是首尾

	/**
	* @param array $opiton = array('page'=>1, 'rownum'=>20, 'total'=>542, 'template'=>'');
	*/
	public function __construct($option)
	{
		if (is_array($option)) {
			if (!array_key_exists('total', $option)) {
				return false;
			}

			$this->Total = intval($option['total']);
			isset($option['rownum'])         && ($this->RowNum = intval($option['rownum']));
			isset($option['offsetnum'])      && ($this->OffsetNum = intval($option['offsetnum']));
			isset($option['barlength'])      && ($this->BarLength = intval($option['barlength']));
			isset($option['pagekey'])        && ($this->pageKey = $option['pagekey']);
			isset($option['template'])       && ($this->Template = $option['template']);
			isset($option['target'])         && ($this->target = $option['target']);
			isset($option['jsOnclick'])      && ($this->jsOnclick = $option['jsOnclick']);
            isset($option['firstPageTotal']) && ($this->firstPageTotal = intval($option['firstPageTotal']));

			$page = isset($option['page']) ? (int)$option['page'] : 0;
			$url = isset($option['url']) ? $option['url'] : '';

		} else {
			$this->Total = intval($option);
			$page = 0;
			$url = '';
		}

		if (!$this->Total) {
			return false;
		}
		$this->_setPage($page);
		$this->_setUrl($url);
		$this->TotalPage = ceil($this->Total/$this->RowNum);

		$this->startPageOver = ($page <= 1);
		$this->endPageOver = ($page >= $this->TotalPage);
	}

	/**
	* 设置样式
	* @return ZOL_Product_Lib_Page
	*/
	public function setStyle(array $style) {
		if (!$style) {
			return false;
		}
		$this->Style = array_merge($this->Style, $style);
		return $this;
	}

	/**
	* 设置标签显示模式
	* @return ZOL_Product_Lib_Page
	*/
	public function setSmartShow($smartShow) {
		$this->SmartShow = is_array($smartShow) ? array_merge($this->SmartShow, $smartShow) : $smartShow;
		return $this;
	}

	public function get($var)
	{
		if (property_exists($this, $var)) {
			return $this->$var;
		} else {
			//trow error
		}
	}

	/**
	* 设置属性值
	*/
	public function set($var, $value)
	{
		if (property_exists($this, $var)) {
			$this->$var = $value;
		} else {
			//trow error
		}
		return $this;
	}

	/**
	* 设置当前页
	*/
	protected function _setPage($page)
	{
		$page = (int)$page;

		if ($page>0) {
			$this->Page = $page;
		} else {
			$this->Page = (int)$_GET[$this->pageKey];
		}
	}

	/**
	* 设置URL头
	*/
	protected function _setUrl($url='')
	{
        if (strpos($url, $this->placeHd) !== false) {
            $this->Url = $url;
            return true;
        }

		$queryString = '';

		if (!empty($url)) {
			if (($offset = strpos($url, '?')) !== false) {
				$queryString = substr($url, $offset+1);
				$url = substr($url, 0, $offset);
			} else {
                $url .= '?' . $this->pageKey . '=';
            }
		} else {
			$queryString = $_SERVER['QUERY_STRING'];
			$url = $_SERVER['PHP_SELF'];
		}

		if ($queryString) {
			parse_str($queryString, $query);
            unset($query[$this->pageKey]);

			if ($query) {
				$this->Url = $url . '?' . str_replace('&', '&amp;', http_build_query($query)) . '&amp;' . $this->pageKey . '=';
			} else {
				$this->Url = $url . '?' . $this->pageKey . '=';
			}
		} else {
			$this->Url = $url;
		}
        return true;
	}

	/**
	* 解析模板
	* @param string $code 模板内容
	*/
	protected function parseTemplate($code='')
	{
		$existBar = false;
		$label = array();
		$code || ($code = $this->Template);
		$tags[] = $this->FirstTag;
		$tags[] = $this->PrevTag;
		$tags[] = $this->PrevHdTag;
		$tags[] = $this->BarTag;
		$tags[] = $this->NextTag;
		$tags[] = $this->NextHdTag;
		$tags[] = $this->LasttTag;
		$tags[] = $this->CurrentTag;
		$tags[] = $this->TotalTag;

		$regex = str_replace(array('{TAGS}', '{ATTRDELIMITER}'),
							array(join('|', $tags), $this->AttrDelimiter),
							$this->RegexTemplate);
		preg_match_all("/{$regex}/s", $code, $result, PREG_SET_ORDER);
		foreach ($result as $re) {
			$_tags[$re[2]] = $re[3];
			//处理中间分页条部分
			if ($re[3] && strpos($re[3], $this->AttrDelimiter) && $re[2] == $this->BarTag) {
				$_result = explode($this->AttrDelimiter, $re[3]);
				$_tags[$re[2]]   = $_result[0];

				$this->BarLength || ($this->BarLength = $_result[1]);
				$this->OffsetNum || ($this->OffsetNum = $_result[2]);
				$existBar = true;
			}
			$label[$re[2]] = $re[0];
		}
		$this->existBar = $existBar;
		$this->Label = $label;
		$this->TagsVal = $_tags;

		$this->interval();
		return $_tags;
	}

	/**
	* 页码条区间
	*/
	private function interval()
	{
		if (!$this->existBar || $this->BarLength < 1) {
			return false;
		}

		if ($this->BarLength >= $this->TotalPage) {
			$start = 1;
			$end   = $this->TotalPage;
		} else {
			$start = $this->Page - $this->OffsetNum;
			$end   = $start + $this->BarLength;
			$end   = $start < 1 ? $this->BarLength : ($end - 1);

			$start = ($end > $this->TotalPage) ? ($start - ($end - $this->TotalPage)) : $start;

			$start = max($start, 1);
			$end   = min($end, $this->TotalPage);
		}

		$this->startPage = $start;
		$this->endPage   = $end;

		$this->startOver = ($start == 1);
		$this->endOver   = ($end == $this->TotalPage);
	}

	/**
	* 第一页
	*/
	public function firstTag($style='')
	{
		if (empty($this->TagsVal[$this->FirstTag])) {
			return false;
		}

		if ((!empty($this->SmartShow['FIRST']) || $this->SmartShow === true) && $this->startOver) {
			return false;
		}

		if ($this->startPageOver) {
			return false;
		}

		$style = isset($this->Style['FIRST']) ? $this->Style['FIRST'] : $style;


		$num = 1;
		if ($this->TotalPage==1) {
			return false;
		}
		$btn = $this->_getText($this->FirstTag, $num);

		if ($this->Page > 1) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}

	/**
	* 上一页
	*/
	public function prevTag($style='')
	{
		if (empty($this->TagsVal[$this->PrevTag])) {
			return false;
		}

		if ((!empty($this->SmartShow['PREV']) || $this->SmartShow === true) && $this->startOver) {

			return false;
		}

		if ($this->startPageOver) {
			return false;
		}

		$style = isset($this->Style['PREV']) ? $this->Style['PREV'] : $style;
		$num = $this->Page-1;
		$btn = $this->_getText($this->PrevTag, $num);
		if ($this->Page > 1) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}

	/**
	* 上一页引导符
	*/
	public function prevhdTag($style='')
	{
		if (empty($this->TagsVal[$this->PrevHdTag])) {
			return false;
		}

		if ((!empty($this->SmartShow['PREVHD']) || $this->SmartShow === true) && $this->startOver) {
			return false;
		}

		if ($this->startPageOver) {
			return false;
		}

		if ($this->startPage < 3) {
			return false;
		}

		$style = isset($this->Style['PREVHD']) ? $this->Style['PREVHD'] : $style;

		$num = $this->Page-1;
		$btn = $this->_getText($this->PrevHdTag, $num);
		return $this->_getNoLink($btn, $style);
	}

	/**
	* 当前分页条
	*/
	public function barTag($style='')
	{
		if (!$this->existBar || $this->BarLength < 1) {
			return false;
		}

		$style = isset($this->Style['BAR']) ? $this->Style['BAR'] : $style;

		$this->interval();

		$bar = '';

		for ($i = $this->startPage; $i <= $this->endPage; $i++) {
            $curStyle = $style;
            if (isset($this->firstPageTotal) && $i == ($this->firstPageTotal + 1) && ($this->firstPageTotal == $this->Page)) {
                $curStyle = 'historyStart';
            } else if (isset($this->firstPageTotal) && $this->firstPageTotal > 0) {
                if ($i >= ($this->firstPageTotal + 1)) {
                    $curStyle = 'history';
                    if ($i == $this->Page) {
                        $curStyle = 'historySel';
                    }
                } else if ($i <= $this->firstPageTotal && ($i == $this->Page)) {
                    $curStyle = 'sel';
                }
            } else {
                if ($i == $this->Page) {
                    $curStyle = 'sel';
                }
            }

			if ($i > $this->TotalPage) {
				break;
			} elseif ($i < 1) {
				continue;
			}
			$btn = $this->_getText($this->BarTag, $i);
			if ($i == $this->Page) {
				$bar .= $this->_getNoLink($btn, $curStyle);
			} else {
				$bar .= $this->_getLink($this->_getUrl($i), $btn, $curStyle);
			}


		}
		return $bar;
	}

	/**
	* 下一页
	*/
	public function nextTag($style='')
	{
		if (empty($this->TagsVal[$this->NextTag])) {
			return false;
		}

        if (isset($this->firstPageTotal) && ($this->firstPageTotal == $this->Page) )  {
            return false;
        }

		$num = $this->Page+1;
		if ((!empty($this->SmartShow['NEXT']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}
		if ($this->endPageOver) {
			return false;
		}

		$style = isset($this->Style['NEXT']) ? $this->Style['NEXT'] : $style;
		$btn = $this->_getText($this->NextTag, $num);
		if ($this->Page < $this->TotalPage) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}

	/**
	* 下一页引导符
	*/
	public function nexthdTag($style='')
	{
		if (empty($this->TagsVal[$this->NextHdTag])) {
			return false;
		}

		$num = $this->Page+1;

		if ((!empty($this->SmartShow['NEXTHD']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}

		if ($this->endPageOver) {
			return false;
		}

		if ($this->endPage + 2 > $this->TotalPage) {
			return false;
		}

		$style = isset($this->Style['NEXTHD']) ? $this->Style['NEXTHD'] : $style;

        if (isset($this->firstPageTotal) && ($this->Page >= $this->firstPageTotal) )  {
            $style = 'history';
        }

		$btn = $this->_getText($this->NextHdTag, $num);
		return $this->_getNoLink($btn, $style);
	}


	/**
	* 最后一页
	*/
	public function lastTag($style='')
	{
		if (empty($this->TagsVal[$this->LasttTag])) {
			return false;
		}

		$num = $this->TotalPage;

		if ((!empty($this->SmartShow['LAST']) || $this->SmartShow === true) && $this->endOver) {
			return false;
		}

		if ($this->endPageOver) {
			return false;
		}

		$style = isset($this->Style['LAST']) ? $this->Style['LAST'] : $style;

		$btn = $this->_getText($this->LasttTag, $num);
		if ($this->Page < $this->TotalPage) {
			return $this->_getLink($this->_getUrl($num), $btn, $style);
		}
		return $this->_getNoLink($btn, $style);
	}

	/**
	* 当前页
	*/
	public function currentTag($style='')
	{
		if (empty($this->TagsVal[$this->CurrentTag])) {
			return false;
		}

		$style = isset($this->Style['CURRENT']) ? $this->Style['CURRENT'] : $style;

		$num = $this->Page;
		$btn = $this->_getText($this->TotalTag, $num);
		if ($style) {
			$btn = $this->_getNoLink($btn, $style);
		}

		return $btn;
	}

	/**
	* 总页
	*/
	public function totalTag($style='')
	{
		if (empty($this->TagsVal[$this->TotalTag])) {
			return false;
		}

		$style = isset($this->Style['TOTAL']) ? $this->Style['TOTAL'] : $style;

		$num = $this->TotalPage;
		$btn = $this->_getText($this->TotalTag, $num);
		if ($style) {
			$btn = $this->_getNoLink($btn, $style);
		}

		return $btn;
	}


	public function pageInfo($style='')
	{
		return '共<span class="' . $style . '">' . $this->Total . '</span>条 第<span class="' . $style . '">' . $this->Page . '</span>/' . $this->TotalPage . '页';
	}

	/**
	* 获取链接文字
	* @param string $tag 链接字符串
	* @param integer $page 页码值
	*/
	public function _getText($tag, $page='')
	{
		return isset($this->TagsVal[$tag])
				? str_replace($this->NumTag, $page, $this->TagsVal[$tag])
				: $page;
	}
	/**
	* 获取URL链接
	*/
	public function _getUrl($page=1)
	{
		if (strpos($this->Url, '{PAGE}') !== false) {
			return str_replace('{PAGE}', $page, $this->Url);
		}

		return $this->Url . $page;
	}
	/**
	* 获取链接按钮
	*/
	public function _getLink($url, $text, $style='')
	{
		$style = empty($style) ? '' : "class=\"{$style}\"";
		return '<a href="' . $url . '" ' . $style
			. ($this->target ? (' target="' . $this->target . '"') : '')
			. ($this->jsOnclick ? (' onclick="' . $this->jsOnclick . '"') : '')
			. '>' . $text . '</a>';
	}

	public function _getNoLink($text, $style='')
	{
		$style = empty($style) ? '' : "class=\"{$style}\"";
		return '<span ' . $style . '>' . $text . '</span>';
	}


	public function display($mixed='')
	{
		$i = 0;
		if (is_string($mixed) && $mixed) {
			$code = $mixed;
		} elseif (is_integer($mixed)) {
			$i = $mixed;
		} else {
			$i = 1;
		}
		//默认几个
		if ($i || !$code) {
			switch ($i) {
				case 1:
					$code = '{FIRST:[NUM]...}{PREV:<}{BAR:[NUM]:10:3}{NEXT:>}{LAST:...[NUM]}';//DZ的
					break;
				case 2:
					$code = '{FIRST:[首页]}{PREV:[上页]}{NEXT:[下页]}{LAST:[尾页]}';//自定义的
					break;
				case 3:
					$code = '{PREV:[上一页]}{BAR:[[NUM]]:20:10}{NEXT:[下一页]}';//百度的
					break;
				case 4 :
					$code = '{PREV:&lt; 上一页}{PREVHD:...}{BAR:[NUM]:10:3}{NEXTHD:...}{NEXT:下一页 &gt;}{LAST:尾页}';//ZOL的
					break;
				case 5 :
					$code = '{PREV:上一页}{BAR:[NUM]:10:8}{NEXT:下一页}';//产品大全
					break;
                case 6 :    //列表页
					$code = '{PREV:<span class="pre">上一页</span>}{FIRST:<span>[NUM]</span>}{PREVHD:<span class="bgno">...</span>}{BAR:<span>[NUM]</span>:5:2}{NEXTHD:<span class="bgno">...</span>}{LAST:<span>[NUM]</span>}{NEXT:<span class="next">下一页&gt;</span>}';
					break;
                case 7 :    //列表页优化结构
					$code = '{PREV:&lt;上一页}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT:下一页&gt;}';
					break;
                case 8 :    //最精简版本
					$code = "{PREV:上一页}<span class='pagenum'><b>{$this->Page}</b>/{TOTAL:[NUM]}</span>{NEXT:下一页&gt;}";
					break;
                case 9 :    //列表页优化结构
					$code = '{PREV:<i></i>上一页}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT:下一页<i></i>}';
					break;
                case 10 :    //点评模块语义点评列表优化结构
					$code = '{PREV:上一页<i></i>}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT:下一页<i></i>}';
					break;
                case 11 :    //微动态专用分页
					$code = '{PREV: }{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{NEXT: }';
					break;
                case 12 :    //bootstrap
					$code = "<li class='disabled'><a href='#'><b>{$this->Page}</b>/{TOTAL:[NUM]}</a></li><li>{PREV:上一页}</li><li>{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}</li><li>{NEXT:下一页&gt;}</li><li>{LAST:尾页}</li><li><span>总数：{$this->Total}</span></li>";
					break;
                case 13 :    //维修库
                    $this->setStyle(array('PREV'=>'previous','NEXT'=>'next'));
					$code = '{PREV:上一页}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{LAST:[NUM]}{NEXT:下一页}';
					break;
                case 14 :    //恒星品牌落地页
					$code = '{PREV:上一页}{FIRST:[NUM]}{PREVHD:...}{BAR:[NUM]:5:2}{NEXTHD:...}{LAST:[NUM]}{NEXT:下一页}';
					break;
			}
		}
		$this->parseTemplate($code);

		foreach ($this->TagsVal as $tag=>$val) {
			$funckey = strtolower($tag) . 'Tag';

			$code = str_replace($this->Label[$tag], $this->$funckey(), $code);
		}
		return $code;
	}

	/**
     * 判断恒星产品页面类型
	 * @param $paramArr
	 * return String
     * @author wangpd
	 */
	public static function getStarProTplType($paramArr)
	{
		$options = array(
			'proInfo' => array(),   #产品信息
		);
		is_array($paramArr) && $options = array_merge($options, $paramArr);
		extract($options);

		#运行版本控制的子类数组
		$globalCfg = ZOL_Config::get("Pro_Global");
		$tplTypeSubArr = array_keys($globalCfg["ProOpenTrial"]);
		#指定某子类下某品牌运行版本控制
		$manMadeArr = array_keys($globalCfg["ProOpenTrialSpecial"]);
		$tplTypeArr = array(
			1=>'Standard',  #恒星的过期版（免费版）
			2=>'Advanced',  #高级版（认证版）
			3=>'Deluxe',    #豪华版
			4=>'Standard',  #标准版（不要了）
			5=>'Svip',      #电商版
		);
		$tplType = '';
		#上线的子类
		if(in_array($proInfo['subcateId'], $tplTypeSubArr)){
			$flag = 1;
		}
		#手工指定某子类下某品牌切换成标准版
		if(in_array($proInfo['subcateId'].'#'.$proInfo['manuId'], $manMadeArr)){
			$flag = 1;
		}
		if(!isset($proInfo['starData'])){//如果没有恒星关联信息，不设置版本
			$flag = 0;
		}else{
			$flag = 1;
		}
		#如果是京东的 就默认为标准版
		if(isset($proInfo['dataFrom'])  &&  $proInfo['dataFrom'] == 1){
			$tplType  = 'Standard';
		}

		#获得品牌信息
		$manuInfo = Helper_Manu::getInfo(array('manuId'=>$proInfo['manuId'],'subcateId' => $proInfo['subcateId']));  #品牌简介

		#确定当前产品的版本状态
		if($flag){#如果是恒星系统开放的产品线,并且是非知名的品牌
			if(isset($proInfo['starData'])){
				#恒星的用户信息
				$starData = $proInfo['starData'];
				#如果再购买期间内容的版本
				if(isset($starData['endTime']) && SYSTEM_TIME < $starData['endTime']){//&& $starData['buyTime'] < SYSTEM_TIME &&
					$tplType  = $tplTypeArr[$proInfo['starData']['versionType']];   #模板信息
				}else{
					if(isset($proInfo['aladdinDate']['endTime']) && SYSTEM_TIME < $proInfo['aladdinDate']['endTime']){
						$tplType  = 'Advanced'; #如果在阿拉丁售卖，改成认证版
					}else{
						$tplType  = 'Standard'; #如果没有任何购买信息，降至标准版
					}
				}
			}elseif(isset($manuInfo['saleLevel']) && $manuInfo['saleLevel'] == 1){#如果没有投放
				$tplType  = 'Standard';
			}
			#恒星Svip判断  ( 这个产品特殊处理，如果到期之后就干掉， 2016-06-10 ，这个用不想用电商版，换成了四个认证版，所以这个产品不能设置为电商版)
			if($proInfo['id']  != 1117191  &&   $proInfo['id'] != 1142557){
				$manuInfo = Helper_Cache::loadCache('Manu', array ('subcateId' => $proInfo['subcateId']));
				foreach ($manuInfo as $manu){
					if(!isset($manu['topPro']))continue;
					$topManuPro = $manu['topPro'];
					if(isset($topManuPro[1]) && $topManuPro[1]['id'] == $proInfo['id'] && $topManuPro[1]['rankSet']['startTime'] <= SYSTEM_TIME && $topManuPro[1]['rankSet']['endTime'] >= SYSTEM_TIME){
						$tplType  = 'Svip';
						break;
					}
					if(isset($topManuPro[2]) && $topManuPro[2]['id'] == $proInfo['id'] && $topManuPro[2]['rankSet']['startTime'] <= SYSTEM_TIME && $topManuPro[2]['rankSet']['endTime'] >= SYSTEM_TIME){
						$tplType  = 'Svip';
						break;
					}
				}
			}
			#恒星版本设置后台
			if(isset($proInfo['starDataSet']) && SYSTEM_DATE >= $proInfo['starDataSet']["startTime"] && SYSTEM_DATE < $proInfo['starDataSet']["endTime"] ){
				$tplType  = $proInfo['starDataSet']['typeName'];
			}
		}
		#如果  他是知名或者恒星品牌认证
		if(!empty($manuInfo['isFamous']) || ( (isset($manuInfo['saleLevel']) && $manuInfo['saleLevel'] == 3))){
			#如果他关联恒星并且版本是标准版 就用一般的 模板，比如  清华同方，因为清华同方 涉及产品线比较广，他们只买了2个产品线的，只想让这2个产品变成认证版，其他的还保留  知名版本
			if($tplType ==  'Standard' || !$tplType ){
				$tplType = '';
			}
		}

		return $tplType;
	}

}