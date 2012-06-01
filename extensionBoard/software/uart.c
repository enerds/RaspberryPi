#include <avr/io.h>
#include <avr/delay.h>

#define F_CPU		8000000
#define UART_BAUD_RATE	9600

#define UART_BAUD_SELECT (F_CPU/(UART_BAUD_RATE*16l)-1)

void send_char(char data){
	while (!(UCSRA & (1<<UDRE)));
	UDR = data;
	for(int x=0;x<10;x++){
		_delay_ms(100);
	}
}

void send_string(char *data){
	while(*data){
		send_char(*data);
		data++;
	}
}


int main(void)
{
   DDRD = 0xFF;
  
   while(1){	
	   /*
   UBRRH = UBRR_VAL >> 8;
   UBRRL = UBRR_VAL & 0xFF;
   */

	   UBRRH = UART_BAUD_SELECT >> 8;
	   UBRRL = UART_BAUD_SELECT & 0xFF;
  
   
   UCSRB = (1<<RXEN)|(1<<TXEN);      //UART TX einschalten
   UCSRC = (1<<URSEL)|(1<<USBS)|(3<<UCSZ0);      //Asynchron 8N1
   
   

	   while (!(UCSRA & (1<<UDRE)))      //warten bis Senden möglich
	   {
	   }   

	   send_string("test hallo!");
	   
	   for(int i=0;i<100;++i){
		   _delay_ms(100);
	   }
   }

   return 0;                     
} 
