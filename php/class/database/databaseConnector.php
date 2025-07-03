<?php
require_once("../../libs/Spyc.php");

class databaseConnector
{
    private $conn;

    public function __construct()
    {
        try {
            $dbPath = realpath(__DIR__ . "/../../../configs/database/database.yml");
            $fileModifiedTime = filemtime($dbPath);

            if (session_status() === PHP_SESSION_NONE) session_start();

            if (
                isset($_SESSION['database']) &&
                isset($_SESSION['database_mtime']) &&
                $_SESSION['database_mtime'] === $fileModifiedTime
            ) {
                $db = $_SESSION['database'];
            } else {
                $db = Spyc::YAMLLoad($dbPath);
                $_SESSION['database'] = $db;
                $_SESSION['database_mtime'] = $fileModifiedTime;
            }

            $this->conn = new mysqli(
                $db['host'],
                $db['username'],
                $db['password'],
                $db['database']
            );
            if ($this->conn->connect_error) {
                die("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
