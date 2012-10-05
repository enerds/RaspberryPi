CC	= g++
CFLAGS	+=
LDFLAGS	+= -g
LIBS	+= -I/usr/lib -lstdc++ -I/usr/include/mysql -lmysqlclient
PROGS	+= main

all: main

default: main

main: main.cpp class_serial.h class_serial.cpp class_mysql.h class_mysql.cpp class_log.h class_log.cpp
	$(CC) $(CFLAGS) $(LIBS) -o serial class_serial.cpp class_mysql.cpp class_log.cpp main.cpp

clean:
	@rm -f *o $(PROGS)

