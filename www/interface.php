<html>
	<head>
		<!-- META -->
		<title>RPI interface test</title>
		
		<!-- CSS INCLUDES -->
		<link rel="stylesheet" type="text/css" href="style/buttons.css" />
		<link rel="stylesheet" type="text/css" href="style/style.css" />

		<!-- SCRIPT INCLUDES -->
		<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="js/smoothie.js"></script>

<?php
	include 'call_once.php';
	include 'include.php';
?>

		<!-- OWN SCRIPTS -->
		<script type="text/javascript">
			$(function(){
				$("#slider").slider({
					value: 0,
					min: 0,
					max: 1023,
					change: function(event, ui){
						setPWM(ui.value);
					}
				});
			});

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
				<h2>Read Pin values (not functional atm)</h2>
				<!-- Way to call the functions -->
				<span class="button" onclick="getPinValue(0)">Test Pin Value 0</span>
				<span class="button" onclick="getPinValue(1)">Test Pin Value 1</span><br /><br />

				<!-- div in which the results get displayed -->
				<div id="result" style="width:200px;border:1px dotted black;">&gt;&gt;</div>
			</div><!-- set -->

			<div class="set">
				<h2>Set Pin values</h2>
				<!-- Way to call the functions -->
<?php
	for($i = 0; $i < 17; $i++){
		echo '<span class="smallbutton" onclick="setPinValue('.$i.',1)" >P'.$i.'</span>
		<span class="smallbutton negative" onclick="setPinValue('.$i.',0)">P'.$i.'</span>';
	}
?>
			<!-- jquery slide control for pwm -->
			<div id="slider"></div>
				<!-- div in which the results get displayed -->
			</div><!-- set -->


			<!-- GRAPHS -->
			<div class="set">
				<h2>Graphs</h2>
				<canvas id="mycanvas" width="400" height="100"></canvas>
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
