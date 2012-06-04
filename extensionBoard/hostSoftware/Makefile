CC	= g++
CFLAGS	+=
LDFLAGS	+= -g
LIBS	+= -I/usr/lib -lstdc++
PROGS	+= main

all: main

default: main

main: main.cpp class_serial.h class_serial.cpp
	$(CC) $(CFLAGS) $(LIBS) -o serial class_serial.cpp main.cpp

clean:
	@rm -f *o $(PROGS)

