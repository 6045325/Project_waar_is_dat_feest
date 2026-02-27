<?php
abstract class Database {
    protected PDO $PDO; 

    public function __construct($host = "localhost", $dbname = "login", $username = "root", $password = "") {
        try {
            $this->PDO = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->PDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("ERROR! Database connectie mislukt: " . $e->getMessage());
        }
    }


    protected function getConnection(): PDO {
        return $this->PDO;
    }
}
?>