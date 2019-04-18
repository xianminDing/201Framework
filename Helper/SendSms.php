<?php
/**
* 发送短信业务操作类
* @date: 2018年10月18日 上午10:17:39
* @author: SYJ
*/
class Helper_SendSms extends Helper_Abstract{
    
    /**
    * 获取用户签到信息
    * @date: 2018年10月18日 上午10:17:39
    * @author: SYJ
    * @param: variable
    * @return:
    */
    public static function getSendCodeCount($paramArr){
        $options = array(
            'phone' => 0,
            'time'  => 0
        );
        
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$phone || !$time){
            return false;
        }
        
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "SELECT COUNT(id) FROM user_quick_login_code
                WHERE addtime>'{$time}' AND phone='{$phone}' LIMIT 1";
        return $dbLink->getOne($sql);
    }
    
    /**
    * 获取某手机号最新发送的验证码信息
    * @date: 2018年10月18日 上午10:31:30
    * @author: SYJ
    * @param: variable
    * @return:
    */
    public static function getLastSendCodeTime($paramArr){
        $options = array(
            'phone' => 0
        );
    
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$phone){
            return false;
        }
    
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "SELECT addtime FROM user_quick_login_code
                WHERE phone='{$phone}'
                ORDER BY addtime DESC LIMIT 1";
        return $dbLink->getOne($sql);
    }
    
    /**
    * 清理一个月之前的记录
    * @date: 2018年10月18日 上午10:59:23
    * @author: dell
    * @param: variable
    * @return:
    */
    public static function clearOneMonthAgoCodeLog(){
        $time = strtotime('-30 days');
        $sql  = "DELETE FROM user_quick_login_code
                WHERE addtime<{$time}";
        $dbLink = ZOL_Db::instance('Db_User');
        return $dbLink->query($sql);
    }
    
    /**
    * 添加发送短信记录
    * @date: 2018年10月18日 上午11:00:09
    * @author: SYJ
    * @param: variable
    * @return:
    */
    public static function addCodeInfo($paramArr){
        $options = array(
            'phone' => 0, #手机号
            'code'  => 0, #验证码
            'ip'    => '',#ip地址
        );
        
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
    
        if (!$phone || !$code) {
            return false;
        }
        
        $addtime = time();
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "INSERT INTO user_quick_login_code(phone,`code`,ip,z_from,addtime)
                VALUES('{$phone}','{$code}','{$ip}',2,'{$addtime}')";
        return $dbLink->query($sql);
    }
    
    /**
    * 添加发送短信日记
    * @date: 2018年10月18日 上午11:13:27
    * @author: dell
    * @param: variable
    * @return:
    */
    public static function addSendCodeLog($paramArr){
        $options = array(
            'phone' => '',
            'code' => '',
            'ip' => '',
        );
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
    
        if (!$phone || !$code) {
            return false;
        }
        
        $year  = date('Y');
        $table = self::_getTableName($year);
        $addtime = time();
        
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "INSERT INTO `{$table}`(phone,`code`,ip,z_from,addtime)
                VALUES('{$phone}','{$code}','{$ip}',2,'{$addtime}')";
        return $dbLink->query($sql);
    }
    
    /**
    * 获取日志表名
    * @date: 2018年10月18日 上午11:17:36
    * @author: dell
    * @param: variable
    * @return:
    */
    private static function _getTableName($paramArr){
        $options = array(
            'year' => ''
        );
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
        
        if (!$year) {
            return false;
        }
        
        $table = 'user_quick_code_log'.$year;
        
        //检测并创建快捷登录发送验证码日志表
        self::_createCodeLogTable($table);
    
        return $table;
    }
    
    /**
    * 创建数据表
    * @date: 2018年10月18日 上午11:19:04
    * @author: dell
    * @param: variable
    * @return:
    */
    private static function _createCodeLogTable($paramArr){
        $options = array(
            'table' => ''
        );
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
        
        if (!$table) {
            return false;
        }
        
        $dbLink = ZOL_Db::instance('Db_User');
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
        `id` int(10) unsigned NOT NULL auto_increment,
        `phone` varchar(20) NOT NULL COMMENT '手机号',
        `code` varchar(6) NOT NULL COMMENT '验证码',
        `ip` varchar(15) NOT NULL COMMENT 'ip地址',
        `ref` varchar(200) NOT NULL COMMENT '来源地址',
        `z_from` tinyint(2) unsigned default '1' COMMENT '来源1：wap，2：客户端,3:PC',
        `addtime` int(10) unsigned default '0' COMMENT '添加时间',
        PRIMARY KEY  (`id`),
        KEY `phone` USING BTREE (`phone`),
        KEY `addtime` USING BTREE (`addtime`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='快捷登录发送验证码日志文件'";
    
        return $dbLink->query($sql);
    }
    
    /**
     * 添加验证用户记录
     * @date: 2018年10月18日 上午11:00:09
     * @author: SYJ
     * @param: variable
     * @return:
     */
    public static function addValidateUser($paramArr){
        $options = array(
            'userid' => 0, #手机号
            'phone'  => 0
        );
    
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
    
        if (!$userid || !$phone) {
            return false;
        }
    
        //获取用户记录
        $true = self::getValidateUser(array(
            'userid' => $userid,
            'phone'  => $phone
        ));
        
        if($true){
            return false;
        }
        
        $addtime = time();
        $dbLink = ZOL_Db::instance('Db_Userlog');
        $sql  = "INSERT INTO double_twelve_activity(userid,phone,addtime)
        VALUES('{$userid}','{$phone}','{$addtime}')";
        return $dbLink->query($sql);
    }
    
    /**
     * 获取验证用户记录
     * @date: 2018年10月18日 上午11:00:09
     * @author: SYJ
     * @param: variable
     * @return:
     */
    public static function getValidateUser($paramArr){
        $options = array(
            'userid' => '',
            'phone'  => 0
        );
    
        if (is_array($paramArr)) {
            $options = array_merge($options, $paramArr);
        }
        extract($options);
    
        if (!$userid || !$phone) {
            return false;
        }
    
        $addtime = time();
        $dbLink = ZOL_Db::instance('Db_Userlog');
        $sql  = "SELECT id FROM double_twelve_activity WHERE userid='{$userid}' or phone='{$phone}'";
        return $dbLink->getOne($sql);
    }
    
    
    /**
     * 获取某手机号最新发送的验证码信息
     * @date: 2018年10月18日 上午10:31:30
     * @author: SYJ
     * @param: variable
     * @return:
     */
    public static function getLastSendCode($paramArr){
        $options = array(
            'phone' => 0,
            'code'  => 0
        );
    
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$phone || !$code){
            return false;
        }
    
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "SELECT addtime FROM user_quick_login_code
        WHERE phone='{$phone}'  AND code='{$code}' 
        ORDER BY addtime DESC LIMIT 1";
        return $dbLink->getOne($sql);
    }
    
    /**
    * 修改手机验证码使用状态
    * @date: 2018年12月12日 上午11:35:10
    * @author: dell
    * @param: variable
    * @return:
    */
    public static function updateCodeStatus($paramArr){
        $options = array(
            'phone'  => 0,
            'code'   => 0,
            'status' => 1
        );
    
        if (is_array($paramArr))$options = array_merge($options, $paramArr);
        extract($options);
    
        if(!$phone || !$code){
            return false;
        }
        
        $time = time();
        $dbLink = ZOL_Db::instance('Db_User');
        $sql  = "UPDATE user_quick_login_code 
                SET z_status={$status},codetime='{$time}'  
                WHERE phone='{$phone}' AND code='{$code}'";
        return $dbLink->query($sql);
    }
}