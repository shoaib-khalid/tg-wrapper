<?php

// include classes
$root = $_SERVER["DOCUMENT_ROOT"];
require_once $root.'/kbot/twilio/liveagent.php';
require_once $root.'/kbot/twilio/router.php';
require_once $root.'/kbot/twilio/logger.php';
require_once 'callback.php';

// receive callback from core
$cb = new kbotcore\callback();

// add msisdn to liveagent routing
$handover = new liveagent();
$handover->insert($cb->msisdn);

// send to liveAgent
$msgToCS = "*Handover from Whatsapp*\nPlease greet the customer (eg: Hi, how may I assist you today?)";
$router = new router($cb->msisdn,$msgToCS);
$router->send();

// logger
$text = "\nCore signal handover to RocketChat";
$text.= $router->logMessage;
$logger = new logger();
$logger->log($text);

?>
