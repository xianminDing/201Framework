<?php
/**
 * 外网文章相关 Helper
 * @author dingxm, xianmin.cat, min.cat   It's all a man～
 * @copyright (c) 2018-07-05
 */
class Helper_Extra extends Helper_Abstract
{
    
    /**
     * 获取外网文章图片 media_id % 100
     */
    public static function getPgcPic($paramArr)
    {
        $options = array(
            'media_id'  => '',   #外网文章的媒体id
            'doc_id'    => '',   #外网文章的id （ 要带o ）
            'size'      => 't_s420x314',
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        $doc_id = (int)substr($doc_id,1);
        
        $db = ZOL_Db::instance('Db_MediaPlatDB');
        $tabeName = 'spider_article_pic_'.$media_id%100;
        $sql = "SELECT local_pic_url,pic_height,pic_width FROM {$tabeName} WHERE article_id = {$doc_id} and data_type = 3";
        $res = $db -> getAll($sql);
//                 echo '<pre>';
//                 print_r($res);exit;
        
        
        if(!empty($res)) {
            $showData = [];
            foreach($res as $k => $v) {
                $showData[$k]['url'] = 'https://i'.rand(1,5).'-toutiao-fd.zol-img.com.cn/'.$size.$v['local_pic_url'];
                $showData[$k]['width'] = $v['pic_width'];
                $showData[$k]['height'] = $v['pic_height'];
            }
            return $showData;exit;
        }else{
            return false;
        }
    

        //$pic = 'http://i1.article.fd.zol-img.com.cn/t_s170x300_q7'.$res[0]['local_pic_url'];
        //         return $pic;exit;
    }
    
    
    
    
    /**
     * 获取外网文章的相关阅读
     * ---------------------------------------------------------------------------
     * return Array
     */
    public static function getDocAbout($paramArr)
    {
        $options = array(
            'docId'  => '',   #ID
            'num'    => '1',  #获取数量
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$docId){
            return $docId;
        }
    
        $docId = substr($docId, 1);
        $docId = (int)$docId;
        $idData = ZOL_Api::run("Recsys.Article.similarArticles" , array(
            'cookie'         => '',        #cookie
            'imei'           => '',        #设备号
            'user'           => '',        #用户登录帐号
            'media'          => 'pgc',           #文章来源
            'articleId'      => $docId,         #articleId
            'recType'        => 'no_read',       #recType
            'recArticleType' => 'pgc',           #recArticleType
            'num'            => $num, #&#数量
        ));
        
        $showArr = [];
        $tmpArr = [];
        foreach($idData as $k => $v) {
            $tmpArr = self::getPgcInfo(array('docId'=>'o'.$v));
            $showArr[$k]['docId'] = 'o'.$v;
            $showArr[$k]['title'] = $tmpArr['title'];
            // 增加无图判断
            if (!empty($tmpArr['head_pics'])) {
                $showArr[$k]['pic_src'] = 'http://article.fd.zol-img.com.cn/'.$tmpArr['head_pics'];
            }
            $showArr[$k]['short_title'] = $tmpArr['title'];
            $showArr[$k]['media_id'] = $tmpArr['media_id'];
            $showArr[$k]['comment_num'] = self::getDocCommentNum(array('docId'=>$v));
            $showArr[$k]['url'] = 'app://article/'.'o'.$v.'/'.$tmpArr['title'];
            $showArr[$k]['type'] = '0';
            $showArr[$k]['pcClassId'] = '290';
        }
        unset($tmpArr);
        return $showArr;exit;
    }
    
    
    /**
     * 获取外网文章info
     * 包括入队列方法，供外网文章内容页使用
     * return Array
     */
    public static function getDocInfo($paramArr)
    {
        $options = array(
            'docId'  => '',   #ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if(!$docId){
            return $docId;
        }
        
        $returnData = [];
        # 获取缓存中的docInfo信息
        $param = array('docId' => $docId); //缓存参数
        $mData = Helper_Mongo::get(array(
            'moduleName' => 'PgcInfo',
            'param'      => $param,
        ));
        # 没数据则数据库获取
        if(empty($mData)) {
            # 读取docInfo
            $mData = self::getPgcInfo(array(
                'docId' => $docId,
            ));
            # 将数据入队列
            $param = array('moduleName'=>'PgcInfo','param'=>array('docId'=>$docId));
            ZOL_Api::run("Queue.RedisQ.push" , array(
                'serverName'     => 'ResysQ',        #服务器名
                'key'            => 'apicloud:cacherf',   #队列名
                'value'          => serialize($param),   #入队数据
            ));
        }
        $returnData = $mData;
        unset($mData);
        
        return $returnData;exit;
        
    }
    
    /**
     * 获取外网文章的评论数量
     */
    public static function getDocCommentNum($paramArr)
    {
        $options = array(
            'docId'  => '',   #ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if(!$docId){
            return $docId;
        }
        $dataArr = ZOL_Api::run("Article.Comment.getCount" , array(
            'docId'          => $docId,          #文章ID
            'kindId'         => 198,             #kindId
            'noAuto'         => 1,               #排除机器人评论
        ));
        return $dataArr;
    }
    
    
    /**
     * 获取外网文章Content
     * 包括入队列方法，供外网文章内容页使用
     * return Array
     */
    public static function getDocContent($paramArr)
    {
        $options = array(
            'docId'  => '',   #ID
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$docId){
            return $docId;
        }
    
        $returnData = [];
        # 获取缓存中的docContent信息
        $param = array('docId' => $docId); //缓存参数
        $mData = Helper_Mongo::get(array(
            'moduleName' => 'PgcContent',
            'param'      => $param,
        ));
        # 没数据则数据库获取
        if(empty($mData)) {
            # 读取数据库信息，返回内容的数组信息
            $mData = Helper_Article::filterContentArray(array(
                'docId' => $docId,
                'type'  => 'pgc',   #外网文章
            ));
            # 将数据入队列
            $param = array('moduleName'=>'PgcContent','param'=>array('docId'=>$docId));
            ZOL_Api::run("Queue.RedisQ.push" , array(
                'serverName'     => 'ResysQ',        #服务器名
                'key'            => 'apicloud:cacherf',   #队列名
                'value'          => serialize($param),   #入队数据
            ));
        }
        $returnData = $mData;
        unset($mData);
    
        return $returnData;exit;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * 获取外网文章info
     * 单独仅获取文章详细信息方法
     */
    public static function getPgcInfo($paramArr)
    {
        $options = array(
            'docId'            => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$docId){
            return $docId;
        }
        $id = (int)substr($docId, 1);
    
        $dbLink = ZOL_Db::instance('Db_MediaPlatDB');
        #根据id获取外网文章信息
        $sql = "select * from spider_article_newbie where article_id = {$id} and fetch_status = 1 and src_state = 0 and check_status = 1";
        $tmp = $dbLink -> getAll($sql);
    
        if ($tmp) {
            $data = $tmp[0];
        }
    
        #页面显示时间审核时间
        $data['src_pubtime'] = ZOL_Api::run('Article.WapFunction.dateFormat',array(
            'date'  => $data['check_date'],
        ));
    
        #获取文章关联的科技号
        $sqlMeida = "select * from spider_media where media_id = {$data['media_id']} and check_flag in(1,3)";
        $tmp = $dbLink -> getAll($sqlMeida);
    
        if ($tmp) {
            $data['media_info'] = $tmp[0];
        } else {
            unset($data);
        }
    
        //        mb_convert_variables('gbk','utf-8',$data);
    
        return $data;exit;
    }
    
    
    
    /**
     * 获取外网文章Content
     * 单独仅获取文章内容方法
     */
    public static function getPgcContent($paramArr)
    {
        $options = array(
            'docId'            => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        if(!$docId){
            return $docId;
        }
        $id = (int)substr($docId, 1);
    
        $dbLink = ZOL_Db::instance('Db_MediaPlatDB');
        
    
        $tabeName = self::_getContentTableName($id);
        
        $sqlContent = "select * from {$tabeName} where article_id = {$id}";
        $tmp = $dbLink -> getAll($sqlContent);
        //                             echo "<pre>";
        //                             print_r($tmp[0]);exit;
        if ($tmp) {
            $data['content'] = $tmp[0]['content_app'] ? $tmp[0]['content_app'] : $tmp[0]['content'];
        }
    
    
        //        mb_convert_variables('gbk','utf-8',$data);
    
        return $data;exit;
    }
    
    
    
    
    
    
    
    
    
    /**
	 * 获得外网文章==头条文章的文章页信息  现只有小程序在用，以后废弃，使用getPgcContent 和 getPgcInfo 方法获取文章信息和文章内容
	 * 根据id获取外网文章详细信息 
	 */
    public static function getToutiaoInfo($paramArr)
    {
        $options = array(
            'articleId'            => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        $id = (int)substr($articleId, 1);
        
        $dbLink = ZOL_Db::instance('Db_MediaPlatDB');
        #根据id获取外网文章信息
        $sql = "select * from spider_article_newbie where article_id = {$id} and fetch_status = 1 and src_state = 0 and check_status = 1";
        $tmp = $dbLink -> getAll($sql);
        
        if ($tmp) {
            $data = $tmp[0];
        }
        
        #页面显示时间审核时间
        $data['src_pubtime'] = ZOL_Api::run('Article.WapFunction.dateFormat',array(
            'date'  => $data['check_date'],
        ));
        
        #获取文章关联的科技号
        $sqlMeida = "select * from spider_media where media_id = {$data['media_id']} and check_flag in(1,3)";
        $tmp = $dbLink -> getAll($sqlMeida);
        
        if ($tmp) {
            $data['media_info'] = $tmp[0];
        } else {
            unset($data);
        }
        
        #获取文章内容
        if ($data)
        {
            $articleId = $data['article_id'];
            $tabeName = self::_getContentTableName($articleId);
            
            $sqlContent = "select * from {$tabeName} where article_id = {$articleId}";
            $tmp = $dbLink -> getAll($sqlContent);
//                             echo "<pre>";
//                             print_r($tmp[0]);exit;
            if ($tmp) {
                $data['content'] = $tmp[0]['content_app'] ? $tmp[0]['content_app'] : $tmp[0]['content'];
            }
        }
        
        
//        mb_convert_variables('gbk','utf-8',$data);
        
        return $data;exit;
    }
    
    
    // 外网文章库 获取内容分表
    private static function _getContentTableName ($articleId)
    {
        $tableName = 'spider_article_content_'.floor($articleId/1000000);
        
        return $tableName;
    }
    
    
    
    
    
    
    
    
    /**
     * 通过文章id获取外网文章内容里的图片
     */
    public static function getPicList($paramArr)
    {
        $options = array(
            'id'  => 0,
            'width' => 1000,
            'height' => 3000,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        $docInfo = self::getDocInfo(array('docId' => $id));
        if(empty($docInfo)) die('error:content NULL...');
        
        $picInfo = self::getPgcPic(array(
            'media_id' => $docInfo['media_id'],
            'doc_id'    => 'o'.$docInfo['article_id'],   #外网文章的id （ 要带o ）
            'size'      => 't_s'.$width.'x'.$height,
        ));
        if(empty($picInfo)) return array();
        
        $returnArr = [];
        foreach($picInfo as $k => $v)
        {
            $returnArr[$k]['origWidth'] = $v['width'];
            $returnArr[$k]['origHeight'] = $v['height'];
            $returnArr[$k]['src'] = $v['url'].'.webp';;
        }
        
        return $returnArr;exit;
    }
}