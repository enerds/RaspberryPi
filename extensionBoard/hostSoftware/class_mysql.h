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
#include <mysql.h>
#include <ctime>
#include <utility>

class Mysql{
	public:
		Mysql(const char * host, const char * user, const char * pwd, const char * db);
		~Mysql();

		std::vector< std::pair<std::string, long int> > getADCs(); 
		void insertValue(std::string pin, int value);
		long int getLastReading(std::string curPin);
		void connectDB();
	private:
		int verbose;
		void closeDB();
		MYSQL *mysql;
		MYSQL_RES *res;
		MYSQL_FIELD *field;
		MYSQL_ROW row;

		time_t t;
		struct tm *timeinfo;
		int year, month, day, hour, minute, second, weekday;

		const char * myHost;
		const char * myUser;
		const char * myPwd;
		const char * myDB;
};
