<?php 
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $keys = $_POST["keys"] ?? [];
    $mtime = [];
    foreach($keys as $key) {
        $filepath = realpath(__DIR__."/../../../configs/languages/".$key.".yml");
        if(file_exists($filepath)) {
            $mtime[$key] = filemtime($filepath);
        } else {
            $mtime[$key] = null;
        }
    } 

    echo json_encode([
        "ok" => true,
        "mtime" => $mtime
    ]);
} else {
    echo json_encode([
        "ok" => false,
        "message" => "Request method not allowed."
    ]);
}