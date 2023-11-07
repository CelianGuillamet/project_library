<?php

function connectDB(){
    require_once 'connect.php';
    try{
        $pdo = new \PDO(DSN, USER, PASS);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET NAMES "utf8"');
        return $pdo;
    } catch (\PDOException $e) {
        echo 'Unable to connect to the database server: ' . $e->getMessage();
    }
}
