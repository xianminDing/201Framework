<?php
/**
* 手机验证码
* @author 刘红亮<liu.hongliang@zol.com.cn>
* @copyright (c) 2016-06-20
* @version v1.0
*/
class Libs_Global_CheckCodeForPhone
{
    /**
     * 获取手机验证码
     * 
     * @param type $param
     * @return 
     */
    public static function getPhoneCode($param = array()){
        $options = array(            
            'phoneNumber' => 0,    // 手机号码
        );
        if (is_array($param) && !empty($param)) {
            $options = array_merge($options, $param);
        }
        extract($options);  
        
        
        $time         = time();
        $ip           = ZOL_Http::getClientIp();
        
        $data              = array();
        $data['flag']      = 0;
        $data['checkCode'] = '';     
        
        if ('' == $phoneNumber){
            $data['msg'] = '请填写手机号码';
            return $data;
        }
        
        // 一小时只能发5次验证码 
        $compareTime     = $time - 3600;
        $phoneCodeWhere  = "phone_number='" . $phoneNumber . "' AND add_time >= '" . $compareTime . "'"; 
        $db = Db_ProductCache::instance();
        $sql = "select count(*) as num from zs_user_phone_code where ".$phoneCodeWhere;
        $phoneCodeNumber = $db->getOne($sql);
        if ($phoneCodeNumber >= 5){
            $data['msg'] = '请不要频繁发送手机验证码';
            return $data;
        }        
        
        //生成验证码
        $phoneCode = rand(100000, 999999);
        
        // 插入验证码记录

        
        $insertSql = "insert into zs_user_phone_code(phone_number,phone_code,add_time,add_ip) values('{$phoneNumber}','{$phoneCode}','{$time}','{$ip}')";
        $flag = $db->query($insertSql);
        if (!$flag){        
            $data['msg'] = '网路繁忙，请稍后再试';
            return $data;
        }         
        
        $data['flag']      = 1;
        $data['msg']       = '验证码生成成功';
        $data['checkCode'] = $phoneCode;        
        return $data;
    }
    
    /**
     * 手机验证码校验
     * 
     * @param type $param
     * @return Array
     */
    public static function checkPhoneCode($param = array()){
        $options = array(
            'phoneNumber' => '',     // 手机号码
            'checkCode'   => '',    // 校验码
            'lifeTime'    => 1800,  // 校验码生存周期
        );
        if (is_array($param) && !empty($param)) {
            $options = array_merge($options, $param);
        }
        extract($options);    
        
        $time         = time();  
        
        $data         = array();
        $data['flag'] = 0;
        
        if ('' == $phoneNumber){
            $data['msg'] = '请填写手机号码';
            return $data;
        }
        
        if ('' == $checkCode){
            $data['msg'] = '验证码不能为空';
            return $data;            
        }        
        
        // 检测手机验证码是否正确
        $compareTime    = $time - $lifeTime; // 手机验证码生命周期
        $phoneCodeWhere = "phone_number='" . $phoneNumber . "' AND add_time >='" . $compareTime . "' AND phone_code='" . $checkCode . "'";
        $sql = "select 'X' from zs_user_phone_code where ".$phoneCodeWhere." order by add_time desc limit 0,1";
        $phoneCodeInfo = $db->getOne($sql);
        if (empty($phoneCodeInfo)){
            $data['msg'] = '验证码错误，请重新获取验证码';
            return $data;
        }        
        
        $data['flag'] = 1;
        $data['msg']  = "验证码正确";
        
        return $data;
    }
}
