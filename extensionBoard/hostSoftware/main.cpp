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

int vec2int(std::vector<char> inp){
	int ret = 0;

	// read the last digit first
	int count = 1;
	for(int x=0 ; x<inp.size()-2 ; x++){
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

	std::vector<std::string> adcPins;

	std::map<std::string, int> adcValues;

	while(1){
		/************************************************* 
		 **  check ADC values of pins configured as ADC **
		 *************************************************/
		// get the pins configured as ADC from the database
		adcPins = myDB.getADCs();

		// cycle through them and get their values via serial
		for(int x=0; x < adcPins.size();x++){
			std::string curPin = adcPins.at(x); // e.g. "PC0"
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
			for(std::map<std::string,int>::const_iterator i = adcValues.begin(); i != adcValues.end(); ++i)
            			std::cout << i->first << ": " << i->second << std::endl;
		}
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
