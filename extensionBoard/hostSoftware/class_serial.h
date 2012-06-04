#include <sys/types.h>                                                                                                                                                                                                                                                                                                         
#include <unistd.h>
#include <cstdlib>
#include <cstring>
#include <string>
#include <sys/stat.h>
#include <fcntl.h>
#include <termios.h>
#include <stdio.h>
#include <vector>

#define BAUDRATE B38400
#define FALSE 0
#define TRUE 1

class Serial{
	public:
		Serial(const char * modemdevice);
		~Serial();
		std::vector<char> sendReceive(char x);
	private:
        	int fd,c, res;
        	struct termios oldtio,newtio;
		void parse_message();
		int Hex2Dec(const char *sz);
		std::vector<char> receive();
		char buf[255];
		volatile int STOP;
};

