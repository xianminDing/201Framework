<?php
/**
 *  Gearman 队列系统
 *  仲伟涛 2012-5
 */
class ZOL_Gearman{
    
    protected static $serverArr = array("10.19.37.148:4730");
    protected static $client       = false;

    /**
     * 初始化对象
     */
    protected static function init($snId=0){

        if (!self::$client){
            if (class_exists("GearmanClient")) {
                self::$client = new GearmanClient();
                foreach(self::$serverArr as $v){
                    self::$client->addServers($v);
                }
            } else {
                die("Gearman 接口模块不可用");
            }
        }

    }

    /**
     * 执行任务
     */
    public static function doNormal($paramArr) {
		$options = array(
			'taskName'          => '', #任务名
			'taskContent'       => '', #任务内容
		);
		if (is_array($paramArr))$options = array_merge($options, $paramArr);
		extract($options);

        self::init();

        return self::$client->doNormal($taskName, $taskContent);

    }
}
?>
