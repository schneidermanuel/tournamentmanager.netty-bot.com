<?php

use Schneidermanuel\Dynalinker\Core\Dynalinker;

use Manuel\Tournamentmanager\controllers\AuthController;

require 'vendor/autoload.php';

$_HEADER = getallheaders();

header('Access-Control-Allow-Origin: https://tabugame.brainyxs.com');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: '.$_SERVER['HTTP_ORIGIN']);
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}

$_POST = json_decode(file_get_contents("php://input"), true);
$dynalinker = Dynalinker::Get();
$dynalinker->AddController("Login", new AuthController());
$dynalinker->Run();

