#include <sys/types.h>
#include <iostream>
#include <unistd.h>
#include <cstring>
#include <stdlib.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <termios.h>
#include <stdio.h>
#include "class_serial.h"
#include <linux/serial.h>
#include <sys/ioctl.h>
#include <ios>

using namespace std;

Serial::Serial(const char * modemdevice){
	fd = open(modemdevice, O_RDWR | O_NOCTTY);

	if(fd < 0){ 
		perror(modemdevice);
		exit(-1);
	}

	tcgetattr(fd, &oldtio);
	tcgetattr(fd, &newtio);
	bzero(&newtio, sizeof(newtio));


	newtio.c_cflag = 0;
	newtio.c_cflag |= CS8;
	newtio.c_cflag |= CREAD;
	newtio.c_cflag |= CLOCAL;
	newtio.c_cflag |= HUPCL;

	cfsetospeed(&newtio, B9600);
	cfsetispeed(&newtio, 0); 

	newtio.c_iflag = 0;
	newtio.c_iflag |= IXON;
	newtio.c_iflag |= IXOFF;
         
	newtio.c_lflag = 0;

	newtio.c_cc[VTIME]    = 5;   
	//newtio.c_cc[VMIN]	= 1;

	tcflush(fd, TCSAFLUSH); // flush buffers and apply change

	if(!tcsetattr(fd,TCSANOW,&newtio) == 0){
		cerr << "Err Settings" << endl;
	}
}


Serial::~Serial(){
	tcsetattr(fd,TCSANOW,&oldtio);
	close(fd);
}

int Serial::Hex2Dec(const char *sz)
{
    int iResult;
    sscanf(sz, "%x", &iResult);
    return iResult;
}

std::vector<char> Serial::receive(){
	std::vector<char> result;
	while(read(fd,buf,255) > 0){
		result.push_back(buf[0]);
	}

	return result;
}


std::vector<char> Serial::sendReceive(std::vector<char> x){
	char message[255];
	int y=0;
	for(y=0; y < x.size(); y++){
		message[y] = x[y];
		//std::cout << "message[" << y << "] = " << x[y] << std::endl;
	}
	//std::cout << "message[" << y << "] = RETURN " << std::endl;
	message[y] = '\r';
	//message[0] = x;
	//message[1] = '\r';
	//cout << "sending message with size: " << x.size()+1 << endl;
	int checkback = write(fd, message, x.size()+1);

	std::vector<char> code = receive();

	return code;
}

