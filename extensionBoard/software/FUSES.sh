#!/bin/sh
# SET FUSE TO INTERNAL 8 MHZ
avrdude -c usbasp -p m8 -P usb -U lfuse:w:0xE4:m
