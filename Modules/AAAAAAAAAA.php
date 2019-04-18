<?php
/** 
* 缓存模块的示例
* 仲伟涛 2014-11-12
*/
class Modules_BestDoc extends ZOL_DAL_MongoCacheModule
{
    
	/**
	* 刷新缓存
	*/
	public function refresh(array $param = array())
	{
        //获取参数参数
		$type      = $param['type'];
		
        //所有分类的列表
        if($type == "ALL"){
            $data = array();
            $bestSubListArr = Libs_Best_Docment::getHotClassSub();
            if($bestSubListArr){
                foreach($bestSubListArr as $subInfo){
                    $subId = $subInfo['id'];
                    $docInfo = Libs_Best_Docment::getDocment(array('sid'=>$subId,'limit'=>16,'startTime' => date('Y-m-d H:i:s', strtotime('-3 days')))); #secTitle是价格
                    $data[$subId] = $docInfo;
                }
            }
            $cacheParam = array('type' => $type); //缓存参数            
            $this->set($cacheParam, $data);      //设置缓存
        }
        
		return true;
	}
}