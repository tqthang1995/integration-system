<?php
namespace Src\Controller;
use Src\TableGateways\UsersGateway;


class UsersController {

    private $db;
    private $requestMethod;
    private $userId;

    private $usersGateway;

    public function __construct($db, $requestMethod, $userId)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->userId = $userId;

        $this->usersGateway = new UsersGateway($db);
    }

    public function processRequest()
    {
        $prefix = strtok($_SERVER["REQUEST_URI"], '?'); // Lấy tiền tố của URI (ví dụ: /users, /login)

        switch ($this->requestMethod) {
            case 'GET':
                if ($prefix === '/users') {
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
                if ($prefix === '/users') {
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


    private function getAllUsers()
    {
        $result = $this->usersGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function getUser($id)
    {
        $result = $this->usersGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function createUserFromRequest()
    {
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usersGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateUserFromRequest($id)
    {
        $result = $this->usersGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = (array) json_decode(file_get_contents('php://input'), TRUE);
        if (! $this->validateUser($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->usersGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteUser($id)
    {
        $result = $this->usersGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->usersGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function login($username, $password)
    {
        $result = $this->usersGateway->login($username, $password);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    private function validateUser($input)
    {
        if (! isset($input['first_name'])) {
            return false;
        }
        if (! isset($input['last_name'])) {
            return false;
        }
        return true;
    }

    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}
