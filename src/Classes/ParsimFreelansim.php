<?php

namespace Classes;

use Classes\Database\TableLinks;

class ParsimFreelansim
{
    private static $parser = null;
    private $lastLink = '.lastLink';
    private $link_for_parse = 'https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh';

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

	/**
	 * @return ParsimFreelansim
	 */
    static function getParser()
    {
        if (is_null(self::$parser))
            self::$parser = new ParsimFreelansim();
        return self::$parser;
    }

	/**
	 * @param \DOMNodeList $items
	 * @return bool|string
	 */
    private function getPatternForXML($items)
    {
        if (!is_readable($this->lastLink)) {
            if (file_exists($this->lastLink)) {
                die ("$this->lastLink - access is denied");
            }
            $fp = fopen( $this->lastLink, 'w') or die ("can't create/use $this->lastLink");
            $link = $items->item(1)->getElementsByTagName("link")->item(0)->nodeValue;
            fwrite($fp, $link);
            fclose($fp);
        }
        $f = fopen($this->lastLink, 'r');
        $patternForXML = fgets($f);
        fclose($f);
        return $patternForXML;
    }

    private function putPatternForXML($link)
    {
        $f = fopen($this->lastLink, 'w') or die ("can't put link in $this->lastLink");
        fwrite($f, $link );
        fclose($f);
    }

	/**
	 * @return \DOMNodeList
	 */
    private function getXmlItems()
    {
        $dom_xml = new \DomDocument(2.0, 'UTF-8');
        $dom_xml->load($this->link_for_parse);
        $items = $dom_xml->getElementsByTagName("item");
        return $items;
    }

    public function getNewPostsInArr()
    {
        $items = $this->getXmlItems();

        $patternForRegexp = '/(<br>)+/';  // шаблон для замены <br> на \n в description

        $tableLinks = TableLinks::getInstance();
        $patternForXML = $tableLinks->getLinks();

        $newPostsInArr = [];
        $viewed_links = [];

        foreach ($items as $item) {
            $date = $item->getElementsByTagName("pubDate");
            $date = $date[0]->nodeValue;

            $title = $item->getElementsByTagName("title");
            $title = strip_tags(trim($title[0]->nodeValue), '<a>');

            $link = $item->getElementsByTagName("link");
            $link = $link[0]->nodeValue;

            $description = $item->getElementsByTagName("description");
            $description = strip_tags(preg_replace($patternForRegexp, "\n", trim($description[0]->nodeValue)), '<a>');

            if (array_key_exists($link, $patternForXML)) {
                break;
            }
            $viewed_links[] = $link;

            $newPostsInArr[] = compact('date', 'title', 'link', 'description');
        }

        if (count($newPostsInArr) > 0) {
            $tableLinks->addViewedLinksToDb($viewed_links);
        }
        return $newPostsInArr;
    }

    public function sendPostsToTelegram($newPostsInArr, $token, $method, $chat_id, $options)
    {
        $postsCount = count($newPostsInArr);
        if ($postsCount === 0) return;
        for ($i = $postsCount - 1; $i >= 0; $i--) {
            $str = "<b>" . $newPostsInArr[$i]['date'] . "</b>\n<b>" .
                $newPostsInArr[$i]['title'] . "</b>\n" .
                $newPostsInArr[$i]['description'] . "\n" .
                $newPostsInArr[$i]['link'];
        $str = urlencode($str);
        $query = "https://api.telegram.org/bot$token/$method?chat_id=$chat_id&$options&text=$str";
        file($query);
        }
    }
    public function sendPostsToTerminal($newPostsInArr)
    {
        $postsCount = count($newPostsInArr);
        if ($postsCount === 0) {
            echo "Новых постов нет\n";
            return;
        }
        for ($i = $postsCount - 1; $i >= 0; $i--) {
            $str = "\n" . $newPostsInArr[$i]['date'] . "\n" .
                $newPostsInArr[$i]['title'] . "\n" .
                $newPostsInArr[$i]['description'] . "\n" .
                $newPostsInArr[$i]['link'] . "\n";
            echo $str;
        }
    }
}
