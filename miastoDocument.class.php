
<?php

/*
 * Klasa reprezentuje dokument w sesie couchdb
 */

class miastoDocument extends phpillowDocument {

    protected static $type = 'miasto';
    protected $requiredProperties = array('nazwa', 'szerokosc', 'dlugosc', 'ludnosc', 'link');

    public function __construct() {
        $this->properties = array('nazwa' => new phpillowStringValidator(), 'szerokosc' => new phpillowStringValidator(), 'dlugosc' => new phpillowStringValidator(), 'ludnosc' => new phpillowStringValidator(), 'link' => new phpillowStringValidator());
        parent::__construct();
    }

    protected function generateId() {
//        return uniqid();
        $bezPolskich = toPermalink($this->storage->nazwa);
//        echo $bezPolskich;
        
//        return $bezPolskich;
        return $this->stringToId($bezPolskich) . '-' . uniqid();
    }

    protected function getType() {
        return self::$type;
    }

}


function toPermalink($string)
{
 
 
$unPretty = array('/ä/', '/ö/', '/ü/', '/Ä/', '/Ö/', '/Ü/', '/ß/', 
'/ą/', '/Ą/', '/ć/', '/Ć/', '/ę/', '/Ę/', '/ł/', '/Ł/' ,'/ń/', '/Ń/', '/ó/', '/Ó/', '/ś/', '/Ś/', '/ź/', '/Ź/', '/ż/', '/Ż/',
'/Š/','/Ž/','/š/','/ž/','/Ÿ/','/Ŕ/','/Á/','/Â/','/Ă/','/Ä/','/Ĺ/','/Ç/','/Č/','/É/','/Ę/','/Ë/','/Ě/','/Í/','/Î/','/Ď/','/Ń/',
'/Ň/','/Ó/','/Ô/','/Ő/','/Ö/','/Ř/','/Ů/','/Ú/','/Ű/','/Ü/','/Ý/','/ŕ/','/á/','/â/','/ă/','/ä/','/ĺ/','/ç/','/č/','/é/','/ę/',
'/ë/','/ě/','/í/','/î/','/ď/','/ń/','/ň/','/ó/','/ô/','/ő/','/ö/','/ř/','/ů/','/ú/','/ű/','/ü/','/ý/','/˙/',
'/Ţ/','/ţ/','/Đ/','/đ/','/ß/','/Œ/','/œ/','/Ć/','/ć/','/ľ/');
 
$pretty  = array('ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss', 
'a', 'A', 'c', 'C', 'e', 'E', 'l', 'L', 'n', 'N', 'o', 'O', 's', 'S', 'z', 'Z', 'z', 'Z',
'S','Z','s','z','Y','A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N',
'O','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e',
'e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y',
'TH','th','DH','dh','ss','OE','oe','AE','ae','u');
 
$permalink = strtolower(preg_replace($unPretty, $pretty, $string));
return str_replace(" ", "-", preg_replace("/[^a-zA-Z0-9 ]/", "", $permalink) );
 
}
?>
