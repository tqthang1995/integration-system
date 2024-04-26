<?php
namespace Src\Controllers;

use Src\TableGateways\UsersGateway;

class UsersController extends BaseController {
    private $userId;
    private $usersGateway;

    public function __construct($db, $requestMethod, $userId) {
        parent::__construct($db, $requestMethod);
        $this->userId = $userId;
        $this->usersGateway = new UsersGateway($db);
    }

    public function processRequest() {
        $prefix = strtok($_SERVER["REQUEST_URI"], '?');

        switch ($this->requestMethod) {
            case 'GET':
                // echo "$prefix";
                $response = $this->handleGetRequest($prefix);
                break;
            case 'POST':
                $response = $this->handlePostRequest($prefix);
                break;
            case 'PUT':
                $response = $this->handlePutRequest($prefix);
                break;
            case 'DELETE':
                $response = $this->handleDeleteRequest($prefix);
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }

        $this->sendResponse($response);
    }

    private function handleGetRequest($prefix) {
        switch ($prefix) {
            case '/users':
                $response = $this->getAllUsers();
                break;
            case '/user':
                $response = $this->getUser();
                break;
            case '/login':
                $response = $this->login();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        return $response;
    }

    private function handlePostRequest($prefix) {
        if ($prefix === '/users') {
            return $this->createUserFromRequest();
        } else {
            return $this->notFoundResponse();
        }
    }

    private function handlePutRequest($prefix) {
        if ($prefix === '/users') {
            return $this->updateUserFromRequest();
        } else {
            return $this->notFoundResponse();
        }
    }

    private function handleDeleteRequest($prefix) {
        if ($prefix === '/users') {
            return $this->deleteUser();
        } else {
            return $this->notFoundResponse();
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
