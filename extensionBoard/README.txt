=== Contents

== Hardware
The schematics and board layout of the extension board.
It is based on an atmega8 and currently on a breadboard.

== Software
This is the software that will run on the atmega8.
It already handles serial communication,
you can set input/output direction of a pin,
high/low value, and get ADC readings from all five channels.
Also, it pulses an LED wired to PB1 as a heartbeat.


== HostSoftware
This is meant to run on the Raspberry Pi, as a connection
between the web-interface and database and the atmega.
It will periodically check ifthenelse-triggers, read ADC
values to the database, and update pin definitions from the database.

=== TODO
- Implement PWM functionality
- Extend HostSoftware
