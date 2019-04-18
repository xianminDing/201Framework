<?php
/**
* SEO相关的助手类
* @author 仲伟涛
* @copyright (c) 2012-01-16
*/
class Helper_Seo extends Helper_Abstract
{

	/**
	 * 获得子类品牌的长尾词
	 */
	public static function getLongWord($paramArr){
        $options = array(
            'subcateId'       => 0,
            'manuId'          => 0,
            'type'            => 'detail', #类型,手工填写的,包含type:detail list pk series

        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
		$cacheParam = $manuId ? array('subcateId'=>$subcateId,'manuId'=>$manuId,'type'=>$type) : array('subcateId'=>$subcateId,'type'=>$type);
		$data = self::loadCache('SeoLongWord', $cacheParam);
		
		if(empty($data) && $manuId){#如果指定了品牌,但是没有没有取得数据,就获得子类的
			$data = self::loadCache('SeoLongWord', array('subcateId'=>$subcateId,'type'=>$type));
		}
        if(!$data)$data = array();
        $data2 = self::loadCache('SeoLongWord', array('subcateId'=>$subcateId,'manuId'=>$manuId,'type'=>$type . '_extra') );
        if($data2){
            $data = array_merge($data,$data2);
        }
		return $data;

	}

	/**
	 * 获得友情链接
	 */
	public static function getFriendLink($paramArr){

		$data = self::loadCache('FriendLink', array());

		$outArr = array();
		if($data){
			$link = $data['link'];
			foreach($data['class'] as $k => $v){
				$outArr[$k] = array(
					 'name' => $v['cname'],
					 'link' => $link[$k],
				);
			}
		}
		unset($data);
		return $outArr;

	}

    /**
     * 得到热词链接
     */
    public static function getHotWordLink($paramArr) {
        $options = array(
            'proId'       => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);
        #需求不用了
        return false;
        $cacheParam = array('proId'=>$proId);
        $data = self::loadCache('HotLink', $cacheParam);
        return $data;
    }
}

?>