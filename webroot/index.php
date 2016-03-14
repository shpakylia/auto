<?php
//phpinfo();
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));
define('VIEWS_PATH', ROOT.DS.'views');
//define('WEBROOT_PATH', ROOT.DS.'webroot');
define('UPLOAD_PATH', ROOT.DS.'webroot'.DS.'uploads'.DS);

require_once(ROOT.DS.'lib'.DS.'init.php');

session_start();

App::run($_SERVER['REQUEST_URI']);