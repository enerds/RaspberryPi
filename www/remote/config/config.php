<?php
/*
 * Created on 14.02.2010
 * file config.php
 * part of phpMp-reloaded
 * 
 * by tswaehn (http://sourceforge.net/users/tswaehn/)
 */
 

/*
 * 	you can edit here: 
 */ 

 define('__THEME__', 'default' );
 date_default_timezone_set  ( "Europe/Berlin" );


/*
 *  * 	please dont touch this:
 */
 
 define('__PROJECT__', 'phpMpReloaded');
 define('__VERSION__', '1.2');
 
 include('./config/mpd_config.php');
 define('__THEME_DIR__', './themes/'.__THEME__.'/' );
 
 
 
?>
