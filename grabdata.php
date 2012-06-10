<?php
//to moze troche potrwac, u mnie maksymalnie okolo 3-4 minut
set_time_limit(600);
ini_set('display_errors', 1);
error_reporting(-1);

class WikiParser {

    protected $url;
    protected $baseUrl;
    protected $file;
    protected $limit;
    protected $counter;

    public function __construct($url = null, $baseUrl = null, $file = null, $limit = null) {
        $this->url = ($url === null) ? 'http://pl.wikipedia.org/wiki/Miasta_w_Polsce' : $url;
        $this->baseUrl = ($baseUrl === null) ? 'http://pl.wikipedia.org' : $baseUrl;
        $this->file = ($file === null) ? 'my.json' : $file;
        $this->limit = ($limit === null ) ? 0 : $limit;
        $this->counter = 0;
    }

    public function run() {
        $data = $this->getData();
        @$this->parseData($data);
    }

    public function getData() {
        $content = @file_get_contents($this->url);
        if (!$content) {
            die('Brak polaczenia z internetem?');
        }
        return $content;
    }

    public function parseData($data) {
        $doc = new DOMDocument();
        @$doc->loadHTML($data);
        $elements = $doc->getElementsByTagName('table');
        unset($doc);

        $tabela = $elements->item(0)->ownerDocument->saveXml($elements->item(0));

        $doc = new DOMDocument();
        @$doc->loadHTML($tabela);
        $lis = $doc->getElementsByTagName('li');
        unset($doc);

        $links = array();
        $length = $lis->length;
        for ($i = 0; $i < $length; $i++) {
            if ($someLink = $this->getLinksFromNode($lis->item($i))) {
                $links[] = $someLink;
            }
        }

        $infos = array();
        foreach ($links as $link) {
            $link = $link->getAttribute('href');
            if ($info = $this->getInfo($this->baseUrl, $link)) {
                $this->counter++;
                if ($this->limit > 0 && $this->counter > $this->limit)
                    break;
                $infos[] = $info;
                $this->saveToFile($infos);
            }
        }

        echo 'Pobrano ' . $this->counter . ' danych<br/>';
    }

    public function getLinksFromNode($node) {
        $children = $node->childNodes;
        foreach ($children as $child) {
            if (method_exists($child, 'hasAttribute') && $child->hasAttribute('href')
//                    && strpos($child->getAttribute('href'), 'Czarna') !== false
            ) {
                return $child;
            }
        }
    }

    public function getInfo($base, $uri) {

        $linkWiki = $base . urldecode($uri);
        $content = file_get_contents($linkWiki);
        if (!$content) {
            echo '-x- Nieudana próba na ' . $linkWiki . '<br/>';
            echo '-?- Próbuję odczytać cURLem na ' . $linkWiki . '<br/>';
            $content = curl_download($linkWiki);
            if (!$content) {
                echo '-!- Pomijam ' . $linkWiki . '<br/>';
                return false;
            }
        }

        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $tds = $doc->getElementsByTagName('td');
        unset($doc);
        $length = $tds->length;
        $nazwa = false;
        for ($i = 0; $i < $length; $i++) {
            $element = $tds->item($i);
            if ($element->hasAttribute('colspan') && $element->getAttribute('colspan') == 2) {
                $nazwa = $element;
                break;
            }
        }

//mam nazwe miasta
        $nazwa = strip_tags($nazwa->ownerDocument->saveXml($nazwa));
        echo $nazwa . '<br/>';
        $doc = new DOMDocument();
        @$doc->loadHTML($content);
        $spans = $doc->getElementsByTagName('span');
        unset($doc);
        $length = $spans->length;
        $szerokosc = false;
        $dlugosc = false;
        for ($i = 0; $i < $length; $i++) {
            $item = $spans->item($i);
            if ($item->hasAttribute('class') && $item->getAttribute('class') == 'longitude') {
                $dlugosc = self::oczysc($item->ownerDocument->saveXml($item));
                break;
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $item = $spans->item($i);
            if ($item->hasAttribute('class') && $item->getAttribute('class') == 'latitude') {
                $szerokosc = self::oczysc($item->ownerDocument->saveXml($item));
                break;
            }
        }

        $ludnosc = false;
        $doc = new DOMDocument();
        $doc->loadHTML($content);
        $tds = $doc->getElementsByTagName('td');
        unset($doc);

        $length = $tds->length;
        $tdWithLiczba = false;
        for ($i = 0; $i < $length; $i++) {
            $item = $tds->item($i);
            $html = htmlspecialchars($item->ownerDocument->saveXml($item));
            $search = 'title=&quot;Liczba ludno';

            if (strpos($html, $search) !== false) {
                $tdWithLiczba = ($tds->item($i + 1));
                break;
            }
        }
        if ($tdWithLiczba) {
            $ludnosc = self::oczyscOproczCyfr($tdWithLiczba);
        }

        $array_to_return = array('nazwa' => $nazwa, 'szerokosc' => $szerokosc, 'dlugosc' => $dlugosc, 'ludnosc' => $ludnosc, 'link' => $linkWiki);
//        _d($array_to_return);
        return $array_to_return;
    }

    public function saveToFile($data) {

        $data = json_encode($data);
        return file_put_contents($this->file, $data);
    }

    public static function oczysc($arg) {
        $r = preg_replace('/[°′″]+/', '_', strip_tags($arg)); //wypindalam wszystko procz cyfr zeby uniknac smieci i paprochow
//        $r = preg_replace('/[�+/', '_', $r); //wypindalam wszystko procz cyfr zeby uniknac smieci i paprochow
//        _d($r);
//        $r = preg_replace('/[″]+/', '_', $r); //wypindalam wszystko procz cyfr zeby uniknac smieci i paprochow
//        _d($r);
        $r = preg_replace('/[^\d,\._]+/', '', $r); //wypindalam wszystko procz cyfr zeby uniknac smieci i paprochow
//        echo $r . '<br/>';
        $r = preg_replace('/_/', '°', $r, 1); //zmieniam po kolei na ustalone symbole
//        echo $r . '<br/>';
        $r = preg_replace('/_/', '′', $r, 1);
//        echo $r . '<br/>';
        $r = preg_replace('/_/', '″', $r, 1);

//        echo $r;
        return $r;
    }

    public static function oczyscOproczCyfr($arg) {

        $textElements = array();
        foreach ($arg->childNodes as $child) {
            if ($child instanceof DOMText) {
                $textElements[] = $child;
            }
        }

        $element = current($textElements);
        $string = $element->ownerDocument->saveXml($element);
//        echo '<br/>--' . htmlspecialchars($string) . '-----<br/>';
        $string = str_replace('&nbsp;', ' ', $string);
        $string = trim($string);
//        $string = preg_replace('/[^0-9]/', '', $string);
        $length = strlen($string);

        $firstDigit = false;
        for ($i = 0; $i < $length; $i++) {
            if (is_numeric($string[$i])) {
                $firstDigit = $i;
                break;
            }
        }

        $string = substr($string, $firstDigit);

        $length = strlen($string);
        $lastDigit = false;
        for ($i = 0; $i < $length; $i++) {
            if (!preg_match('/[a-z]/', $string[$i])) {
                $lastDigit = $i;
            } else {
                break;
            }
        }

        $string = substr($string, 0, $lastDigit + 1);
        $string_return = preg_replace('/[^0-9]/', '', $string);
//        echo htmlspecialchars($string_return);
        return $string_return;
    }

}

function curl_download($Url) {

// is cURL installed yet?
    if (!function_exists('curl_init')) {
        die('Sorry cURL is not installed!');
    }

// OK cool - then let's create a new cURL resource handle
    $ch = curl_init();

// Now set some options (most are optional)
// Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);

// Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");

// User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

// Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 1);

// Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Download the given URL, and return output
    $output = curl_exec($ch);

// Close the cURL resource, and free system resources
    curl_close($ch);

    return $output;
}

//echo '<!doctype html><html lang="pl" dir="ltr"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><title>Grabdata</title></head><body>';
$wp = new WikiParser(null, null, null, 999);
$wp->run();
//echo '</body></html>';

function _d($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}
