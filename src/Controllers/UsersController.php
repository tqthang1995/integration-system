<?php
namespace Src\Controllers;
use Src\TableGateways\UsersGateway;

class UsersController extends BaseController {

    private $userId;

    private $usersGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        parent::__construct($db, $requestMethod);

        $this->userId = $userId;
        $this->usersGateway = new UsersGateway($db);
    }

    public function processRequest()
    {
        $prefix = strtok($_SERVER["REQUEST_URI"], '?');

        switch ($this->requestMethod) {
            case 'GET':
                if ($prefix === '/users' || strpos($prefix, '/users/') === 0) {
                    if ($this->userId) {
                        $response = $this->getUser($this->userId);
                    } else {
                        $response = $this->getAllUsers();
                    }
                } elseif ($prefix === '/login') {
                    $username = $_GET['username'] ?? null;
                    $password = $_GET['password'] ?? null;
                    $response = $this->login($username, $password);
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'POST':
                if ($prefix === '/users') {
                    $response = $this->createUserFromRequest();
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'PUT':
                if ($prefix === '/users') {
                    $response = $this->updateUserFromRequest($this->userId);
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            case 'DELETE':
                if (strpos($prefix, '/users/') === 0) {
                    $response = $this->deleteUser($this->userId);
                } else {
                    $response = $this->notFoundResponse();
                }
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllUsers() {

        $result = $this->usersGateway->findAll();
        return $result ? $this->jsonResponse($result) : $this->notFoundResponse();
    }

    private function getUser() {
        $result = $this->usersGateway->find($this->userId);
        return $result ? $this->jsonResponse($result) : $this->notFoundResponse();
    }

    private function createUserFromRequest() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usersGateway->insert($input);
        return $this->jsonResponse(null, 201);
    }

    private function updateUserFromRequest() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usersGateway->update($this->userId, $input);
        return $this->jsonResponse(null, 200);
    }

    private function deleteUser() {
        $result = $this->usersGateway->find($this->userId);
        if (!$result) {
            return $this->notFoundResponse();
        }
        $this->usersGateway->delete($this->userId);
        return $this->jsonResponse(null, 200);
    }

    private function login() {
        $username = $_GET['username'] ?? null;
        $password = $_GET['password'] ?? null;
        $result = $this->usersGateway->login($username, $password);
        return $result ? $this->jsonResponse($result) : $this->notFoundResponse();
    }

    private function validateUser($input) {
        return isset($input['first_name']) && isset($input['last_name']);
    }

    private function unprocessableEntityResponse() {
        return $this->jsonResponse(['error' => 'Invalid input'], 422);
    }
}
