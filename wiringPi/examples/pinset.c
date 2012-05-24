#include <wiringPi.h>

#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>

int main (int argc, char *argv[]){
  if (wiringPiSetup () == -1)
    exit (1) ;

  int pin = -1;
  int mode = -1;
  int val = -1;

  if(argc == 4){
    mode = atoi(argv[1]);
    pin = atoi(argv[2]);
    val = atoi(argv[3]);
  }else{ 
    printf("Usage: pinset <MODE> <PIN> <VALUE>");
    exit(1);
  }

  if(mode == 0){ // set
    pinMode(pin, OUTPUT);
    digitalWrite (pin, val) ;
  }else if(mode == 1){ // pwm
    if(pin != 1){
      printf("Only Pin 1 can be PWM currently");
      exit(1);
    }
    pinMode(1,PWM_OUTPUT);
    pwmWrite(1,val);
  }

  return 0 ;
}
