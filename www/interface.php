<html>
	<head>
		<!-- META -->
		<title>RPI interface test</title>
		
		<!-- CSS INCLUDES -->
		<link rel="stylesheet" type="text/css" href="style/buttons.css" />
		<link rel="stylesheet" type="text/css" href="style/style.css" />

		<!-- SCRIPT INCLUDES -->
		<!--
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		-->
		<script type="text/javascript" src="js/jqplot/jquery.min.js"></script>
		<script type="text/javascript" src="js/smoothie.js"></script>

		<script type="text/javascript" src="js/jqplot/jquery.jqplot.js"></script>
		<script type="text/javascript" src="js/jqplot/jquery.jqplot.css"></script>

		<script type="text/javascript" src="js/jqplot/plugins/jqplot.barRenderer.min.js"></script>
		<script type="text/javascript" src="js/jqplot//plugins/jqplot.dateAxisRenderer.min.js"></script>
		<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
		<script type="text/javascript" src="js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
		<script type="text/javascript" src="js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>

<?php
	include 'call_once.php';
	include 'include.php';

	$dbh = mysql_connect($host, $usr, $pwd) or die("Could not connect to mysql");
	$sel = mysql_select_db($db, $dbh) or die("Could not select database");

?>

		<!-- OWN SCRIPTS -->
		<script type="text/javascript">
  var line1 = [['Cup Holder Pinion Bob', 7], ['Generic Fog Lamp', 9], ['HDTV Receiver', 15], 
  ['8 Track Control Module', 12], [' Sludge Pump Fourier Modulator', 3], 
  ['Transcender/Spice Rack', 6], ['Hair Spray Danger Indicator', 18]];

  var plot2;
  var plot2_drawn = 0;

$(document).ready(function(){
	getADCvalues('PC0', 50);
});


			function setNewArtist(){
				$.post("functions.php",{
					func: 'setNewArtist',
					artist: $("#newArtist").val()
				})
				$("#newArtist").val("");

			}

			function setNewGenre(){
				$.post("functions.php",{
					func: 'setNewGenre',
					genre: $("#newGenre").val()
				})
				$("#newGenre").val("");

			}


			function setLight(mynr, myonoff){
				$.post("functions.php",{
					func: 'setLight',
					nr: mynr,
					onoff: myonoff
				})
			}

			function getADC(mypin){
				$.ajax({
					url: 'functions.php',
					type: "POST",
					data: {func : 'getADC', pin:mypin},
					success:function(html){strReturn = html;}, async:false
				});

				return strReturn;
			}
					

			function setPWM(value){
				$.post("functions.php",{
					func: 'setPWM',
					pwm: value
				})
			}

			function deleteAlarm(myid){
				$.post("functions.php",{
					func: 'deleteAlarm',
					id : myid
				},
				function(){
					getAlarm();
				})
			}

			function setPinValue(mypin,myval){
				$.post("functions.php",{
					func: 'setPinValue',
						pin : mypin,
						onoff : myval
					},function(html){
						if(html != "OK"){	
							alert(html);
						}
				});
			}		

			function play(myfolder){
				$.post("functions.php",{
					func: 'play',
						folder : myfolder
					});
			}

			function getAlarm(){
				$.post("functions.php", {
					func: 'getAlarm'
				},
				function (data){	
					JSONobject = JSON.parse(data);
					alarmStr = "";
					for(i=0; i < JSONobject.length; i++){	
						alarmStr += "<span class=\"button\" onclick=\"deleteAlarm(" + JSONobject[i].id + ")\" >D</span>";
						alarmStr += " " + JSONobject[i].hour + ":" + JSONobject[i].min;
						alarmStr += " " + JSONobject[i].days + " " + JSONobject[i].cmd;
						alarmStr += "<br />";
					};
					$("#alarm").html(alarmStr);
				});
			}

			function getADCvalues(myadc, mylimit){
				$.post("sql_adapter.php", {
					adc : myadc,
					limit : mylimit
				},function (data){
					JSONobject = JSON.parse(data);
					list1 = new Array();
					for(i=0; i < JSONobject.length; i++){
						data = new Array();
						data[0] = JSONobject[i].date;
						data[1] = JSONobject[i].value;
						list1[i] = data;
					};
					if(plot2_drawn == 1){
						plot2.destroy();
					}
					plot2_drawn = 1;
					plot2 = $.jqplot('chart2', [list1], {
					    title: myadc,
					    axesDefaults: {},
					    axes: {
					      xaxis: {
					        renderer: $.jqplot.CategoryAxisRenderer,
					        tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
						numberTicks:11,
					        tickOptions: {
					          angle: -30,
					          fontSize: '10pt',
						  textColor: '#000000'
					        }
					      },
						yaxis:{
							min:0,
							max:1030
						}
					    }
					  });
				});
			}


			function setAlarm(){
				// get input values of fields
				myhour = $("#alarm-hour").val();
				mymin = $("#alarm-min").val();
				mycmd = $("#alarm-cmd").val();
				mydays = "";
				count = 0;
				$("input[name=alarm-days]").each(function() {
					if($(this).is(":checked")){
						mydays += "1";
						count++;
					}else{
						mydays += "0";
					}
				});

				if(myhour != "" && mymin != "" && mycmd != "" && count > 0){
					$.ajax({
						url: 'functions.php',
						type: "POST",
						data: {func : 'setAlarm', hour : myhour, min : mymin, days : mydays, cmd : mycmd},
						success:function(html){
							if(html != "OK"){	
								alert(html);
							}else{
								// if everything went ok, clear fields
								$("#alarm-hour").val('');
								$("#alarm-min").val('');
								$("#alarm-days").val('');
								$("#alarm-cmd").val('');
							}
						}, async:false
					});
				}else{
					alert("Bitte jedes Feld ausfüllen");
				}
				getAlarm();
			}
		

			function getCPULoad(){
				$.ajax({
					url: 'functions.php',
					type: "POST",
					data: {func : 'getCPULoad'},
					success:function(html){strReturn = html;}, async:false
				});
				return strReturn;
			}
					

			function getPinValue(myPin){
				$.post("functions.php", {
						func: 'getPinValue',
						pin: myPin
				},
				function (data){	
					$("#result").html(">> " + data);
				});
			}




		</script>
	</head>

	<body>
	<div class="wrapper">
		<div class="header" style="margin-bottom:10px;">
			<img src="style/images/logo_big_2.png" style="float:left;margin-right:10px;" />
			<h1 style="margin-top:30px;">Ajax interface to control the RPI ! </h1>
		<br style="clear:both;" />
		</div><!-- header -->

		<div class="main">
			<!-- PIN BUTTONS -->
			<div class="set">
				<h2>Read ADCs</h2>
				<!-- Way to call the functions -->
<?php
	$result = mysql_query("SELECT * FROM atmega WHERE `activefunc` = 'adc' ");
	while($res = mysql_fetch_array( $result )){ 
 		echo '<span class="button" onclick="getADC('.$res['pin'].')">'.$res['pin'].'</span>';
	}
?>

				<!-- div in which the results get displayed -->
				<div id="result" style="width:200px;border:1px dotted black;">&gt;&gt;</div>
			</div><!-- set -->

			<div class="set">
				<h2>Set Pin values</h2>
				<!-- Way to call the functions -->
<?php
	$result = mysql_query("SELECT * FROM atmega WHERE `activefunc` = 'dig' AND `dir` = 'out' ");
	while($res = mysql_fetch_array( $result )){ 
		echo '<span class="smallbutton" onclick="setPinValue('.$res['pin'].',1)" >'.$res['pin'].'</span>
		<span class="smallbutton negative" onclick="setPinValue('.$res['pin'].',0)">'.$res['pin'].'</span>';
	}
?>
			</div><!-- set pin values -->

			<!-- LIGHTS -->
			<div class="set">
				<h2>Lights</h2>
<?php
				echo '<span class="smallbutton" onclick="setLight(4,1)">Blue On</span><span class="smallbutton negative" onclick="setLight(4,0)">Blue Off</span>';
				echo '<span class="smallbutton" onclick="setLight(3,1)">Sides On</span><span class="smallbutton negative" onclick="setLight(3,0)">Sides Off</span>';
?>
			</div>
			<!-- set -->

			<!-- PIN FUNCS -->
			<div class="set">
				<h2>Set Pin Functions</h2>
				TODO: Call Serial Program to tell atmega new pin definitions, or create backend server that reads database and talks to atmega
				<br style="clear:both;" />
				<br style="clear:both;" />

<?php
/* insert new pin definition in database */
if($_POST["update_pin"]){
	mysql_query("UPDATE atmega SET `dir`='".$_POST['dir']."', `activefunc`='".$_POST['func']."',`desc`='".$_POST['desc']."'  WHERE pin='".$_POST['pin']."' ");
	echo 'UPDATED PIN DEFINITION<br /><br />';
}

if($_POST["pin_func"]){
	echo '<form action="" method="post">';
	if($_POST["pin"]){
		echo '<input type="hidden" name="update_pin" value="1" />';
		echo '<input type="hidden" name="pin" value="'.$_POST['pin'].'" />';
		echo $_POST["pin"].' ';
		$desc = "n/a";
		$dir = "n/a";
		$result = mysql_query("SELECT * FROM atmega WHERE `pin` = '".$_POST["pin"]."' ");
		while($res = mysql_fetch_array( $result )){ 
			$available_funcs = explode("|", $res['availablefunc']);
			$desc = $res['desc'];
			$dir = $res['dir'];
		}
		echo '<select name="func">';
		foreach($available_funcs as $func){
			echo '<option value="'.$func.'">'.$func.'</option>';
		}
		echo '</select>';
		echo ' ';
		echo '<select name="dir">';
		if($dir == "in"){
			echo '<option value="in" selected>in</option>';
		}else{
			echo '<option value="in">in</option>';
		}
		if($dir == "out"){
			echo '<option value="out" selected>out</option>';
		}else{
			echo '<option value="out">out</option>';
		}
		echo '<select>';
		echo '<input type="text" name="desc" value="'.$desc.'"/>';
		echo '<input type="submit" value="save" />';

	}else{
		echo 'NO PIN SUPPLIED !';
	}
	echo '</form>';
}
				/* left side pin descriptions */
				$result = mysql_query("SELECT * FROM atmega ORDER BY `nr` ");
				echo '<div style="float:left;">';
				echo '<form action="" method="post"><input type="hidden" name="pin_func" id="pin_func" value="1" />';
				while($res = mysql_fetch_array( $result )){ 
					if($res['nr'] < 15){
						echo '<div style="float:left;width:250px;color:#444;">'.$res['desc'].' </div>';
						echo $res['pin'] ;
						if($res['activefunc'] != "pwr") echo ' '.$res['activefunc'].'  ';
						if($res['dir'] == "in") echo '<span style="float:right;margin-left:10px;margin-right:5px;">&rarr;</span>';
						if($res['dir'] == "out") echo '<span style="float:right;margin-left:10px;margin-right:5px;">&larr;</span>';
						if($res['activefunc'] == "pwr"){
							echo '<span style="float:right;margin-left:10px;margin-right:5px;">&#9889;</span>';
						}else{
							if($res['dir'] == "n/a") echo '<span style="float:right;margin-left:10px;margin-right:5px;">&#9744;</span>';
						}
						echo '<br style="clear:both;height:0px;padding:0;margin:0"/>';
					}
				}
				echo '</div>';

				echo '<div style="border:2px solid black;border-right:none;width:80px;float:left;">';
				$result = mysql_query("SELECT * FROM atmega ORDER BY `nr` ");
				while($res = mysql_fetch_array( $result )){ 
					if($res['nr'] < 15) 
						if($res['activefunc'] != "pwr"){
							echo '<input type="submit" class="smallerbutton" name="pin" id="pin" value="'.$res['pin'].'" /><br />';	
						}else{
							echo '<span style="float:left">'.$res['pin'].'</span><br />';
						}
				}
				echo '</div>';
				echo '<div style="border:2px solid black;border-left:none;width:80px;float:left;">';
				$result = mysql_query("SELECT * FROM atmega ORDER BY `nr` DESC");
				while($res = mysql_fetch_array( $result )){ 
					if($res['nr'] >= 15) 
						if($res['activefunc'] != "pwr"){
							echo '<input type="submit" class="smallerbutton" style="float:right;" name="pin" id="pin" value="'.$res['pin'].'" /><br />';
						}else{
							echo '<span style="float:right">'.$res['pin'].'</span><br />';
						}
				}
				echo '</div>';
				
				/* right side pin descriptions */
				$result = mysql_query("SELECT * FROM atmega ORDER BY `nr` DESC ");
				echo '<div style="float:left;">';
				while($res = mysql_fetch_array( $result )){ 
					if($res['nr'] >= 15){
						if($res['dir'] == "in") echo '<span style="float:left;margin-right:10px;margin-left:5px;">&larr;</span>';
						if($res['dir'] == "out") echo '<span style="float:left;margin-right:10px;margin-left:5px;">&rarr;</span>';
						if($res['activefunc'] == "pwr"){
							echo '<span style="float:left;margin-right:10px;margin-left:5px;">&#9889;</span>';
						}else{
							if($res['dir'] == "n/a") echo '<span style="float:left;margin-right:10px;margin-left:5px;">&#9744;</span>';
						}

						echo '  '.$res['pin'] ;
						if($res['activefunc'] != "pwr") echo ' '.$res['activefunc'].' ';
						echo '<div style="float:right;width:250px;color:#444;text-align:right;"> '.$res['desc'].'</div>';
						echo '<br style="clear:both;height:0px;padding:0;margin:0"/>';
					}
				}
				echo '</form>';
				echo '</div>';

?>
			<br style="clear:both;" />
			</div><!-- set -->

			<!-- GRAPHS -->
			<div class="set">
				<h2>Graphs</h2>
				<canvas id="mycanvas" width="400" height="100"></canvas>
				<a href="#" onClick="getADCvalues('PC0', 50)">Replot</a>
			</div><!-- set -->

			<!-- GRAPHS -->
			<div class="set">
				<h2>Temperatures</h2>
<div id="chart2" style="height:300px; width:100%;"></div>
<div class="code prettyprint">
<pre class="code prettyprint brush: js"></pre>
</div>
			</div><!-- set -->

			<div class="set">
				<h2>Music</h2>
				<a href="./remote/web_clients/phpmp">Remote</a><br />
				<h2>Add LastFm Playlists</h2>
				<input type="text" id="newArtist" />
				<span class="smallbutton" onclick="setNewArtist()">+Artist</span><br />
				<input type="text" id="newGenre" />
				<span class="smallbutton" onclick="setNewGenre()">+Genre</span><br />
			</div><!-- set -->

			
			<!-- ALARM CLOCK -->
			<div class="set">
				<h2>Alarm Clock</h2>
				<input type="text" id="alarm-hour" style="width:50px;" />
				<input type="text" id="alarm-min" style="width:50px;" />
				<input type="text" id="alarm-cmd" style="width:300px;" />
				<input type="checkbox" name="alarm-days" value="Mo" checked />Mo
				<input type="checkbox" name="alarm-days" value="Di" checked />Di
				<input type="checkbox" name="alarm-days" value="Mi" checked />Mi
				<input type="checkbox" name="alarm-days" value="Do" checked />Do
				<input type="checkbox" name="alarm-days" value="Fr" checked />Fr
				<input type="checkbox" name="alarm-days" value="Sa" />Sa
				<input type="checkbox" name="alarm-days" value="So" />So
				<input type="submit" class="button" value="set" onclick="setAlarm()" />
				<div id="alarmres" style="float:left;"></div><br />
				<div id="alarm"></div>
				
				
			</div><!-- set -->

		</div><!-- main -->
	</div><!-- wrapper -->

	<!-- SCRIPTS -->
	<script type="text/javascript">
		var smoothie = new SmoothieChart({
			minValue: 0.0,
			maxValue: 100.0,
			millisPerPixel: 100,
			grid: { strokeStyle: '#555555', lineWidth: 1, millisPerLine: 10000, verticalSections: 5 }
		});
		smoothie.minValue = 0;
		smoothie.maxValue = 100;
		smoothie.streamTo(document.getElementById("mycanvas"), 2000);
		var cpuLine = new TimeSeries();
		setInterval(function(){
			cpuLine.append(new Date().getTime(), getCPULoad());
		}, 1000);
		smoothie.addTimeSeries(cpuLine, { strokeStyle: 'rgba(0, 255, 0, 1)', fillStyle: 'rgba(0, 255, 0, 0.2)', lineWidth: 3 });

	</script>
	<script type="text/javascript">
			/* refresh the fields directly on page load */
			getAlarm();
	</script>

	</body>
</html>
