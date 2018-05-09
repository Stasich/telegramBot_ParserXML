<?php
require_once __DIR__ . '/autoload.php';

use Classes\ParsimFreelansim;
use Classes\Config;

$debug = array_search('--debug', $argv);

$parser = ParsimFreelansim::getParser();
$newPostsInArr = $parser->getNewPostsInArr();

if ($debug === FALSE) {
    $parser->sendPostsToTelegram(
        $newPostsInArr,
        Config::TOKEN,
        Config::METHOD,
        Config::CHAT_ID,
        Config::OPTIONS
    );
} else {
    $parser->sendPostsToTerminal($newPostsInArr);
}
