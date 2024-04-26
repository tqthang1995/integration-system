<?php
use Src\Controllers\IntegrationSystemController;
use Src\Controllers\UsersController;
use Src\Controllers\BaseController;
require "../bootstrap.php";
// $dbSeedFilePath = "../dbseed.php";

// Set headers for CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Parse the request URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Extract user ID from URI if present
$userId = null;
if (isset($uri[2])) {
    $userId = (int)$uri[2];
}

// Determine request method
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
