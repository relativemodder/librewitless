<?php

include_once __DIR__ . "/../include/librewitless.php";
include_once __DIR__ . "/../include/telegram.php";


$update = json_decode(file_get_contents("php://input"), true);

if (!array_key_exists("message", $update)) {
    die("Да мне похуй");
}

$message = $update["message"];
$chat_id = $message["chat"]["id"];


LibreWitless::learnFromText($chat_id, $message["text"]);
LibreWitless::increaseChatMessageCounter($chat_id);

$counter = LibreWitless::getChatMessageCounter($chat_id);

if ($counter % LW_WORDS_GAP == 0) {
    $sentence = LibreWitless::generateSentence($chat_id);
    Telegram::sendMessage($chat_id, $sentence);
}
