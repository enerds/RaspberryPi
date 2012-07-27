#include <iostream>
#include <string>
#include <time.h>
#include <stdio.h>
#include <stdlib.h>

#include "class_serial.h"                                                                                                                                                                                                                                                                                                      

using namespace std;

int main(int argc, char* argv[]){
	Serial mySerial("/dev/ttyAMA0");

	std::vector<char> request;
	std::vector<char> response;

	if(argc < 2){
		int x = 0;
		while(response.size() == 0){
			request.push_back('G');
			response = mySerial.sendReceive(request);
		}
		int k=0;
		for(k=0;k<response.size();k++){
			cout << response[k] ;
		}
	}

	cout << "Argument-count argc: " << argc << endl;
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
