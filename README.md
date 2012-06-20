0. Informacje wstępne:
Dane aplikacji są przechowywane w bazach o nazwie 'miasta', w razie konfliktu nazw należy zrobić backup swoich danych.
Skrypty pomocnicze działają na standardowym porcie mongodb i porcie 5987 dla Couchdb.

1. Dane pochodzą z wikipedii i dotyczą polskich miast. Ponieważ dump artykułów w polskiej wikipedii jest trochę duży (ok. 1 GB) napisałem skrypt w php, który wędruje po artykułach z kategorii 'Miasta polskie' zbierając interesujące go dane i zapisuje je do pliku json. Generowanie tego pliku może trochę potrwać (u mnie maksymalnie 5 minut) dlatego załączam swoje dane w pliku my.json. (Wymagania w tym kroku: wymagane uprawnienia do zapisu w katalogu ze skryptem grabdata.php, wymagane włączone rozszerzenie curl dla php)

Przed przystąpieniem do załadowania danych do kolekcji mongo proponuję uruchomić deamony Mongo i CouchDb (np. skryptami runmongod.sh i runcouch.sh. W razie niepowodzenia uruchomienia należy sprawdzić logi - odpowiednio mongod.log, i couch.log. W przypadku awarii bazy Mongo można spróbować uruchomić skrypt repairmongo.sh). Następnie uruchomić skrypt prepare.sh, który utworzy kolekcję 'miasta' w bazie 'database' i przy okazji utworzy bazę 'miasta' dla Couchdb.

2. Skrypt jsonToMongo.php pozwala na zapisanie naszych danych do kolekcji mongo. Wymagane będzie rozszerzenie Mongo dla PHP (instalacja: http://www.php.net/manual/en/mongo.installation.php)

3. Skrypt mongoToCouch.php pozwala na zapisanie danych z kolekcji Mongo do bazy CouchDb. Wymagania: PHP co najmniej 5.2 i biblioteka PHPillow (załączona w repo)

4. Kolejnym krokiem jest zapisanie widoków i dodatkowych plików (style, obrazki itp.) w bazie CouchDb. Można to zrobić skryptem ./push_views.sh (wymagane Couchapp, w widokach użyto systemu szablonów Mustache)

5. Całość jest dostępna pod adresem:
http://localhost:5987/miasta/_design/default/_list/main/miasta/ Do poprawnego działania wymagane jest połączenie z internetem (pobranie bibliotek d3js, jquery)

Uruchomiłem też działającą aplikację pod adresem
https://mmackows-nosql.iriscouch.com/miasta/_design/default/_list/main/miasta/

Głównym celem była wizualizacja różnorodności polskich miast czy to na wykresie czy na mapie, przy okazji wplątał się jakże aktualny wątek Euro 2012. Świetnie do tego nadała się biblioteka d3js, którą jestem oczarowany. Wspaniale było odkrywać jej tajemnice i choć po pierwszym spotkaniu miałem mieszane odczucia (wykres słupkowy męczyłem chyba z 5 godzin) , to po lekturze dokumentacji i kilku tutków poszło dużo lepiej - zegarek powstał w 15 minut. Z pewnością jest to projekt godny zatrzymania się przy nim na dłużej.

Przy okazji sprawdziłem jak PHP radzi sobie z nowoczesnymi bazami danych. Ze wsparciem Mongo nie było większych problemów, biblioteka obsługująca Mongo jest nieźle udokumentowana. Kilka kłopotów i niespodzianek było z CouchDb ponieważ PHP nie dorobił się jeszcze oficjalnego wsparcia, zmuszony byłem skorzystać z inicjatywy PHPillow (trochę informacji o współpracy PHP i CouchDb http://wiki.apache.org/couchdb/Getting_started_with_PHP). Z niewiadomych przyczyn omawiana biblioteka losowo odmawiała współpracy (rzucała wyjątkami), ale takie są uroki wersji beta :)

TODO
Z różnych powodów, głównie braku czasu, nie udało mi się zrealizowac wszystkich funkcjonalności. Przede wszystkim brakuje możliwości zarządzania bazą miast, nad czym najbardziej ubolewam. Ciekawe byłoby też wyznaczanie odległości między miastami, interaktywna nawigacja po mapie (zoomy, przesuwanie kursorem itp).
