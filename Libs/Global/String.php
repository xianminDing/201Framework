<?php
/**
 * @name     String.php
* @describe  字符串公用处理
* @version   v1.0
* @author    weixj
* @copyright comment.zol.com.cn
* @date      2014-06-09
*/
class Libs_Global_String{
	/**
	 * @desc ajax返回
	 */
	public static function setAjaxReturn($paramArr){
		$options = array(
			'callback'	=>'', 	#回调函数名
			'data'	 	=>null, #要返回的数据字符串或数组
			'encoding'	=>1,	#转换编码为utf 	
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);
		#校验
		if(!$data){
			return 'null';
		}
		if(is_array($data)){
			if($encoding){
				array_walk_recursive($data, "Libs_Global_String::convertGbkToUtf");
			}
			return $callback ? $callback.'('.json_encode($data).')' : json_encode($data);
		}else{
			$data = $encoding ? mb_convert_encoding($data, 'UTF-8', 'GBK') : $data;
			return $callback ? $callback.'('.$data.')' : $data;
		}
	}
	public static function convertGbkToUtf(&$value, &$key){
		$value = mb_convert_encoding($value, "UTF-8", "GBK");
	}
    
            public static function convertUtfToGbk(&$value, &$key){
		$value = mb_convert_encoding($value, "GBK", "UTF-8");
            }
    
    /**
     * utf转GBk
     * 
     * **/
      public static function StrToGBK($paramArr){
            $options = array(
                    'data'	 	=>null, #要返回的数据字符串或数组
            );
            if(is_array($paramArr)) $options = array_merge($options, $paramArr);
            extract($options);
            if($data){
                array_walk_recursive($data, "Libs_Global_String::convertUtfToGbk");
            }
            return $data;
      }
		  
	/**
	 * 调整数字大小    type=1    如1005100   =》 100.51万
	 * 
	 * */
	public static function digitalConverters($num){

		if(strpos($num, "e+")!==false){
			$num = substr($num, 0,strpos($num, "e+"));
			$num = $num * 1000000;
			$num = sprintf("%.2f",($num/10000))."万";
			return $num;
		}else{
			return $num;
		}
		
	}	  
	/**
	 *  分页处理
	 * 
	 **/
	public static function preNewPaging ($paramArr){
		$options = array(
		   'nums'             => 0,  #总条数
		   'pageNums'         => 0,  #每页显示条数
		   'curPage'          => 1,  #当前页
		   'pageUrl'          => 0,  #页面链接
		   'replacePage'      => "_page",  #替换方式默认是page
		   'onePage'          => 0,  #如果第一页显示分页输入1，默认第一页不显示
		   'preNums'          => 2,  #前间隔
		   'nextNums'         => 2,  #后间隔
		   'isShow'           => 0,  #是否显示_ 0是显示 1不显示
		   'morePage'         => 0,  #0是不限制，其他为页数
		   'isMark'           => 0,  #是否加左右箭头<>
		   #'showNums'         => 0,  #显示数目          
	   );
	   if($paramArr)$options = array_merge($options,$paramArr);
	   extract($options);
	   if ($pageNums <=0 )return '每页显示条数不能为不正常值';
	   #总页数
	   $zPage   = ceil($nums/$pageNums);
	   if ($morePage)$zPage = $zPage <= $morePage ? $zPage : $morePage;
	   #当前页数
	   $curPage = $curPage <=0 ?  1 : $curPage;
	   $curPage = $curPage >=$zPage ?  $zPage : $curPage;
	   $isShow  = $isShow == 0 ? '_' : '';
	   if($isMark){
		   $lName = "&laquo;上一页";
		   $rName = "下一页&raquo;";
	   }else{
		   $lName = "上一页";
		   $rName = "下一页";
	   }
	   $outArr  = array();
	   $baseName= strpos($pageUrl,'.') !==false ?  basename($pageUrl) : $replacePage;
	   if ($curPage  >= 2){
		   $outArr['0']['page']    =  $lName;
		   $outArr['0']['pageUrl'] = $onePage == 0 ? str_ireplace ($replacePage, $isShow.($curPage-1), $pageUrl) 
				   : ($curPage == 2 ? str_ireplace ($baseName, '', $pageUrl) 
				   : str_ireplace ($replacePage, $isShow.($curPage-1), $pageUrl));
	   }
	   if($zPage <=5){
		   for ($i=1;$i<=$zPage;$i++){
			   $outArr[$i]['page'] = $i;
			   if ($i != $curPage){$outArr[$i]['pageUrl'] = 
							   $onePage && $i == 1 ? str_ireplace ($baseName, '', $pageUrl) 
							   : str_ireplace ($replacePage, $isShow.$i, $pageUrl);
			   }else  $outArr[$i]['pageUrl'] = "cur";
		   }
		   if (($curPage+1) <= $zPage){
			   $outArr[$zPage + 2]['page']   =  $rName;
			   $outArr[$zPage + 2]['pageUrl'] = str_ireplace ($replacePage, $isShow.($curPage+1), $pageUrl);
		   }
	   }
	   #总页数大于5
	   if($zPage > 5){
		   $outArr['1']['page'] = 1;
		   $outArr['1']['pageUrl'] = 
				   $onePage ? str_ireplace ($baseName, '', $pageUrl) 
							: str_ireplace ($replacePage, $isShow.'1', $pageUrl);
		   $tmp  = $curPage - $preNums;
		   $pre  = $tmp > 1 ?  $tmp : $preNums -1 ;
		   $pre  = $curPage == 1 ? 1 : $pre;
		   $tmp  = $zPage - $curPage - $nextNums;
		   $next =  $tmp >=0 ? $nextNums + $curPage : $zPage; 
		   if( 2 >= $curPage ) $next = $nextNums + 3 ;
		   if( $zPage == $curPage && $zPage > 4) $pre = $zPage - 4 ;
		   for ($i = $pre,$j = 1; $i<=$next ; $i++){
			  if (2 < $pre && 1==$j) {
				   $outArr['2']['page']    = -1;
				   $outArr['2']['pageUrl'] = '...';
				   $j++;
			  }
			  $outArr[$i]['page'] = $i;
			  if ($i != $curPage)$outArr[$i]['pageUrl'] = 
							   $onePage && $i == 1 ? str_ireplace ($baseName, '', $pageUrl) 
							   : str_ireplace ($replacePage, $isShow.$i, $pageUrl);
			  else  $outArr[$i]['pageUrl'] = "cur";
		   }
		   if($tmp >=0 ){
			   if ($next < $zPage){
				   $outArr[$zPage + 1]['page']    =  -1;
				   $outArr[$zPage + 1]['pageUrl'] = '...';
			   }
		   }
		   if (($curPage+1) <= $zPage){
			   $outArr[$zPage + 2]['page']   =  $rName;
			   $outArr[$zPage + 2]['pageUrl'] = str_ireplace ($replacePage, $isShow.($curPage+1), $pageUrl);
		   }

	   }
	   return $outArr;

	}

	public static function filterEmoji($nickname)
	{
		$nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);

		$nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);

		$nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);

		$nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);

		$nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);

		$nickname = str_replace(array('"','\'','&zwj;'), '', $nickname);

		return addslashes(trim($nickname));
	}
}