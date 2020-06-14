<?php
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('log_errors', 'On');
ini_set('error_log', './logs/php_errors.log');

header("HTTP/1.1 200 OK");
header('Content-type: text/html; charset=utf-8');

if (empty(file_get_contents('php://input'))) {
    echo "Hello world!";
    exit();
}

include "Services/Autoloader.php";
spl_autoload_register([new Autoloader(), 'getClass']);
new WebHook();



