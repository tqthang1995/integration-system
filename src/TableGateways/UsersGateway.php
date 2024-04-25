<?php
namespace Src\TableGateways;

class UsersGateway {

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function findAll()
    {
        $statement = "
            SELECT 
                id, first_name, last_name, user_name, email, pwd
            FROM
                users;
        ";

        try {
            $statement = $this->db->query($statement);
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function find($id)
    {
        $statement = "
            SELECT 
                id, first_name, last_name, user_name, email, pwd
            FROM
                users
            WHERE id = ?;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array($id));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function insert(Array $input)
    {
        $statement = "
            INSERT INTO users 
                (first_name, last_name, user_name, email, pwd, r_pwd)
            VALUES
                (:first_name, :last_name, :user_name, :email, :pwd, :r_pwd);
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name'],
                'user_name' => $input['user_name'],
                'email' => $input['email'],
                'pwd' => $input['pwd'],
                'r_pwd' => $input['r_pwd'],

            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function update($id, Array $input)
    {
        $statement = "
            UPDATE users
            SET 
                first_name = :first_name,
                last_name  = :last_name
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(
                'id' => (int) $id,
                'first_name' => $input['first_name'],
                'last_name'  => $input['last_name']
            ));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }


    public function delete($id)
    {
        $statement = "
            DELETE FROM users
            WHERE id = :id;
        ";

        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array('id' => $id));
            return $statement->rowCount();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }    
    }

    public function login($username, $password) {
        $statement = "
            SELECT 
                user_name, pwd
            FROM
                users
            WHERE user_name = :username AND pwd = :password;
        ";
        try {
            $statement = $this->db->prepare($statement);
            $statement->execute(array(':username' => $username, ':password' => $password));
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            
            if (count($result) == 0) {
                http_response_code(401);
                exit("Unauthorized");
            }
            
            return $result;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }   
    }
    
}
