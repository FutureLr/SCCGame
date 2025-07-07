<?php

require_once '/../getConfig.php';
require_once '/../getErrorMessageByEnv.php';

class databaseConnector
{
    private $conn;

    public function __construct()
    {
        try {
            $config = getConfig();

            $this->conn = new mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database']
            );

            if ($this->conn->connect_error) {
                throw new Exception(getErrorMessageByEnv("Database connection failed: " . $this->conn->connect_error));
            }
        } catch (Exception $e) {
            throw new Exception(getErrorMessageByEnv("Database error: " . $e->getMessage()));
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
