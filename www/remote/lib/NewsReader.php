<?php
/*
 * Created on 25.04.2010
 * file NewsReader.php
 * part of NewsReader
 * 
 * by tswaehn (http://sourceforge.net/users/tswaehn/)
 */
 
// defines for different reading types
define('__READ_UNKNOWN__', 	0);
define('__READ_CURL__', 	1);
define('__READ_TEXT__',		2);
define('__READ_IMAGE__',	3);


// setting debug
define('__DEBUG__',	0	);


class NewsReader {
	
	var $readMethods;

	function __construct(){
		
		// check the available methods for reading news from an external server
		$this->checkAvailableReadMethods(); 		
	}

	/*
	 *  check different read-news-methods
	 */	
	function checkAvailableReadMethods(){
		// 1. check curl ... most safe option
		if ($this->isCurlInstalled()){
			$this->readMethods[] = __READ_CURL__;
		}
		// 2. check fopen ... quite easy method
		if ($this->canUrlFOpen()){
			$this->readMethods[] = __READ_TEXT__;
		}
		
		// ... more methods like rss can be implemented
		
		// n. at least put the picture load method 
		$this->readMethods[] = __READ_IMAGE__;
		
	}
	
	/*
	 * 	common "readNews" member function
	 *  decides internally which read method to choose
	 *
	 */
	function readNews( $url ){
		// take the first(best) available method
		$method = $this->readMethods[0];
		
		
		$news = '';
		
		// read news depending on method
		switch ($method){
			case __READ_CURL__ : $news = $this->curlReadNews( $url ); break;
			case __READ_TEXT__ : $news = $this->textReadNews( $url ); break; 	
			default:
					//$news = $this->imgReadNews( $url );
		}
		
		// generate a "frame"
		if ($news != '' ){
			$news = '<div id="news">'.$news.'</div>';
		}
		
		// return the news :-)
		return $news;
	}	

	//	-------------------------------------------
	//	Curl
	function isCurlInstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			if (__DEBUG__){
				echo 'Yes, "curl" is installed.';
			}
			return true;
		}
		else{
			if (__DEBUG__){
				echo 'Sorry, "curl" is not installed.';
			}
			return false;
		}
	}


	function curlReadNews( $url ){
		if (__DEBUG__){
			echo 'using curl for reading news<p>';
		}
		$ch = @curl_init( $url );
		if ($ch == false){
			if (__DEBUG__){
				echo 'curl is unavailable.<p>';
			}
			return '';
		}

		curl_setopt($ch,CURLOPT_FAILONERROR,true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
	
		$news = curl_exec($ch);
		$curl_error = curl_error($ch);
		
		curl_close($ch);
				
		if ( $curl_error != '' ){
			if (__DEBUG__){
				echo $curl_error;
			}
			return '';
		}
		
		return $news;
	}

	//	-------------------------------------------
	//	Text
	function canUrlFOpen(){
		$allow_url = ini_get('allow_url_fopen');
		if ($allow_url){
			if (__DEBUG__){
				echo 'Yes, "allow_url_fopen" is enabled on this server.<p>';
			}
			return true;
		} else {
			if (__DEBUG__){
				echo 'Sorry, "allow_url_fopen" is disabled on this server.<p>';
			}
			return false;
		}
	}
	
	function textReadNews( $url ){
	
	 	$fh = fopen ($url, 'r' );
	 	
	 	if (!$fh){
	 		return '';
	 	}
	 	
	 	$contents='';
	 	
	 	while(!feof($fh)){
	 		$contents .= fread($fh,10000);
	 	}
	 	
	 	fclose($fh);
	 	
	 	return $contents;
	}

	//	-------------------------------------------
	//	DOM
	function isDOMenabled(){
		return false;
	}
	
	function readRSS( $url ){
		
		$xml = new DOMDocument('1.0', 'UTF-8');
		$xml->preserveWhiteSpace = FALSE;
		if (($xml->load($url))==FALSE){
			echo 'Sorry, cannot load xml file.';
			return;			
		}
		
		
		
		$titles = $xml->getElementsByTagName('title');
		
		$title = $titles->item(0);
		
			
		print_r($title->nodeValue);
		

			
	}
	
	//	-------------------------------------------
	//	Image
	function imgReadNews( $url ){
		$url .= '?type=image';
		$news = '<img src="'.$url.'" />';
		return $news;		
	}
		
}
 
 
 
?>
