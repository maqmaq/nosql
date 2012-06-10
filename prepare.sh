#! /bin/bash
$HOME/.nosql/bin/mongo --port=27017 < prepareMongo.js
curl -X DELETE http://localhost:5987/miasta
curl -X PUT http://localhost:5987/miasta
