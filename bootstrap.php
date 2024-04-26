<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\System\DatabaseConnector;

$dotenv = new Dotenv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();
// echo '<script>console.log('.json_encode($dbConnection).')</scrip>';