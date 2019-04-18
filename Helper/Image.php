<?php
/**
* 图片信息操作类
* @date: 2018年6月27日 上午10:59:39
* @author: SYJ
*/
class Helper_Image {

	
    /**
     * 获取帖子的图片信息   
     */
    static function getBookPicInfo($paramArr){
        $options = array(
            'boardid'   => 0,
            'bookid'    => 0,
            'bbsid'     => 0,
            'replyid'   => 0,
            'userid'    => false,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
         
        if(!$boardid || !$bookid || !$bbsid) return false;
        $table = "zpic_{$boardid}";

        $whereSql = " bookid='{$bookid}' AND replyid='{$replyid}'";
        $whereSql .= $userid === false ? '' : " AND userid = '{$userid}'";
        $sql = "SELECT * FROM {$table} WHERE {$whereSql}";
        $db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
        return $db->getAll($sql);
    }
    
    /**
     * 获取帖子的图片数
     */
    static function getBookPicNum($paramArr){
        $options = array(
            'boardid'   => 0,
            'bookid'    => 0,
            'bbsid'     => 0,
            'replyid'   => 0,
            'userid'    => false
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
         
        if(!$boardid || !$bookid || !$bbsid) return false;
        $table = "zpic_{$boardid}";
    
        $whereSql = " bookid='{$bookid}' AND replyid='{$replyid}'";
        $whereSql .= $userid === false ? '' : " AND userid = '{$userid}'";
        $sql = "SELECT count(*) FROM {$table} WHERE {$whereSql}";
        $db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
        return (int)$db->getOne($sql);
    }
    
    /**
     * 根据picid获取一张图片
     */
    public static function getPicInfoByPicId($paramArr){
        $options = array(
                'boardid'   => 0,
                'bookid'    => 0,
                'bbsid'     => 0,
                'picid'	    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
         
        if(!$boardid || !$bookid) return false;
        $table = API_Item_Bbsv2_Table::createBookPicTable(array('boardid'=>$boardid,'bbsid'=>$bbsid));
        $sql = "SELECT * FROM {$table} WHERE bookid='{$bookid}' AND picid='{$picid}'";
        $db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
        return $db->getRow($sql);
    }
    
    /**
     * 根据picid获取book_img_path表中存储的fastdfs文件名称
     */
    public static function getPicNameByPicId($paramArr){
    	$options = array(
    			'bbsid'     => 0,
    			'picid'	    => 0,
    	);
    	if (is_array($paramArr))$options = array_merge($options, $paramArr);
    	extract($options);
    	 
    	if(!$picid || !$bbsid) return false;
    	
    	$sql = "SELECT pic FROM book_img_path WHERE picid='{$picid}'";
    	$db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
    	return $db->getOne($sql);
    }

    /**
     *获取帖子图片信息,picid 可以为数组
     */
    public static function getBookPicInfoByPicId($paramArr){
        $options = array(
            'bbsid'     => 0,
            'boardid'   => 0,
            'bookid'    => 0,
            'replyid'   => 0,
            'userid'    => '',
            'picid'	    => 0,
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
         
        if(!$boardid || !$bookid) return false;
    
        $table = API_Item_Bbsv2_Table::createBookPicTable(array('boardid'=>$boardid, 'bbsid' => $bbsid));
        $sql = "SELECT * FROM {$table} WHERE bookid='{$bookid}' ";
        if ($replyid) {
            $sql .= " AND replyid='{$replyid}' ";
        }
        if ($userid) {
            $sql .= " AND userid='{$userid}' ";
        }
        if(is_array($picid)){
            $picids = join(',',$picid);
            $sql .= " AND picid IN ({$picids}) ORDER BY id ";
        }else if ($picid){
            $sql .= " AND picid='{$picid}'";
        }
    
        $db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
        $data = $db->getAll($sql);
        return $data;
    }
    
    /**
     * 获取帖子的图片列表
     */
    static function getBookPicList($paramArr){
    	$options = array(
    	    'boardid' => 0,
    		'bookid'  => 0,
    		'bbsid'   => 0,
    		'size'	  => '210',
    		'replyid' => 0,
    		'userid'  => false,
    	    'cutpic'  => '',
    	    'https'   => false  
    	);
    	if (is_array($paramArr))$options = array_merge($options, $paramArr);
    	extract($options);
    	 
    	if(!$boardid || !$bookid || !$bbsid) return false;
    	$table = "zpic_{$boardid}";
    
    	$whereSql = " bookid='{$bookid}' AND replyid='{$replyid}'";
    	$whereSql .= $userid === false ? '' : " AND userid = '{$userid}'";
    	$sql = "SELECT * FROM {$table} WHERE {$whereSql}";
    	$db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
    	$picArr = $db->getAll($sql);
    	
    	$picList = array();
    	if ($picArr) {
    		foreach ($picArr as $key=>$val) {
    			$picList[] = ZOL_Api::run("Image.Util.getImgUrl" , array(
    					'module'   => 'bbswater',    			#业务类型名称
    					'fileName' => $val['pic'],   			#文件名
    					'size'     => $size.'x'.$size.$cutpic,        #尺寸
    					'https'    => $https    
    			));
    		}
    	}
    	
    	return $picList;
    }
    
    /**
     * 获取帖子的图片
     */
    public static function getBookPic($paramArr){
    	$options = array(
    			'boardid'   => 0,
    			'bookid'    => 0,
    			'bbsid'     => 0,
    			'replyid'   => 0,
    			'width'     => false,
    			'height'    => false,
    			'userid'    => false,
    			'limit'     => false,
    			'filterPic' => false, //过滤小图
    	);
    	if (is_array($paramArr))$options = array_merge($options, $paramArr);
    	extract($options);
    	 
    	if(!$boardid || !$bookid) return false;
    	$table = API_Item_Bbsv2_Table::createBookPicTable(array(
    			'bbsid' => $bbsid,
    			'boardid'=>$boardid
    	));
    	$whereSql = " bookid='{$bookid}' ";
    	$whereSql .= $userid === false ? '' : " AND userid = '{$userid}'";
    	$whereSql .= " AND replyid = '{$replyid}' ";
    
    	$whereSql .= !$height
    	? ''
    			: ($filterPic ? " AND (height > '{$height}' or height = 0)"  : " AND height > '{$height}'");
    
    	$whereSql .= !$width
    	? ''
    			: ($filterPic ? " AND (width > '{$width}' or width = 0)"  : " AND width > '{$width}'");
    
    	$limitSql = $limit === false ? '' : " LIMIT {$limit}";
    
    	$sql = "SELECT * FROM {$table} WHERE {$whereSql} {$limitSql}";
    
    	$db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
    	return $db->getAll($sql);
    	 
    }
    
    /**
     * 插入图片 生成picid
     */
    public static function insertImage($paramArr){
    	$options = array(
    			'userid'    => '',
    			'bbsid'     => 0,
    			'date'      => '',
    	);
    	if (is_array($paramArr))$options = array_merge($options, $paramArr);
    	extract($options);
    
    	if (!$userid) return false;
    
    	$table = 'book_img';
    	$date  = $date ? $date : date('Y-m-d H:i:s');
    
    	$db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
    	$sql = "INSERT INTO {$table} (date,userid) VALUES ('{$date}','{$userid}')";
    	if ($db->query($sql)){
    		return $db->lastInsertId();
    	}
    
    	return false;
    }
    
    /**
     * 插入图片 生成picid
     */
    public static function insertImagePath($paramArr){
    	$options = array(
    			'bbsid'    => 0,
    			'picid'    => '',
    			'pic'      => '',
    	);
    	if (is_array($paramArr))$options = array_merge($options, $paramArr);
    	extract($options);
    
    	if (!$picid || !$pic) return false;
    
    	$table = 'book_img_path';
    	
    	$db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
    	$sql = "INSERT INTO {$table} (picid,pic) VALUES ('{$picid}','{$pic}')";
    	if ($db->query($sql)){
    		return $db->lastInsertId();
    	}
    
    	return false;
    }
    
    /**
     *修改帖子宽高
     */
    public static function updateBookPicInfoByPicId($paramArr){
        $options = array(
            'bbsid'     => 0,
            'boardid'   => 0,
            'bookid'    => 0,
            'replyid'   => 0,
            'userid'    => '',
            'picid'	    => 0,
            'width'     => 0,
            'height'    => 0
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
         
        if(!$boardid || !$bookid || !$picid || !$width || !$height) return false;
    
        $table = API_Item_Bbsv2_Table::createBookPicTable(array('boardid'=>$boardid, 'bbsid' => $bbsid));
        $sql = "update {$table} set width='{$width}',height='{$height}' WHERE bookid='{$bookid}' and picid='{$picid}'";

        $db = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);
        return (int)$db->query($sql);

    }

	/*
	 * git转定帧图
	*/
	public static function imageResize($paramArr){
		$options = array(
			'gif'     => "",
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

		if(!$gif) return false;

		$gif = str_replace('https:', 'http:', $gif);

		$newimage = new Libs_Global_ImageResize();

		$imgInfo = $newimage->resize($gif,"/tmp/temp.jpg");

		$picInfo = ZOL_Upload::saveFileToFastDFSByCurl(array(
			'moduleName' => 'common',         #模块名
			'filePath'   => "/tmp/temp.jpg", #本地文件路径
			'cpng'       => 0,                #转换png格式为jpg，默认强制转换
		));

		if(isset($picInfo['fullName'])){
			$imgUrl = ZOL_Api::run("Image.Util.getImgUrl" , array(
				'module'         => 'doc',           #业务类型名称
				'fileName'       => $picInfo['fullName'],   #文件名
			));

			$imgInfo['img'] = $imgUrl;

			return $imgInfo;
		}

		return false;
	}
}
