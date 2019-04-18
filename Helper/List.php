<?php
/**
* 论坛列表数据操作类
* @date: 2018年6月27日 上午9:45:33
* @author: SYJ
*/
class Helper_List {    
    
    /**
     *	分类信息常用判断
     *	(子类,子子类) 和(品牌,产品)
     */
    private static function cateIf($paramArr){
        $options = array(
            'boardid'   => 0,			//分表id(子类id)
            'subid'		=> 0,			//如果查二级列表(子类)
            'manuid'	=> 0,			//品牌id
            'productid' => 0,			//产品id
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        //拼接条件语句
        if((int)$manuid){
            $where = '';
            if ($manuid != $boardid) {
                $where = " manuid = '{$manuid}' ";
                $andStr = " AND ";
            } else {
                $andStr = "";
            }
    
            $where .= $productid ? " {$andStr} productid = '{$productid}'" : '';
        }else if($boardid){
            $where = " boardid = '{$boardid}' ";
            $where = $subid ? " subid = '{$subid}'" : '';
        }else{
            $where = '';
        }
    
        return $where;
    }
    

    /**
     *	帖子类型判断
     */
    private static function bookTypeIf($paramArr){
        $options = array(
            'type' => ''
        );
    
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$type){
            return '';
        }
    
        $arr =(array)$type;
        $where = '';
        foreach($arr as $type){
            switch($type){
            	case 'good':
            	    $where .= ' AND good > 0';
            	    break;
            	case 'newproduct':
            	    $where .= ' AND is_new = 1';
            	    break;
            	case 'master':
            	    $where .= ' AND is_talent = 1';
            	    break;
            	case 'active':
            	    $where		.= " AND book_type = 120";
            	    break;
            	case 'topic':
            	    $where 		.= ' AND is_topic = 1';
            	    break;
            	case 'resource':
            	    $where 		.= ' AND is_resource = 1';
            	    break;
            	case 'pic':
            	    $where 		.= ' AND is_pic = 1';
            	    break;
            	default:
            	    $where .= '';
            	    break;
            }
        }
    
        return $where ? ltrim($where, ' AND') : '';
    }
    
    /**
     * 获取板块下的帖子数量
     * 针对分表
     */
    public static function getBookNum($paramArr){
        $options = array(
            'bbsid'         => 0,
            'boardid'   	=> 0,
            'subid'			=> 0,
            'manuid'		=> 0,
            'productid' 	=> 0,
            'type'			=> '',		//帖子类型
            'bookType'      => 0,
            'sDate'         => false,
            'eDate'         => false,
        	'getCache'		=> '',		//是否读取缓存数
        	'getNumType'	=> 'book'	//获取哪个  book 主题数   total帖子数
        );
        if(is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if ($productid) {
            //获取产品信息
            $proInfo = API_Item_Pro_Product::getSimpleInfo(array(
                    'proId' 	=> $productid,
                    'rtnCols' 	=> 'bbsProId,mainId'
            ));
             
            if(!$proInfo){
                return false;
            }
             
            //保持系列下的产品都是一个入口(主产品)
            $productid  = empty($proInfo['bbsProId']) ?  (empty($proInfo['mainId']) ? $productid : $proInfo['mainId']) : $proInfo['bbsProId'];
        }
        
    	if ($getCache) {
    		//获取数据
    		$redis = API_Item_Kv_Redis::getObj(array(
    				'serverName' => 'Default'
    		));
    		
    		$cateInfo = API_Item_Bbsv2_Board::getCateidByBoardid(array(
    				'boardid'		=> $boardid,
    				'bbsid'         => $bbsid,
    				'subid'         => $subid,
    		));
    		$cateid = $cateInfo['cateid'];
    		$listType = API_Item_Bbsv2_Board::getBookBoardType(array(
    				'cateid'    => $cateid,
    				'bbsid'     => $bbsid,
    				'boardid'   => $boardid,
    				'subid'     => $subid,
    				'productid' => $productid,
    				'manuid'    => $manuid,
    		));
    		switch($listType){
    			case 'product':
    				$id = $productid;
    				break;
    			case 'subProduct':
    				$id = $productid;
    				break;
    			case 'board':
    				$id = $boardid;
    				$listType = 'subcate';
    				break;
    			case 'manu':
    				$id = $boardid;
    				break;
    			case 'subManu':
    				$id = $manuid;
    				break;
    			case 'subSubManu':
    				$id = $manuid;
    				break;
    			case 'sub':
    				$id = $subid;
    				break;
    			case 'bbs':
    				$id = $boardid;
    				break;
    			case 'cate':
    				$id = $cateid;
    				break;
    			case 'custCate':
    				$id = $cateid;
    				break;
    			default:
    				$listType = 'subcate';
    				$id = $boardid;
    				break;
    		}
    		
    		if ($getNumType == 'total') {
    			//帖子数+回复数
    			$keyInfo = API_Item_Bbsv2_Key::getBoardBookNum(array(
    					'bbsid'        => $bbsid,
    					'listType'     => $listType,
    					'numType'      => $getNumType,
    					'id'           => $id,
    			));

    			$bookNum = $redis -> hGet (
    					$keyInfo['key'],
    					$keyInfo['smallKey']
    			);
    		} else {
    			$keyInfo = API_Item_Bbsv2_Key::listTotal(array(
    					'bbsid'        => $bbsid,
    					'listType'     => $listType,
    					'id'           => $id,
    					'selfBoardid'  => $boardid,
    					'type'         => $type,
    					'bookType'     => $bookType,
    			));
    			$bookNum = $redis -> hGet (
    					$keyInfo['key'],
    					$keyInfo['smallKey']
    			);
    		}
    		
    		if ($bookNum) {
    			return $bookNum;
    			die;
    		}
    	}
    	
        $where 		= ' forbid = 0 ';
        //获取分类信息条件
        $cateWhere 	= self::cateIf($options);
        if(false === $cateWhere){
            return false;
        }
    
        //获取是否有帖子类型筛选
        $typeWhere = self::bookTypeIf(array('type' => $type));
    
        $where .= $cateWhere ? " AND {$cateWhere}" : '';
        $where .= $typeWhere ? " AND {$typeWhere} ": '';
        $where .= $bookType  ? " AND book_type='{$bookType}' " : '';
        $where .= $sDate  ? " AND wdate >= '{$sDate}'" : '';
        $where .= $eDate  ? " AND wdate < '{$eDate}'" : ''; 
        $where .= $productid ? " AND productid='{$productid}' " : '';
    
        //获取表名
        $table = API_Item_Bbsv2_Table::getBookTable(array(
            'boardid' => $boardid
        ));
    
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
    
        $db  = API_DbAdv::instance(API_Item_Bbsv2_Book::getDbInfo(array('bbsid'=>$bbsid))->z_db_link);

        $bookNum = (int)$db -> getOne($sql);
             
        return $bookNum;
        
    }
    
    /**
     * 获取某个版块的帖子列表
     */
    public static function getBoardBookList($paramArr){
        $options = array(
            'bbsid'     => 0,   //dc 1, diy 2, nb 3 , pad 4, sj 5, 其他 6
            'cateid'    => 0,
            'boardid'   => 0,
            'manuid'    => 0,
            'productid' => 0,
            'subid'     => 0,
            'isGood'    => 0,   //只获取精华
            'isPic'     => 0,   //只获取有图
            'gtHits'    => 0,   //浏览大于 int
            'gtReplys'  => 0,   //回复大于 int
            'gtWdate'   => false, //发帖时间大于date
            'order'     => '',  //默认最新,可选有hits,replys,wdate  分别按点击和回复倒序排
            'isGetIntro'=> 0,   //是否获取帖子的简介内容(文本内容)
            'isGetImg'  => 0,   //是否获取帖子的图片地址
            'isGetcount'=> 0,   //是否获取总数
            'imgSize'   => '800x800', //图片大小
            'param'		=> '',	//其他自定义条件
            'start'     => 0,
            'num'       => 20,
        	'isFavourNum'   => 0,   //是否需要帖子的点赞数
        	'isWap'     => 0,      //是否是无线端，为了区分活动区的定制范围
        	'isApp'     => 0,      //如果是客户端把审核中的帖子也查出来
        	'userid'    => '',
            'noBookid'    => 0,//是否过滤谋篇帖子
            'https'     => false,  
            'isdebug'   => 0
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        
        if (!$bbsid) return false;
        //参数处理
        $gtHits   = (int)$gtHits;
        $gtReplys = (int)$gtReplys;
        
        //条件
        //获取分类信息条件
        $cateWhere 	= self::cateIf($options);
        if(false === $cateWhere){
            return false;
        }
        
        $where  = " 1 ".$param;
        $where .= ($boardid && $subid) ? " AND subid='{$subid}' " : '';
        $where .= $cateWhere ? " AND $cateWhere ": '';
        $where .= (int)$isGood ? ' AND good > 0 ' : '';
        $where .= (int)$isPic  ? ' AND is_pic = 1 '  : '';
        $where .= (int)$gtHits ? " AND hits>='{$gtHits}' " : '';
        $where .= (int)$gtReplys ? " AND replys>='{$gtReplys}' " : '';
        $where .= $gtWdate ? " AND wdate>='{$gtWdate}' ": '';
        $where .= $noBookid ? " AND id !='{$noBookid}'" : '';
         
        //活动增加条件筛选
        $where .= $bbsid == 6 && $boardid == 2 ? ($isWap ? ' AND ranger <> 0' : ' AND ranger <>1 ' ): '';
        
        //对客户端特殊处理
        if($isApp && $boardid && $userid){
            $where .= " AND forbid=0 or (forbid = 5 and userid = '{$userid}') ";
        }else{
            $where .= $boardid ? " AND forbid=0 " : '';
        }
        
        //排序
        $orderField = 'lastdate';
        if ($order) {
            switch ($order) {
            	case 'hits'   :
            	    $orderField = 'hits';
            	    break;
            	case 'replys' :
            	    $orderField = 'replys';
            	    break;
            	case 'wdate'  :
            	    $orderField = 'wdate';
            	    break;
            	default:
            	    $orderField = 'lastdate';
            	    break;
            }
        }
        $orderSql = " ORDER BY {$orderField} DESC";
        
        //获取表名
        if ($boardid) {
            $table = Helper_Table::getBookTable(array('boardid'=>$boardid));
        } elseif ($cateid) {
            $table = Helper_Table::getBookIndexTable(array('cateid'=>$cateid));
        } else {
            $table = Helper_Table::getBookIndexTable(array());
        }
        
        $link = Helper_Const::getDbLink(array('bbsid' => $bbsid));
    	$db = ZOL_Db::instance($link);
        $sql = "SELECT * FROM {$table} WHERE {$where} {$orderSql} LIMIT {$start},{$num} ";
        
        $bookList = $db->getAll($sql);
        
        if($isdebug){
            echo $sql;
        }
        
        if($isGetcount){
            $countsql = "SELECT count(*) FROM {$table} WHERE {$where}";
            $bookCount = $db->getOne($countsql);
            return $bookCount;
        }
        
        if (!$bookList) {
            return false;
        }
        
        if (is_array($bookList)) {
            foreach ($bookList as $key=>&$val) {
                $bookid  = isset($val['bookid']) ? (int)$val['bookid'] : $val['id'];
                $boardid = isset($val['boardid']) ? $val['boardid'] : $boardid;
                $val['bbsid']  = $bbsid;
                $val['id']     = $bookid;
                $val['boardid'] = $boardid;
                 
                $baseInfo = Helper_Book::getBookBaseInfo(array(
                    'bbsid'   => $bbsid,
                    'boardid' => $boardid,
                    'bookid'  => $bookid
                ));
                
                if (!$baseInfo) continue;
                
                $val = array_merge($val,$baseInfo);
                $userInfo = ZOL_Api::run("User.Base.getUserInfo" , array(
                    'userid'         => $val['userid'],        #userid
                ));

                $bookList[$key]['nickname'] = $userInfo['nickName'];
                $bookList[$key]['photo'] = $userInfo['photo'];
                //获取内容
                if ($isGetIntro) {
                    //从论坛缓存中拿内容
                    $keyInfo = array(
                        'key'        => "community:book_content_{$bbsid}_{$boardid}_{$bookid}",
                        'module'     => 'bbs_session_v2',
                        'tbl'        => 'bbs',
                    );
                    $content    = ZOL_Api::run("Kv.MongoCenter.get", array(
                        'module' => $keyInfo['module'],
                        'tbl'    => $keyInfo['tbl'],
                        'key'    => $keyInfo['key'],
                    ));
                    if ($content){ #缓存中得直接去标签
                        $content = trim(strip_tags($content['content']));
                    }else{
                        $content = Helper_Book::getBookContent(array(
                            'bbsid'   => $bbsid,
                            'boardid' => $boardid,
                            'bookid'  => $bookid
                        ));
                        $content = trim(strip_tags(htmlspecialchars_decode($content)));
                        $pattern = array(
                            '/\[img\](\d+)#?(\d+)?#?(\d?)\[\/img\]/i',
                            '/\[img\](.*)\[\/img\]/iU',
                            '/\[attach\](\d+)\[\/attach\]/i',
                            '/&nbsp;/i'
                        );
                        $content = preg_replace($pattern, '', $content);
                    }
                     
                    $val['content'] = $content;
                }
                 
                $bookList[$key]['url'] = Libs_Bbs_Links::getBookUrl(array(
                    'bbsid'   => $bbsid,
                    'boardid' => $boardid,
                    'bookid'  => $bookid
                ));
                
                //帖子点赞数
                if($isFavourNum) {
                	$favourInfo = Helper_Book::getFavourInfo(array(
                    'bbsid'   => $bbsid,
                    'boardid' => $boardid,
                    'bookid'  => $bookid
                ));
                	$bookList[$key]['favour'] = (int)$favourInfo['num'];
                }
                
                //获取图片信息
                if($isGetImg) {
                    $imgInfo = Helper_Image::getBookPicInfo(array(
                        'bbsid'   => $bbsid,
                        'boardid' => $boardid,
                        'bookid'  => $bookid
                    ));
                    if ($imgInfo && is_array($imgInfo)) {
                        foreach ($imgInfo as $k=>$img) {
                            if (preg_match('#^(http://|https://)#iUs', $img['pic'])) {
                                $path = $img['pic'];
                            }else {
                                $path = ZOL_Api::run("Image.Util.getImgUrl" , array(
                                    'module'         => 'bbswater',    #业务类型名称
                                    'fileName'       => $img['pic'],   #文件名
                                    'size'           => $imgSize,         #尺寸
                                    'https'          => $https
                                ));
                            }

                            $val['img'][] = $path;
                        }
                    }
                }
            }
            unset($val);
        }
        
        return $bookList;
    }
    
    /**
     * 获取
     */
    public static function getBbsBookNum($paramArr = array()){
        $options = array(
            'bbsid'		=> 0,
            'boardid'	=> 0,
            'listType'    => 'bbs',
            'id' => 5
        );
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
        //获取数据
        $redis = API_Item_Kv_Redis::getObj(array(
            'serverName' => 'RubbishKill'
        ));
       
        $keyInfo = API_Item_Bbsv2_Key::listTotal($a = array(
    					'bbsid'        => $bbsid,
    					'listType'     => $listType,
    					'id'           => $id,
    					'selfBoardid'  => $boardid,
    					'type'         => "",
    					'bookType'     => 0,
    			));

		$bookNum = $redis -> hGet (
			$keyInfo['key'],
			$keyInfo['smallKey']
		);
    
        return $bookNum;
    }
}