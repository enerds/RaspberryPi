/***********************************************************************
 * This is a library for those cheap 1.8" SPI displays,
 * like the ones that Adafruit sells for 4 times the price than ebay.
 * It is known to work with display with the "red" flag, there are also
 * green and blue ones. Also similar, they might need some tweaking
 * (pixel offsets, different init procedures).
 *
 * An article with more details can be found here:
 * http://enerds.eu/blog/18-spi-lcd-display-10-eur-with-atmega.html
 *
 * This code is based on the work of Limor Fried/Ladyada,
 * many thanks for your work !!
 *
 * I do not like arduinos, especially the arduino-"code",
 * as it does not good besides rapid prototyping,
 * so i prefer developing in real code to stay portable
 * in the "production" phase.
 *
 * Set the Pins and Ports in the headerfile and you are ready to go.
 *
 * I wired the LCD as follows:
 * VCC      +5V
 * GND      GND
 * SCL      PB5 (SCK), Digital Pin 13 on Arduino
 * SDA      PB3 (MOSI), Digital Pin 11 on Arduino
 * RS       PD7, Digital Pin 7 on Arduino
 * RST      PD6, Digital Pin 6 on Arduino
 * CS       GND
 *
 * I wired CS to GND because this is the only SPI device in my current
 * setup. 
 *
 * Feel free to contact me: Tobias Weis <mail@enerds.eu>
 */
#define __PROG_TYPES_COMPAT__
#define F_CPU 16000000L
#include "china_lcd.h"
#include "gfx.h"
#include <avr/pgmspace.h>
#include <avr/io.h>
#include <util/delay.h>
#include <avr/interrupt.h>
#include <limits.h>

/*
 * myDelay value must be known at compile time,
 * so here's a dirty hack to circumvent this
 */
void myDelay(uint16_t ms){
    for(int x=0 ; x < ms ; x+=5){
        _delay_ms(5);
    }
}

/********************** START SPI STUFF *********************************/
void SPI_begin(void){
    DDRB |= (1 << PB5);//out
    DDRB |= (1 << PB3);//out
    DDRB |= (1 << PB2);//out

    PORTB &= ~(1 << PB5);//lo
    PORTB &= ~(1 << PB3);//lo
    PORTB |= (1 << PB2);//hi

    SPCR |= (1 << MSTR) | (1 << SPE);
}

void SPI_end(void){
    SPCR &= ~(1 << SPE);
}

void SPI_setBitOrder(uint8_t bitOrder){
    if(bitOrder == LSBFIRST){
        SPCR |= (1 << DORD);
    }else{
        SPCR &= ~(1 << DORD);
    }
}

void SPI_setDataMode(uint8_t mode){
    SPCR = (SPCR & ~SPI_MODE_MASK) | mode;
}

void SPI_setClockDivider(uint8_t rate){
    SPCR = (SPCR & ~SPI_CLOCK_MASK) | (rate & SPI_CLOCK_MASK);
    SPSR = (SPSR & ~SPI_2XCLOCK_MASK) | ((rate >> 2) & SPI_2XCLOCK_MASK);
}
/********************** END SPI STUFF *********************************/
uint8_t _datapinmask,clkpinmask,cspinmask,rspinmask,colstart,rowstart;

inline void spiwrite(uint8_t c) {
    SPDR = c;
    while (!(SPSR & (1 << SPIF))) ; 
}

void writecommand(uint8_t c) {
    RSPORT &= ~(1 << RS);
    spiwrite(c);
}

void writedata(uint8_t c) {
    RSPORT |= (1 << RS);
    spiwrite(c);
}


// Rather than a bazillion writecommand() and writedata() calls, screen
// initialization commands and arguments are organized in these tables
// stored in PROGMEM.  The table may look bulky, but that's mostly the
// formatting -- storage-wise this is hundreds of bytes more compact
// than the equivalent code.  Companion function follows.
#define DELAY 0x80
PROGMEM static const prog_uchar
  Bcmd[] = {                  // Initialization commands for 7735B screens
    18,                       // 18 commands in list:
    ST7735_SWRESET,   DELAY,  //  1: Software reset, no args, w/delay
      50,                     //     50 ms delay
    ST7735_SLPOUT ,   DELAY,  //  2: Out of sleep mode, no args, w/delay
      255,                    //     255 = 500 ms delay
    ST7735_COLMOD , 1+DELAY,  //  3: Set color mode, 1 arg + delay:
      0x05,                   //     16-bit color
      10,                     //     10 ms delay
    ST7735_FRMCTR1, 3+DELAY,  //  4: Frame rate control, 3 args + delay:
      0x00,                   //     fastest refresh
      0x06,                   //     6 lines front porch
      0x03,                   //     3 lines back porch
      10,                     //     10 ms delay
    ST7735_MADCTL , 1      ,  //  5: Memory access ctrl (directions), 1 arg:
      0x08,                   //     Row addr/col addr, bottom to top refresh
    ST7735_DISSET5, 2      ,  //  6: Display settings #5, 2 args, no delay:
      0x15,                   //     1 clk cycle nonoverlap, 2 cycle gate
                              //     rise, 3 cycle osc equalize
      0x02,                   //     Fix on VTL
    ST7735_INVCTR , 1      ,  //  7: Display inversion control, 1 arg:
      0x0,                    //     Line inversion
    ST7735_PWCTR1 , 2+DELAY,  //  8: Power control, 2 args + delay:
      0x02,                   //     GVDD = 4.7V
      0x70,                   //     1.0uA
      10,                     //     10 ms delay
    ST7735_PWCTR2 , 1      ,  //  9: Power control, 1 arg, no delay:
      0x05,                   //     VGH = 14.7V, VGL = -7.35V
    ST7735_PWCTR3 , 2      ,  // 10: Power control, 2 args, no delay:
      0x01,                   //     Opamp current small
      0x02,                   //     Boost frequency
    ST7735_VMCTR1 , 2+DELAY,  // 11: Power control, 2 args + delay:
      0x3C,                   //     VCOMH = 4V
      0x38,                   //     VCOML = -1.1V
      10,                     //     10 ms delay
    ST7735_PWCTR6 , 2      ,  // 12: Power control, 2 args, no delay:
      0x11, 0x15,
    ST7735_GMCTRP1,16      ,  // 13: Magical unicorn dust, 16 args, no delay:
      0x09, 0x16, 0x09, 0x20, //     (seriously though, not sure what
      0x21, 0x1B, 0x13, 0x19, //      these config values represent)
      0x17, 0x15, 0x1E, 0x2B,
      0x04, 0x05, 0x02, 0x0E,
    ST7735_GMCTRN1,16+DELAY,  // 14: Sparkles and rainbows, 16 args + delay:
      0x0B, 0x14, 0x08, 0x1E, //     (ditto)
      0x22, 0x1D, 0x18, 0x1E,
      0x1B, 0x1A, 0x24, 0x2B,
      0x06, 0x06, 0x02, 0x0F,
      10,                     //     10 ms delay
    ST7735_CASET  , 4      ,  // 15: Column addr set, 4 args, no delay:
      0x00, 0x02,             //     XSTART = 2
      0x00, 0x81,             //     XEND = 129
    ST7735_RASET  , 4      ,  // 16: Row addr set, 4 args, no delay:
      0x00, 0x02,             //     XSTART = 1
      0x00, 0x81,             //     XEND = 160
    ST7735_NORON  ,   DELAY,  // 17: Normal display on, no args, w/delay
      10,                     //     10 ms delay
    ST7735_DISPON ,   DELAY,  // 18: Main screen turn on, no args, w/delay
      255 },                  //     255 = 500 ms delay

  Rcmd1[] = {                 // Init for 7735R, part 1 (red or green tab)
    15,                       // 15 commands in list:
    ST7735_SWRESET,   DELAY,  //  1: Software reset, 0 args, w/delay
      150,                    //     150 ms delay
    ST7735_SLPOUT ,   DELAY,  //  2: Out of sleep mode, 0 args, w/delay
      255,                    //     500 ms delay
    ST7735_FRMCTR1, 3      ,  //  3: Frame rate ctrl - normal mode, 3 args:
      0x01, 0x2C, 0x2D,       //     Rate = fosc/(1x2+40) * (LINE+2C+2D)
    ST7735_FRMCTR2, 3      ,  //  4: Frame rate control - idle mode, 3 args:
      0x01, 0x2C, 0x2D,       //     Rate = fosc/(1x2+40) * (LINE+2C+2D)
    ST7735_FRMCTR3, 6      ,  //  5: Frame rate ctrl - partial mode, 6 args:
      0x01, 0x2C, 0x2D,       //     Dot inversion mode
      0x01, 0x2C, 0x2D,       //     Line inversion mode
    ST7735_INVCTR , 1      ,  //  6: Display inversion ctrl, 1 arg, no delay:
      0x07,                   //     No inversion
    ST7735_PWCTR1 , 3      ,  //  7: Power control, 3 args, no delay:
      0xA2,
      0x02,                   //     -4.6V
      0x84,                   //     AUTO mode
    ST7735_PWCTR2 , 1      ,  //  8: Power control, 1 arg, no delay:
      0xC5,                   //     VGH25 = 2.4C VGSEL = -10 VGH = 3 * AVDD
    ST7735_PWCTR3 , 2      ,  //  9: Power control, 2 args, no delay:
      0x0A,                   //     Opamp current small
      0x00,                   //     Boost frequency
    ST7735_PWCTR4 , 2      ,  // 10: Power control, 2 args, no delay:
      0x8A,                   //     BCLK/2, Opamp current small & Medium low
      0x2A,  
    ST7735_PWCTR5 , 2      ,  // 11: Power control, 2 args, no delay:
      0x8A, 0xEE,
    ST7735_VMCTR1 , 1      ,  // 12: Power control, 1 arg, no delay:
      0x0E,
    ST7735_INVOFF , 0      ,  // 13: Don't invert display, no args, no delay
    ST7735_MADCTL , 1      ,  // 14: Memory access control (directions), 1 arg:
      0xC8,                   //     row addr/col addr, bottom to top refresh
    ST7735_COLMOD , 1      ,  // 15: set color mode, 1 arg, no delay:
      0x05 },                 //     16-bit color

  Rcmd2green[] = {            // Init for 7735R, part 2 (green tab only)
    2,                        //  2 commands in list:
    ST7735_CASET  , 4      ,  //  1: Column addr set, 4 args, no delay:
      0x00, 0x02,             //     XSTART = 0
      0x00, 0x7F+0x02,        //     XEND = 127
    ST7735_RASET  , 4      ,  //  2: Row addr set, 4 args, no delay:
      0x00, 0x01,             //     XSTART = 0
      0x00, 0x9F+0x01 },      //     XEND = 159
  Rcmd2red[] = {              // Init for 7735R, part 2 (red tab only)
    2,                        //  2 commands in list:
    ST7735_CASET  , 4      ,  //  1: Column addr set, 4 args, no delay:
      0x00, 0x00,             //     XSTART = 0
      0x00, 0x7F,             //     XEND = 127
    ST7735_RASET  , 4      ,  //  2: Row addr set, 4 args, no delay:
      0x00, 0x00,             //     XSTART = 0
      0x00, 0x9F },           //     XEND = 159

  Rcmd3[] = {                 // Init for 7735R, part 3 (red or green tab)
    4,                        //  4 commands in list:
    ST7735_GMCTRP1, 16      , //  1: Magical unicorn dust, 16 args, no delay:
      0x02, 0x1c, 0x07, 0x12,
      0x37, 0x32, 0x29, 0x2d,
      0x29, 0x25, 0x2B, 0x39,
      0x00, 0x01, 0x03, 0x10,
    ST7735_GMCTRN1, 16      , //  2: Sparkles and rainbows, 16 args, no delay:
      0x03, 0x1d, 0x07, 0x06,
      0x2E, 0x2C, 0x29, 0x2D,
      0x2E, 0x2E, 0x37, 0x3F,
      0x00, 0x00, 0x02, 0x10,
    ST7735_NORON  ,    DELAY, //  3: Normal display on, no args, w/delay
      10,                     //     10 ms delay
    ST7735_DISPON ,    DELAY, //  4: Main screen turn on, no args w/delay
      100 };                  //     100 ms delay



// Companion code to the above tables.  Reads and issues
// a series of LCD commands stored in PROGMEM byte array.
void commandList(const uint8_t *addr) {
  uint8_t  numCommands, numArgs;
  uint16_t ms;

  numCommands = pgm_read_byte(addr++);   // Number of commands to follow
  while(numCommands--) {                 // For each command...
    writecommand(pgm_read_byte(addr++)); //   Read, issue command
    numArgs  = pgm_read_byte(addr++);    //   Number of args to follow
    ms       = numArgs & DELAY;          //   If hibit set, delay follows args
    numArgs &= ~DELAY;                   //   Mask out delay bit
    while(numArgs--) {                   //   For each argument...
      writedata(pgm_read_byte(addr++));  //     Read, issue argument
    }

    if(ms) {
      ms = pgm_read_byte(addr++); // Read post-command delay time (ms)
      if(ms == 255) ms = 500;
      myDelay(ms);
    }
  }
}


void init(void){
    colstart  = rowstart = 0; // May be overridden in init func

    RSREG |= (1 << RS); //out
    RSTREG |= (1 << RST);//out

    SPI_begin();
    SPI_setClockDivider(SPI_CLOCK_DIV4); // 4 MHz (half speed)
    SPI_setBitOrder(MSBFIRST);
    SPI_setDataMode(SPI_MODE0);

    RSTPORT |= (1 << RST);
    myDelay(500);
    RSTPORT &= ~(1 << RST);
    myDelay(500);
    RSTPORT |= (1 << RST);
    myDelay(500);

    commandList(Rcmd1);
    commandList(Rcmd2red);
    commandList(Rcmd3);
}


void setAddrWindow(uint8_t x0, uint8_t y0, uint8_t x1, uint8_t y1) {
  writecommand(ST7735_CASET); // Column addr set
  writedata(0x00);
  writedata(x0+colstart);     // XSTART 
  writedata(0x00);
  writedata(x1+colstart);     // XEND

  writecommand(ST7735_RASET); // Row addr set
  writedata(0x00);
  writedata(y0+rowstart);     // YSTART
  writedata(0x00);
  writedata(y1+rowstart);     // YEND

  writecommand(ST7735_RAMWR); // write to RAM
}


void fillScreen(uint16_t color) {
  uint8_t x, y, hi = color >> 8, lo = color;

  setAddrWindow(0, 0, _width-1, _height-1);

    RSPORT |= (1 << RS);

  for(y=_height; y>0; y--) {
    for(x=_width; x>0; x--) {
      spiwrite(hi);
      spiwrite(lo);
    }
  }
}


void drawPixel(int16_t x, int16_t y, uint16_t color) {
    if((x < 0) ||(x >= _width) || (y < 0) || (y >= _height)) return;

    setAddrWindow(x,y,x+1,y+1);

    RSPORT |= (1 << RS);

    spiwrite(color >> 8);
    spiwrite(color);

}

/*
void drawFastVLine(int16_t x, int16_t y, int16_t h, uint16_t color) {
  // Rudimentary clipping
  if((x >= _width) || (y >= _height)) return;
  if((y+h-1) >= _height) h = _height-y;
  setAddrWindow(x, y, x, y+h-1);

  uint8_t hi = color >> 8, lo = color;
  RSPORT |= (1 << RS);

  while (h--) {
    spiwrite(hi);
    spiwrite(lo);
  }
}


void drawFastHLine(int16_t x, int16_t y, int16_t w,
  uint16_t color) {

  // Rudimentary clipping
  if((x >= _width) || (y >= _height)) return;
  if((x+w-1) >= _width)  w = _width-x;
  setAddrWindow(x, y, x+w-1, y);

  uint8_t hi = color >> 8, lo = color;
  RSPORT |= (1 << RS);
  while (w--) {
    spiwrite(hi);
    spiwrite(lo);
  }
}
*/


// fill a rectangle
void fillRect(int16_t x, int16_t y, int16_t w, int16_t h,uint16_t color) {
  // rudimentary clipping (drawChar w/big text requires this)
  if((x >= _width) || (y >= _height)) return;
  if((x + w - 1) >= _width)  w = _width  - x;
  if((y + h - 1) >= _height) h = _height - y;

  setAddrWindow(x, y, x+w-1, y+h-1);

  uint8_t hi = color >> 8, lo = color;
  RSPORT |= (1 << RS);

  for(y=h; y>0; y--) {
    for(x=w; x>0; x--) {
      spiwrite(hi);
      spiwrite(lo);
    }
  }
}


// Pass 8-bit (each) R,G,B, get back 16-bit packed color
uint16_t Color565(uint8_t r, uint8_t g, uint8_t b) {
  return ((r & 0xF8) << 8) | ((g & 0xFC) << 3) | (b >> 3);
}


void invertDisplay(unsigned char i) {
  writecommand(i ? ST7735_INVON : ST7735_INVOFF);
}


