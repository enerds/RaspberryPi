<?php
session_start();
if (isset($_SESSION["dir"]) ) { $dir = $_SESSION["dir"]; } // restore dir when coming from another page
include "command_body.php";
 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
<title><?php echo $title; ?> - Folderlist</title>
<META HTTP-EQUIV="Expires" CONTENT="Thu, 01 Dec 1994 16:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="../../clientswitcher.css">
<link rel="stylesheet" type="text/css" href="css.css" />
<meta name = "viewport" content = "width=device-width"><!-- important for NOT zooming the window-->
<script src="javascript.js" type="text/javascript"></script>
<script type="text/javascript">
	// needed by control_body to decide which buttons are displayed
	var module= '<?php print(basename($_SERVER["PHP_SELF"])); ?>';
</script>
</head>
<body class="col_background" onLoad="setInterval('chase_move()',150); command('','control_body.php','chase');">
<!-- div for the chasing control window -->
<div id="chase" style="position:absolute; top:0px; left:0px;width:100%"></div>
<br>
<br>
<br>
<br>
<?php
include "main_body.php";
?>
</body>
</html>
