#include <iostream>
#include <string>
#include <time.h>
#include <stdio.h>
#include <stdlib.h>
#include <map>

#include "class_serial.h"
#include "class_mysql.h"
#include "class_log.h"

using namespace std;

void ts2str(long ts){
	time_t     now;
	struct tm  tsa;
  	char       buf[80];
	now = ts;
    	// Get current time
    	time(&now);
    	// Format time, "ddd yyyy-mm-dd hh:mm:ss zzz"
    	tsa = *localtime(&now);
    	strftime(buf, sizeof(buf), "%a %Y-%m-%d %H:%M:%S %Z", &tsa);
    	printf("which equals: %s\n", buf);
}

int vec2int(std::vector<char> inp){
	int ret = 0;

	/*
	for(int x=inp.size()-1; x>=0; x--){
		std::cout << "Index " << x << " is: " << inp.at(x) << std::endl;
	}
	*/

	// read the last digit first
	int count = 1;
	for(int x=inp.size()-3 ; x >= 0; x--){
		ret += ((int)inp.at(x)-48) * count;
		count *= 10;
	}
	
	return ret;
}

int main(int argc, char* argv[]){
	Serial mySerial("/dev/ttyAMA0");
	Mysql myDB("localhost", "pi","Twe28+-","rpi");	

	std::vector<char> request;
	std::vector<char> response;

	std::vector< std::pair<std::string, long int> > adcPins;

	std::map<std::string, int> adcValues;

	while(1){
		/************************************************* 
		 **  check ADC values of pins configured as ADC **
		 ** 						**
		 ** TODO					**
		 ** - check interval user desires		**
		 ** - get current time and compare		**
		 *************************************************/
		// get the pins configured as ADC from the database
		adcPins = myDB.getADCs();

		// cycle through them and get their values via serial
		for(int x=0; x < adcPins.size();x++){
			std::string curPin = adcPins.at(x).first; // e.g. "PC0"
			long int interval = adcPins.at(x).second;

			// get last reading time of this pin
			long int lastRead = myDB.getLastReading(curPin);

			// get current system timestamp	
			time_t     now;
			time(&now);

			response.clear();

			// if we waited long enough, take the reading and insert into database
			if(now - lastRead >= interval){ 
				while(response.size() == 0){
					request.clear(); // request of form GXY, e.g. GC0
					request.push_back('G');
					request.push_back(curPin[1]);
					request.push_back(curPin[2]);
					response = mySerial.sendReceive(request);
				}

				// convert the char-vector to an int
				adcValues[curPin] = vec2int(response);

				// output the values
				for(std::map<std::string,int>::const_iterator i = adcValues.begin(); i != adcValues.end(); ++i){
	            			std::cout << i->first << ": " << i->second << std::endl;

					// put the values in the database
					myDB.insertValue(i->first, i->second);
				}
			}
		}
		sleep(1);
	}
	



	//cout << "Argument-count argc: " << argc << endl;
	if(argc == 4){
		if(strcmp(argv[1], "L") == 0){
			 if(strcmp(argv[3], "0") == 0){
				cout << "Switching blue light off" << endl;
				request.push_back('L');
				request.push_back(*argv[2]);
				request.push_back('0');
				response = mySerial.sendReceive(request);
			
			}
			 if(strcmp(argv[3], "0") == 1){
				cout << "Switching blue light on" << endl;
				request.push_back('L');
				request.push_back(*argv[2]);
				request.push_back('1');
				response = mySerial.sendReceive(request);
			}
		}

	}

	return 0;
}                                  
