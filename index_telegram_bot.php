<?php
require_once 'model.php';
require_once 'settings_for_api.php';

$debug = array_search('--debug', $argv);

$parser = ParsimFreelansim::getParser();
$newPostsInArr = $parser->getNewPostsInArr();
if ( $debug === false )
    $parser->sendPostsToTelegram($newPostsInArr, $token, $method, $chat_id, $options );
else
    $parser->sendPostsToTerminal($newPostsInArr);
