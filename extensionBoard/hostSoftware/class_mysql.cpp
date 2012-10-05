#include <cstdlib>
#include <cstring>	
#include <stdio.h>
#include <iostream>
#include <sstream>
#include <stdlib.h>
#include <vector>
#include <string>
#include <mysql.h>
#include <vector>
#include <ctime>
#include <time.h>
#include "class_mysql.h"
#include "class_log.h"

Mysql::Mysql(const char * host, const char * user, const char * pwd, const char * db){
	/*
	 * verbose level:
	 * 1 - high level
	 * 2 - hex-daten
	 */
	verbose = 0;

	myHost = host;
	myUser = user;
	myPwd = pwd;
	myDB = db;
	

	std::clog.rdbuf(new Log("rpid", LOG_LOCAL5));
	
	mysql = mysql_init(NULL);
}

Mysql::~Mysql(){
	mysql_close(mysql);
}

void Mysql::connectDB(){
	mysql = mysql_init(NULL);
	if(!mysql_real_connect(mysql, myHost, myUser, myPwd, myDB, 0, NULL, 0) != 0){
		std::clog << "[Mysql] Kann nicht verbinden!" << std::endl;
	}else{
		std::clog << "[Mysql] Datenbank verbunden." << std::endl;
	}
}


void Mysql::closeDB(){
	mysql_close(mysql);
}


std::vector<std::string> Mysql::getADCs(){
	std::vector<std::string> adcPins;

	connectDB();

	std::string query = "SELECT * FROM atmega WHERE activefunc = 'adc'";
	if(verbose > 2) std::clog << "[Mysql] Getting ADC pins" << std::endl;
	mysql_query(mysql, query.c_str());
	res = mysql_store_result(mysql);
	if(mysql_errno(mysql) != 0) std::clog << "[Mysql] Error " << mysql_errno(mysql) << mysql_error(mysql) << std::endl;
	int numrows = mysql_num_rows(res);

	if(mysql_num_rows(res) == 0){
		std::clog << "[Mysql] No pins configured as ADC." << std::endl;
	}else{
		while(row=mysql_fetch_row(res)){
			std::clog << "[Mysql] Pin configured as ADC: " << row[0] << std::endl;
			adcPins.push_back(row[0]);
		}
	}

	closeDB();

	return adcPins;
}
