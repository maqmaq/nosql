var couchapp = require('couchapp'),
path = require('path')
, fs = require('fs');

ddoc = {
    _id:   '_design/default'
    , 
    views: {}
    , 
    lists: {}
    , 
    shows: {}
    , 
    templates: {}
}
module.exports = ddoc;

couchapp.loadAttachments(ddoc, path.join(__dirname, '_attachments'));

ddoc.templates.mustache = fs.readFileSync('templates/mustache.js', 'UTF-8');
ddoc.templates['miasto.html'] = fs.readFileSync('templates/miasto.html.mustache', 'UTF-8');
ddoc.templates['lista_miast_miasto.html'] = fs.readFileSync('templates/lista_miast_miasto.html.mustache', 'UTF-8');  
ddoc.templates['lista_miast_miasta.html'] = fs.readFileSync('templates/lista_miast_miasta.html.mustache', 'UTF-8');  
ddoc.templates['default.html'] = fs.readFileSync('templates/default.html.mustache', 'UTF-8');  
ddoc.templates['box.html'] = fs.readFileSync('templates/box.html.mustache', 'UTF-8');  
ddoc.templates['navigation.html'] = fs.readFileSync('templates/navigation.html.mustache', 'UTF-8');  
ddoc.templates['welcome.html'] = fs.readFileSync('templates/welcome.html.mustache', 'UTF-8');  
ddoc.templates['statystyki.html'] = fs.readFileSync('templates/statystyki.html.mustache', 'UTF-8');  

ddoc.views.miasta = {};

ddoc.views.miasta.map = function(doc) {
    if (true) {
        emit(doc.id, doc);
    }
    
}

// show dla pojedynczego miasta
ddoc.shows.miasto = function(doc, req) {
    
//    start({
//        "headers": {
//            "Content-Type": "text/html"
//        }
//    });
    
    var mustache = require('templates/mustache');
    //szablony 
    var main_template = this.templates['default.html'];
    var content_template = this.templates['miasto.html'];
    var navigation_template = this.templates['navigation.html'];
    var box_template = this.templates['box.html'];
    //htmle
    var content_html = mustache.to_html(content_template, {
        nazwa : doc.nazwa,
        szerokosc : doc.szerokosc,
        dlugosc : doc.dlugosc,
        ludnosc : doc.ludnosc,
        link : doc.link
    });   
    
    var html = mustache.to_html(main_template, {
        content : content_html,
        navigation : navigation_template,
        box: box_template
    });   
    
    send(html);
}

//list miast posortowanych po zaludnieniu
ddoc.lists.miasta = function(head, req) {
    var row;
    var rows = [];
    start({
        "headers": {
            "Content-Type": "text/html"
        }
    });

    while(row = getRow()) {
        rows.push(row);
    }
    
    var alfabet = 'AĄBCĆDEĘFGHIJKLŁMNŃOÓPRSŚTUWXYZŹŻaąbcćdeęfghijklłmnńoóprsśtuwxyzźż ';

    var alpha = function(alphabet, dir, caseSensitive){
        return function(a, b){
            
            a = a.value.nazwa;
            b = b.value.nazwa;
           
            var pos = 0,
            min = Math.min(a.length, b.length);
            dir = dir || 1;
            caseSensitive = caseSensitive || false;
            if(!caseSensitive){
                a = a.toLowerCase();
                b = b.toLowerCase();
            }
            while(a.charAt(pos) === b.charAt(pos) && pos < min){
                pos++;
            }
            return alphabet.indexOf(a.charAt(pos)) > alphabet.indexOf(b.charAt(pos)) ?
            dir:-dir;
        };
    };

    rows.sort(alpha(alfabet));
    
    var object= {
        miasta: rows
    };
    
    var redirect = req.query.cityselect;
    if (redirect != undefined) {
        object.redirect = '/miasta/_design/default/_show/miasto/' + redirect;
    }
        
    var mustache = require('templates/mustache');
    var template_list = this.templates['lista_miast_miasta.html'];
           
    var html_content = mustache.to_html(template_list, object);
    
    var main_template = this.templates['default.html'];
    var navigation_template = this.templates['navigation.html'];
    var box_template = this.templates['box.html'];
    //htmle    
    var html = mustache.to_html(main_template, {
        content : html_content,
        navigation : navigation_template,
        box: box_template
    });   
    send(html);
     
}

// lista top zaludnionych miast
//parametr w ?myLimit=x
ddoc.lists.top = function(head, req) {
    var row;
    var rows = [];
    start({
        "headers": {
            "Content-Type": "text/html"
        }
    });

    while(row = getRow()) {
        rows.push(row);
    }
    
    var myLimit = (req.query.myLimit != undefined) ? req.query.myLimit : 0;
     
    rows.sort(function(a,b) {
        //porowanie po liczbie mieszkancow
        return b.value.ludnosc - a.value.ludnosc; 
    });
   
    if (myLimit > 0) {
        rows.length = myLimit;
    }
    var length = rows.length;
    var json_array = [];
    var html = '[';
    for (var i = 0; i < length; i++) {
        if (rows[i]) {
        json_array.push(JSON.stringify(rows[i]));
        }
    }
    
    var output = '[' + json_array + ']';
    
    send(output);
}

// widok statystyk
ddoc.lists.statystyki = function(head, req) {
    start({
        "headers": {
            "Content-Type": "text/html"
        }
    });
    
    var mustache = require('templates/mustache');
    //szablony 
    var main_template = this.templates['default.html'];
    var statystyki_template = this.templates['statystyki.html'];
    var navigation_template = this.templates['navigation.html'];
    var box_template = this.templates['box.html'];
    //htmle    
    var html = mustache.to_html(main_template, {
        content : statystyki_template,
        navigation : navigation_template,
        box: box_template
    });   
    send(html);

}

//widok glowny
ddoc.lists.main = function(head, req) {
    
    start({
        "headers": {
            "Content-Type": "text/html"
        }
    });
    
    var mustache = require('templates/mustache');
    //szablony 
    var main_template = this.templates['default.html'];
    var welcome_template = this.templates['welcome.html'];
    var navigation_template = this.templates['navigation.html'];
    var box_template = this.templates['box.html'];
    //htmle    
    var html = mustache.to_html(main_template, {
        content : welcome_template,
        navigation : navigation_template,
        box: box_template
    });   
    send(html);
}

//widok testowy
ddoc.lists.test = function(head, req) {
    
    start({
        "headers": {
            "Content-Type": "text/html"
        }
    });
    
    var mustache = require('templates/mustache');
    //szablony
    var main_template = this.templates['default.html'];
    var navigation_template = this.templates['navigation.html'];
    var box_template = this.templates['box.html'];
    //htmle    
    var html = mustache.to_html(main_template, {
        navigation : navigation_template,
        box: box_template
    });   
    send(html);
}
