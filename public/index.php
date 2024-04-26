<?php
use Src\Controllers\IntegrationSystemController;
use Src\Controllers\UsersController;
use Src\Controllers\BaseController;
require "../bootstrap.php";
// $dbSeedFilePath = "../dbseed.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

// all of our endpoints start with /users
// everything else results in a 404 Not Found
// if ($uri[1] !== 'api') {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

// if (file_exists($dbSeedFilePath)) {
//     echo "run dbSeed";
//     require $dbSeedFilePath;
// } else {
//     echo "Error: dbseed.php not found!";
// }

// the user id is, of course, optional and must be a number:
$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

$requestMethod = $_SERVER["REQUEST_METHOD"];
$prefix = strtok($_SERVER["REQUEST_URI"], '?');
// Instantiate the base controller
$baseController = new BaseController($dbConnection, $requestMethod);

// Instantiate the appropriate controller based on the request URI
if ($prefix === '/users' || strpos($prefix, '/users/') === 0 || $prefix === '/login') {
    $controller = new UsersController($dbConnection, $requestMethod, $userId);
    $controller->processRequest();

} elseif ($prefix === '/crawl') {
    $controller = new IntegrationSystemController($dbConnection, $requestMethod);
    $controller->processRequest();
} 
