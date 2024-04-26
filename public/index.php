<?php

use Src\Controllers\BaseController;
use Src\Controllers\IntegrationSystemController;
use Src\Controllers\UsersController;

require "../bootstrap.php";

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

// Example: Output the PDO object's DSN
// echo '<script>console.log('.json_encode($dbConnection).')</script>';

// Instantiate the base controller
$baseController = new BaseController($dbConnection, $requestMethod);

// Instantiate UsersController for user-related requests
$usersController = new UsersController($dbConnection, $requestMethod, $userId);
$usersController->processRequest();

// Instantiate IntegrationSystemController for integration system-related requests
$integrationSystemController = new IntegrationSystemController($dbConnection, $requestMethod);
$integrationSystemController->processRequest();