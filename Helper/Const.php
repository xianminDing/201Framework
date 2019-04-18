<?php
/**
* 数据库连接操作类
* @date: 2018年6月27日 上午10:04:20
* @author: SYJ
*/
class Helper_Const {
    public  static  $dbLink  = '';
    public  static  $dbBbsid = '';

    //根据bbsi获取置顶的链接类
    public static function getDbLink($paramArr = array()){
        $options = array(
            'bbsid'    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
		if($bbsid){
			$linkArr = ZOL_Config::get('Bbs_Config', 'BBSID_LINS_MAPPING');
			$link = $linkArr[$bbsid];
		}else{
			$link = self::$dbLink;
		}
		
		return $link;
    }
}
