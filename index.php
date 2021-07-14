<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *, Authorization');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header("Content-type: application/json");

//Вивід помилок під час розробки
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

require_once 'config/config.php';

require_once 'libraries/Controller.php';
require_once 'libraries/Database.php';
require_once 'libraries/Router.php';
require_once 'libraries/Route.php';

$router = new Router();

require_once 'routes/routes.php';

$router->run();