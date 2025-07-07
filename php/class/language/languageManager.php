<?php
require_once("../database/databaseConnector.php");
require_once('../getConfig.php');
require_once('../getErrorMessageByEnv.php');

class languageManager
{
    private $conn;

    public function __construct()
    {
        $this->conn = (new databaseConnector())->getConnection();
    }

    public function getUserLang()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_SESSION['user']['username'])) {
            $stmt = ($this->conn)->prepare("SELECT language FROM user  WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $_SESSION['user']['username']);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                return $row['language'];
            } else {
                throw new Exception(getErrorMessageByEnv("getUserLang: Account not found."));
            }
        } else {
            throw new Exception(getErrorMessageByEnv("getUserLang: User is not logged in."));
        }
    }

    public function setUserLang($lang)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_SESSION['user']['username'])) {
            $stmt = ($this->conn)->prepare("UPDATE user SET language = ? WHERE username = ? ");
            $stmt->bind_param("ss", $lang, $_SESSION['user']['username']);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                return true;
            } else {
                throw new Exception(getErrorMessageByEnv("setUserLang: Account not found."));
            }
        } else {
            throw new Exception(getErrorMessageByEnv("setUserLang: User id not logged in."));
        }
    }

    public function getLangList()
    {
        try {
            $stmt = ($this->conn)->prepare('SELECT * FROM languages');
            if (!$stmt) {
                throw new Exception(getErrorMessageByEnv("Prepare failed: " . $this->conn->error));
            }

            $stmt->execute();
            $result = $stmt->get_result();
            $languages = [];

            while ($row = $result->fetch_assoc()) {
                $languages[] = $row;
            }

            return $languages;
        } catch (Exception $e) {
            throw new Exception(getErrorMessageByEnv("getLangList error: ". $e->getMessage()));
        }
    }
}
