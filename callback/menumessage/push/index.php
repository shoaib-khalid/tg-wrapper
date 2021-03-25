<?php
// include classes
$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root.'/kbot/telegram/logger.php';
require_once 'callback.php';

// receive callback from core
$cb = new kbotcore\callback();

// construct response to telegram
$path = "https://api.telegram.org/bot1534661646:AAHtbvPxQGgKH3R-ne6qe0sURTrz7hgOU4M";
$response = urlencode($cb->message);
$sendmessage = "chat_id=".$cb->msisdn."&text=".$response."&parse_mode=markdown";
file_get_contents($path."/sendmessage?".$sendmessage);

// logger
$text = "\nReceive from Telegram";
$text.= "\n\tchatid:".$chatId.", msg:".$message;
$text.= "\n\tresponse:".$sendmessage;
$logger = new logger();
$logger->log($text);

?>
