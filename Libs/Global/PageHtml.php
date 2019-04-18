<?php
/**
* 本文件存放所有与页面HTML相关的函数
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-20
* @version v1.0
*/
class Libs_Global_PageHtml
{

    /**
    * 获得页面的Meta信息
    *
    * @param    array  $paramArr 参数数组
    * @return   string 返回所有的meta标签中title keywords description 数据
    * @example  传入参数
    *            $paramArr = array(
    *                              'title'       => $seo['title'],
    *                              'keywords'    => $seo['keywords'],
    *                              'description' => $seo['description'],
    *                            );
    *            调用方式
    *            echo Libs_Global_PageHtml::getPageMeta($paramArr);
    */

    public static function getPageMeta($paramArr) {
        if (is_array($paramArr)) {
			$options = array(
                'id'          => 0,       #详情id
                'indexFlag'   => 0,       #首页标示
				'noFollow'    => 0,       #是否允许搜索引擎抓取
                'noCache'     => 0,       #是否缓存
                'chartSet'    => 'GBK',   #默认字符集
                'pageType'    => '',      #页面类型，暂时没用到
                'title'       => '',      #页面标题
                'keywords'    => '',      #页面关键字
                'description' => '',      #页面表述
			);
			$options = array_merge($options, $paramArr);
			extract($options);
		}
        
        #页面编码
        $metaStr = '<meta charset="' . $chartSet . '" />' . "\n\t";
        if($indexFlag == true){ #首页
            $metaStr.= '<meta http-equiv="mobile-agent" content="format=html5; url=http://m.zol.com.cn/z/" />' . "\n\t";
        }else if($pageType == 'Detail' ){#详情页
            $metaStr.= '<meta http-equiv="mobile-agent" content="format=html5; url=http://m.zol.com.cn/article/'.$id.'.html" />' . "\n\t";
        }
        
        if (!empty($noFollow)) {  #如果不允许搜索引擎抓取
            $metaStr .= '<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />' . "\n\t";
        }

        if (!empty($noCache)) {   #如果不允许缓存
            $metaStr .= '<meta http-equiv="pragma" content="no-cache" />' . "\n\t";
        }
 
        if (!empty($title)) {     #如果有标题
            $metaStr .= '<title>' . $title . '</title>' . "\n\t";
        }

        if (!empty($keywords)) {  #如果有关键字
            $metaStr .= '<meta name="keywords" content="' . $keywords .'" />' . "\n\t";
        }

        if ($description) {       #如果有描述
            $metaStr .= '<meta name="description" content="' . $description .'" />' . "\n";
        }
        #所有pc端页面禁止转码并且声明设备
        $metaStr .= '<meta http-equiv="Cache-Control" content="no-siteapp"/>'."\n\t";
        $metaStr .= '<meta http-equiv="Cache-Control" content="no-transform"/>'."\n\t";
        $metaStr .= '<meta name="applicable-device" content="pc">'."\n\t";
        return $metaStr;
    }

    /**
     * 得到与当前页面对应的wap站的链接地址，此标签为百度专有标签，用于wap端搜索的时候跳转到wap页面，对于其他的搜索引擎并不适用
     *
     * @param    array  $paramArr 参数数组
     * @return   string 返回百度WAP端搜索所需当前页面对应的wap地址的meta字符串
     * @example  传入参数
     *            $paramArr = array(
     *                            'subcateId' => 0,
     *                            'manuId'    => 0,
     *                            'proId'     => 0,
     *                            'seriesId'  => 0,
     *                            'param'     => array(),
     *                            'pageType'  => '', #页面类型，暂时没用到
     *                        );
     *            调用方式
     *            echo Libs_Global_PageHtml::getWapMeta($paramArr);
     */
    public static function getWapMeta($paramArr) {
        if (is_array($paramArr)) {
			$options = array(
                'subcateId'     => 0,
                'subcateEnName' => '',
                'manuId'        => 0,
                'proId'         => 0,
                'seriesId'      => 0,
                'param'         => array(), #这个主要是列表页能用到
                'pageType'      => '',     #页面类型
                'reviewId'      => '',    #某条点评的ID值 2014-05-16
                'paramType'     => '', 	  #排行榜页.参数类型 manu pricerange param
                'paramValue'    => '',	  #排行榜页.参数值 品牌ID 价格，参数区间ID
                'isSeries'      => 0,	  #排行榜页用到
			);
			$options = array_merge($options, $paramArr);
			extract($options);
            #下边的xhtml是简版的wap地址
            #html5是新版wap地址
            $metaStr  = '';   #百度WAP端搜索所需当前页面对应的wap地址的meta字符串
            $xhtmlUrl = '';   #与当前页面对应的xhtml的地址
            $wmllUrl  = '';   #与当前页面对应的wml的地址，这个目前没有用到
            $html5Url = '';   #与当前页面对应的html5的地址
            switch ($pageType) {
                case 'list':
                    $xhtmlUrl = !empty($paramVal) ? '' : Libs_Global_Url::getWapListUrl(array('subcateId'=>$subcateId, 'subcateEnName' => $subcateEnName,'manuId'=>$manuId,'priceId'=>$priceId,'paramVal'=>$paramVal,'pageType'=>'list','wapExt'=>'html','full'=>1));
                    $html5Url = Libs_Global_Url::getWapListUrl(array('subcateId'=>$subcateId,'manuId'=>$manuId,'priceId'=>$priceId,'paramVal'=>$paramVal,'pageType'=>'list','wapExt'=>'html5','full' =>1));
                    break;
                case 'detail': #点评页
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'detail','wapExt'=>'html'));
                    $newUrl  = !empty($propriId) ? array('proId'=>$proId,'pageType'=>'seriesdetail','propriId' => 9999,'wapExt'=>'html5') : array('proId'=>$proId,'pageType'=>'detail','wapExt'=>'html5');
                    $html5Url = Libs_Global_Url::getWapUrl($newUrl);
                    break;
                case 'reviewdetail': #点评最终页 2014-05-16
                    $html5Url = Libs_Global_Url::getWapUrl(array('pageType'=>'reviewdetail','wapExt'=>'html5','full'=>0,'newRule'=>1,'proId'=>$proId,'reviewId'=>$reviewId, 'full' => 1));
                  
                    $newUrl  = !empty($propriId) ? array('proId'=>$proId,'pageType'=>'seriesdetail','propriId' => 9999,'wapExt'=>'html5') : array('proId'=>$proId,'pageType'=>'detail','wapExt'=>'html5');
                    $xhtmlUrl = Libs_Global_Url::getWapUrl($newUrl);
                    
                    break;
                case 'price':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'price','wapExt'=>'html','param'=>$param));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'price','wapExt'=>'html5','param'=>$param));
                    break;
                case 'param':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'param','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'param','wapExt'=>'html5'));
                    break;
                case 'pic':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'pic','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'pic','wapExt'=>'html5'));
                    break;
                case 'article':
                     $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'article','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'article','wapExt'=>'html5'));
                    break;
                case 'review':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'review','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'review','wapExt'=>'html5'));
                    break;
                case 'series':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('subcateId'=>$subcateId,'manuId'=>$manuId,'seriesId'=>$seriesId,'proId'=>$proId,'pageType'=>'series','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('subcateId'=>$subcateId,'manuId'=>$manuId,'seriesId'=>$seriesId,'proId'=>$proId,'pageType'=>'series','wapExt'=>'html5'));
                    break;
                case 'index':
                    $xhtmlUrl = Libs_Global_Url::getWapUrl(array('pageType'=>'index','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('pageType'=>'index','wapExt'=>'html5'));
                    break;
                case 'sample':
                    //$xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'cateId'=>$cateId,'pageType'=>'sample','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'cateId'=>$cateId,'pageType'=>'sample','wapExt'=>'html5'));
                    break;
                case 'articleEval':
                    //$xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'cateId'=>$cateId,'subcateId'=>$subcateId,'pageType'=>'articleEval','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'cateId'=>$cateId,'subcateId'=>$subcateId,'pageType'=>'articleEval','wapExt'=>'html5'));
                    break;
                case 'video':
                    //$xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'video','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'video','wapExt'=>'html5'));
                    break;
                case 'CompareParam':
                    //$xhtmlUrl = Libs_Global_Url::getWapUrl(array('proId'=>$proId,'pageType'=>'video','wapExt'=>'html'));
                    $html5Url = Libs_Global_Url::getCompareUrl(array('proIdStr'=>$proIdStr,'pkProIdStr'=>$pkProIdStr,'pageType'=>'CompareParam','wapExt'=>'html5'));
                    break;
                case 'TopHome':
                    $html5Url = Libs_Global_Url::getWapUrl(array('pageType'=>'TopHome','wapExt'=>'html5'));
                    break;
                case 'TopSubcate':
                    $html5Url = Libs_Global_Url::getWapUrl(array('cateId'=>$cateId,'pageType'=>'TopSubcate','wapExt'=>'html5'));
                    break;
                case 'TopTop':
                    $html5Url = Libs_Global_Url::getWapUrl(array('subcateEnName'=>$subcateEnName,'pageType'=>'TopTop','wapExt'=>'html5'));
                    break;
                case 'TopDetail':
                    $html5Url = Libs_Global_Url::getWapUrl(array('paramType'=>$paramType,'paramValue'=>$paramValue,'isSeries'=>$isSeries,'subcateEnName'=>$subcateEnName,'pageType'=>'TopDetail','wapExt'=>'html5'));
                    break;
                default:
                    break;
            }
            $xhtmlUrl = $xhtmlUrl ? str_replace("http:","https:",$xhtmlUrl) : '';
            $html5Url = $html5Url ? str_replace("http:","https:",$html5Url) : '';
            if($xhtmlUrl) {
                $metaStr .= "<meta http-equiv=\"mobile-agent\" content=\"format=xhtml; url=".$xhtmlUrl."\"/>\n";
            }
            if($wmllUrl) {
                $metaStr .= "<meta http-equiv=\"mobile-agent\" content=\"format=wml; url=".$wmllUrl."\"/>\n";
            }
            if($html5Url) {
                $metaStr .= "<meta http-equiv=\"mobile-agent\" content=\"format=html5; url=".$html5Url."\"/>\n";
                $metaStr .= "<meta name=\"mobile-agent\" content=\"format=wml;url=".$html5Url."\"/>\n";
                $metaStr .= "<meta name=\"mobile-agent\" content=\"format=xhtml;url=".$html5Url."\"/>\n";
                $metaStr .= '<link rel="alternate" media="only screen and(max-width:640px)" href="'.$html5Url.'"/>'."\n";
            }
            //wap站适配  2014-05-15修改  
            if(in_array($pageType, array('list','detail', 'param', 'review', 'reviewdetail','price','article', 'index','series','video','sample','CompareParam','pic','TopHome','TopSubcate','TopTop','TopDetail'))) {
                $metaStr .= self::getAdapter(array('jumpUrl'=>$html5Url));
                $metaStr .= "\n";
            }
		}
        return $metaStr;
    }
    
    /**
     * 获取地区页面的地区识别meta信息
     * e.x:http://detail.zol.com.cn/cell_phone_index/subcate57_0_list_1_0_1_1_26_1.html
     * 这种的页面需要在meta中声明province=河南,city=濮阳
     */
    
    public static function getLocationMeta($paramArr){
        $option = array(
            'locationId' => 1,
            'isCoord'    => 0,   #是否返回百度地图api的坐标
        );
        is_array($paramArr) && $option=array_merge($option,$paramArr);
        extract($option);
        $metaStr = "";
        if($locationId>1){
            $locationInfo = Helper_Area::getLocationInfo(array('locationId'=> $locationId,));
            if(!empty($locationInfo)){
                
                if(!empty($locationInfo['fid'])){
                    $tmpLocationInfo = Helper_Area::getLocationInfo(array('locationId'=> $locationInfo['fid'],));
                    $provinceName = str_replace('省', '', $tmpLocationInfo['name']);
                    $metaStr .= "<meta name=\"location\" content=\"province={$provinceName};city={$locationInfo['name']};";
                }else{
                    if(!empty($locationInfo['isProvincialCapital'])){
                        $metaStr .= "<meta name=\"location\" content=\"province={$locationInfo['name']};city={$locationInfo['name']};";
                    }else{
                        return "";
                    }
//                    $metaStr .= "<meta name=\"location\" content=\"province={$locationInfo['name']};city={$locationInfo['name']};";
                }
                
                if($isCoord){
                    $metaStr .= "coord=";
                }
                $metaStr .= "\">\r\n";
            }
        }
        return $metaStr;
    }
    
    /**
     * 获取合并的前台JS CSS 链接
     * @param string|array $link
     * @param string $type 文件类型
     * @return string
     */
    public static function getMergeFrontendLink($file, $type,$cssMedia=false, $user = false)
    {
        #PRODUCTION_ROOT的值为/www/wangt2 为当前程序相对根目录的地址,这个文件目前只存在于前天服务器上，本地测试服务器莫有
        $ver = (int)ZOL_File::get(PRODUCTION_ROOT . '/version.txt');

        if (IS_PRODUCTION && !IS_DEBUGGING_L2) { # 1、如果是detail.zol.com.cn域名（IS_PRODUCTION表示是detail.zol.com.cn域名）
                                                 # 2、并且没有打开二级报错（也就是不是在8089下，8089下是打开二级报错的）
                                                 # 3、表明是生产环境，生产环境读取合并压缩后的CSS、JS文件
            $fileArr = array("//s.zol-img.com.cn/d/".APP_NAME."/".APP_NAME."_{$file}.{$type}?v={$ver}");
        } else {                                 # 否则表明是测试环境，读取未经合并压缩的各个CSS文件
            #读取配置文件
            $cssJsCfg = parse_ini_file(PRODUCTION_ROOT . "/Config/CssJs.ini",true);
            $files = $cssJsCfg[APP_NAME . "_" .$type][$file];
            if(!$files)return '';
            #每个文件前面添加 /
			if(strpos($files, "Static") === false){
				$files = str_replace(",",",/{$type}/","/{$type}/".$files);
			} else {
				$files = str_replace("Static", "/Static", $files);
			}
            $fileArr = explode(",",$files);
        }
        $html = '';
        if($fileArr){
            foreach( $fileArr as $url){
                switch (strtolower($type)) {
                    case 'css':
                        $cssMedia = $cssMedia ? 'media="'.$cssMedia.'"' : '';
						if($user){
							$html .= '<link href="http://' . $user . '.test.detail.zol.com.cn' . $url . '" rel="stylesheet"  '.$cssMedia.' />';
						} else {
							$html .= '<link href="' . $url . '" rel="stylesheet"  '.$cssMedia.'  />';
						}
                        break;
                    case 'js':
						if($user){
							$html .= '<script src="http://' . $user . '.test.detail.zol.com.cn' . $url . '"></script>';
						} else {
							$html .= '<script src="' . $url . '"></script>';
						}
                        break;
                    default :
                        return false;
                }
                $html .= "\r\n";
            }
        }
        if(strtolower($type) == 'css' && $html){
            $html .= self::getGrowingioVdsLink();
        }
        return $html;
    }
	
    /**
     * 获取合并的前台JS CSS 链接
     * 这个是为https准备的,V4也在用
     * @param string|array $link
     * @param string $type 文件类型
     * @return string
     */
    public static function getMergeFrontend($paramArr)
    {
        $ver = (int)ZOL_File::get(PRODUCTION_ROOT . '/version.txt');
        $option = array(
            'file'  => '', #file name in *.ini
            'type'  => 'css', #js or css
            'user'  => '',
            'media' => '',
        );
        if(is_array($option))$option = array_merge ($option,$paramArr);
        extract($option);
        if(!$file) return false;
        if (IS_PRODUCTION && !IS_DEBUGGING_L2) {
            $fileArr = array("//s.zol-img.com.cn/d/".APP_NAME."/".APP_NAME."_{$file}.{$type}?v={$ver}");
        } else {
            #读取配置文件
            $cssJsCfg = parse_ini_file(PRODUCTION_ROOT . "/Config/CssJs.ini",true);
            $files = $cssJsCfg[APP_NAME . "_" .$type][$file];
            if(!$files)return '';
			#每个文件前面添加 /
			if(strpos($files, "Static") === false){
				$files = str_replace(",",",/{$type}/","/{$type}/".$files);
			} else {
				$files = str_replace("Static", "/Static", $files);
			}
            $fileArr = explode(",",$files);
        }
        $html = '';
        if($fileArr){
            foreach( $fileArr as $url){
                switch (strtolower($type)) {
                    case 'css':
                        $media = $media ? 'media="'.$media.'"' : '';
						if($user){
							$html .= '<link href="//' . $user . '.test.detail.zol.com.cn' . $url . '" rel="stylesheet"  '.$media.' />';
						} else {
							$html .= '<link href="' . $url . '" rel="stylesheet"  '.$media.'  />';
						}
                        break;
                    case 'js':
						if($user){
							$html .= '<script src="//' . $user . '.test.detail.zol.com.cn' . $url . '"></script>';
						} else {
							$html .= '<script src="' . $url . '"></script>';
						}
                        break;
                    default :
                        return false;
                }
                $html .= "\r\n";
            }
        }
        if(strtolower($type) == 'css' && $html) {
            $html .= self::getGrowingioVdsLink();
        }
        return $html;
    }
    /**
     * 获取前台JS CSS 链接
     * @param string|array $link
     * @param string $type 文件类型
     * @return string
     */
    public static function getFrontendLink($link, $type = '')
    {
        $ver = (int)ZOL_File::get(PRODUCTION_ROOT . '/version.txt');
        if (is_array($link)) {
            $url = '';
            foreach ($link as $l) {
                $url .= self::getFrontendLink($l, $type);
            }
            return $url;
        }

        $url = '/';
        if (IS_PRODUCTION) {
            $url = '//icon.zol-img.com.cn/zj2011/'; //ZOL_Config::get('Url', 'ICON_URL');
        }

        if (!$type) {
            $type = substr($link, strrpos($link, '.')+1);
        }

        $url .= "{$type}/{$link}";

        $url .= $ver ? ('?v=' . $ver) : '';
        switch (strtolower($type)) {
            case 'css':
                $url = '<link href="' . $url . '" rel="stylesheet" type="text/css" />';
                break;
            case 'js':
                $url = '<script type="text/javascript" src="' . $url . '"></script>';
                break;
            default :
                return false;
        }

        $url .= "\r\n";
        return $url;
    }
    /**
	* 设置过期时间
	*
	* @param integer $sec 秒
	* @param boolen $duly 是否正点过期
	*/
	public static function setExpires($sec, $duly = false)
	{
		$lastModified = $duly ? (SYSTEM_TIME - (SYSTEM_TIME % $sec)) : (SYSTEM_TIME);
		$expireTime   = $lastModified + $sec;
		if(0 == $sec){
			header("Cache-Control: no-cache");
            header('Pragma: no-cache');
            $expireTime =  SYSTEM_TIME - 86400 * 3650;
		}else{
			header('Cache-Control: max-age=' . $sec);  
            header('Last-Modified:' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
            header('Pragma: public'); 
		}
        header('Expires:' . gmdate('D, d M Y H:i:s', $expireTime) . ' GMT');
	}
    
    
    /*
     * @Desc 页面适配相关的js代码
     * @Version 14-7-17 上午11:58 beta 0.1
     * @Author jiebl
     * @主要读取cookie，判断cookie是否存在。
     */
    public static function getAdapter($paramArr = FALSE){
        $options = array(
            'cookieName' => 'mobile_agent_global_dapter',
            'jumpUrl' => '' #产品库现在的适配是使用shareDbUrl这个变量的。注意如果没有传递这个参数。就使用js变量，无引号。
        );
        if(is_array($paramArr)){ $options = array_merge($options,$paramArr);}
        extract($options);
        
        // 不带修正的跳转
        // $html = '<script>(function(){var a=1,b="'.$jumpUrl.'";document.cookie&&document.cookie.match(/'.$cookieName.'=([^;$]+)/)&&(a=document.cookie.match(/'.$cookieName.'=([^;$]+)/)[1]);if(1===a&&""!==b&&(/AppleWebKit.*Mobile/i.test(navigator.userAgent)||/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))&&0>window.location.href.indexOf("?via="))try{/Android|Windows Phone|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)&&(window.location.href=b)}catch(c){}})();</script>';    
        
        // 带修正的跳转
        // 功能：手机访问PC页面连接的时候，进行跳转，跳转结果页的referrer修正
        // 办法：在需要跳转的页面跳转前以cookie形式先记录正确的referrer, 然后在跳转结果页取正确的referrer
        $html = '<script>(function(){var a=1,d="'.$jumpUrl.'",b=document.cookie,c=document.referrer;b&&b.match(/'.$cookieName.'=([^;$]+)/)&&(a=b.match(/'.$cookieName.'=([^;$]+)/)[1]);if(1===a&&""!==d&&(/AppleWebKit.*Mobile/i.test(navigator.userAgent)||/MIDP|SymbianOS|NOKIA|SAMSUNG|LG|NEC|TCL|Alcatel|BIRD|DBTEL|Dopod|PHILIPS|HAIER|LENOVO|MOT-|Nokia|SonyEricsson|SIE-|Amoi|ZTE/.test(navigator.userAgent))&&0>window.location.href.indexOf("?via=")){a=new Date;c=""===c?"none":-1<c.indexOf("?")?c+"&pcjump=1":c+"?pcjump=1";b&&(a.setDate(a.getDate()+1),document.cookie="PC2MRealRef="+escape(c)+";expires="+a.toGMTString()+
"; domain=.zol.com.cn; path=/");try{/Android|Windows Phone|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)&&(window.location.href=d)}catch(e){}}})();</script>';
        return $html;
    }
    
    /**
     * 合并mip的静态资源,返回静态资源内容文本.
     * @param type $paramArr
     * @return string
     */
    public static function mergeStaticFile($paramArr){
        $options = array(
            'appName'    => APP_NAME,     //当前APP调用时,这个可以省略掉.跨app调用其他静态资源时必须加上.
            'iniName'    => 'CssJsMip',   //默认的CssJs配置文件名
            'cfgName'    => '',           //ini配置大项(如:Pro_css)
            'cfgSubName' => '',           //ini配置子项(如:detailv3)
        );
        if(is_array($paramArr))$options = array_merge($options,$paramArr);
        extract($options);
        //防止有牛人删除这个常量
        defined('APP_HTML_DIR') || define('APP_HTML_DIR', PRODUCTION_ROOT . '/Html/' . APP_NAME);
        
        if(!$cfgName || !$cfgSubName || !$iniName)return false;
        $staticType = explode('_', $cfgName);
        $staticType = $staticType[1];
        //读取ini配置
        //产品库有两个ini文件,所以要判断下.
        
        $iniPath = PRODUCTION_ROOT."/Config/".$iniName.".ini";
        $iniArr  = parse_ini_file($iniPath,true);
        $staticName = isset($iniArr[$cfgName][$cfgSubName]) ? $iniArr[$cfgName][$cfgSubName] : '';
        
        $staticFileArr = array();
        if(!empty($staticName)){
            $staticFileArr = explode(',', $staticName);
        }
        $content = $htmlDir = "";
        $htmlDir = $appName==APP_NAME ? APP_HTML_DIR : (PRODUCTION_ROOT . '/Html/' . $appName);
        foreach ($staticFileArr as $staticFile){
            $content .= file_get_contents($htmlDir."/".$staticType."/".$staticFile);
        }
        return $content;
    }
    /**
     * growingio 统计代码 多次使用
     * @param $paramArr
     * @return string
     */
    public static function getGrowingioVdsLink($paramArr = array())
    {
        $options = array();
        if (is_array($paramArr)) $options = array_merge($options, $paramArr);
        extract($options);

        $html = '<script src="//icon.zol-img.com.cn/growingio/vds.js"></script>' . "\r\n";
        return $html;
    }
}
