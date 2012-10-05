The hostSoftware is meant to run on the Raspberry Pi.
This will act as a server,
- communicating with the Extension Board via Serial, and
- getting pin-definitions and triggers fromt the database

Prerequisites:
- To use the logger correctly, you have to add the following line to /etc/rsyslog.conf:
	local5.*			/var/log/rpid.log


DISCUSSIONS:

=== ADCs, Interval, and database
I want some ADCs to get checked every 1 sec.,
but others maybe only every hour, how do I implement that?

Also, do I want to log every ADC reading to the database?
If I check some ADCs every second the database could get very big very fast.
How do I determine how often I clean up the database ?
And how do I do this? With a timestamp in the database for each value ?


