<?php
require_once("languageManager.php");

header('Content-Type: application/json; charset=utf-8');

$langMng = new languageManager();

try {
    $languages = $langMng->getLangList();
    
    echo json_encode([
        'ok' => true,
        'languages' => $languages
    ]);
} catch(Exception $error) {
    echo json_encode([
        'ok' => false,
        'message' => $error->getMessage()
    ]);
}