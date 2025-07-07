<?php 
require_once('../getConfig.php');
require_once('../getErrorMessageByEnv.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $keys = $_POST["keys"] ?? [];
    $mtime = [];
    foreach($keys as $key) {
        if (preg_match('/^[a-z]{2}$/', $key)) {
            $filepath = realpath(__DIR__."/../../../languages/".$key.".yml");
            if(file_exists($filepath)) {
                $mtime[$key] = filemtime($filepath);
            } else {
                $mtime[$key] = null;
            }
        }
    } 

    echo json_encode([
        "ok" => true,
        "mtime" => $mtime
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "message" => getErrorMessageByEnv('Request method not allowed.')
    ]);
}