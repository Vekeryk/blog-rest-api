<?php

class Database {
    
    private $host = HOST;
    private $username = USER;
    private $password = PASSWORD;
    private $db_name = DB_NAME;

    public $conn;

    public function __construct()
    {
        $this->conn = null;

        try { 
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }

    public function run($query) {
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
  }