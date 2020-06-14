<?php

abstract class Config
{
    // Настройки для соединения с Mysql
    public static $db_host = 'localhost';
    public static $db_name = '';
    public static $db_user = '';
    public static $db_pass = '';
    public static $db_charset = 'utf8mb4';
    public static $db_table_prefix = "anketsbot_";

    public static $debug_elem = ['mysql', 'access']; // 'mysql', 'access'

    // проверяем тип дебагера
    public static function debug($value) {
        foreach(self::$debug_elem as $elem) {
            if($elem == $value) {
                return true;
            }
        }
        return false;
    }

}