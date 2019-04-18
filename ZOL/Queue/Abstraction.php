<?php
abstract class ZOL_Queue_Abstraction
{
    abstract public static function get($key,$limit=1);
    abstract public static function set($key = '', $var = '');
}
