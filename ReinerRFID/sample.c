#include <stdio.h>
#include <stdlib.h>
#include <winscard.h>

#define CHECK(f, rv) \
if (SCARD_S_SUCCESS != rv) \
{ \
        printf(f ": %s\n", pcsc_stringify_error(rv)); \
}

int main(void){
    LONG rv;

    SCARDCONTEXT hContext;
    LPTSTR mszReaders;
    SCARDHANDLE hCard;
    DWORD dwReaders, dwActiveProtocol, dwRecvLength;

    SCARD_IO_REQUEST pioSendPci;
    BYTE pbRecvBuffer[256];
    BYTE cmd1[] = { 0x00, 0xA4, 0x04, 0x00, 0x0A, 0xA0,
    0x00, 0x00, 0x00, 0x62, 0x03, 0x01, 0x0C, 0x06, 0x01 };
    BYTE cmd2[] = { 0x00, 0x00, 0x00, 0x00 };
    BYTE code1[] = {0x6d, 0x00};

    int cardThere = 1;

    unsigned int i;
    while(1){
        rv = SCardEstablishContext(SCARD_SCOPE_SYSTEM, NULL, NULL, &hContext);
        //CHECK("SCardEstablishContext", rv)

        rv = SCardListReaders(hContext, NULL, NULL, &dwReaders);
        //CHECK("SCardListReaders", rv)

        mszReaders = calloc(dwReaders, sizeof(char));
        rv = SCardListReaders(hContext, NULL, mszReaders, &dwReaders);
        //CHECK("SCardListReaders", rv)
        //printf("reader name: %s\n", mszReaders);

        rv = SCardConnect(hContext, mszReaders, SCARD_SHARE_SHARED,
        SCARD_PROTOCOL_T0 | SCARD_PROTOCOL_T1, &hCard, &dwActiveProtocol);
        //CHECK("SCardConnect", rv)
        if(rv == -2146435060 && cardThere == 1){
            printf("============== KEINE SMARTCARD !!!\n");
            //system("/etc/init.d/motion start");
		system("/home/pi/scripts/bye.php");
            cardThere = 0;
        }else{
            if(cardThere == 0){
                switch(dwActiveProtocol){
                    case SCARD_PROTOCOL_T0:
                        pioSendPci = *SCARD_PCI_T0;
                        break;
                    case SCARD_PROTOCOL_T1:
                        pioSendPci = *SCARD_PCI_T1;
                        break;
                }
                dwRecvLength = sizeof(pbRecvBuffer);
                rv = SCardTransmit(hCard, &pioSendPci, cmd1, sizeof(cmd1),NULL, pbRecvBuffer, &dwRecvLength);
                //CHECK("SCardTransmit", rv)

                /*printf("response: ");
                for(i=0; i<dwRecvLength; i++)
                    printf("%02X ", pbRecvBuffer[i]);
                printf("\n");
                */

                dwRecvLength = sizeof(pbRecvBuffer);
                rv = SCardTransmit(hCard, &pioSendPci, cmd2, sizeof(cmd2),NULL, pbRecvBuffer, &dwRecvLength);
                //CHECK("SCardTransmit", rv)

                /*printf("response: ");
                for(i=0; i<dwRecvLength; i++)
                    printf("%02X ", pbRecvBuffer[i]);
                printf("\n");*/

                if(pbRecvBuffer[0] == code1[0] && pbRecvBuffer[1] == code1[1]){
                    printf("=========== KURREKTER CODE !!!\n");
                    //system("/etc/init.d/motion stop");
			system("/home/pi/scripts/welcome.php");
                    cardThere = 1;
                    pbRecvBuffer[0] = 0x0;
                    pbRecvBuffer[1] = 0x0;
                }

                rv = SCardDisconnect(hCard, SCARD_LEAVE_CARD);
                //CHECK("SCardDisconnect", rv)
            } // if card there
        } // if smart card is there

        free(mszReaders);

        rv = SCardReleaseContext(hContext);

        CHECK("SCardReleaseContext", rv)
        sleep(10);
    } // while 1

return 0;
}
