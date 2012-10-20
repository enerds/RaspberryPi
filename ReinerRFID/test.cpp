#include <iostream> 
#include <string> 
#include "MCardAPI.h" 
using namespace std; 


int main( ) 
{ 
/* 
* MCard Init. 
*/ 
SCARDCONTEXT   ContextHandle; 
char      szReader[] = "SCR333 USB Smart Card Reader"; 
MCARDCONTEXT   hMCardContext; 
DWORD      dwDLLVersion; 

LONG      rv; 
LONG      IReturn; 


rv = SCardEstablishContext(SCARD_SCOPE_SYSTEM, NULL, NULL, &ContextHandle); 

IReturn = MCardInitialize(ContextHandle, szReader, &hMCardContext, &dwDLLVersion); 
cout << "Init. IReturn: " << IReturn << endl;   


/* 
* MCard Connect 
*/ 
DWORD   dwConnectMode; 
BYTE   byCardType; 
MCARDHANDLE hMCard; 

IReturn = MCardConnect(hMCardContext, dwConnectMode, byCardType, &hMCard ); 
cout << "Connect IReturn: " << IReturn << endl; 

/* 
* read MCard 
*/ 
BYTE abyData [20]; 
DWORD dwLen = 20; 

IReturn = MCardReadMemory(hMCard, 0, 0x80, abyData, &dwLen  ); 
cout << "read IReturn: " << IReturn << endl; 


return 0; 
}
