<?php
/*
  Wymaga rozszerzenia mongo. Instalacja
  http://www.php.net/manual/en/mongo.installation.php#mongo.installation.nix
 */

ini_set('display_errors', 'On');

class jsonToMongoExporter {

    protected $file;
    protected $host;
    protected $collection;
    protected $port;
    protected $database;

    public function __construct($file = 'my.json', $host = 'localhost', $port = '27017', $database = 'database', $collection = 'miasta') {
        $this->file = $file;
        $this->host = $host;
        $this->database = $database;
        $this->collection = $collection;
        $this->port = $port;
    }

    public function run() {
        $data = $this->getDataFromFile();
        $this->saveDataToMongo($data);
die('relax');
    }

    public function getDataFromFile() {
        $data = file_get_contents($this->file);
        return json_decode($data);
    }

    public function saveDataToMongo($data) {
        $connectTo = "{$this->host}:{$this->port}";
        $mongo = new Mongo($connectTo);
        $collection = $mongo->selectDb($this->database)->selectCollection($this->collection);
       
        foreach ($data as $k => $v) {
		if ($v)
            $collection->insert((array)$v);
        }
    }

}

$exporter = new jsonToMongoExporter();
$exporter->run();

/*
 * use database
 * db.miasta.find()
 */

function _d($arg) {
    echo '<pre>';
    var_dump($arg);
    echo '</pre>';
}
?>
