<?php 
require_once("languageManager.php");

header('Content-Type: application/json; charset=utf-8');

$langMng = new languageManager();

try {
    $userLang = $langMng->getUserLang();
    
    echo json_encode([
        'ok' => true,
        'userLang' => $userLang
    ]);

} catch(Exception $error) {
    echo json_encode([
        'ok' => false,
        'message' => $error->getMessage()
    ]);
}
