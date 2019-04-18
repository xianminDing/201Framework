<?php
/**
 * redis访问数据设置
 */
class DAL_Redis
{
    /**
     * 数据KEY的注册
     */
    private $dataKey = array(
        #类型包括 STRING HASH SET ZSET LIST
        'ListParamPro' => array('type'=>'SET','time' => 86400000,'serverId'=>0,'app'=>'Pro'),
        'VideoVote'    => array('type'=>'SET','time' => 300,'serverId'=>0,'app'=>'Pro'),
        'Review'       => array('type'=>'SET','time' => 300,'serverId'=>1,'app'=>'Pro'),
        'List'         => array('type'=>'SET','time' => 172800,'serverId'=>1,'app'=>'Pro'), #产品库列表页的相关数据2天失效.
        'Detail'       => array('type'=>'SET','time' => 864000,'serverId'=>1,'app'=>'Pro'), #产品库综述页的相关数据10天失效.
        'Price'        => array('type'=>'SET','time' => 864000,'serverId'=>1,'app'=>'Pro'), #产品库报价相关数据10天失效.
        'AladdinData'  => array('type'=>'SET','time' => 864000,'serverId'=>2,'app'=>'Pro'), #阿拉丁的数据放10天失效
        'Weixin'       => array('type'=>'SET','time' => 864000,'serverId'=>1,'app'=>'Pro'), #微信相关数据
    );

    /**
     * 服务器
     */
    private $connentServer = array(
        0 =>  array( #主服务，本业务的服务器redis
                    'host' => 'server_redis_datawarehouse', #IP
                    'port' => '6505'           #port
                ),
        1 => array(#SSDB服务,使用redis的API
            'host' => 'ssdb_cache_dbm_8810_product.zoldbs.com.cn.',
            'port' => '8810',
        ),
        2 => array(
            'host' => 'ssdb_cache_dbm_8890_pro_aladdin.zoldbs.com.cn.',
            'port' => '8890',
        ),
    );

    /**
     * 获得链接信息，不同业务链接信息不同
     * $snId : 要连接的redis sn
     */
    public function getConnectInfo($snId=0){
        return $this->connentServer[$snId];
	}

    /**
     * 获得数据key
     */
    public function getKeyInfo($name){
        if(isset($this->dataKey[$name])){
            $info     = $this->dataKey[$name];

            return array(
                'server' => $this->getConnectInfo($info['serverId']),
                'key'    => $info['app'] . ":" . $name,
                'type'   => $info['type'],
                'time'   => $info['time'],
            );
        }
        return false;
    }


}
