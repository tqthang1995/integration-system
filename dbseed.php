<?php
require 'bootstrap.php';
$statement = <<<EOS
    CREATE TABLE IF NOT EXISTS users (
        id int unsigned not null auto_increment primary key,
        first_name varchar(255) not null,
        last_name varchar(255) not null,
        user_name varchar(255) not null unique,
        email varchar(255) not null unique,
        pwd varchar(255) not null,
        r_pwd varchar(255) not null
    );
    INSERT INTO users
        (id, first_name, last_name, user_name, email, pwd, r_pwd)
    VALUES
        (1, 'Thang', 'Tran', 'thangtran', 'tohutieu@gmail.com', '123456', 'secondpwd'),
        (2, 'Vale', 'V', 'vvale', 'vvale@gmail.com', '123456', 'secondpwd');
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}