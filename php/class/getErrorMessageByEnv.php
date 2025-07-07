<?php 
require_once('getConfig.php');
function getErrorMessageByEnv($devMessage, $defaultMessage = "Something went wrong.")
{
    try {
        $config = getConfig();
        if (isset($config['env']) && strtolower($config['env']) === 'dev') {
            return $devMessage;
        }
    } catch (Exception $e) {
        return $e->getMessage();
    }

    return $defaultMessage;
}