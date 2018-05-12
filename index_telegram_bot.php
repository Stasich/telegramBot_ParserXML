<?php
require_once __DIR__ . '/autoload.php';

use Classes\ParsimFreelansim;
use Classes\Config;

$debug = array_search('--debug', $argv);

try {
    $parser = ParsimFreelansim::getParser();
    foreach (Config::SERVICES_FOR_PARSING as $service) {
        $parser->setConfig($service);

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
    }
} catch (Exception $exception) {
    file_put_contents(
        __DIR__ . '/log/parser.log',
        $exception->getMessage() . "\n",
        FILE_APPEND
    );
}
