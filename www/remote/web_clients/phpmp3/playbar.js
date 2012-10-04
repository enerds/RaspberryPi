var pos;
var playbar_req;

function playbar_refresh() {
    var cmd_list = new Array;
    cmd_list[0] = 'status';
    cmd_list[1] = 'currentsong';
    commands("window.playbar_req",cmd_list,playbar_refresh_handler);

}


function playbar_refresh_handler() {
    if (playbar_req.readyState == 4 && playbar_req.status == 200) {
        
        var lines = new Array();
        lines = playbar_req.responseText.split('\n');

        var status = new Array();
        
        for (var i = 0; i < lines.length; i++) {
            var fields = lines[i].split(':');
            status[fields[0]] = fields;

        }

        //time (bar)
        var time_graph_back = document.getElementById('time_graph_back');
        var time_graph = document.getElementById('time_graph');

        if ((status['time']) && (time_graph_back) && (time_graph)) {
            var pos = status['time'][1];
            var len = status['time'][2];
        
                
            var w1 = time_graph_back.offsetWidth;
            var w2 = w1*pos/len;
        
            time_graph.style.width = w2 + 'px';

        
            //time (number)
            var time = document.getElementById('time');
            if (time) {
                time.innerHTML = make_time(pos) + '/' + make_time(len);
            }
        }
        

        // volume 
        var volume_graph_back = document.getElementById('volume_graph_back');
        var volume_graph = document.getElementById('volume_graph');

        if ((status['volume']) &&  (volume_graph_back) && (volume_graph)) {
            var w1 = volume_graph_back.offsetWidth;
            var w2 = w1*(status['volume'][1]/100);
            volume_graph.style.width = w2 + 'px';
        }

        // current song
        var current_song = document.getElementById('current_song');

        if (current_song) {
            var title = '';

            if (status['file']) {

                if ((!status['Artist']) || (!status['Title']) ) {
                    
                    var from = status['file'][1].lastIndexOf('/')+1;
                    var to = status['file'][1].lastIndexOf('.');
                    title = status['file'][1].substring(from,to);
                }
                else {
                    title = status['Artist'][1] + ' - '+status['Title'][1];
                }
            }
            
            if (current_song.innerHTML != title) {
                current_song.innerHTML = title;
            }
        }
        
        if ((status['state']) && ((status['state'][1] == ' play') || (status['state'][1] == ' pause') || (status['state'][1] == ' stop'))) {
            
            var state = status['state'][1].replace(' ','');
            var button_play = document.getElementById('button_play');
            var button_pause = document.getElementById('button_pause');
            var button_stop = document.getElementById('button_stop');
            var button_selected = document.getElementById('button_'+state);

            button_play.className = 'button_play';
            button_pause.className = 'button_pause';
            button_stop.className = 'button_stop';

            button_selected.className = 'button_'+state+'_selected';
        }
        
        setTimeout('playbar_refresh()',refresh_period);
        

    }
}

function playbar_seek(t) {
    pos = (window.event.x - t.offsetParent.offsetLeft)/document.getElementById('time_graph_back').offsetWidth;
    command("window.playbar_req",'status',playbar_seek_handler);
}

function playbar_seek_handler() {

    if (playbar_req.readyState == 4 && playbar_req.status == 200) {

        var lines = new Array();
        lines = playbar_req.responseText.split('\n');

        var status = new Array();
        
        for (var i = 0; i < lines.length; i++) {
            var fields = lines[i].split(':');
            status[fields[0]] = fields;
        }

        command("window.playbar_req",'seek '+status['song'][1]+' '+Math.round(pos*status['time'][2]),fake);
        
    }
}


function playbar_volume(t) {
    //alert(t.offsetParent.offsetLeft);

    vol = 100*(window.event.x - t.offsetParent.offsetLeft)/document.getElementById('volume_graph_back').offsetWidth;
    //alert(vol);
    command("window.playbar_req",'setvol '+Math.round(vol),fake);
}


