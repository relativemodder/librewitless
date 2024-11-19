<?php

include_once __DIR__ . "/../config/librewitless.php";
include_once __DIR__ . "/telegram.php";


class LibreWitless {

    public static function parseWords($text) {
        return explode(" ", $text);
    }

    public static function learnFromText($chat_id, $text) {
        $words = LibreWitless::parseWords($text);

        include_once __DIR__ . "/database.php";

		$cursor = Database::getCursor();

        foreach ($words as $word) {
            $query_get = $cursor->prepare("SELECT count(*) FROM lw_words WHERE `chat_id` = ? AND `word` LIKE ?");
            $query_get->execute([ $chat_id, '%' . $word . '%' ]);

            $count = $query_get->fetchColumn();

            if ($count > 0) {
                continue; 
            }

            $word = str_replace([".,/!()"], ["      "], $word);
            $word = trim($word);
            $word = strtolower($word);

            $query = $cursor->prepare("INSERT INTO lw_words (`chat_id`, `word`) VALUES (?, ?)");
            $query->execute([$chat_id, $word]);
        }
		
    }

    public static function getChatMessageCounter($chat_id) {
        include_once __DIR__ . "/database.php";
        $cursor = Database::getCursor();
        $query = $cursor->prepare("SELECT counter FROM lw_chat_counters WHERE chat_id = ?");
        $query->execute([$chat_id]);

        $result = $query->fetchColumn();

        if ($result == null) {
            $query1 = $cursor->prepare("INSERT INTO lw_chat_counters VALUES (?, ?)");
            $query1->execute([$chat_id, 0]);
            return 0;
        }

        return $result;
    }

    public static function increaseChatMessageCounter($chat_id) {
        $old_count = LibreWitless::getChatMessageCounter($chat_id);

        include_once __DIR__ . "/database.php";
        $cursor = Database::getCursor();
        $query = $cursor->prepare("UPDATE lw_chat_counters SET counter = ? WHERE chat_id = ?");
        $query->execute([
            $old_count + 1, 
            $chat_id
        ]);
    }

    public static function generateSentence($chat_id) {
        include_once __DIR__ . "/database.php";
        $cursor = Database::getCursor();
        $query = $cursor->prepare("SELECT word FROM lw_words WHERE chat_id = ? ORDER BY RAND() LIMIT 20");
        $query->execute([$chat_id]);
        $words = $query->fetchAll(PDO::FETCH_ASSOC);
        $sentence = [];
        $bayans = [];

        for ($i = 0; $i < rand(1, count($words)); $i++) {
            $word = $words[$i]['word'];

            $word = strtolower($word);

            if (in_array($word, $bayans)) {
                continue;
            }

            $bayans[] = $word;
            $sentence[] = $word;
        }

        return implode(" ", $sentence);
    }

}