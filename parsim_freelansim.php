<?php
require_once 'settings_for_api.php';
$dom_xml = new DomDocument(2.0, 'UTF-8');
$dom_xml->load('https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh');

$items=$dom_xml->getElementsByTagName("item");

$patternForRegexp = '/(<br>)+/';  // шаблон для замены <br> на \n в description

$f = fopen('.lastLink', 'r+') or die("can't read");
$patternForXML = fgets($f);
fclose($f);
//$patternForXML = ( file_exists('.lastLink') ) ? file_get_contents('.lastLink') : die("Can't open file");

$count = 0;
foreach ($items as $item )
{
    $date = $item->getElementsByTagName("pubDate");
    $date = $date[0]->nodeValue;

    $title = $item->getElementsByTagName("title");
    $title = strip_tags( trim($title[0]->nodeValue),'<a>' );

    $link = $item->getElementsByTagName("link");
    $link = $link[0]->nodeValue;

    $description = $item->getElementsByTagName("description");
    $description = strip_tags( preg_replace( $patternForRegexp, "\n", trim( $description[0]->nodeValue ) ),'<a>' );

    if ($link === $patternForXML)
    {
        break;
    }

    $str = "<b>" .$date . "</b>\n<b>" . $title . "</b>\n" . $description . "\n" . $link;
    $str = urlencode($str);
    $query = "https://api.telegram.org/bot$token/$method?$options&text=$str";
    file($query);
    $count++;
}

//если файла для сравнения нет, или с не было совпадений с ним
if ( $count !== 0 )
{
    $link = $items->item(0)->getElementsByTagName("link")->item(0)->nodeValue;
    $f = fopen('.lastLink', 'w');
    fwrite($f, $link);
    fclose($f);
}