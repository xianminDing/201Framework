<?php
/*
* @describe  公共方法
* @author    huanght
* @date      2018-09-29
*/
class Libs_Global_Common{

	/**
	 * 内网ip判断
	 */
	public static function checkIp(){

		$ip = ZOL_Api::run("Service.Area.getClientIp", array());

		return false; #正式上线取消内网测试流程

		if((strpos($ip, '10.21.')!== false || strpos($ip, '10.22.'))!== false)
		{
			return true;
		}

		return false;
	}

	/**
	 * 通过ssid获得user_id
	 * @param $paramArr
	 */
	public static function getUserIdByssid($paramArr){

		$options = array(
			'userId'	=> 0, #传入ssid
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);


		$outArr = array(
			'status' => '1',
			'msg'    => "操作失败!"

		);

		$userId = ZOL_Api::run("User.Base.getUserIdBySSid" , array(
			'ssid'           => $userId,             #用户登录ssid
		));
		if($userId){
			return $userId;
		}

		$outArr = ZOL_String::arrayIconv('GBK', 'UTF-8', $outArr);
		echo json_encode($outArr); die;

	}

	/**
	 * 返回从小到大排序,以 - 分割的产品ids
	 * @param $paramArr
	 */
	public static function getPkIdsByProStr($paramArr){
		$options = array(
			'pkIds'	=> '', #传入ssid
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if(!$pkIds) return false;

		$proArr = explode('-',$pkIds);
		#从小到大排序
		sort($proArr);
		$pkIds = implode($proArr,'-');
		return $pkIds;
	}

	/**
	 * 时间范围内ip调用接口次数限制
	 */
	public static function checkIpCount($paramArr)
	{
		$options = array(
			'zolIps'	=> '10.21.',   #过滤zol测试ip
			'key'       => '',  	   #key
			'num'       => '', 		   #限制次数
			'msg'       => '操作太频繁,请稍后再试',#提示语
			'life'      => '',         #生命周期
			'callback'  => '',         #jsonp
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if(!$key || !$life || !$num) return false;

		#获得ip
		$dataArr = ZOL_Api::run("Service.Area.getIp" , array(
		));

		#查询key值是否大于num
		$ipCnt = ZOL_Api::run("Kv.Redis.stringGet" , array(
			'serverName'     => 'Default',       #服务器名
			'key'            => $key.'_'.$dataArr['ip'].'_'.date("Y-m-d",time()),   #获得数据的Key
		));

		if($ipCnt <= $num){
			#根据ip计数
			$ipCnt = ZOL_Api::run("Kv.Redis.stringIncr" , array(
				'serverName'     => 'Default',
				'key'            => $key.'_'.$dataArr['ip'].'_'.date("Y-m-d",time()),
				'value'          => 1,
				'life'           => $life,
			));
		}

		if($ipCnt > $num && (strpos($dataArr['ip'],$zolIps)===false)){
			$outArr = array(
				'status' => '1',
				'msg'    => ZOL_String::convToU8($msg)
			);

			if($callback){
				echo $callback . "(" . json_encode($outArr) . ")";
				die;
			}
			echo json_encode($outArr); die;
		}
		return true;
	}

	/**
	 * 签名验证
	 * @param $paramArr
	 * @return bool
	 */
	public static function checkSign($paramArr){
		$options = array(
			'data' => [],
		);
		if(is_array($paramArr)) $options = array_merge($options, $paramArr);
		extract($options);

		if(!$data) Libs_Global_Common::outFormatData(['status'=>1,'msg'=>'joke ?']);

		$sign = isset($data['sign']) ? ZOL_String::htmlSpecialChars($data['sign']) : '';
		$timestamp = isset($data['timestamp']) ?(int)$data['timestamp'] : '';

		if($sign == 'zol_test_debug')  return true; #调试使用

		#签名验证
		if(!$timestamp || md5(ZOL_Config::get("Global","CASH_SECRET").$timestamp) != $sign || (time()-$timestamp)>5){
			#如果签名不匹配或者接口存留时间超过5秒则报错
            Libs_Global_Common::outFormatData(['status'=>1,'msg'=>'Sign Faild']);
		}
		return true;
	}

	/**
	 * 获取焦点图
	 * @param ZOL_Request $input
	 * @param ZOL_Response $output
	 * @return array
	 */
	public static function getBanner($paramArr){

		$option = array(
			'moduleIds' => '',
			'num' => '',
		);
		if (is_array($paramArr)) $option = array_merge($option, $paramArr);
		extract($option);

		$dataArr = ZOL_Api::run("Article.Module.getCmsModule" , array(
			'moduleIds'      => $moduleIds,      #手工ID
			'num'            => $num,            #数量
			'width'          => 750,             #图片宽度
			'height'         => 375,             #图片高度
		));

		$out = array();
		if (isset($dataArr) && $dataArr) {
			foreach ($dataArr as $v) {
				$tmp['type'] = $v['digest'];
				$tmp['stitle'] = $v['title'];
				$tmp['url'] = $v['url'];
				$tmp['imgsrc'] = $v['img1'];
				$tmp['date'] = $v['date'];

				switch($v['digest'])
				{
					case '2': #论坛
						$tmp['type'] = '6';
						break;
					case '6': #电商
						$tmp['id'] = $v['url'];
						break;
					case '5': #产品
						$tmp['id'] = $v['title'];
						break;
					case '4': #攒机
						$tmp['id'] = $v['title'];
						break;
					case '3': #话题
						$tmp['id'] = $v['title'];
						break;
					default:
						$tmp['id'] = $v['id'];
						break;
				}
				$out[] = $tmp;
			}
		}
		return $out;
	}

	/**
	 * 只能在涉及到跨部门配合时使用
	 * 跨域白名单
	 */
	public static function setCors() {

		$dataArr = ZOL_Api::run("Service.Area.getIp" , array(
		));

		$ip = $dataArr['ip'];
		$origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';

		if(IS_DEBUGGING && strpos($ip,'10.') !== FALSE){ #如果是测试环境可以随意跨域
			header('Access-Control-Allow-Origin:'.$origin);
			header('Access-Control-Allow-Credentials:true');
		}else{
			#线上需要时添加过滤
			$originArr = array(
				'http://liuxg.com',
			);
			if (!empty($origin)) {
				if (in_array($origin,$originArr) || strpos($origin,'.zol.com') !== FALSE) {
					header('Access-Control-Allow-Origin:'.$origin);
					header('Access-Control-Allow-Credentials:true');
				}  else {
					//禁止访问
					header('HTTP/1.1 403 Forbidden');
					exit();
				}
			}
		}
	}

	/**
	 * 获得跳转对应版本的类名
	 * @param ZOL_Request $input
	 * @param ZOL_Response $output
	 */
	public static function getVersionClassName($paramArr){
		$option = array(
			'className' => '',
		);
		if (is_array($paramArr)) $option = array_merge($option, $paramArr);
		extract($option);

		if(!$className) return false;

		$className = strchr($className,'Base',true).'V';
		return $className;
	}

	/**
	 * get、post请求
	 * 基础验证
	 * 接口分发
	 * @param $paramArr
	 * @return bool
	 */
	public static function requestBase(ZOL_Request $input, ZOL_Response $output){

		$paramArr = $output->paramArr;
		$option = array(
			'method'  => 'get', 	# 请求方式
			'isDebug' => '', 		# 1 开启debug，提测或上线前必须关闭
            'fields'  => [], 		# 自定义字段 eg: 类型=>字段名
			'requestLimits' => [],  # 请求频率限制
			'className' => '',
			'onlyValidate'  => 0,   # 仅做数据完整性验证，不实例化路由
            'startVersion'  => 1,   # 开启签名验证的起始版本，用于兼容老版Base接口
            'cache' => 0,           # 1支持TS缓存 0默认不支持，需要签名
		);
		if (is_array($paramArr)) $option = array_merge($option, $paramArr);
		extract($option);


		#设置白名单
		Libs_Global_Common::setCors();

		$data = strtolower($method) == 'post' ? $input->post() : $input->get();

		#调用接口版本号 大于 起始验证版本号 则 开启签名验证，用于处理老版base接口没有签名的情况
		if($input->get('version') >= $startVersion)
		{
            if($cache == 0){
                #签名验证
                !$isDebug && Libs_Global_Common::checkSign(['data'=>$data]);

                # 线上强制签名，防止开发漏改.
                # 测试环境之所以不默认跳过签证，目的是防止前端客户端漏参上线后报错.
                !IS_DEBUGGING && Libs_Global_Common::checkSign(['data'=>$data]);
            }

            #请求的客户端版本标识
            $output->clientVersion = !empty($data['ci']) ? ZOL_String::htmlSpecialChars($data['ci']) : Libs_Global_Common::outFormatData(['status'=>1,'msg'=>'ci none!']);
        }

        #接口请求限制
        !empty($requestLimits) && Libs_Global_Common::checkIpCount($requestLimits);

		#自定义参数
		if(!empty($fields)){
			foreach($fields as $field){
				!is_array($field) && Libs_Global_Common::outFormatData(['status'=>1,'msg'=>'fields 格式有误，请按照规范填写']);
				foreach($field as $type=>$fieldName)
				{
					if( $type == 'string' ){
						$output->{$fieldName} = isset($data[$fieldName]) ? (string)ZOL_String::htmlSpecialChars($data[$fieldName]) : '';
					}else{
						/**
						 * 支持类型
						 * boolean （或为 bool）
						 * integer （或为 int）
						 * float
						 * string
						 * array
						 * object
						 */
						$fieldData = isset($data[$fieldName]) ? $data[$fieldName] : '';
						settype($fieldData, $type);
						$output->{$fieldName} = $fieldData;
					}
				}
			}
		}

        #接口版本  【自动读取路由，无默认值防止写入数据紊乱】
		$version = $output->version = $input->get('version') ? (int)$input->get('version') : Libs_Global_Common::outFormatData(['status'=>1,'msg'=>'version none']);
		
		$action = $output->action = isset($data['a']) ? ZOL_String::htmlSpecialChars($data['a']) : '';           #动作
		
		//如果只做验证，到此结束.
		if($onlyValidate){
			return true;
		}
		
		$className = $className.$version;
		$controler = new $className($input, $output);
		$actionName = $action ? ('do'.ucfirst($action)) : 'doDefault';
		$controler->$actionName($input, $output);
		return true;
	}

	/**
	 * 插入数组到指定位置
	 * @param $paramArr
	 * @return bool
	 */
	public static function insertAssignPosition($paramArr){
		$option = array(
			'data' => '', 		 #原数组
			'insertArray' => '', #要插入的数组
			'position'=> '',     #插入位置
		);
		if(is_array($paramArr))$option = array_merge ($option,$paramArr);
		extract($option);

		if(!$data || !$position || !$insertArray) return false;

		$first_array = array_splice ($data, 0, $position-1);
		$data = array_merge ($first_array, array($insertArray), $data);
		return $data;
	}

	/**
	 * 统一接口返回格式
	 * @param $paramArr
	 */
	public static function outFormatData($paramArr){
		$option = array(
			'status' => '0',   		  #返回状态值 0 成功 1失败
			'msg'    => 'success',	  #提示信息
			'code'   => '',   		  #自定义状态码
			'data'   => [],   	      #返回数据
			'callback' => '', 		  #支持jsonp则传入
		);
		if(is_array($paramArr))$option = array_merge ($option,$paramArr);
		extract($option);

		#组装格式
		$outArr = ['status'=>(string)$status,'msg'=>$msg,'code'=>(string)$code,'data'=>$data];

		#统一转码
		$outArr = ZOL_String::arrayIconv('GBK', 'UTF-8', $outArr);

		#支持回调
		if($callback){
			echo $callback . '(' . json_encode($outArr). ');';die;
		}
		echo json_encode($outArr);die;
	}
}