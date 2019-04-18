<?php
/**
 * 调试器
 * @author wiki<charmfocus@gmail.com>
 * @copyright(c) 2010-11-23
 * @version v1.0
 */
class ZOL_Debugger
{
    /**
     * 打印变量结果
     * @param mixed $var 变量
     * @param bool $exit 打印完是否退出
     * @return void
     */
    public static function dump($var, $exit = true)
    {
        if (!IS_PRODUCTION) {
            echo '<pre>';
            if($var)
				print_r($var) ;
			else
				echo "NULL";
            echo '</pre>';

            $exit && exit();
        }
    }
    
    public static function stop($code = 0)
    {
        if (!IS_PRODUCTION) {
            exit($code);
        }
    }
}