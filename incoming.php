<?php

$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root.'/kbot/telegram/logger.php';
require_once 'router.php';

// receive from telegram
$update = json_decode(file_get_contents("php://input"), TRUE);
$userId = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

// determine routing
$router = new router($userId,$message);
$router->send();

// logger
$text = "\nReceive from Telegram";
$text.= $router->logMessage;
$logger = new logger();
$logger->log($text);

?>
