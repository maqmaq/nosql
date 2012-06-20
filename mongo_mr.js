use database;

var m = function() {
    emit('statystyki', {
        'nazwa' : this.nazwa, 
        'ludnosc' : this.ludnosc
        });
};

var r = function(key, values) {
    var total = 0;
    var count = 0;
    var l_max = {
        'nazwa' : 'xxx', 
        'ludnosc': 0
    };
    //takiego miasta to jeszcze sie nie doczekalismy
    var l_min = {
        'nazwa' : 'xxx', 
        'ludnosc': 10000000
    };
    values.forEach(function(item) {
        var nazwa = item.nazwa;
        var ludnosc = parseInt(item.ludnosc);
        total = total + parseInt(ludnosc);
        l_max = (l_max.ludnosc < ludnosc) ? item : l_max;
        l_min = (l_min.ludnosc > ludnosc) ? item : l_min;
        count++;
    });
    return {
        'iloscMiast' : count,
        'najbardziejZaludnione': l_max.nazwa + ' - ' + l_max.ludnosc,
        'najmniejZaludnione': l_min.nazwa + ' - ' + l_min.ludnosc,
        'srednieZaludnienie' : Math.floor(total/count)
    };
};

var ret = db.miasta.mapReduce(m, r, {
    out: "ms"
});
z = ret.convertToSingleObject();
db.ms.drop();
