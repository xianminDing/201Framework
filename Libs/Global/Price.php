<?php
/**
 * @name     Price.php
* @describe  价格金钱公用处理
* @author    huanght
* @date      2018-09-19
*/
class Libs_Global_Price{

	/**
	 * 分转元
	 * @desc 格式化元单位
	 */
	public static function getDollarPrice($paramArr){
		$options = array(
			'price'	=>0,
			'isNumber' =>1
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if(!$price){
			return "0.00";
		}

		if(!$isNumber) return floor($price/100);

		return number_format(($price/100),2,'.','');
	}

	/**
	 * 签名验证
	 * @param $paramArr
	 * @return bool
	 */
	public static function checkSign($paramArr){
		$options = array(
			'timestamp'	=>0, #请求时间戳
			'sign' => ''
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if($sign == 'zol_test_debug')  return true; #调试使用

		#签名验证
		if(!$timestamp || md5(ZOL_Config::get("Global","CASH_SECRET").$timestamp) != $sign || (time()-$timestamp)>5){
			#如果签名不匹配或者接口存留时间超过5秒则报错
			die('Sign Faild');
		}
		return true;
	}

	/**
	 * 获得支付提现公司名
	 * @param $paramArr
	 * @return bool|string
	 */
	public static function getCompanyName($paramArr){

		$options = array(
			'type'	=> 0, #支付提现类型
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if($type == 0)
			return '无';
		elseif($type == 1)
			return '支付宝';
		elseif($type == 2)
			return  '微信';
		elseif($type == 3)
			return '银联';

		return true;
	}

	/**
	 * 检查当前账期余额，0则失败
	 */
	public static function checkCapitalCash($paramArr){
		$options = array(
			'callback'	=> '',
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		$db = ZOL_Db::instance('Db_Wap');
		$viewDate = date('Y-m-d H:i:s',time());

		#根据账期id去查返利系数
		$sql = "select period_id,period_begin_date,period_end_date from promotion_capital_period";
		$paymentList = $db->getAll($sql);

		$promotionRate = 0; #返利系数默认0
		$paymentId = 0; #账期ID
		foreach($paymentList as $payment){
			#根据当前日期所在账期范围取得账期id
			$periodBeginDate = strtotime($payment['period_begin_date']);
			$periodEndDate = strtotime($payment['period_end_date']);
			if(strtotime($viewDate) > $periodBeginDate && strtotime($viewDate) < $periodEndDate){
				$paymentId = $payment['period_id'];
				$promotionRate = Helper_Promotion_AmountSet::getPromotionRate(array('periodId'=>$paymentId));
				break;
			}
		}
		if($promotionRate == 0){ #返利系数0时,资金没有了
			if($callback){
				echo $callback . '(' . json_encode(array()). ');';
				exit;
			}
			echo json_encode(array()); die;
		}
		return true;
	}
}