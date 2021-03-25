<?php

// include classes
$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root.'/kbot/telegram/liveagent.php';
require_once $root.'/kbot/telegram/logger.php';
require_once 'callback.php';

// receive callback from core
$cb = new kbotcore\callback();

// remove msisdn from liveAgent
$handover = new liveagent();
$handover->remove($cb->msisdn);

// logger
$text = "\nRocketChat close agent session";
$logger = new logger();
$logger->log($text);

?>
