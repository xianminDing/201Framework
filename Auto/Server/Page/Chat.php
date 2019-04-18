<?php

/**
 * 用于socket服务的自动运行
 * cd /www/rest/html/Auto/Server/; php index.php --c=Chat
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(0);
class Auto_Server_Page_Chat extends Auto_Abstract
{
    public function doDefault(ZOL_Request $input){
        $ws = new Libs_Global_WebSocket("0.0.0.0", "8088");
    }
}