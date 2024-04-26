<?php
namespace Src\Controllers;

use Src\TableGateways\IntegrationSystemGateway;

class IntegrationSystemController extends BaseController {
    private $integrationSystemGateway;

    public function __construct($db, $requestMethod) {
        parent::__construct($db, $requestMethod);
        $this->integrationSystemGateway = new IntegrationSystemGateway($db);
    }

    public function processRequest()
    {
        // Extract the request path
        $prefix = strtok($_SERVER["REQUEST_URI"], '?'); 
        if ($prefix === '/crawl') {
            // Retrieve the URL from the query parameters
            $url = $_GET['url'] ?? null;

            // Process the request based on the provided URL
            $response = $this->getAllContentByUrl($url);
        } else {
            // Handle unsupported endpoints
            $response = $this->notFoundResponse();
        }

        // Ensure $response is defined in all cases
        $response = $response ?? $this->notFoundResponse();

        // Send the response
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    public function getAllContentByUrl($url) {
        // Check if the URL is provided
        if (!$url) {
            // Return a 400 Bad Request response if URL is missing
            return $this->badRequestResponse('URL is missing');
        }

        // Retrieve content based on the provided URL
        $result = $this->integrationSystemGateway->getAllContentByUrl($url);
        
        // Check if content is found
        if (!$result) {
            // Return a 404 Not Found response if content is not found
            return $this->notFoundResponse();
        }

        // Return a 200 OK response with the retrieved content
        return $this->jsonResponse($result);
    }
}
