<?php

include_once __DIR__ . "/../config/librewitless.php";
include_once __DIR__ . "/../include/telegram.php";

echo json_encode(
	Telegram::setWebhook(LW_URL_BASE . "/bot/handle.php")
);