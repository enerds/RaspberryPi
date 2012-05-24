<html>
	<head>
		<title>RPI</title>
	</head>
	<body>
		<h1>RPI Server</h1>
		<?php
			echo exec('uptime');
		?>
		
		<h2>GPIOs</h2>
		There is the GPIO-Pinout: <a href="http://elinux.org/Rpi_Low-level_peripherals">Pinout</a><br />
		And here is a sample C-Program so set/unset them: <a href="http://elinux.org/Rpi_Low-level_peripherals#GPIO_Driving_Example_.28C.29">Example Program</a><br />
	</body>
</html>
