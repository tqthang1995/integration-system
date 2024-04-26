<?php
namespace Src\Controllers;

class BaseController {
    protected $db;
    protected $requestMethod;

    public function __construct($db, $requestMethod) {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
    }

    protected function notFoundResponse() {
        return $this->jsonResponse(null, 404);
    }

    protected function jsonResponse($data, $statusCode = 200) {
        return [
            'status_code_header' => 'HTTP/1.1 ' . $statusCode,
            'body' => json_encode($data)
        ];
    }

    protected function sendResponse($response) {
        header($response['status_code_header']);
       
    }

    protected function badRequestResponse($message = 'Bad Request') {
        return $this->jsonResponse(['error' => $message], 400);
    }
}