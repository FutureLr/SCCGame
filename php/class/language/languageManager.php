<?php
    require_once("../database/databaseConnector.php");

    class languageManager {
        private $conn;

        public function __construct() {
            $this->conn = (new databaseConnector())->getConnection();
        }

        public function getUserLang() {
            if(session_status() === PHP_SESSION_NONE) session_start();

            if(isset($_SESSION['user']['username'])) {
                $stmt = ($this->conn)->prepare("SELECT language FROM user  WHERE username = ? LIMIT 1");
                $stmt->bind_param("s",$_SESSION['user']['username']);
                $stmt->execute();

                $result = $stmt->get_result();
                if ($result && $row = $result->fetch_assoc()) {
                    return $row['language'];
                } else {
                    throw new Exception("Account not found.");
                }
            } else {
                throw new Exception("User is not logged in.");
            }
        }

        public function setUserLang($lang) {
            if(session_status() === PHP_SESSION_NONE) session_start();

            if(isset($_SESSION['user']['username'])) {
                $stmt = ($this->conn)->prepare("UPDATE user SET language = ? WHERE username = ? ");
                $stmt->bind_param("ss",$lang,$_SESSION['user']['username']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    return true;
                } else {
                    throw new Exception("Account not found.");
                }
            } else {
                throw new Exception("User is not logged in.");
            }
        }
    }