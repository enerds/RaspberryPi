#include <avr/io.h>
#ifndef _GFX_H
#define _GFX_H

void constructor(int16_t w, int16_t h);


void drawCircle(int16_t x0, int16_t y0, int16_t r, uint16_t color);
void drawCircleHelper(int16_t x0, int16_t y0,int16_t r, uint8_t cornername, uint16_t color);
void fillCircle(int16_t x0, int16_t y0, int16_t r, uint16_t color);
void fillCircleHelper(int16_t x0, int16_t y0, int16_t r,uint8_t cornername, int16_t delta, uint16_t color);

/*
void drawTriangle(int16_t x0, int16_t y0, int16_t x1, int16_t y1,int16_t x2, int16_t y2, uint16_t color);
void fillTriangle(int16_t x0, int16_t y0, int16_t x1, int16_t y1,int16_t x2, int16_t y2, uint16_t color);
void drawRoundRect(int16_t x0, int16_t y0, int16_t w, int16_t h,int16_t radius, uint16_t color);
void fillRoundRect(int16_t x0, int16_t y0, int16_t w, int16_t h,int16_t radius, uint16_t color);

void drawBitmap(int16_t x, int16_t y, const uint8_t *bitmap, int16_t w, int16_t h,uint16_t color);
*/

void drawChar(int16_t x, int16_t y, unsigned char c,uint16_t color, uint16_t bg, uint8_t size);

void print(const char str[]);
void write(uint8_t);

void setCursor(int16_t x, int16_t y);
//void setTextColor(uint16_t c);
void setTextColor(uint16_t c, uint16_t bg);
void setTextSize(uint8_t s);
void setTextWrap(uint8_t w);

int16_t height(void);
int16_t width(void);

void setRotation(uint8_t r);
uint8_t getRotation(void);

int16_t  WIDTH, HEIGHT;   // this is the 'raw' display w/h - never changes
int16_t  cursor_x, cursor_y;
uint16_t textcolor, textbgcolor;
uint8_t  textsize;
uint8_t  rotation;
uint8_t  wrap; // If set, 'wrap' text at right edge of display

#endif
