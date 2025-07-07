<?php
require_once("../../libs/Spyc.php");

function getConfig()
{
    $path = realpath(__DIR__ . "/../../configs/config.yml");
    if (!$path || !file_exists($path)) {
        throw new Exception("Config file not found.");
    }
    $fileModifiedTime = filemtime($path);

    if (session_status() === PHP_SESSION_NONE) session_start();

    if (
        isset($_SESSION['config'], $_SESSION['config_mtime']) &&
        $_SESSION['config_mtime'] === $fileModifiedTime
    ) {
        return $_SESSION['config'];
    } else {
        $config = Spyc::YAMLLoad($path);
        $_SESSION['config'] = $config;
        $_SESSION['config_mtime'] = $fileModifiedTime;
        return $config;
    }
}
