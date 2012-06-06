#!/bin/sh
# SET FUSE TO INTERNAL 8 MHZ
avrdude -c usbasp -p m8 -P usb -U lfuse:w:0xE4:m
# for external 8Mhz:
#-U lfuse:w:0xe0:m -U hfuse:w:0xd9:m
