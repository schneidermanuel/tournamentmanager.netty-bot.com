<?php

use Manuel\Tournamentmanager\controllers\TournamentsController;
use Schneidermanuel\Dynalinker\Core\Dynalinker;

use Manuel\Tournamentmanager\controllers\AuthController;

require 'vendor/autoload.php';

$_HEADER = getallheaders();

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');   // Cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    exit();
}

$_POST = json_decode(file_get_contents("php://input"), true);
$dynalinker = Dynalinker::Get();
$dynalinker->AddController("Login", new AuthController());
$dynalinker->AddController("Tournaments", new TournamentsController());
$dynalinker->Run();
