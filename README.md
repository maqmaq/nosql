0. Informacje wstępne:
Dane aplikacji są przechowywane w bazach o nazwie 'miasta', w razie konfliktu nazw należy zrobić backup swoich danych.
Skrypty pomocnicze działają na standardowym porcie mongodb i porcie 5987 dla Couchdb.

1. Dane pochodzą z wikipedii i dotyczą polskich miast. Ponieważ dump artykułów w polskiej wikipedii jest trochę duży (ok. 1 GB) napisałem skrypt w php, który wędruje po artykułach z kategorii 'Miasta polskie' zbierając interesujące go dane i zapisuje je do pliku json (wymagane uprawnienia do zapisu w katalogu ze skryptem). Generowanie tego pliku może trochę potrwać (u mnie maksymalnie 5 minut) dlatego załączam swoje dane w pliku my.json. (wymagane rozszerzenie curl dla php)

Przed przystąpieniem do załadowania danych do kolekcji mongo proponuję uruchomić deamony Mongo i CouchDb (np. skryptami runmongod.sh i runcouch.sh. W razie niepowodzenia uruchomienia należy sprawdzić logi - odpowiednio mongod.log, i couch.log. W przypadku awarii bazy Mongo można spróbować uruchomić skrypt repairmongo.sh). Następnie uruchomić skrypt prepare.sh, który utworzy kolekcję 'miasta' w bazie 'database' i przy okazji utworzy bazę 'miasta' dla Couchdb.
2. Skrypt jsonToMongo.php pozwala na zapisanie naszych danych do kolekcji mongo. Być może wymagane będzie rozszerzenie Mongo dla PHP (instalacja :http://www.php.net/manual/en/mongo.installation.php)
3. Skrypt mongoToCouch.php pozwala na zapisanie danych z kolekcji Mongo do bazy CouchDb. Wymagania: PHP co najmniej 5.2 i biblioteka PHPillow (załączona w repo)
4. Kolejnym krokiem jest zapisanie widoków i dodatkowych plików (style, obrazki itp.) w bazie CouchDb. Można to zrobić skryptem ./push_views.sh (wymagane Couchapp, w widokach użyto systemu szablonów Mustache)
2
5. Całość jest dostępna pod adresem:
http://localhost:5987/miasta/_design/default/_list/main/miasta/
Do poprawnego działania wymagane jest połączenie z internetem (pobranie bibliotek d3js, jquery)
