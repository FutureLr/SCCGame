<?php
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require_once('../getConfig.php');
    require_once("languageManager.php");

    header('Content-Type: application/json; charset=utf-8');

    $langMng = new languageManager();

    try {
        if (!isset($_POST['userLang'])) throw new Exception("Missing parameter: userLang");
        $userLang = $langMng->setUserLang($_POST['userLang']);

        echo json_encode([
            'ok' => true
        ]);
    } catch (Exception $error) {
        echo json_encode([
            'ok' => false,
            'message' => $error->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        "ok" => false,
        "message" => getErrorMessageByEnv('Request method not allowed.')
    ]);
}
