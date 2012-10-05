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

class Mysql{
	public:
		Mysql(const char * host, const char * user, const char * pwd, const char * db);
		~Mysql();
		int checkCode(std::vector<int> code, int door);		
		void cleanupTemp();
		std::vector<std::string> getADCs(); 
	private:
		int verbose;
		void connectDB();
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
