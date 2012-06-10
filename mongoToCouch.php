<?php

/*
  Wymaga rozszerzenia mongo. Instalacja
  http://www.php.net/manual/en/mongo.installation.php#mongo.installation.nix
 * PHP w wersji co najmniej 5.2
 * PHPillow * http://arbitracker.org/phpillow/download.html calaczony w repo
 * skrypt zaklada ze baza docelowa zostala utworzona
 */

require_once('PHPillow-0.6-beta/bootstrap.php');
require_once('miastoDocument.class.php');

ini_set('display_errors', 'On');
error_reporting(-1);

class mongoToCouchExporter {

    protected $from_host;
    protected $from_collection;
    protected $from_port;
    protected $from_database;
    protected $to_host;
    protected $to_port;
    protected $to_database;

    public function __construct($f_host = 'localhost', $f_port = '27017', $f_database = 'database', $f_collection = 'miasta', $t_host = 'localhost', $t_port = '5987', $t_database = 'miasta') {
        $this->from_host = $f_host;
        $this->from_database = $f_database;
        $this->from_collection = $f_collection;
        $this->from_port = $f_port;
        $this->to_host = $t_host;
        $this->to_port = $t_port;
        $this->to_database = $t_database;
    }

    public function run() {
        $data = $this->getDataFromMongo();
        $this->saveDataToCouch($data);
        die('relax');
    }

    public function getDataFromMongo() {

        $connectTo = "{$this->from_host}:{$this->from_port}";
        try {
            $mongo = new Mongo($connectTo);
            $collection = $mongo->selectDb($this->from_database)->selectCollection($this->from_collection);
        } catch (Exception $e) {
            die("Nie mogę nazwiązać połączenia z bazą mongo ({$connectTo} - db:{$this->from_database}, col: {$this->from_collection}) ");
        }

// find everything in the collection
        $cursor = $collection->find();

        $array_out = array();
        foreach ($cursor as $obj) {
            $array_out[] = $obj;
        }

        return $array_out;
    }

    public function saveDataToCouch($data) {

        try {
            phpillowConnection::createInstance($this->to_host, $this->to_port, '', '');
            phpillowConnection::setDatabase($this->to_database);
        } catch (Exception $e) {
            die("Nie moge nawiazac polaczenia z baza Couch ({$this->to_host}:{$this->to_port})");
        }

        $allowKeys = array(
            'nazwa', 'dlugosc', 'szerokosc', 'ludnosc', 'link'
        );
        foreach ($data as $k => $value) {
            if (is_array($value) && !empty($value)) {
                $doc = new miastoDocument();
                foreach ($value as $k1 => $v1) {
                    if (in_array($k1, $allowKeys)) {
                        $doc->$k1 = $v1;
                    }
                }
                $doc->save();
            }
        }
    }

}

$exporter = new mongoToCouchExporter();
$exporter->run();

function _d($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}

?>
