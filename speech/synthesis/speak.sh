#!/bin/bash
UUID=`uuid`
echo $UUID
SPEECH=/tmp/$UUID.wav
TEXT=$1

# check if mpd is playing
for i in $(mpc --format ""); do
	a=$i
	break;
done
echo $a

# do not speak before 6 o clock !
HOUR=`date +%H`
if [ "$HOUR" -gt 6 ] ; then
    /usr/bin/espeak -v us-mbrola-1 -a 1 -s120 "$TEXT" | /usr/bin/mbrola -e /usr/share/mbrola/us1/us1 - $SPEECH

    if [ -z $2 ]; then
	if [ $a = "[playing]" ]; then
		mpc pause
	fi

	play --volume 0.8 $SPEECH
	rm $SPEECH

	if [ $a = "[playing]" ];then
		mpc play
	fi
    fi  
fi

