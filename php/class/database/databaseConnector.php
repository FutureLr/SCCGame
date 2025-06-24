<?php
    require_once("../../libs/Spyc.php");

    class databaseConnector {
        private $conn;

        public function __construct() {
            try {
                $db = Spyc::YAMLLoad(realpath(__DIR__ . "/../../../configs/database/database.yml"));

                $this->conn = new mysqli(
                    $db['servername'],
                    $db['username'],
                    $db['password'],
                    $db['dbname']
                );

                if ($this->conn->connect_error) {
                    die($this->conn->connect_error);
                }

            } catch(Exception $e) {
                die($e->getMessage());
            }
        } 

        public function getConnection() {
            return $this->conn;
        }
    }