#!/bin/sh
# for external 8Mhz:
avrdude -c usbasp -p m8 -P usb -U lfuse:w:0xfe:m 
avrdude -c usbasp -p m8 -P usb -U hfuse:w:0xd9:m
