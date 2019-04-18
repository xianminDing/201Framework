<?php
/**
* 本文件存放所有与页面HTML相关的函数
* @author 仲伟涛 <zhong.weitao@zol.com.cn>
* @copyright (c) 2011-06-20
* @version v1.0
*/
class Libs_Global_UsedHtml
{
    /**
    * 获得页面的Meta信息
    *
    * @param array $paramArr 参数数组
    * @return string 返回所有的meta标签
    * @example $paramArr = array(
    *                  'title'=>$seo['title'],
    *                  'keywords'=>$seo['keywords'],
    *                  'description'=>$seo['description'],
    *             );
    *             echo Libs_Global_PageHtml::getPageMeta($paramArr);
    */
    public static function getPageMeta($paramArr) {
        $options = array(
            'noFollow' => 0,#是否允许搜索引擎抓取
            'noCache'=>0,#是否缓存
            'chartSet'=>'GBK',#默认字符集
            'pageType'=>'',#页面类型，暂时没用到
            'title'=>'',#页面标题
            'keywords'=>'',#页面关键字
            'location'=>'',#页面location
            'description'=>'',#页面表述
        );
        is_array($paramArr) && $options = array_merge($options, $paramArr);
        extract($options);
        $metaStr = "<meta charset=\"".$chartSet."\" />\n";
        $noFollow && $metaStr .= "<meta name=\"ROBOTS\" content=\"NOINDEX, NOFOLLOW\" />\n";
        $noCache && $metaStr .= "<meta http-equiv=\"pragma\" content=\"no-cache\" />\n";
        $metaStr .= "<title>".$title."</title>\n";
        $keywords && $metaStr .= "<meta name=\"keywords\" content=\"".$keywords."\" />\n";
        $description && $metaStr .= "<meta name=\"description\" content=\"".$description."\" />\n";
        $location && $metaStr .= "<meta name=\"location\" content=\"".$location."\" />\n";
        $metaStr .= '<meta name="viewport" content="width=device-width,maximum-scale=1,minimum-scale=1" />';
        $metaStr .= '<meta content="telephone=no" name="format-detection" />';
        return $metaStr;
    }
}
