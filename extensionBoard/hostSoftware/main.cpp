#include <iostream>
#include <string>
#include <time.h>
#include <stdio.h>
#include <stdlib.h>

#include "class_serial.h"                                                                                                                                                                                                                                                                                                      

using namespace std;

int main() {
	Serial mySerial("/dev/ttyAMA0");

	std::vector<char> y;
	int x = 0;
	y = mySerial.sendReceive('G');
	int k=0;
	for(k=0;k<y.size();k++){
		cout << y[k] ;
	}

	return 0;
}                                  
