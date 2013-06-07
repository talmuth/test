<?php

// should be removed starting from PHP version >= 5.3.0
defined('__DIR__') || define('__DIR__', dirname(__FILE__));

// initialize the application path, library and autoloading
defined('APPLICATION_PATH') ||
    define('APPLICATION_PATH', realpath(__DIR__));

defined('PUBLIC_HTML_FOLDER') ||
    define('PUBLIC_HTML_FOLDER', realpath(__DIR__));

$paths = explode(PATH_SEPARATOR, get_include_path());
$paths[] = realpath(__DIR__.'/library');
set_include_path(implode(PATH_SEPARATOR, $paths));
unset($paths);

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();