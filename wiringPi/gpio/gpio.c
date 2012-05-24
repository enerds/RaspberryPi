
/*
 * gpio.c:
 *	Command-line program to fiddle with the GPIO pins on the
 *	Raspberry Pi
 */

#undef	DEBUG

#include <wiringPi.h>

#include <stdio.h>
#include <stdlib.h>
#include <stdint.h>
#include <string.h>

char *usage = "Usage: gpio <read/write/pwm/mode> ..." ;

/*
 * doMode:
 *	gpio mode pin mode ...
 *********************************************************************************
 */

void doMode (int argc, char *argv [])
{
  int pin ;
  char *mode ;

#ifdef	DEBUG
  printf ("doMode:\n") ;
#endif

  if (argc != 4)
  {
    fprintf (stderr, "Usage: %s mode pin mode\n", argv [0]) ;
    exit (1) ;
  }

  pin = atoi (argv [2]) ;
  if ((pin < 0) || (pin >= NUM_PINS))
    return ;

  mode = argv [3] ;

#ifdef	DEBUG
  printf ("  %2d -> %s\n", pin, mode) ;
#endif

  /**/ if (strcmp (mode, "in")   == 0)
    pinMode (pin, INPUT) ;
  else if (strcmp (mode, "out")  == 0)
    pinMode (pin, OUTPUT) ;
  else if (strcmp (mode, "pwm")  == 0)
    pinMode (pin, PWM_OUTPUT) ;
  else if (strcmp (mode, "up")   == 0)
    pullUpDnControl (pin, PUD_UP) ;
  else if (strcmp (mode, "down") == 0)
    pullUpDnControl (pin, PUD_DOWN) ;
  else if (strcmp (mode, "tri") == 0)
    pullUpDnControl (pin, PUD_OFF) ;
  else
  {
    fprintf (stderr, "%s: Invalid mode: %s\n", argv [1], mode) ;
    exit (1) ;
  }
}

/*
 * doWrite:
 *	gpio write pin value
 *********************************************************************************
 */

void doWrite (int argc, char *argv [])
{
  int pin, val ;

#ifdef	DEBUG
  printf ("doWrite:\n") ;
#endif

  if (argc != 4)
  {
    fprintf (stderr, "Usage: %s write pin value\n", argv [0]) ;
    exit (1) ;
  }

  pin = atoi (argv [2]) ;
  if ((pin < 0) || (pin >= NUM_PINS))
    return ;

  val = atoi (argv [3]) ;

#ifdef	DEBUG
  printf ("  %2d -> %d\n", pin, val) ;
#endif

  /**/ if (val == 0)
    digitalWrite (pin, LOW) ;
  else
    digitalWrite (pin, HIGH) ;
}


void doRead (int argc, char *argv []) 
{
  int pin, val ;

#ifdef	DEBUG
  printf ("doRead:\n") ;
#endif

  if (argc != 3)
  {
    fprintf (stderr, "Usage: %s read pin\n", argv [0]) ;
    exit (1) ;
  }

  pin = atoi (argv [2]) ;
  if ((pin < 0) || (pin >= NUM_PINS))
  {
    printf ("0\n") ;
    return ;
  }

  val = digitalRead (pin) ;

#ifdef	DEBUG
  printf ("  %2d -> %d\n", pin, val) ;
#endif

  printf ("%s\n", val == 0 ? "0" : "1") ;
}


void doPwm (int argc, char *argv [])
{
  int pin, val ;

#ifdef	DEBUG
  printf ("doPwm:\n") ;
#endif

  if (argc != 4)
  {
    fprintf (stderr, "Usage: %s pwm pin value\n", argv [0]) ;
    exit (1) ;
  }

  pin = atoi (argv [2]) ;
  if ((pin < 0) || (pin >= NUM_PINS))
    return ;

  val = atoi (argv [3]) ;

#ifdef	DEBUG
  printf ("  %2d -> %d\n", pin, val) ;
#endif

  pwmWrite (1, val) ;
}


int main (int argc, char *argv [])
{
  if (argc == 1)
  {
    fprintf (stderr, "%s: %s\n", argv [0], usage) ;
    return 1 ;
  }

  if (wiringPiSetup () == -1)
  {
    fprintf (stderr, "%s: Unable to initialise GPIO\n", argv [0]) ;
    exit (1) ;
  }

  /**/ if (strcmp (argv [1], "write") == 0)
    doWrite (argc, argv) ;
  else if (strcmp (argv [1], "read" ) == 0)
    doRead  (argc, argv) ;
  else if (strcmp (argv [1], "mode" ) == 0)
    doMode  (argc, argv) ;
  else if (strcmp (argv [1], "pwm"  ) == 0)
    doPwm   (argc, argv) ;
  else
  {
    fprintf (stderr, "%s: Unknown command (read, write, pwm or mode expected)\n", argv [0]) ;
    return 1 ;
  }
  return 0 ;
}
