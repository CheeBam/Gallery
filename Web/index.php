<?php
/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 10.12.15
 * Time: 0:06
 */

define('CORE', __DIR__.'/../Core/');
define('ROOT', __DIR__.'/../');
define('DOC_ROOT', __DIR__);
define('BR', '<Br/>');
define('PRE', '<pre>');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once(ROOT . '/vendor/autoload.php');
require_once(CORE . 'Application.php');

use \Core\Application;

$di = new Lebran\Container();
$di->register(new \Core\ServiceProvider());
$app = new Application($di);
$app->handle();




