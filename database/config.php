<?php 

function getConnection(): PDO

{
    $host = 'localhost';
    $db = 'emptyhood';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=3307;dbname=$db;charset=utf8mb4",
            $user,
            $pass,
           
        );

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }   catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}