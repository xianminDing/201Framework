<?php


/**
 * 自动运行发布mongo缓存示例
 * 需要在crontab中定时刷新某个缓存时使用。
 * 类的名称一般是"Auto_业务名称_Page_模块名称",例如 "Auto_Article_Page_ArticleContent"
 */
class Auto_AAAAAA extends Auto_Abstract
{
    
    public function doDefault(ZOL_Request $input, ZOL_Response $output){
        
        //自动运行程序里面的$output和$input对象和页面程序一样用,只不过没有模板,所以$output没有了特色,不过可以当成不同action之间绑定变量的一个媒介。
        $dbDoc = Db_Document::instance();
        $docId = $input->get("docId");
        
        if(!$docId){
            //这里要写当全量发布时的条件,遍历某个id集批量发布缓存
        }
        $sql = "xxxxx";
        
        $data = $dbDoc->getAll($sql);
        
        //处理逻辑
        
        Helper_Mongo::set(array(
            'moduleName' => 'AAAAAA',                  #缓存模块名称首字母大写,例如ArticleContent
            'param'      => array('docId' => $docId,), #参数必须用数组,可以为空,但是必须是空数组
            'data'       => $data,                     #数据内容,一般为数组,如果为空可以是empty或null,也可以为空数组
        ));
        
        echo "文章ID{$docId}发布成功\n";
        return true;
    }
    
}