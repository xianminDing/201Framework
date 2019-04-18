<?php
/**
 * 论坛Db_DcbbsV2
 */
class Db_DcbbsV2 extends ZOL_Abstract_Pdo
{
    protected $servers   = array(
        //'engner' => 'mysql',
        'master' => array(
            'host' => 'dbserver_sjbbs_v2_read',
            'database' => 'z_dcbbs',
        ),
        'slave' => array(
            'host' => 'dbserver_sjbbs_v2_read',
            'database' => 'z_dcbbs',
        ),
    );
}