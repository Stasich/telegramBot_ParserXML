<?php

namespace Classes;

use Classes\Database\TableLinks;

class ParsimFreelansim
{
    private static $parser = null;
    private $prefix;
    private $link_for_parse;

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
     * @param array $service
     */
    public function setConfig($service) {
        $this->prefix = $service['prefix'];
        $this->link_for_parse = $service['link'];
    }

    private function curlLoad($url) {
        $cookie = tmpfile();
        $userAgent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.64 Safari/537.31' ;

        $ch = curl_init($url);

        $options = array(
            CURLOPT_CONNECTTIMEOUT => 20 ,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEFILE => $cookie,
            CURLOPT_COOKIEJAR => $cookie ,
            CURLOPT_SSL_VERIFYPEER => 0 ,
            CURLOPT_SSL_VERIFYHOST => 0
        );

        curl_setopt_array($ch, $options);
        $kl = curl_exec($ch);
        curl_close($ch);
        return $kl;
    }

	/**
	 * @return \DOMNodeList
	 */
    private function getXmlItems()
    {
        $dom_xml = new \DomDocument(2.0, 'UTF-8');
        $dom_xml->loadXML($this->curlLoad($this->link_for_parse));
        $items = $dom_xml->getElementsByTagName("item");
        return $items;
    }

    /**
     * @return array
     */
    public function getNewPostsInArr()
    {
        $items = $this->getXmlItems();

        $patternForRegexp = '/(<br>)+/';  // шаблон для замены <br> на \n в description

        $tableLinks = TableLinks::getInstance();
        $patternForXML = $tableLinks->getLinks($this->prefix);

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
            $tableLinks->addViewedLinksToDb($viewed_links, $this->prefix);
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
