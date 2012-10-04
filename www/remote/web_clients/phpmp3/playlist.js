var playlist_version = -1; 
var playlist_received = 0; 
var current_song = -1;

var selection_started = 0;
var selection_action = 1; // 1 means "select items", -1 - "deselect items"

var playlist_req;

function playlist_check() {
    //alert('1');
    //log('1');
    command("window.playlist_req",'status',playlist_check_handler);
    //setTimeout('playlist_check()',refresh);

}

function playlist_check_handler() {
    //alert(playlist_req.readyState);
    if (playlist_req.readyState == 4 && playlist_req.status == 200) {
        
        //document.getElementById('status').innerHTML = playlist_req.responseText;
        //alert(req.responseText);
        
        var lines = new Array();
        lines = playlist_req.responseText.split('\n');

        var status = new Array();
        
        for (var i = 0; i < lines.length; i++) {
            var fields = lines[i].split(': ');
            status[fields[0]] = fields;

        }
        
        if ((!playlist_received) || (status['playlist'][1] != playlist_version)) {
            playlist_refresh();
            playlist_version = status['playlist'][1];
            playlist_received = 0;
            current_song = -1;
            //log(playlist_received);
        }

        
        if ((status['songid']) && (status['songid'][1] != current_song)) {
            update_current_song(status['songid'][1]);
            //current_song = status['songid'][1];
        }
        setTimeout('playlist_check()',refresh_period);
        
    }
    //log(playlist_req.readyState + ' ' + playlist_req.status);
}

function update_current_song(song_id) {

    var playlist_node = document.getElementById('playlist');

    if (playlist_node) {
        items = playlist_node.childNodes;

        for(i = 0; i < items.length; i++) {

            if (items[i].tagName == "TR") {

                var td = items[i].childNodes[0];
                if (td) {

                    //alert(items[i].getAttribute('id').split('_')[1] + '='+ song_id);

                    if (items[i].getAttribute('id').split('_')[1] == song_id) {
                        td.className = "current_song";
                        current_song = song_id;
                    }
                    else {
                        td.className = "";
                    }
                }
            }
            
        }
    }    


}

function playlist_refresh() {
    
    command("window.playlist_req",'playlistinfo',playlist_refresh_handler);

}

function playlist_refresh_handler() {
    //may be, readyState can be also 3.... ? for Opera, i think...
    if ((playlist_req.readyState == 4) && (playlist_req.status == 200)) {
        //document.getElementById('status').innerHTML = playlist_req.responseText;

        var lines = new Array();
        lines = playlist_req.responseText.split('\n');

        var playlist = new Array();
        
        var group_size = 6;
        var count = 0;

        for (var i = 0; i < lines.length; i++) {

            if (lines[i] == "") {

                continue;
            }

            var fields;
            
            fields = lines[i].split(': ');

            if (fields[0] == 'file') {
                playlist[count] = new Array();
                playlist[count]['file'] = fields[1];
                count += 1;
                continue;
            }
            
            playlist[count-1][fields[0]] = fields[1];
            
        }

        var playlist_node = document.getElementById('playlist');
        var playlist_length = 0;

        if (playlist_node) {
            items = playlist_node.childNodes;
            // clear old rows
            for(i = items.length-1; i >= 0; i--) {
                playlist_node.removeChild(items[i]);
            }
            
            // create new rows
            for(i = 0; i < playlist.length; i++) {
                
                var tr = document.createElement('tr');
                if (i % 2) {
                    tr.className = 'row_dark';
                }
                else {
                    tr.className = 'row_light';
                }
                tr.setAttribute("id","song_"+playlist[i]['Id']);
                
                playlist_node.appendChild(tr);
                
                //var td = document.createElement('td');
                //tr.appendChild(td);
                //td.innerHTML = playlist[i]['Pos'];
                
                var td = document.createElement('td');
                tr.appendChild(td);
                td.innerHTML = make_title(playlist[i]);
                td.onclick = new Function("selection_start(this.parentNode)");
                //td.onmousedown = new Function("selection_start(this.parentNode)");
                //td.onmouseup = new Function("selection_stop()");
                td.onmouseover = new Function("select(this.parentNode)");

                var td = document.createElement('td');
                tr.appendChild(td);
                td.setAttribute('align','right');
                var a = document.createElement('a');
                td.appendChild(a);
                
                a.setAttribute('href',"javascript:command(\"window.playlist_req\",'playid " + playlist[i]['Id'] + "',fake);");
                a.innerHTML = make_time(playlist[i]['Time']);
                
                playlist_length += Math.round(playlist[i]['Time']);

            }
            
            var playlist_length_node = document.getElementById('playlist_length');
            
            if (playlist_length_node) {
                playlist_length_node.innerHTML = make_time(playlist_length);
            }
            
        }

        update_current_song(current_song);
        playlist_received = 1;
        //setTimeout('playlist_check()',refresh);

    }



}

function selection_start(item){
    if (selection_started) {
        selection_started = 0;
        //log('stop2');
        var x = document.getElementById('selecting');
        if (x) {
            x.style.display = 'none';
        }
    }
    else {
        selection_started = 1;
        selection_action = (item.className.indexOf('_selected') == -1)?1:-1;
        //log('start');
        var x = document.getElementById('selecting');
        if (x) {
            x.style.display = 'block';
        }
        select(item);
    }
}

function select(item) {

    if (selection_started) {
        //log('selecting...');
        _select(item,selection_action);
        //item.className = item.className.replace('_selected','');
        //item.className += '_selected';
        
    }


}

//force_select = 1 means "force select"
//force_select = -1 means "force deselect"
function _select(item,force_select) {
    
    if ((item.className.indexOf('_selected') == -1) && (force_select != -1)) {
        item.className += '_selected';
    }
    else {
        if (force_select != 1) {
            item.className = item.className.replace('_selected','');
        }
    }
    
}
function select_all(select_type) {

    var playlist_node = document.getElementById('playlist');

    if (playlist_node) {
        items = playlist_node.childNodes;
        
        for(i = 0; i < items.length; i++) {
            //alert(items.tagName);
            if (items[i].tagName == "TR") {
                _select(items[i],select_type);
            }
            
        }
    }    

}

// selected_only = 1 - deletes only selected items
// selected_only = 1 - deletes only unselected items
function delete_all(selected_only) {

    var playlist_node = document.getElementById('playlist');

    if (playlist_node) {
        var items = playlist_node.childNodes;
        var cmds = new Array();
        
        for(i = 0; i < items.length; i++) {
            if (items[i].tagName == "TR") {
                
                if (((selected_only ==  1) && (items[i].className.indexOf('_selected') != -1)) || 
                    ((selected_only == -1) && (items[i].className.indexOf('_selected') == -1)) || 
                    (!selected_only)) {
                    cmds[cmds.length] = "deleteid "+items[i].getAttribute('id').split('_')[1];
                    
                }
            }
        }
        
        commands("window.playlist_req",cmds,fake);
        
    }    


}

