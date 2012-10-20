var refresh_period = 1000;

function htmlspecialchars(s) {

    var out = s.replace('&','%26');
    
    return out;
}

function command(req,cmd,handler) {

    cmd = htmlspecialchars(cmd);
    url = './xmlhttp.php?cmd='+cmd;

    //ну и извратом приходится быть в этом гребаном ёкмаскрипте... ;-[  ]

    eval(req+" = new XMLHttpRequest();");
    
    eval(req+".onreadystatechange = handler;");
    
    eval(req+".open('GET',url);");
    eval(req+".send(null);");

//     req = new XMLHttpRequest();

//     req.onreadystatechange = handler;
    
//     //alert(url);

//     req.open('GET',url);
//     req.send(null);
}

function commands(req,cmd_list,handler) {

    url = './xmlhttp.php?';

    for (var i = 0; i < cmd_list.length; i++) {
        url += 'cmd[]=' + htmlspecialchars(cmd_list[i]);
        
        if (i < (cmd_list.length - 1)) {
            url += '&';
        }
    }

    eval(req+" = new XMLHttpRequest();");
    
    eval(req+".onreadystatechange = handler;");
    
    eval(req+".open('GET',url);");
    eval(req+".send(null);");


}

function fake() {
    
}


function make_title(item) {

    var title;

    if ((item['Title']) && (item['Artist'])) {

        title = item['Artist'] + ' - ' + item['Title'];

        if (item['Track']) {
            var to = item['Track'].lastIndexOf('/');
            var track;
            
            if (to != "-1") {
                track = item['Track'].substring(0,to);
            }
            else {
                track = item['Track'];
            }
            
            title = track + '. ' + title;

        }
        
        if (item['Album']) {
            title = title + ' (' + item['Album'] + ')';
        }
        
    }
    else {
        //alert('1');
        var from = item['file'].lastIndexOf('/')+1;
        var to = item['file'].lastIndexOf('.');
        title = item['file'].substring(from,to);
    }
    
    return title;

}


function make_time(sec) {
    //alert(sec);
    var hours = Math.floor(sec / 3600);
    var minutes = Math.floor((sec - hours*3600) / 60);
    var seconds = sec % 60;

    if (minutes.toString().length == 1) {
        minutes = '0'+minutes;
    }

    if (seconds.toString().length == 1) {
        seconds = '0'+seconds;
    }

    
    
    var time = minutes + ':' + seconds;
    

    if (hours) {
        
        time = hours + ':' + time;
        
    }
    //alert(time);    
    return time;

}


function addslashes(s) {
    var res = "";
    var i = 0;
    
    for (i = 0; i < s.length; i++) {
        var c = s.charAt(i);
        if ((c == "'") || (c == "\"")) {
            res += "\\"+c;
        }
        else {
            res += c;
        }
    }
    
    return res;

}


function debug_handler() {
    //alert(req.readyState);
    if (req.readyState == 4 && req.status == 200) {
        
        alert(req.responseText);

    }

}


function log(message) {
    
    var x = document.getElementById('log');
    x.innerHTML = x.innerHTML + '<br />\n' + message;

}

