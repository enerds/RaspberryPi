#define F_CPU 8000000L
#define BAUD 9600L

#include <string.h>
#include <avr/io.h>
#include <avr/interrupt.h>
#include <util/delay.h>
#include <stdlib.h>
#include <stdio.h>
#include <stdint.h>
#include <stdbool.h>
 
 
#define UBRR_VAL  ((F_CPU+BAUD*8)/(BAUD*16)-1)
#define BAUD_REAL (F_CPU/(16*(UBRR_VAL+1)))
#define BAUD_ERROR ((BAUD_REAL*1000)/BAUD-1000)
#if ((BAUD_ERROR>10) || (BAUD_ERROR<-10))
  #error Systematischer Fehler der Baudrate grösser 1% und damit zu hoch! 
#endif

#define R_PC0 1000 // 1000 Ohm am Spannungsteiler von PC0
/* Connection to PC0 :
 ---------------+
                |         +- R_PC0 == 1000 Ohm --- +3.3v
                |         |
                --- PC0 --+
                |         |
                |         +- Temp-Sensor --------- GND
                |
                |
*/

#define uart_buffer_size 256

#define STECKDOSENPIN 2

volatile uint8_t uart_rx_flag=0;            // Flag, String komplett empfangen
volatile uint8_t uart_tx_flag=1;            // Flag, String komplett gesendet
char uart_rx_buffer[uart_buffer_size];      // Empfangspuffer
char uart_tx_buffer[uart_buffer_size];      // Sendepuffer

int nRepeatTransmit = 10; // wie oft soll sendung an steckdose wiederholt werden
int nProtocol = 1;
int nPulseLength = 350;

void put_string(char *daten) {
   if (uart_tx_flag == 1) {
      strcpy(uart_tx_buffer, daten);      
      uart_tx_flag = 0;                    
      UCSRB |= (1<<UDRIE); 
   }
}

void get_string(char *daten) {
   if (uart_rx_flag==1) {
      strcpy(daten, uart_rx_buffer);
      uart_rx_flag = 0;
   }
}

void send_ok(void){
	char message[4];
	message[0] = 'O';
	message[1] = 'K';
	message[2] = '\n';
	message[3] = '\r';
	put_string(message);
}

int adc2res(int adcValue){
	int resistance;
	double adcVoltage = adcValue * 2.56f / 1023.0f;
	resistance = R_PC0 * (adcVoltage / (3.3f - adcVoltage));
	return resistance;
}


double res2temp(int res){
	return (res - 815.0f) / 7.5f;
}

char* dec2binWzerofill(unsigned long Dec, unsigned int bitLength){
  static char bin[64];
  unsigned int i=0;

  while (Dec > 0) {
    bin[32+i++] = ((Dec & 1) > 0) ? '1' : '0';
    Dec = Dec >> 1;
  }

  for (unsigned int j = 0; j< bitLength; j++) {
    if (j >= bitLength - i) {
      bin[j] = bin[ 31 + i - (j - (bitLength - i)) ];
    }else {
      bin[j] = '0';
    }
  }
  bin[bitLength] = '\0';
  
  return bin;
}

void transmit(int nHighPulses, int nLowPulses){
    int tmpDelay;
    // set Output pin to high
    PORTD |= (1<<STECKDOSENPIN);

    for(tmpDelay=0;tmpDelay < nPulseLength*nHighPulses;tmpDelay++){
        _delay_us(1);
    }

    PORTD &= ~(1<<STECKDOSENPIN);

    for(tmpDelay=0;tmpDelay < nPulseLength*nLowPulses;tmpDelay++){
        _delay_us(1);
    }

}

void send0(void){
	if(nProtocol == 1) transmit(1,3);
	if(nProtocol == 2) transmit(1,2);
}

void send1(void){
	if(nProtocol == 1) transmit(3,1);
	if(nProtocol == 2) transmit(2,1);
}

void sendT0(void){
    transmit(1,3);
    transmit(1,3);
}

void sendT1(void){
	transmit(3,1);
	transmit(3,1);
}

void sendTF(void){
	transmit(1,3);
	transmit(3,1);
}

void sendSync(void){
	if(nProtocol == 1) transmit(1,31);
	if(nProtocol == 2) transmit(1,10);
}

char* getCodeWordA(char* sGroup, int nChannelCode, bool bStatus) {
   int nReturnPos = 0;
   static char sReturn[13];

  char* code[6] = { "FFFFF", "0FFFF", "F0FFF", "FF0FF", "FFF0F", "FFFF0" };

  if (nChannelCode < 1 || nChannelCode > 5) {
      return '\0';
  }
  
  for (int i = 0; i<5; i++) {
    if (sGroup[i] == '0') {
      sReturn[nReturnPos++] = 'F';
    } else if (sGroup[i] == '1') {
      sReturn[nReturnPos++] = '0';
    } else {
      return '\0';
    }
  }
  
  for (int i = 0; i<5; i++) {
    sReturn[nReturnPos++] = code[ nChannelCode ][i];
  }
  
  if (bStatus) {
    sReturn[nReturnPos++] = '0';
    sReturn[nReturnPos++] = 'F';
  } else {
    sReturn[nReturnPos++] = 'F';
    sReturn[nReturnPos++] = '0';
  }
  sReturn[nReturnPos] = '\0';

  return sReturn;
}

/*
void sendFilled(unsigned long Code, unsigned int length){
	send(dec2binWzerofill(Code, length) );
}

void send(char* sCodeWord){
  for (int nRepeat=0; nRepeat<nRepeatTransmit; nRepeat++) {
	int i = 0;
	while (sCodeWord[i] != '\0') {
		if(sCodeWord[i] == '0') this->send0();
		if(sCodeWord[i] == '1') this->send1();
	i++;
    }
    sendSync();
  }
}
*/

void sendTriState(char* sCodeWord) {
  for (int nRepeat=0; nRepeat<nRepeatTransmit; nRepeat++) {
    int i = 0;
    while (sCodeWord[i] != '\0') {
	if(sCodeWord[i] == '0') sendT0();
	if(sCodeWord[i] == 'F') sendTF();
	if(sCodeWord[i] == '1') sendT1();
	i++;
    }
    sendSync();    
  }
}

void switchOn(char* sGroup, int nChannel){
    sendTriState(getCodeWordA(sGroup, nChannel, true) );
}

void switchOff(char *sGroup, int nChannel){
    sendTriState(getCodeWordA(sGroup, nChannel, false) );
}

int main (void) {
    char stringbuffer[64];  // Allgemeiner Puffer für Strings
    uint8_t buffer_full=0;  // noch ein Flag, aber nur in der Hauptschleife
    char * charpointer;     // Hilfszeiger
	int sampleValue = 0;

    // IO konfigurieren
    DDRB = 0xFF;
    DDRC = 0x00;
	PORTC = 0x00;
    //DDRD = 0xFF;
    DDRD |= (1<<STECKDOSENPIN); // Output pin für Steckdosensteuerung

	// FIXME: this is how you control a power outlet
/*
    int mytmp = 0;
    for(mytmp=0;mytmp<4;mytmp++){
        switchOn("11111", 4); // 1st parameter: 1st 5 dip-switches, 2nd parameter: the one switch that is on!
        _delay_ms(10000);
        switchOff("11111", 4);
        _delay_ms(10000);
    }
*/

    //DDRD = (DDRD|0x01);

	ADMUX = 0x00;
	ADMUX |= (1<<REFS1)| (1<<REFS0); // 0b01000000; // interne Ref-Spannung, avcc, pc0 als adc
	ADCSRA |= (1<<ADEN) | (1<<ADPS2) | (1<<ADPS1) | (1<<ADPS0);


    // Servo konfigurieren
    // Werte für OCR1A: zw. 650 und 1900!
    /*
    ICR1=10000;
    TCCR1A|=(0<<COM1A0)|(1<<COM1A1)|(0<<COM1B0)|(0<<COM1B1)|(0<<FOC1A)|(0<<FOC1B)|(1<<WGM11)|(0<<WGM10);
    TCCR1B|=(0<<ICNC1)|(0<<ICES1)|(1<<WGM13)|(1<<WGM12)|(0<<CS12)|(1<<CS11)|(0<<CS10);
    OCR1A = 1300;
    */

    // UART konfigurieren
    UBRRH = UBRR_VAL >> 8;
    UBRRL = UBRR_VAL & 0xFF;
    UCSRB = (1<<RXCIE) | (1<<RXEN) | (1<<TXEN); 

    UCSRC = (1<<URSEL)|(1<<USBS)|(3<<UCSZ0);      //Asynchron 8N1

    // Stringpuffer initialisieren
    //stringbuffer[0] = '\n';
    //stringbuffer[1] = '\r';

    // Interrupts freigeben
    sei();

    while(1) {
        if (uart_rx_flag==1 && buffer_full==0) {
            get_string(stringbuffer);
            buffer_full=1;
        }

	/*
	 * zum testen, falls "S1" gesendet wurde,
	 * schalte Pin C0 ein
	 */
	if(stringbuffer[0] == 'S'){
		if(stringbuffer[1] == '1'){
			PORTC |= (1<<5);
			send_ok();
			// put other char in there to not trigger it again
			stringbuffer[0] = 'N';
			buffer_full = 0;
		}	
	}else if(stringbuffer[0] == 'U'){
		if(stringbuffer[1] == '1'){
			PORTC &= ~(1<<5);
			send_ok();
			stringbuffer[0] = 'N';
			buffer_full = 0;
		}
	}else if(stringbuffer[0] == 'G'){ // adc wert anfordern
		ADCSRA |= (1<<ADSC);  //single conversion mode ein
		while(ADCSRA & (1<<ADSC));  //warten bis konvertierung abgeschlosen
		sampleValue = ADCW;
		dtostrf( res2temp(adc2res(sampleValue)), 5, 2, stringbuffer );
		strcat(stringbuffer, "\n\r");
		_delay_ms(100);
		put_string(stringbuffer);
		buffer_full = 0;
	}else if(stringbuffer[0] == 'L'){ // lichter schalten
		if(stringbuffer[2] == '1'){ // blaues Licht an
			// TOBI
		        switchOn("11111", stringbuffer[1] - '0'); // 1st parameter: 1st 5 dip-switches, 2nd parameter: the one switch that is on!
			_delay_ms(100);
		        switchOn("11111", stringbuffer[1] - '0'); // 1st parameter: 1st 5 dip-switches, 2nd parameter: the one switch that is on!
			_delay_ms(100);
		}else if(stringbuffer[2] == '0'){ // blaues Licht aus
		        switchOff("11111", stringbuffer[1] - '0');
			_delay_ms(100);
		        switchOff("11111", stringbuffer[1] - '0');
			_delay_ms(100);
		}	
		stringbuffer[0] = 'N';
		buffer_full = 0;
	}else{
		_delay_ms(10);
	        if (uart_tx_flag==1 && buffer_full==1) {
	            strcat(stringbuffer, "\n\r");
	            put_string(stringbuffer); // zurücksenden
	            buffer_full=0; // Buffer ist wieder verfügbar
	        }
	}
    }
}

ISR(USART_RXC_vect) {
    static uint8_t uart_rx_cnt=0;     // Zähler für empfangene Zeichen
    uint8_t data;

    data = UDR;

    if (!uart_rx_flag) {
        if (data == '\r') {
            uart_rx_buffer[uart_rx_cnt]=0;
            uart_rx_flag=1;
            uart_rx_cnt=0;
        }else if (uart_rx_cnt<(uart_buffer_size-1)) {
            uart_rx_buffer[uart_rx_cnt]=data;
            uart_rx_cnt++; // Zähler erhöhen
        }
    }
}


ISR(USART_UDRE_vect) {
    static char* uart_tx_p = uart_tx_buffer;    
    uint8_t data;

    data = *uart_tx_p++;

    if (data == 0 ) {
        UCSRB &= ~(1<<UDRIE);       // ja, dann UDRE Interrupt ausschalten
        uart_tx_p = uart_tx_buffer; // Pointer zurücksetzen
        uart_tx_flag = 1;           // Flag setzen, Übertragung beeendet
    }else{
	 UDR = data;                // nein, Daten senden
	_delay_ms(10);
	}
}
