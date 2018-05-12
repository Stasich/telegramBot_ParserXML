<?php

namespace Classes;

class Config {
    const TOKEN = 'Your Token';
    const METHOD = 'sendMessage';
    const CHAT_ID = '@parsim_freelansim';
    const OPTIONS = 'disable_web_page_preview=true&parse_mode=html';
    const PATH_TO_DB_FILE = __DIR__ . '/../';
    const SERVICES_FOR_PARSING = [
        [
            'link' => 'https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh',
            'prefix' => 'freelansim'
        ],
        [
            'link' => 'https://www.fl.ru/rss/all.xml?category=5',
            'prefix' => 'fl'
        ],
    ];
}
