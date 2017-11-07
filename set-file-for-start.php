<?php
$dom_xml = new DomDocument(2.0, 'UTF-8');
$dom_xml->load('https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh');

$items=$dom_xml->getElementsByTagName("item");
$link = $items->item(1)->getElementsByTagName("link")->item(0)->nodeValue;

$f = fopen('.lastLink', 'w');
fwrite($f, $link);
fclose($f);

