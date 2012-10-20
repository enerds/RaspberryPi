<?php
/*
 * Created on 14.02.2010
 * file index.php
 * part of phpMpReloaded
 * 
 * by tswaehn (http://sourceforge.net/users/tswaehn/)
 */
 
 
 
 // here is some bootstrap code
 
 include('./config/config.php');
 include('./lib/header.php');
 	
 	include ('./lib/NewsReader.php');
 	include ('./lib/ClientHandler.php');
 

	import_request_variables  ( 'gp', 'url_' );		
 
 	$clientHandler = new ClientHandler;
 	$news = new NewsReader;
 	
	echo '<div id="client_selector">';
	echo '<h3>::phpMpReloaded:: The Webclient for mpd</h3>';

		echo '<div id="main">';		
		$clientHandler->execute();
		echo '</div>';
		
	echo '<div id="credit">';
		
		// recive news from news server 
		echo $news->readNews('http://phpmpreloaded.sourceforge.net/NewsProvider/?version='.__VERSION__);
		
		// support links
		echo 'Support <a href="http://sourceforge.net/projects/phpmpreloaded/">us</a> and visit<br>'; 
		echo '<a href="http://sourceforge.net/projects/phpmpreloaded"><img src="http://sflogo.sourceforge.net/sflogo.php?group_id=304529&amp;type=9" width="80" height="15" alt="Get ::phpMpReloaded:: The webclient for mpd at SourceForge.net. Fast, secure and Free Open Source software downloads" /></a>';	
	echo '</div>';
 
 	echo '</div>'; 
 
 
 include('./lib/footer.php');
 
 // 
 
 
?>
