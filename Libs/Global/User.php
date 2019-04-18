<?php
/**
* 用户相关
* @author wiki <wu.kun@zol.com.cn>
* @copyright (c)
*/

class Libs_Global_User
{
	const ZOL_USER_KEY = 'sa^2fa*%mdpyw$@4';
	/**
	* @var ZOL_Product_Lib_User
	*/
	private static $_instance;
	
	private static $_userId;
	
	private static $_userInfo;
	/**
	* 论坛库
	* @var ZOL_Db_User
	*/
	private static $_dbDiscussion;
	
	/**
	* 用户库
	* @var ZOL_Db_User
	*/
	private static $_dbUser;
    private static $_dbUserWrite;
    private static $_dbUserLog;
	private static $_userLevelArr = array(
		'god'  => array('img' => 'super_sun', 'name' => '超级太阳'),
		'sun'  => array('img' => 'time_sun', 'name' => '太阳'),
		'moon' => array('img' => 'time_yueliang', 'name' => '月亮'),
		'star' => array('img' => 'time_star', 'name' => '星星'),
	);
	/**
	* 缓存方法结果
	* 
	* @var array
	*/
	private static $_cache;
	
	public function __construct($userId = 0)
	{
		if ($userId) {
			self::$_userInfo = self::getUserFace($userId);
			self::$_userId = $userId;
		}
	}
	
	/**
	* 单例
	* @return ZOL_Product_Lib_User
	*/
	public static function instance()
	{
		if (self::$_instance == null) {
			$className = get_called_class();
			self::$_instance = new $className;
		}
		return self::$_instance;
	}
	
	/**
	* 初始化
	*/
	public static function init()
	{
		self::loadDb();
	}
	
	/**
	* 加载数据库
	*/
	public static function loadDb()
	{
		self::$_dbDiscussion = Db_Discussion::instance();
		self::$_dbUser       = Db_User::instance();
        self::$_dbUserWrite  = Db_UserWrite::instance();
        self::$_dbUserLog    = Db_Userlog::instance();
	}
	
	/**
	* 获取用户头像的数据
	* @param userid $userId 用户ID
	* @return array
	*/
	public static function getUserFaceData($userId)
	{
        if(!$userId)return false;
        $userData = ZOL_Api::run("User.Base.getUserInfo" , array(
            'userid'         => $userId,       #userid
        ));
        $retData = array();
        if(!empty($userData)){
            $retData['id'] = $userData['photoId'];
            $retData['isPhoto'] = $userData['isPhoto'];
        }
        return $retData;
	}

	/**
	* 批量获取用户头像的数据
	* @param $userIdArr 用户ID数组
	* @return array
	*/
	public static function getMulUserFaceData($userIdArr)
	{
        if(empty($userIdArr))return false;
        $outArr = array();
        foreach ($userIdArr as $userId){
            $userData = ZOL_Api::run("User.Base.getUserInfo" , array(
                'userid'         => $userId,       #userid
            ));
            if(!empty($userData)){
                $outArr[$userId]['id'] = $userData['sid'];
                $outArr[$userId]['isPhoto'] = $userData['isPhoto'];
                $outArr[$userId]['userId'] = $userId;
            }
        }
        return $outArr;
	}
	
	/**
	* 获取用户基本信息
	*/
	public static function getUserInfo($userId = 0)
	{
		#上次取过,缓存一下
		$ckey = 'getUserInfo';
		if(!isset(self::$_cache[$ckey]))self::$_cache[$ckey] = array();

		if (isset(self::$_cache[$ckey][$userId])) {
			return self::$_cache[$ckey][$userId];
		}
        
		self::init();
		$faceInfo = self::getUserFaceData($userId);
		
        $sql = "SELECT userid,nickname nickName, fullName, Sex sex, RegisterDate regDate, Birthday birthday, Employment emp, Education edu, Province province FROM UserInfo WHERE UserID='{$userId}'";
        $userInfo = self::$_dbUser->getRow($sql);
        
		if ($userInfo) {
			$userInfo['id']      = $faceInfo['id'];
			$userInfo['isPhoto'] = $faceInfo['isPhoto'];
			$userInfo['face']    = self::getUserFace($userInfo['id'], $userInfo['userid'],$userInfo['isPhoto']);	
            $scoreSql = "SELECT  score FROM  z_user_score   WHERE userid = '{$userId}'";
            $scoreData = self::$_dbUser->getOne($scoreSql);
            $score = !empty($scoreData) ? $scoreData : 0;
            $userInfo['score'] = $score;
		}
		self::$_cache[$ckey][$userId] = $userInfo;
		return $userInfo;
	}


	/**
	* 获取多个用户的基本信息
	*/
	public static function getMulUserInfo($userIdArr)
	{
		if(empty($userIdArr))return false;

		$userIds = $comma = '';
		$ckey = 'getUserInfo';
		if(!isset(self::$_cache[$ckey]))self::$_cache[$ckey] = array();
		
		$outArr = array();
		foreach($userIdArr as $u){
			if (isset(self::$_cache[$ckey][$u])) {#判断是否从内存中取得用户
				$outArr[$u] = self::$_cache[$ckey][$u];
				continue;
			}
            #没在内存中的用户
            self::init();
            $userInfo = self::getUserInfo($u);
            $outArr[$u] = $userInfo;
			self::$_cache[$ckey][$u] = $userInfo;
		}

		return $outArr;

	}

    /**
	* 获取用户昵称
	*/
	public static function getNickName($userStr)
	{
		self::init();
		$sql = "SELECT UserID,nickname FROM UserInfo WHERE UserID in ({$userStr})";
		$rs  = self::$_dbUser->getAll($sql);
        $userArr = '';
        if ($rs) {
            foreach ($rs as $value) {
                $userArr[$value['UserID']] = 
                $value['nickname'] ? $value['nickname'] : $value['UserID'];
            }
        }
		return $userArr;
	}
    
    /**
    * 获取用户头像是否是真实头像
    */
    public static function getUserRealPhoto($userId = 0)
    {
        if ($userId && $userId == self::$_userId && self::$_userInfo) {
            return self::$_userInfo;
        }
        self::init();
        
        $sql = "SELECT real_photo from z_user_extend where userid ='{$userId}'";
        $realPhoto = self::$_dbUser->getRow($sql);

        if ($realPhoto) {
            $isRealPhoto = $realPhoto['real_photo'];
        } else {
            $isRealPhoto = '0';
        }
        return $isRealPhoto;
    }
	
	
	/**
	* 获取用户地区
	*/
	public static function getUserArea($userId)
	{
		if (!$userId) {
			return false;
		}
		
		$sql = "SELECT e.Name provinceName, c.name townName,d.name cityName 
				from UserInfo a 
					LEFT JOIN UserInfo_pro_town_city b ON a.UserID=b.userid
					LEFT JOIN UserInfo_town c ON b.town_id=c.id 
					LEFT JOIN UserInfo_city d ON b.city_id=d.id
					LEFT JOIN Province e ON a.Province=e.SID
			WHERE a.UserID='{$userId}'";
		self::loadDb();
		return self::$_dbUser->getRow($sql);
	}
	
	/**
	* 用户其它信息类别
	* @param int 学历ID
	* @param enum 类别 {edu|emp|income|industry|province}
	* @return array
	*/
	public static function getCate($id = 0, $type = 'edu')
	{
		$tables = array(
			'edu' => 'Education',
			'emp' => 'Employment',
			'income' => 'Income',
			'industry' => 'Industry',
			'province' => 'Province'
		);
		
		if (!isset($tables[$type])) {
			return false;
		}
		$table = $tables[$type];
		$key = 'get' . $table;
		if (isset(self::$_cache[$key][$id])) {
			return self::$_cache[$key][$id];
		}
		
		if ($id) {
			$conditions = " AND SID='{$id}'";
			$orderBy = '';
			$cols = "Name";
			$method = 'getOne';
		} else {
			$orderBy = " ORDER BY Sequence";
			$conditions = '';
			$cols = 'SID id, Name `name`';
			$method = 'getAll';
		}
		$conditions .= ' AND Status=1';
		
		$sql = "SELECT {$cols} FROM {$table} WHERE 1 {$conditions} {$orderBy}";
		$data = ZOL_Db_User::instance()->$method($sql);
		$_data = array();
		if (is_array($data)) {
			foreach ($data as $row) {
				$_data[$row['id']] = $row['name'];
			}
		}
		self::$_cache[$key][$id] = $_data;
		return self::$_cache[$key][$id];
	}
	
	/**
	* 获取职业
	* @param int $id 职业ID
	* @return array
	*/
	public static function getEmp($id = 0)
	{
		return self::getCate($id, 'emp');
	}
	
	/**
	* 获取学历
	* @param int $id 学历ID
	* @return array
	*/
	public static function getEdu($id = 0)
	{
		return self::getCate($id, 'edu');
	}
	
	/**
	* 获取省份
	* 
	* @param int $id 省份ID
	* @return array
	*/
	public static function getProvince($id = 0)
	{
		return self::getCate($id, 'province');
	}

	/**
	* 获取所有省份
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011年3月8日13:53:55
	*/
	public static function getAllProvince()
	{
        self::init();
        $allProvinceSql = 'select SID id, Name name from Province where Status = 1 order by Sequence ASC';
        $allProvinceRes = self::$_dbUser->getAll($allProvinceSql);
		return $allProvinceRes;
	}

	/**
	* 获取当前省份下所有城市
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011年3月8日14:14:17
	*/
	public static function getAllTown($provinceId)
	{
        $allCitySql = 'select id, name from UserInfo_town where pro_id = ' . $provinceId . ' order by sequence';
        $allCityRes = self::$_dbUser->getAll($allCitySql);
		return $allCityRes;
	}

	/**
	* 获取当前省份下所有城市
    * @author wang.tao5@zol.com.cn
	* @return array
    * @copyright 2011年3月8日14:14:17
	*/
	public static function getAllCity($townId)
	{
        $allCitySql = 'select id, name from UserInfo_city where town_id = ' . $townId . ' order by sequence';
        $allCityRes = self::$_dbUser->getAll($allCitySql);
		return $allCityRes;
	}

	/**
	* 更新用户信息
    * @author wang.tao5@zol.com.cn
	* @return bool
    * @copyright 2011年3月8日16:49:01
	*/
	public static function getUpdateUserInfo($userInfo)
	{
        self::init();

        #更新用户信息
        $brithday = $userInfo['year'] . '-' . $userInfo['month'] . '-' . $userInfo['day'];
        'select UserID, FullName, nickname, Sex, Birthday, Province from UserInfo where userid = ';
        $updateInfoSql = 'update UserInfo set FullName = "' . $userInfo['fullName'] .
                                            '", nickname = "' . $userInfo['nickName'] .
                                            '", Sex = ' . $userInfo['sex'] .
                                            ', Birthday = "' . $brithday .
                                            '" where UserID = "' . $userInfo['id'] . '"';
        self::$_dbUserWrite->query($updateInfoSql);

        #省份ID
        $provinceIdSql = 'select SID from Province where Name = "' . $userInfo['province'] . '"';
        $provinceIdRes = self::$_dbUser->getAll($provinceIdSql);

        #城市ID
        $townIdSql     = 'select id from UserInfo_town where pro_id = ' . $provinceIdRes[0]['SID'] . ' and name = "' . $userInfo['town'] . '"';
        $townIdRes     = self::$_dbUser->getAll($townIdSql);

        #县级区域ID
        $cityIdSql     = 'select id from UserInfo_city where pro_id = ' . $provinceIdRes[0]['SID'] . ' and town_id = ' . $townIdRes[0]['id'] . ' and name = "' . $userInfo['city'] .'"';
        $cityIdRes     = self::$_dbUser->getAll($cityIdSql);

        #更新用户所在区域
        $updateAreaSql = 'update UserInfo_pro_town_city set pro_id = ' . $provinceIdRes[0]['SID'] . ', town_id = ' . $townIdRes[0]['id'] . ', city_id = ' . $cityIdRes[0]['id'] .' where userid = "' . $userInfo['id'] . '"';
        self::$_dbUserWrite->query($updateAreaSql);
		return $allCityRes;
	}
	
	/**
	* 获取用户头像
	*/
	public static function getUserFace($uid = 0, $userId='',$isPhoto = 0, $size = 50)
	{
		if ($isPhoto === 'USERID') {
			$faceInfo = self::getUserFaceData($uid);
			$uid = $faceInfo['id'];
			$isPhoto = $faceInfo['isPhoto'];
		}
        
        $baseFaceArr = array(
            50=>"http://icon.zol-img.com.cn/group/default_zoler/zoler_50.jpg",
            30=>"http://icon.zol-img.com.cn/group/default_zoler/zoler_30.jpg",
        );
		
        if ($isPhoto) {
            if($userId){
                $userData = ZOL_Api::run("User.Base.getUserInfo" , array(#因为点评列表和个人中心的头像不一致,故统一改用私有云
                    'userid'         => $userId,       #userid
                ));
                $face = $userData['photo'];
            }else{
                $face = 'http://8.zol-img.com.cn/bbs/user_photo/'.ceil($uid/1000).'/'.$uid.'_s.jpg';
            }
        } else {
            $face = isset($baseFaceArr[$size]) ? $baseFaceArr[$size] : $baseFaceArr[50];
        }
		return $face;
	}
	
	/**
	* 获取用户头像
	*/
	public static function getFace($uid)
	{
		$face = 'http://8.zol-img.com.cn/bbs/user_photo/' . ceil($uid / 1000) . '/' . $uid . '_s.jpg';
		return $face;
	}	
	
	/**
	* 检查用户状态
	* 
	* @param mixed $userId 网友id $_COOKIE['zol_userid']
	* @param mixed $checkId 登录后的验证码 $_COOKIE['zol_check']
	* @param mixed $cipher userid 和 checkid的加密字符串 $_COOKIE['zol_cipher']
	* @return boolean 登录状态
	*/
	public static function checkUserStatus($userId, $checkId, $cipher)
	{
		if(!$userId || !$checkId || !$cipher) {
			return false;
		}
		
		$zcipher = md5(md5(self::ZOL_USER_KEY . $checkId) . $userId . self::ZOL_USER_KEY);
		return ($zcipher == $cipher);
	}

    /**
     * 得到用户的省份ID
     * @param <type> $userId
     * @author wang.tao5@zol.com.cn
     * @copyright 2011年3月10日11:59:22
     */
    public static function getUserProvince ($userId)
    {
        self::init();
        $provinceSql = 'select province from UserInfo where UserID = "' . $userId . '"';
        $provinceRes = self::$_dbUser->getOne($provinceSql);
        return $provinceRes;
    }
   /**
     * 得到用户的性别和年龄
     * @param <type> $userId
     * @author wang.tao5@zol.com.cn
     * @copyright 2011年3月11日16:31:24
     */
    public static function getUserSexBirth ($userId)
    {
        self::init();
        $sexBirthSql = 'select Sex sex, Birthday birthday from UserInfo where UserID = "' . $userId . '"';
        $sexBirthRes = self::$_dbUser->getRow($sexBirthSql);
        return $sexBirthRes;
    }

   /**
     * 得到用户的级别
     * @param string $userId
     * @param string $sex
     * @author wang.tao5@zol.com.cn
     * @copyright 2011年3月11日16:31:24
     */
    public static function getUserLevel($score,$sex)
    {

		if($score<50) return "考生";
		elseif($score<150) return "秀才";
		elseif($score<350) return "举人";
		elseif($score<750) return "进士";
		elseif($score<1550) return "亚员";
		elseif($score<3150) return "探花";
		elseif($score<6350) return "榜眼";
		elseif($score<12750) return "状元";
		elseif($score<25550) return "九品";
		elseif($score<51150) return "八品";
		elseif($score<102350) return "七品";
		elseif($score<204750) return "六品";
		elseif($score<409550) return "五品";
		elseif($score<819150) return "四品";
		elseif($score<1639350) return "三品";
		elseif($score<3278750) return "二品";
		elseif($score<6557500) return "一品";
		elseif($score<13115000) return "皇帝";
			
    }

	/**
	* 获得用户博客星级
	*/
	public static function getUserLevelStar($score)
	{
		$levelInfo = self::getLevelInfo( $score);
		$imgUrl = 'http://icon.zol-img.com.cn/bbs/detail/';
		if(!is_array($levelInfo)){
			return false;
		}

		$levelInfo['userStar'] = '';
		$imgStr = '<img src="' . $imgUrl . '{IMG}.gif" alt="'.$levelInfo['z_name'] . '[' . $levelInfo['z_score'] . '经验]" />';
		$userStar = '';
		foreach (self::$_userLevelArr as $key => $val) {
			$col = 'z_' . $key;
			if (!empty($levelInfo[$col])) {
				$_img = str_replace('{IMG}', $val['img'], $imgStr);
				$userStar .= str_repeat($_img, $levelInfo[$col]);
			}
		}

		return $userStar;
	}
	/**
	 * 获取用户的等级的具体信息,包括太阳和星星
	 */
	public static function getLevelInfo($score)
	{

		$_dbDiscussion = Db_Discussion::instance();
		//获得当前用户等级
		$level	= (int)self::getLevelValue($score);

		#是否刚才已经取过
		$ckey = 'getLevelInfo';
		if (isset(self::$_cache[$ckey]) && isset(self::$_cache[$ckey][$level])) {
			return self::$_cache[$ckey][$level];
		}
		
		//获得当前登记信息
		$sql = "SELECT `z_level`,`z_name`,`z_score`,`z_god`,`z_sun`,`z_moon`,`z_star`
				FROM z_rank WHERE `z_level`={$level}";

		$levelInfo = $_dbDiscussion->getRow($sql);
		
		self::$_cache[$ckey][$level] = $levelInfo;

		return $levelInfo;
	}

	/**
	 * 获取用户的等级值
	 */
	public static function getLevelValue($score)
	{
		$score = (!empty($score) ? (int)$score : 0);

		$userScore = $score;
		for($level=1; $level<=18; $level++) {
			$levelScore = pow(2,$level-1)*100-50;
			if( $userScore	< $levelScore ){
				return $level;
			}
		}
		return 18;
	}

    public static function Login($User,$Password) {


	   self::$_dbUser       = Db_User::instance();
       if ($User){
          $backUrl=$_SERVER["HTTP_REFERER"];
          //第一步：验证用户名和密码
          $strsql="select UserID,Password,nickname,checkcode,UNIX_TIMESTAMP(LastLogin) as lastlogin,is_del , sid
                   from UserInfo where UserID = '{$User}'";
          $flag = 0;
          if ($rows = self::$_dbUser->getRow($strsql)){

                $UserID = $rows['UserID'];
                $pwd = $rows['Password'];
                $is_del = $rows['is_del'];
                $nickname = $rows['nickname'];

                $md_pwd = md5(md5($Password."zol").$User);
                $md_pwd = substr($md_pwd,0,16);
                if ((($pwd ==$Password)&&($is_del==0)) || (($pwd ==$md_pwd)&&($is_del==0))){
                    //$check = $rows['checkcode'];

					srand((double)microtime()*1000000);
					$check      = rand();

                    $cipher = md5(md5(self::ZOL_USER_KEY.$check).$UserID.self::ZOL_USER_KEY);
                    setcookie("zol_cipher", $cipher, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_userid", $UserID, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_check", $check, SYSTEM_TIME + 86400,"/",".zol.com.cn");
                    setcookie("zol_nickname", $nickname, SYSTEM_TIME + 86400,"/",".zol.com.cn");
					$sql = "update UserInfo set checkcode = '$check',LastLogin='".SYSTEM_DATE."' where UserID = '$UserID'";
					self::$_dbUser->query($sql);


					 //支持多环境登录
					if ($rows['sid']) {
						$dateTime   = SYSTEM_TIME;
						$checkcodeTable = "z_checkcode_".ceil($rows['sid']/1000000);
						$sql = "CREATE TABLE if not exists `{$checkcodeTable}` (
							   `z_id` int(10) NOT NULL auto_increment,
							   `z_uid` int(10) unsigned NOT NULL default '0' COMMENT '用户id',
							   `z_checkcode` bigint(20) unsigned default NULL COMMENT '登录check码',
							   `z_time` int(10) unsigned NOT NULL default '0' COMMENT '时间',
							   PRIMARY KEY  (`z_id`),
							   KEY `uid` (`z_uid`,`z_checkcode`),
							   KEY `time` (`z_time`)
							 ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 comment '登录check码表'";
						self::$_dbUser->query($sql);

						$sql = "insert into {$checkcodeTable} (z_uid, z_checkcode, z_time) values ({$rows['sid']}, {$check}, {$dateTime}) ";
						self::$_dbUser->query($sql);

						$sql = "select count(*) from {$checkcodeTable} where z_uid={$rows['sid']} ";
						$totalCheckNum = (int)self::$_dbUser->getOne($sql);

						if ($totalCheckNum > 10) {
							$deleteNum = $totalCheckNum - 10;
							$sql = "delete from {$checkcodeTable} where z_uid={$rows['sid']} order by z_id asc limit {$deleteNum}";
							self::$_dbUser->query($sql);

						}
					}

					/* 记录登录 */
					$login_log_table = "user_login_log".date("Y");
					$sql = "CREATE TABLE if not exists $login_log_table (
							 `sid` int(11) NOT NULL auto_increment,
							 `userid` varchar(20) NOT NULL default '',
							 `ip` varchar(15) NOT NULL default '',
							 `wdate` datetime NOT NULL default '0000-00-00 00:00:00',
							 `ref_url` varchar(100) NOT NULL default '',
							 PRIMARY KEY  (`sid`),
							 KEY `userid` (`userid`),
							 KEY `wdate` (`wdate`)
							) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='网友登录记录表'";
					self::$_dbUser->query($sql);
					
					$sql = "insert into $login_log_table (userid,ip,wdate,ref_url) values ('$UserID','".$_SERVER["REMOTE_ADDR"]."','".SYSTEM_DATE."','".$_SERVER['REQUEST_URL']."')";
					self::$_dbUser->query($sql);

                    $return_val = 1;  //表示用户名和密码正确，登录成功

                 }else{
                    $return_val = 0;  //密码错误
                 }
              }else{
                 $return_val = 0;  //用户名不存在
              }
           }else{
              $return_val = -1;  //没有输入用户名
           }

         return $return_val;

    }
    
    /**
	* 获取有帮助的相关用户数据
	*/
	public static function getHelpUserInfo($reviewId)
	{
        $dbProduct = Db_Product::instance();
		$haveHelpSql = "select user_id from review_vote where rev_id=".$reviewId." order by user_id desc limit 40";
        $haveHelpArr = $dbProduct->getAll($haveHelpSql);
        $haveHelpAllUserInfo = array();
        if($haveHelpArr){
            #批量获得用户的信息
            $haveHelpUserIdArr = array(); #存储用户的ID
            $haveHelpUserIdArr_ = array(); #存储用户的ID
            $haveHelpUserIdArr_t = array(); #存储用户的ID
            foreach($haveHelpArr as $d){
                if ($d['user_id']) {
                    $haveHelpUserIdArr[] = $d['user_id'];
                }
            }
            $haveHelpAllUserInfo = Libs_Global_User::getMulUserInfo($haveHelpUserIdArr);
        }
        if (!$haveHelpAllUserInfo) {
            return FALSE;
        }
        foreach($haveHelpAllUserInfo as $id=>$vals){
            if($vals['face'] != "http://icon.zol-img.com.cn/photo/zoler_50.jpg"){
                $haveHelpUserIdArr_[$id]['face'] = $vals['face'];
                $haveHelpUserIdArr_[$id]['url'] = Libs_Global_Url::getMyUrl(array('userId'=>$vals['userid']));
                $haveHelpUserIdArr_[$id]['userId'] = $vals['userid'];
            }else{
                $haveHelpUserIdArr_t[$id]['face'] = "http://icon.zol-img.com.cn/photo/zoler_50.jpg";
                $haveHelpUserIdArr_t[$id]['url'] = Libs_Global_Url::getMyUrl(array('userId'=>$vals['userid']));
                $haveHelpUserIdArr_t[$id]['userId'] = $vals['userid'];
            }
        }
        
        return $haveHelpUserIdArr_+$haveHelpUserIdArr_t;
	}
    
    /**
	 * ip请求次数
	 */
	public static function getIpCount($paramArr)
	{
	    $options = array(
	        'ip'      => '',
	        'addtime' => strtotime('-1 day')
	    );
	    if (is_array($paramArr))
	        $options = array_merge($options, $paramArr);
	    extract($options);
	    if (! $ip)
	        return 0;
	    self::init();
	    // 拼接sql条件
	    $whereSql = " where z_ip='{$ip}' and z_addtime>={$addtime}";
	
	    $comSql = " select count(*) num from z_phone_check_records  {$whereSql} ";
	    // 查询数据
	    return self::$_dbUser->getOne($comSql);
	}
    
    /**
	 * 手机短息次数
	 */
	public static function getSmsCount($paramArr)
	{
	    $options = array(
	        'wdate' => date('Y-m-d'),
	        'phone' => ""
	    );
	    if (is_array($paramArr))
	        $options = array_merge($options, $paramArr);
	    extract($options);
	    if (! $phone)
	        return array();
	    // 拼接sql条件
	    self::init();
	    $tableName = "sms_log_" . date('Y');
	    $dateLimit = date('Y-m-d');
	    $whereSql = "  where telno='{$phone}' and wdate>'{$wdate}' order by wdate desc limit 10";
	
	    $comSql = " select count(id) from {$tableName} {$whereSql} ";
	    // 查询数据
	    $rs = self::$_dbUserLog->getOne($comSql);
	    return $rs;
	}
    
    /**
	 * 手机验证最后的时间
	 */
	public static function getSmsLast($paramArr)
	{
	    $options = array(
	        'phone' => ""
	    );
	    if (is_array($paramArr))
	        $options = array_merge($options, $paramArr);
	    extract($options);
	    if (! $phone)
	        return 0;
	    // 拼接sql条件
	
	    $whereSql = " where z_phone='{$phone}'";
	
	    $comSql = " select * from z_phone_check_records {$whereSql} ";
	    // 查询数据
	    $rs = self::$_dbUser->getRow($comSql);
	    return $rs ? $rs['z_addtime'] : 0;
	}
}