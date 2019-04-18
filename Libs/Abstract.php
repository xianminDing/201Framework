<?php
/**
*
*/
class Libs_Abstract
{
    /*
	* @var ZOL_Product_Caching_GetCacheLoader
	*/
	protected static $cache;

	/**
	* 初始化缓存
	*/
	public static function init()
	{
		self::$cache = ZOL_DAL_GetCacheLoader::getInstance();
	}

	/**
	* 加载缓存数据
	*/
	protected static function loadCache($moduleName, $param = array(), $num = 0)
	{
		self::init();
		$data = self::$cache->loadCacheObject($moduleName, $param);

		if ($num && $data && count($data) > $num) {
			$data = array_slice($data, 0, $num, true);
		}

		return $data;
	}
    
    protected static function comWhere($option){
       $where = "WHERE ";
       if(empty($option)) return ;
       if(is_string($option)){
           $where .= $option; // 10 自行sql
       }else if(is_array($option)){
           foreach($option as $k => $v ){
               if(is_array($v)){
                   $where .= "{$k} IN (".implode(',', array_values($v)).")";//array('uid'=>array(1,2,3,4))
               }else if(strpos($k, '>')){
                   $where .= "$k '{$v}'";
               }else if(strpos($k,"<")){
                   $where .= "$k '{$v}'";
               }else if(substr($v,0,1) == '%' && substr($v,-1) =='%'){
                   $where .= "{$k} LIKE '%$v%'";
               }elseif(strpos($k,"!=")){
                   $k = str_replace('!=', '', $k);
                   $where .= "{$k} != '$v'";
               }else{
                   $where .= "$k = '{$v}'";
               }
               $where.= " AND ";
           }
           $where = rtrim($where,"AND ");
           $where.=" OR ";
       }
       $where = rtrim($where,"OR ");
       return $where;
   }
   protected static function sel($par){
       $options=array(
           'table'  => '',
           'field' => '*',
           'where'  => '1',
           'group'  => '',
           'having' => '',
           'order'  => '',
           'limit'  => '',
       );
       $options= array_merge($options,$par);
       extract($options);
       $sql ="SELECT {$field} FROM {$table} {$where} {$group} {$having} ".(!empty($order) ? ' ORDER BY '.$order : '')."".(!empty($limit) ? ' LIMIT '.$limit : '')."";
       
       return $sql;
   }
    protected static function colLimit($args){
        if(is_array($args)){
            return "LIMIT {$args[0]},{$args[1]}";
        }else if(is_numeric($args) && !empty($args)){
            return "LIMIT 0,$args";
        }else if(is_string($args) && !empty($args)){
            return "LIMIT $args";
        }
        return;
    }
    protected static function insertSql($table,$a = null){
       if(is_array($a) && !empty($table) ){
           $item = $a;
           foreach ($a as $k => $v) $item[$k] = "'".$v."'";
           if (empty($item)) return false;
           $sql = 'INSERT INTO '.$table.'('.implode(',',array_keys($a)).') VALUES('.implode(',',$item).')';
           return $sql;
       }
		
   }
   protected static function updateSql($table,$uPar,$where){
       if(empty($table) ||  empty($uPar) || empty($where)) return;
       if(is_string($uPar)){
           $setfield = $uPar;
       }else if(is_array($uPar)){
           $s = '';
           foreach ($uPar as $key => $v) $s .= $key ." = '" . $v . "',";
           $s = rtrim($s,",");
           $setfield = $s;
       }
       $u = "UPDATE {$table} SET {$setfield} ".self::comWhere($where);
       return $u;
   }
    protected static function total($table,$where){
        if($table){
            $sql="SELECT COUNT(*) AS count FROM {$table} {$where}";
            return $sql;
        }
    }
}
?>