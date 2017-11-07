<?php
require_once 'settings_for_api.php';
$dom_xml = new DomDocument(2.0, 'UTF-8');
$dom_xml->load('https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh');

$items=$dom_xml->getElementsByTagName("item");
$date = $items->item(0)->getElementsByTagName("pubDate");

$arrData = []; //
foreach ($items as $item )
{
    $date = $item->getElementsByTagName("pubDate");
    $date = strip_tags(trim($date[0]->nodeValue),'<a>');

    $title = $item->getElementsByTagName("title");
    $title = strip_tags(trim($title[0]->nodeValue),'<a>');

    $link = $item->getElementsByTagName("link");
    $link = strip_tags(trim($link[0]->nodeValue),'<a>');

    $description = $item->getElementsByTagName("description");
    $description = strip_tags(trim($description[0]->nodeValue),'<a>');
    //$arrData[] = compact('date', 'title', 'link', 'description' );
    $str = "<b>" .$date . "</b>\n<b>" . $title . "</b>\n" . $description . "\n" . $link;
    $str = urlencode($str);

    $query = "https://api.telegram.org/bot$token/$method?$options&text=$str";
    file($query);
    break;
}