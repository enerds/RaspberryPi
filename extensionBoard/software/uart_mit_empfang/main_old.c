#define F_CPU 8000000
#define BAUD 38400

#include <string.h>
#include <avr/io.h>
#include <avr/interrupt.h>
#include <util/delay.h>
 
 
#define UBRR_VAL  ((F_CPU+BAUD*8)/(BAUD*16)-1)
#define BAUD_REAL (F_CPU/(16*(UBRR_VAL+1)))
#define BAUD_ERROR ((BAUD_REAL*1000)/BAUD-1000)
#if ((BAUD_ERROR>10) || (BAUD_ERROR<-10))
  #error Systematischer Fehler der Baudrate grösser 1% und damit zu hoch! 
#endif

#define uart_buffer_size 256

volatile uint8_t uart_rx_flag=0;            // Flag, String komplett empfangen
volatile uint8_t uart_tx_flag=1;            // Flag, String komplett gesendet
char uart_rx_buffer[uart_buffer_size];      // Empfangspuffer
char uart_tx_buffer[uart_buffer_size];      // Sendepuffer

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

int main (void) {
    char stringbuffer[64];  // Allgemeiner Puffer für Strings
    uint8_t buffer_full=0;  // noch ein Flag, aber nur in der Hauptschleife
    char * charpointer;     // Hilfszeiger

    // IO konfigurieren
    DDRB = 0xFF;
    DDRC = 0xFF;
    //DDRD = 0xFF;
    //DDRD = (DDRD|0x01);

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
    stringbuffer[0] = '\n';
    stringbuffer[1] = '\r';

    // Interrupts freigeben
    sei();

    while(1) {
        if (uart_rx_flag==1 && buffer_full==0) {
            get_string(stringbuffer+2);
            buffer_full=1;
        }

	_delay_ms(10);

        if (uart_tx_flag==1 && buffer_full==1) {
            strcat(stringbuffer, "\n\r");
            put_string(stringbuffer); // zurücksenden
            buffer_full=0; // Buffer ist wieder verfügbar
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
