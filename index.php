<?php

//defines

$db = null;
define('ROOT', getcwd());
define('FRAMEWORK', ROOT . "/framework");
define('CORE', ROOT . "/framework/core");
define('CONFIG', ROOT . "/config");
define('DEBUG', ROOT . "/debug");
define('APP', ROOT . "/app");
define('DB', ROOT . "/db");

//var's requires
$config = require(CONFIG . "/web.config.php");
$routes = require(ROOT . "/routes/routes.php");

if($config['debug'] == true){
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
}

//traits

$traits = glob(FRAMEWORK . '/traits/*.php');
foreach ($traits as $trait) {
        require $trait;
}

//requires

//debuger (doesn't work)
//require(DEBUG . "/check.php");
require(CORE . "/router.php");
require(CORE . "/factoryRoutes.php");
require(APP . "/controller.php");




//echo CORE;
$app = new framework\core\router($routes, $config);
if($app->app === false){
        die();
}
$controller_name = $app->app[0];
$method = $app->app[1];

if($config['db']['activate'] == true){
        require(DB . "/db.php");
        $db = new db\db($config['db']);
        require(APP . "/model.php");
        app\Model::setDBvar($db);
        $models = glob(APP . '/models/*.php');
        foreach ($models as $model) {
                require $model;
        }
}



require(APP . "/controllers/" . $controller_name . ".php");

$controller = core\factoryRoutes::route($controller_name);

$controller->$method();

if($config['db']['activate'] == true){
        $db->close($db->link);
}
