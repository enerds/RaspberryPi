<?php
/*
 * Created on 23.02.2010
 * file ClientHandler.php
 * part of phpMpReloaded
 * 
 * by tswaehn (http://sourceforge.net/users/tswaehn/)
 */
 class ClientHandler {
 	

 	function __construct(){
 		
 	}	
 	
 	function execute(){
 		
 		$this->checkUrlVariables();
 		
 		if ( $this->checkConfig() == 0 ){
 			$this->showConfig();
 			return;
 		}
 		if ( $this->checkMpd() == 0 ){
 			$this->showConfig();
 			return;
 		}
 		
 		
 		$this->showAvailableClients();	
 		
	}
 	
	function checkConfig(){
		global $mpd_host, $mpd_port;
		
		$has_error = 0;
		if (!isset($mpd_host)){
			$mpd_host = 'localhost';
			echo 'mpd host is not set<br>';
			$has_error = 1;
		}
			
		if (!isset($mpd_port)){
			$mpd_port= 6600;
			echo 'mpd port is not set<br>';
			$has_error = 1;
		}
		
		if ($has_error){
			return 0;
		}
		
		return 1;	
	}
	
	function checkMpd(){
		global $mpd_host, $mpd_port;
		
		$has_error=0;
		
		$fp = @fsockopen($mpd_host,$mpd_port,$errno,$errstr,10);
		
		if(!$fp) {
			echo 'Cannot connect to mpd: '.$errstr.'('.$errno.')<p>';
			return 0;
		}
				
		fclose( $fp );
		return 1;	
	}
	
	function addClient( $client, $link, $desc ){
		
		echo '<a href="./web_clients/'.$link.'">'.$client.'</a>';
		
		echo '<div id="description">';
		echo $desc;
		echo '</div>';
		
		echo '<br>';
	 	
	}
 	
 	function showAvailableClients(){
 		
		$this->addClient( 'phpMp', 'phpmp', 'v0.11.0 - the original client');
		$this->addClient( 'phpMp+', 'phpmp+', 'v0.2.3 - client by BohwaZ');

		$this->addClient( 'phpMp2', 'phpmp2', 'v0.11.0 - client by mpd team');
		$this->addClient( 'phpMp3', 'phpmp3', 'v0.2 - client by <a href="http://sourceforge.net/projects/phpmp3/" target="_blank">angry_elf</a>');
		
		$this->addClient( 'IPodMp', 'ipodmp', 'client by Hendrik Stoetter 03/2008 <a href="http://www.itrium.de/pages/home/mpd_ipod_touch_musikserver_remote_wlan.php?font_size=100" target="_blank">link</a>');
		
		$this->addClient( 'MPD-Web-Remote', 'mobile.clients/MPD-Web-Remote', 'client by Thomas Preston <a href="https://github.com/tompreston/MPD-Web-Remote/tree/tswaehn" target="_blank">link</a>');
		
		//only for testing
		//$this->addClient( 'mpd.class.test', 'phpMpClassTest', 'test client by tswaehn');		 		
 	}
	
	function checkUrlVariables(){
		global $url_mpd_host, $url_mpd_port, $url_action;
		global $mpd_host, $mpd_port;
				
		$host = 'localhost';
		$port = 6600;
		
		if (isset($url_mpd_host)){
			$mpd_host = $url_mpd_host;
		}
		if (isset($url_mpd_port)){
			$mpd_port = $url_mpd_port;
		}
		if (isset($url_action)){
			
			switch ($url_action){
				case 'save_mpd_settings' : $this->saveSettings( $mpd_host, $mpd_port ); break;
			}
		}	
	}
	
	function saveSettings( $host, $port ){
		
		$text = '<?php
			$mpd_host	 	= "'.$host.'";
 			$mpd_port 		= '.$port.';
		?>';
		
		$filename = './config/mpd_config.php';
		$fh = @fopen( $filename , 'w');
		
		if ($fh){
			fwrite( $fh, $text );
			fclose($fh);
		} else {
			echo 'Failed to write settings to '.$filename.'<br>';
			echo 'Please check your file permissions.<p>';
		}
		
			
	}
	
	function showConfig(){
		global $mpd_host, $mpd_port;
		
		echo '<p>';
		echo 'Please configure mpd and enter your mpd settings';
		echo '<p>';
		echo '<form method="get" >';
		echo 'mpd host ';
		echo '<input type="edit" name="mpd_host" value="'.$mpd_host.'" />';
		echo '<p>';
		echo 'mpd port ';
		echo '<input type="edit" name="mpd_port" value="'.$mpd_port.'" />';
		echo '<p>';
		echo '<input type="hidden" name="action" value="save_mpd_settings" />';
		echo '<input type="submit" value="test connection" />';
		echo '</form>';
	} 	
 	
 }
 
?>
