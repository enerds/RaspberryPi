//////////////////////////////////////////////////////////////////////////////////////
// using control.php via ajax for actions where we dont want to reload the actual windows
//////////////////////////////////////////////////////////////////////////////////////
 
var xmlHttp = false;
var return_value = '';
// creating XMLHttpRequest-Instanz 
// ... for Internet Explorer
try {
    xmlHttp  = new ActiveXObject("Msxml2.XMLHTTP");
} catch(e) {
    try {
        xmlHttp  = new ActiveXObject("Microsoft.XMLHTTP");
    } catch(e) {
        xmlHttp  = false;
    }
}
// ... for Mozilla, Opera and Safari
if (!xmlHttp  && typeof XMLHttpRequest != 'undefined') {
    xmlHttp = new XMLHttpRequest();
}
function command(command2send,script2call,div2write) {

	// sends a command2send to script2call and puts the output into div2write
	
	//default
	var script2call = (script2call == null) ? 'command_body.php' : script2call;
	var div2write 	= (div2write == null) 	? false : div2write;

 	if (xmlHttp) {
		xmlHttp.open('GET', script2call+'?'+command2send+'&module='+module, true);
		xmlHttp.onreadystatechange = function () {
			if (xmlHttp.readyState == 4) {
				if (div2write) {
					document.getElementById(div2write).innerHTML = xmlHttp.responseText;
					// dirty hack to execute sended js-script
					if (document.getElementById('js') ) {
						eval(document.getElementById('js').innerHTML);
						document.getElementById('js').innerHTML = '';
					}
				}
				 
			}
		};
	}
	xmlHttp.send(null); 	
} 

 
//////////////////////////////////////////////////////////////////////////////////////
// chasing control window
//////////////////////////////////////////////////////////////////////////////////////

function chase_move() {

	top_actual = parseInt(window.pageYOffset); // actual distance from top (NS + Safari)
	if (!top_actual) { 
 		top_actual = parseInt(document.body.scrollTop ); // actual distance from top (IE)
 	} 	
 	document.getElementById('chase').style.top = String(top_actual)+'px'; 

}
//////////////////////////////////////////////////////////////////////////////////////
//sets the song_slider at the actual position
//////////////////////////////////////////////////////////////////////////////////////

var song_slider_timeout	= 0;
var freq				= 1000;
var num_ticks 			= 50;
var song_length			= 0;
var song_position		= 0;
var song_status			= '';

function song_slider_init(init_song_length,init_song_position,init_song_status) {

	song_length 	= init_song_length;
	song_position 	= init_song_position;
	song_status 	= init_song_status;

	if (song_slider_timeout == 0) { // create slider 
		song_slider_move(); 
	} 		
	if (song_status != 'play' && song_slider_timeout != 0) { // stop slider 
			clearTimeout(song_slider_timeout);
			song_slider_timeout = 0;		
	}		
}

function song_slider_move() {

	var time_per_tick 		= song_length/num_ticks;
	var actual_tick			= parseInt(song_position/time_per_tick);

	// color actual tick and uncolor the rest
	for (i=0;i<num_ticks;i++) {	
		if (i==actual_tick) { document.getElementById('song_slider_'+i).style.backgroundColor = '#EEEEEE'; }
		else 				{ document.getElementById('song_slider_'+i).style.backgroundColor = ''; }
	}	
	
	if (song_length-song_position<0) { // song over; reload command_body
		command('','control_body.php','chase'); 
	}
	song_position = song_position + (freq/1000);
	if (song_status == 'play') { song_slider_timeout = setTimeout("song_slider_move()",freq); }
}

function bg(object,status) {
	if (status == 0) 	{ object.style.backgroundColor = ''; }
	else				{ 
		object.style.backgroundColor = '#666666'; 
		o = object; // js is weird...
		window.setTimeout("bg(o,0)", 300);
	}
	
}
 
//////////////////////////////////////////////////////////////////////////////////////
//reload control_body when song is over (in playlist.php & command_body.php)  
//////////////////////////////////////////////////////////////////////////////////////
