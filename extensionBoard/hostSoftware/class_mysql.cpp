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


void Mysql::insertValue(std::string pin, int value){
	connectDB();

	std::stringstream sstm;
	sstm << value;
	std::string query = "INSERT INTO sensors (pin,value) VALUES ('" + pin + "' , " + sstm.str() + ")";
	mysql_query(mysql, query.c_str());
	if(mysql_errno(mysql) != 0) std::clog << "[Mysql] insertValue Error " << mysql_errno(mysql) << mysql_error(mysql) << std::endl;

	closeDB();
}


long int Mysql::getLastReading(std::string curPin){
	connectDB();

	long int ret = 0;
	std::string query = "SELECT UNIX_TIMESTAMP(`ts`) FROM sensors WHERE `pin`='" + curPin +"' ORDER BY `ts` DESC LIMIT 1";
	mysql_query(mysql, query.c_str());

	res = mysql_store_result(mysql);
	if(mysql_errno(mysql) != 0) std::clog << "[Mysql] getLastReading Error " << mysql_errno(mysql) << mysql_error(mysql) << std::endl;

	while(row=mysql_fetch_row(res)){
		ret = atol(row[0]);
	}

	closeDB();

	return ret;
}

std::vector< std::pair<std::string, long int> > Mysql::getADCs(){
	std::vector< std::pair<std::string, long int> > adcPins;

	connectDB();

	std::string query = "SELECT `pin`,`interval` FROM atmega WHERE activefunc = 'adc'";
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
			std::pair<std::string, long int> tmp;
			tmp.first = row[0];
			tmp.second = atol(row[1]);
			adcPins.push_back(tmp);
		}
	}

	closeDB();

	return adcPins;
}
