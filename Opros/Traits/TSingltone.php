<?php

trait TSingltone
{
    protected static $instance = null;
    //закрытие на создание объекта
    private function __construct(){}
    private function __clone(){}
    private function __wakeup(){}

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }
        return self::$instance;
    }
}