<?php
	/*
	 * Cross-platform, web-based (PHP+JavaScript/EcmaScript) MPD (Music Player Daemon) client. Yet another web-client for famous Music Player Daemon. Totally in JavaScript, driven by XMLHTTPRequest (AJAX) with a small php-backend.
	 * http://sourceforge.net/projects/phpmp3/
	 * 
	 */ 

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
  <head>
    <title>phpMp3</title>
    <link type="text/css" href="style.css" rel="stylesheet" />
    <link type="text/css" href="content.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="../../clientswitcher.css">    
    <link type="text/css" href="library_content.css" rel="stylesheet" />

    <script type="text/javascript" src="code.js"></script>
    <script type="text/javascript" src="library.js"></script>
    <script type="text/javascript" src="playbar.js"></script>
    <script type="text/javascript" src="playlist.js"></script>
    <script type="text/javascript">

var req;

function refresh() {

    library_refresh();
    playbar_refresh(); 
    playlist_check();
}

    </script>
  </head>
  <body onload="refresh();">
  
  <?php
  	include ('../../lib/ClientSwitcher.php');
  ?>
    <!-- <pre id="log"></pre> -->
    <div id="library">

      <div id="crumb"></div>

      <div id="library_dir">
        <table cellspacing="0" border="0">
          <thead>
            <tr>
              <td class="column_add">&nbsp;</td>
              <td class="column_upd">&nbsp;</td>
              <td class="column_name">Title</td>
              <td class="column_time">Time</td>
            </tr> 
          </thead>
          <tbody id="dir">
          </tbody>
          <tfoot>
            <tr>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <div id="playbar">

      <form action="." method="post">
        <div class="buttons">
          <a href="javascript:command('window.req','previous',fake);" class="button_prev"></a>
          <a href="javascript:command('window.req','play',fake);" id='button_play' class="button_play"></a>
          <a href="javascript:command('window.req','pause',fake);" id='button_pause' class="button_pause"></a>
          <a href="javascript:command('window.req','stop',fake);" id='button_stop' class="button_stop"></a>
          <a href="javascript:command('window.req','next',fake);" class="button_next"></a>
        </div>

      </form>

      <span id="time"></span>
      <div id="time_graph_back" onclick="playbar_seek(this);">
        <div id="time_graph"></div>
      </div>

      <div id="volume_graph_back" class="volume_back" onclick="playbar_volume(this);">
        <div id="volume_graph" class="volume" ></div>
      </div>
      <div id="current_song"></div>


    </div>
    
    <div id="playlist_frame">
      <input type="button" value="refresh_list" onclick="playlist_refresh();" />
      <input type="button" value="Select all" onclick="select_all(1);" />
      <input type="button" value="Deselect all" onclick="select_all(-1);" />
      <input type="button" value="Toggle all" onclick="select_all();" />
      <input type="button" value="Shuffle list" onclick="command('window.req','shuffle',fake);"/>
      <br /><br />
      <input type="button" value="delete selected" onclick="delete_all(1);"/>
      <input type="button" value="clear list" onclick="delete_all();"/>
      <input type="button" value="crop selection" onclick="delete_all(-1);"/>
      <br /><br />
      <div style="height:1.1em;">
        <div id="selecting" style="display:none"><strong>Selecting...</strong></div>
      </div>
      <div id="log"></div>
      
      <div class="playlist" style="height:70%;overflow:scroll">
        <table cellspacing="0">
          <thead>
            <tr>
              <td>Track</td>
              <td>Time</td>
            </tr> 
          </thead>
          <tbody id="playlist">
            
          </tbody>
          <tfoot>
            <tr>
              <td></td>
              <td id="playlist_length"></td>
            </tr>
          </tfoot>
        </table>
      </div>
      
    </div>
    
  </body>
</html>
