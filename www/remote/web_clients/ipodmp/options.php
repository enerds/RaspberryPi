<?php 
session_start();
if (isset($_SESSION["dir"]) ) { $dir = $_SESSION["dir"]; } // restore dir when coming from another page
include "command_body.php";
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo $title; ?> - Options</title>
<META HTTP-EQUIV="Expires" CONTENT="Thu, 01 Dec 1994 16:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<meta name = "viewport" content = "width=device-width"><!-- important for NOT zooming the window on IPod-->
<link rel="stylesheet" type="text/css" href="../../clientswitcher.css">
<link rel="stylesheet" type="text/css" href="css.css" />
<script src="javascript.js" type="text/javascript"></script>
<script type="text/javascript">
	// needed by control_body to decide which buttons are displayed
	var module= '<?php print(basename($_SERVER["PHP_SELF"])); ?>';
</script>
</head>
<body class="col_background" onLoad="setInterval('chase_move()',250); command('','control_body.php','chase');">
<!-- div for the chasing control window -->
<div id="chase" style="position:absolute; top:0px; left:0px;width:100%"></div>
<br /><br /><br /><br />

<div style="width:100%;" class="col_directories_title sf">Search</div>


<table> 
	<colgroup>
	    <col width="35">
	    <col width="*">
  	</colgroup>
  	<tr><td><img src="images/search.gif" onClick="bg(this,1);command('search='+document.getElementById('search_input').value,'search.php','search_result')"></td><td><input type="text" size="15" id="search_input"></td></tr>
</table>
 
<div id="search_result"><br> </div>
<div style="width:100%;" class="col_directories_title sf">Options</div>
<br />
 

<?php
include "options_body.php";
?>

</body>
</html>
