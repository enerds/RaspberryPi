#define __PROG_TYPES_COMPAT__
#include <avr/pgmspace.h>
#include <avr/io.h>
#include "china_lcd.h"

void myDelay(int16_t ms);

PROGMEM static const prog_uchar 
r[] = {255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,205,91,40,12,2,2,3,5,5,7,15,40,91,205,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,207,57,7,6,5,4,1,0,3,5,8,11,13,16,19,18,15,10,57,206,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,172,21,11,13,12,9,7,4,1,0,3,5,8,10,13,16,18,21,24,26,26,20,22,172,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,208,26,15,20,17,15,12,9,8,28,72,114,152,173,173,153,118,76,36,20,23,26,29,32,34,24,27,207,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,90,13,24,23,20,17,15,61,167,225,225,221,219,219,219,218,218,217,218,221,220,163,66,26,32,35,37,37,21,89,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,253,24,23,28,26,23,20,118,219,223,221,222,223,224,223,224,223,223,223,221,220,219,217,216,217,213,117,30,37,40,42,33,23,253,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,236,11,30,31,28,23,87,220,222,222,224,225,227,227,227,228,229,228,228,226,226,225,223,221,219,218,215,214,212,91,36,42,45,42,12,236,255,255,255,255,255,255,255,255,255,255,255,255,255,236,10,34,34,31,27,196,222,223,225,226,228,229,230,232,232,232,233,232,231,231,230,228,227,226,224,221,219,217,215,213,189,38,45,48,48,12,236,255,255,255,255,255,255,255,255,255,255,255,253,11,37,36,34,39,223,222,224,226,228,231,233,234,235,236,236,237,237,237,236,235,234,233,231,230,228,226,223,220,218,215,212,212,48,48,50,50,13,253,255,255,255,255,255,255,255,255,255,255,24,37,39,36,47,227,223,226,228,231,233,235,236,238,240,240,241,241,242,242,240,240,238,237,235,233,231,229,227,224,221,218,215,211,216,53,50,53,49,24,255,255,255,255,255,255,255,255,255,89,33,42,39,43,228,223,226,229,232,234,237,239,240,242,243,244,246,246,246,246,245,244,243,241,240,237,235,233,230,227,225,221,218,215,211,214,51,53,55,43,88,255,255,255,255,255,255,255,208,23,44,42,36,224,223,227,229,233,235,238,240,242,244,246,248,249,250,249,249,249,249,248,247,245,244,241,239,236,233,231,228,225,222,218,215,211,210,46,55,58,29,207,255,255,255,255,255,255,27,44,44,39,197,223,226,229,232,236,239,241,244,246,249,251,252,253,254,254,254,254,254,252,251,249,247,245,243,240,237,234,231,228,224,221,217,213,210,186,51,58,57,28,255,255,255,255,255,172,32,47,44,96,223,225,229,233,236,239,242,245,248,250,253,254,254,255,254,255,255,255,254,254,254,254,255,248,246,243,240,237,234,230,227,223,220,216,212,210,96,58,61,40,172,255,255,255,255,24,49,47,39,221,224,228,231,235,239,242,245,248,251,254,254,254,255,254,255,255,255,255,254,255,255,156,140,255,249,246,243,240,237,233,229,226,222,218,214,210,207,49,60,61,25,255,255,255,206,33,50,47,126,224,226,230,235,238,241,245,248,252,254,254,255,254,255,255,254,255,254,255,233,117,14,3,120,255,253,249,246,243,239,236,232,228,225,221,216,212,209,122,60,61,38,206,255,255,57,47,50,42,221,225,228,233,237,240,244,248,251,254,254,255,255,254,255,254,255,255,207,82,9,25,32,13,143,255,254,253,249,245,242,238,235,230,226,222,219,214,210,206,52,61,55,57,255,255,19,53,50,78,226,226,230,235,239,242,246,250,254,254,255,254,255,254,255,255,193,62,16,35,39,37,35,14,161,255,255,254,252,247,244,240,236,232,228,224,220,215,211,210,80,61,61,21,255,205,35,53,48,171,224,228,232,237,240,244,249,252,254,255,255,255,255,255,185,55,19,42,45,44,42,40,39,12,187,255,255,254,254,250,246,242,238,234,230,226,222,217,213,208,162,58,61,39,205,90,47,53,44,225,225,229,234,238,242,246,250,254,254,254,255,255,185,54,18,44,48,47,47,47,46,44,41,14,202,254,255,255,254,252,247,244,240,236,231,227,223,219,213,216,197,51,61,52,86,40,55,53,57,227,227,230,235,239,244,247,252,254,254,255,191,57,15,42,46,48,51,52,52,51,49,46,45,19,214,254,255,255,255,253,249,245,241,237,232,228,223,229,197,93,88,58,61,61,39,22,56,53,93,225,227,232,235,240,245,248,253,255,206,64,11,35,43,45,44,33,45,56,56,55,52,50,47,28,224,255,255,255,254,254,250,246,242,238,232,241,197,76,23,157,217,92,61,61,22,25,56,53,131,223,227,232,236,241,245,255,230,83,7,27,34,31,16,37,99,168,52,60,60,58,55,52,48,36,235,255,255,255,255,254,252,247,241,251,204,72,0,92,218,214,207,125,61,61,26,29,56,51,162,224,228,233,236,242,248,111,2,14,17,0,44,124,207,254,255,233,48,63,63,61,57,53,49,47,249,255,255,255,255,254,251,255,215,71,0,8,170,225,216,213,208,153,59,62,32,31,56,50,179,223,228,232,248,145,3,0,0,51,143,233,255,255,255,254,255,224,44,64,65,62,57,54,49,56,255,255,255,254,255,255,226,76,0,0,53,223,226,220,217,213,207,168,58,61,33,31,56,50,179,224,236,166,18,0,67,157,247,255,255,254,254,255,254,255,255,213,35,61,61,58,56,52,47,72,255,254,254,255,227,82,0,11,0,126,242,229,225,221,217,212,207,168,58,61,33,29,56,52,162,165,53,81,172,247,254,249,253,255,255,255,255,255,255,255,255,202,32,57,57,56,53,50,41,98,255,255,219,81,0,14,10,18,200,244,233,229,224,220,215,211,207,153,60,61,31,25,56,53,100,180,242,237,234,239,244,248,252,254,255,255,255,255,255,255,255,188,30,54,53,52,50,48,34,126,208,75,0,22,23,0,80,245,241,236,233,228,224,220,216,211,207,124,61,61,26,22,56,53,93,222,226,230,234,238,242,247,251,254,254,255,255,255,255,255,255,172,29,50,50,49,47,44,36,34,10,28,30,17,10,166,255,243,240,236,232,227,223,219,215,210,207,92,61,61,22,40,56,53,57,225,224,229,233,237,241,245,249,253,254,255,255,255,255,255,255,154,29,45,44,45,43,41,39,35,34,30,2,73,237,253,246,242,239,234,230,226,222,218,213,209,209,61,61,61,40,91,47,53,44,223,223,227,231,235,239,243,247,251,254,254,254,255,255,255,255,139,27,42,41,41,39,38,36,34,22,14,169,255,252,249,244,241,237,233,228,225,221,216,212,207,207,53,61,52,90,205,35,53,48,170,221,225,229,233,237,241,245,248,252,254,254,254,255,255,255,126,26,37,37,37,36,34,31,4,86,242,255,253,250,246,242,239,235,230,227,223,219,215,210,206,160,58,61,39,205,255,19,54,51,78,222,223,227,231,235,238,242,246,249,252,254,254,255,255,255,107,24,34,34,33,33,18,26,194,255,254,253,251,247,243,240,236,233,228,225,221,217,212,209,207,80,61,61,21,255,255,57,48,51,42,217,221,225,228,233,236,239,243,246,249,252,254,255,254,255,92,22,29,30,22,3,129,255,254,254,253,251,248,244,241,237,234,230,227,223,219,216,211,207,203,52,61,55,57,255,255,206,33,51,48,124,220,223,226,230,233,237,240,243,246,249,251,253,254,255,79,21,21,4,80,238,255,254,254,252,250,247,244,241,238,235,232,227,224,221,217,213,209,206,121,59,61,38,206,255,255,255,24,50,48,40,217,220,223,227,231,234,236,240,243,245,248,249,251,255,70,1,48,205,255,254,254,252,251,249,246,243,241,238,235,232,228,225,221,218,214,210,207,203,48,59,60,25,255,255,255,255,172,33,48,46,95,219,221,224,227,230,234,237,239,242,244,246,248,255,89,175,255,251,252,250,250,248,246,244,242,240,237,235,232,228,226,222,219,216,212,208,206,95,56,59,40,172,255,255,255,255,255,27,45,46,40,195,218,221,224,227,230,233,235,237,239,242,243,244,253,249,247,247,247,246,245,244,242,241,239,236,234,231,228,226,223,219,216,212,209,206,183,50,56,55,28,255,255,255,255,255,255,207,23,46,43,37,218,218,221,224,226,229,232,234,236,237,239,240,242,242,243,243,242,242,241,239,238,237,235,233,230,228,225,222,219,216,213,209,206,206,45,54,57,29,208,255,255,255,255,255,255,255,89,34,43,41,44,222,217,220,223,226,228,230,232,233,235,236,237,238,238,239,238,238,237,235,234,233,230,229,227,224,221,219,216,213,210,207,210,50,51,54,42,88,255,255,255,255,255,255,255,255,255,23,38,41,38,47,221,217,219,221,224,226,228,230,231,232,233,233,234,234,233,234,233,231,230,228,227,225,223,221,218,215,212,209,207,210,53,48,51,47,23,255,255,255,255,255,255,255,255,255,255,253,11,39,38,35,40,217,216,218,220,222,224,226,227,228,228,229,229,229,229,229,228,227,226,225,223,221,219,216,214,212,209,206,207,46,46,49,49,13,253,255,255,255,255,255,255,255,255,255,255,255,236,10,36,35,33,28,192,215,216,218,219,221,222,223,224,224,225,225,224,224,224,223,222,220,219,217,215,212,211,208,207,183,36,43,46,46,12,236,255,255,255,255,255,255,255,255,255,255,255,255,255,236,11,32,33,30,25,86,213,215,215,217,218,218,220,221,221,221,221,219,220,219,218,216,214,213,211,209,208,206,87,35,40,43,41,13,236,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,253,24,24,30,28,25,22,115,213,216,214,215,216,216,216,216,217,215,215,214,213,212,210,208,210,206,113,29,35,38,40,32,24,253,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,89,15,26,25,22,20,16,61,160,217,217,213,212,212,211,211,211,210,210,214,212,157,64,24,29,32,35,35,20,89,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,208,26,16,22,20,17,14,11,10,28,70,111,147,165,166,148,113,72,34,18,20,24,27,29,32,22,26,208,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,172,21,12,15,15,12,9,7,4,1,0,3,5,8,11,13,16,19,22,24,24,19,22,172,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,207,56,7,7,7,7,4,1,0,3,6,8,11,14,16,16,14,10,56,206,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,205,91,41,12,3,1,2,3,4,5,14,40,91,205,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255};
PROGMEM static const prog_uchar 
g[] = {255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,205,91,40,12,1,2,3,4,4,6,14,40,91,205,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,206,56,7,6,5,4,1,0,3,5,8,11,13,16,18,18,15,10,56,206,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,172,21,11,13,12,9,7,4,1,0,3,5,8,10,13,16,18,21,24,26,26,20,21,172,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,207,25,15,20,17,15,12,9,8,28,72,114,152,173,172,153,117,76,36,20,23,26,29,32,34,24,27,207,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,89,13,24,23,20,17,15,61,167,224,225,221,219,219,219,219,218,217,218,221,220,163,66,26,32,35,37,37,21,88,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,253,23,23,28,26,23,20,118,219,223,221,222,223,223,223,224,223,224,223,221,220,219,217,216,217,213,117,30,37,40,43,33,23,253,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,236,10,30,31,28,23,87,220,222,222,224,225,226,227,227,228,229,228,228,226,226,225,223,221,219,218,215,214,212,91,36,42,45,42,12,236,255,255,255,255,255,255,255,255,255,255,255,255,255,236,9,34,34,31,27,196,222,223,225,226,228,229,231,232,232,232,232,232,232,231,230,229,227,226,224,221,219,217,214,213,189,38,45,48,48,12,236,255,255,255,255,255,255,255,255,255,255,255,253,10,37,36,34,39,223,222,224,227,228,231,233,234,235,236,236,237,237,237,236,235,234,233,231,230,228,226,223,220,218,215,212,212,48,48,50,50,12,253,255,255,255,255,255,255,255,255,255,255,22,37,39,36,47,227,223,226,228,231,233,235,237,238,239,240,241,242,242,242,240,240,238,237,235,233,231,229,227,224,221,218,215,211,215,53,50,53,49,23,255,255,255,255,255,255,255,255,255,88,33,42,39,43,228,223,226,229,232,234,237,239,240,242,243,245,246,246,246,246,245,244,243,241,240,237,235,233,230,228,225,221,218,215,211,214,51,53,55,43,88,255,255,255,255,255,255,255,207,23,44,42,36,224,223,226,229,233,235,238,240,242,244,246,248,249,250,249,249,249,249,248,247,245,243,241,238,236,233,231,228,225,222,218,215,211,210,46,55,58,29,207,255,255,255,255,255,255,27,44,44,39,198,223,226,229,233,236,238,242,244,247,249,251,252,253,254,254,254,254,253,252,251,249,247,245,243,240,237,234,231,228,224,221,217,214,210,186,51,58,57,28,255,255,255,255,255,172,32,47,44,96,223,226,229,233,236,239,242,245,248,250,253,254,254,255,254,255,255,255,255,254,254,254,255,248,246,243,240,237,234,230,227,223,220,216,212,210,96,58,61,40,171,255,255,255,255,23,49,47,39,221,225,228,231,235,239,242,245,248,251,254,254,254,255,254,255,255,255,255,254,255,255,155,140,255,249,246,243,240,237,233,229,226,222,218,214,210,207,49,61,61,24,255,255,255,206,32,50,47,126,224,226,230,235,238,241,245,248,252,254,254,255,255,255,255,254,255,254,255,233,111,22,65,116,255,253,249,246,243,239,236,232,228,225,221,216,212,209,122,60,61,38,206,255,255,56,47,50,42,221,225,228,233,237,240,244,248,251,254,254,255,255,254,255,254,255,255,208,75,39,156,236,142,134,255,254,253,249,245,242,238,235,230,226,223,219,214,210,206,52,61,55,56,255,255,18,53,50,78,225,226,230,235,239,242,246,250,254,254,255,254,255,254,255,255,192,57,67,187,237,226,230,121,155,255,255,254,252,248,244,240,237,232,228,224,220,215,211,210,80,61,61,20,255,204,35,53,48,172,224,228,232,237,240,244,248,252,254,255,255,255,255,255,182,54,83,205,236,227,227,226,232,103,183,255,255,254,254,249,246,242,238,233,230,226,221,217,213,208,162,58,61,39,204,89,47,53,44,225,225,229,234,238,242,246,250,254,254,254,255,255,182,56,89,211,236,227,227,227,227,227,233,83,201,254,255,255,254,252,247,244,240,236,231,227,223,219,213,216,198,51,61,52,85,39,56,53,57,227,226,230,235,239,243,248,252,254,254,255,189,55,86,210,236,232,232,228,229,228,227,227,236,71,214,254,255,255,255,253,249,245,241,237,232,228,223,229,197,93,88,58,61,61,38,21,56,53,93,225,227,232,235,240,244,249,253,255,206,57,76,204,245,229,186,117,148,231,228,228,229,227,232,66,225,255,255,255,254,254,250,246,242,238,232,241,197,76,23,157,216,92,61,61,22,24,56,53,131,223,228,232,236,240,245,255,230,72,59,207,225,159,74,47,94,164,71,228,229,229,228,228,228,63,235,255,255,255,255,254,251,247,241,250,204,72,0,92,218,214,207,125,61,61,26,28,56,51,162,223,228,233,236,242,249,106,43,158,139,42,45,120,206,254,255,234,78,232,229,229,228,229,224,62,249,255,255,255,255,254,251,255,215,67,2,8,170,225,216,212,207,153,59,62,31,30,56,50,179,224,228,232,248,145,3,34,29,45,141,233,255,255,255,254,255,224,83,235,230,229,229,227,219,58,255,255,255,254,255,255,226,68,39,52,52,222,226,220,217,213,208,168,58,61,33,30,56,51,179,224,236,166,18,0,67,156,247,255,255,254,254,255,254,255,255,213,85,236,230,229,228,228,212,62,255,254,254,255,227,76,23,152,42,123,242,229,225,221,217,212,207,168,58,61,33,28,56,52,162,165,53,81,173,247,254,249,253,255,255,255,255,255,255,255,255,200,96,235,229,228,228,229,191,85,255,255,217,78,25,171,164,19,200,244,233,230,225,220,215,211,207,153,60,61,31,24,56,53,99,180,242,237,234,239,244,248,252,254,255,255,255,255,255,255,255,185,110,234,227,228,228,229,160,118,206,71,40,173,235,70,70,246,241,237,233,228,224,220,216,211,207,124,61,61,26,21,56,53,93,222,225,230,234,238,242,247,251,255,254,255,255,255,255,255,255,166,125,232,228,227,227,229,171,32,62,182,245,168,23,163,255,243,240,236,232,227,223,218,214,210,207,92,61,61,22,39,56,53,57,225,224,228,233,237,241,245,249,253,254,255,255,255,255,255,255,147,137,230,227,227,227,226,229,214,237,223,78,65,238,253,246,242,239,234,230,226,222,218,213,209,209,61,61,61,39,89,47,53,44,223,223,227,231,235,239,243,247,251,254,254,254,255,255,255,255,130,152,229,227,227,226,226,225,235,161,27,165,255,252,249,245,240,237,233,229,225,221,216,212,208,207,53,61,52,89,205,35,53,48,170,221,225,229,233,237,241,245,248,252,254,254,254,255,255,255,115,166,227,226,226,226,229,216,60,80,242,255,253,250,246,242,239,235,231,227,223,219,214,210,206,160,58,61,39,205,255,18,54,51,78,222,223,227,231,235,238,242,246,249,252,254,254,255,255,255,96,177,226,226,226,235,124,25,193,255,254,253,251,247,243,240,236,233,228,225,221,217,213,209,207,80,61,61,20,255,255,56,48,51,42,217,221,225,228,233,236,239,243,246,249,252,254,254,255,255,80,187,225,232,174,22,127,255,254,254,253,251,248,244,241,237,234,230,226,223,219,216,211,207,203,52,61,55,56,255,255,206,33,51,48,124,220,222,226,230,233,237,240,243,246,249,251,253,254,255,70,198,199,55,70,238,255,254,254,252,250,247,244,241,238,235,232,227,224,221,217,212,209,206,121,59,61,38,206,255,255,255,23,50,48,40,217,220,224,227,230,234,237,240,243,245,247,249,251,255,66,66,39,205,255,254,254,252,251,249,246,243,241,238,235,231,228,225,221,218,214,210,207,203,48,59,60,24,255,255,255,255,172,33,48,46,95,219,221,224,227,231,233,237,239,242,244,246,247,255,89,174,255,251,252,250,250,249,246,244,242,240,237,235,231,229,226,222,219,216,211,208,206,95,57,59,40,172,255,255,255,255,255,27,45,46,40,195,218,221,224,227,230,233,235,237,239,242,243,244,253,249,247,247,247,246,246,244,242,241,239,236,234,231,228,226,223,219,216,212,209,206,183,50,56,55,27,255,255,255,255,255,255,207,23,46,43,37,218,217,221,224,227,229,232,234,236,237,239,240,242,242,243,243,242,242,240,239,238,237,235,232,230,228,225,222,219,216,213,209,206,206,45,54,56,29,207,255,255,255,255,255,255,255,88,34,43,41,44,222,218,220,223,226,228,230,232,233,235,236,237,238,239,238,238,238,237,236,235,233,230,229,226,224,222,219,216,213,210,207,210,50,51,54,42,88,255,255,255,255,255,255,255,255,255,23,38,41,38,47,221,217,219,221,224,226,228,230,231,232,233,234,234,234,233,233,233,231,230,228,227,225,223,220,218,215,212,210,207,210,53,48,51,48,22,255,255,255,255,255,255,255,255,255,255,253,10,39,38,35,40,217,216,218,220,222,224,226,227,228,228,229,229,229,229,229,228,227,226,224,223,221,219,217,214,212,209,206,207,46,46,48,49,12,253,255,255,255,255,255,255,255,255,255,255,255,236,10,36,35,33,28,192,215,216,218,219,221,222,223,224,224,225,225,225,225,223,223,222,220,219,217,215,213,210,208,207,183,36,43,46,46,11,236,255,255,255,255,255,255,255,255,255,255,255,255,255,236,10,32,33,30,25,86,213,215,215,216,218,219,220,221,221,221,220,220,220,219,218,216,214,213,211,209,208,206,87,35,40,43,41,11,236,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,253,23,24,30,28,25,22,115,212,216,214,214,216,216,216,217,217,215,215,214,213,212,210,209,210,206,113,29,35,38,40,32,22,253,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,89,15,26,25,22,20,16,61,160,217,218,213,212,212,211,211,211,210,210,214,212,157,64,24,30,32,35,35,19,88,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,207,25,16,22,20,17,14,11,10,28,70,111,147,165,166,148,113,72,34,18,21,24,27,29,32,22,25,207,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,172,20,12,15,15,12,9,7,4,1,0,3,5,8,11,13,16,19,22,24,24,18,21,172,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,207,55,7,7,7,7,4,1,0,3,6,8,11,14,16,16,14,9,55,206,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,205,91,40,11,2,0,2,3,3,5,13,39,91,205,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255};
PROGMEM static const prog_uchar 
b[] = {255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,229,166,130,80,43,29,26,28,34,48,81,129,166,228,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,230,143,62,17,6,4,1,0,3,5,8,11,13,16,18,19,26,66,143,230,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,212,101,24,13,12,9,7,4,1,0,3,5,8,10,13,16,18,21,24,26,27,33,103,212,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,231,110,25,20,17,15,12,9,8,28,72,114,152,173,172,153,117,76,36,20,23,26,29,32,34,35,110,231,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,166,41,24,23,20,17,14,61,166,224,225,221,219,219,219,219,218,217,218,221,220,163,66,26,32,35,37,37,48,165,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,254,119,27,28,26,23,20,117,219,223,221,222,223,223,224,224,223,223,223,221,220,219,217,216,217,213,117,30,37,40,42,38,120,254,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,245,92,30,31,28,23,87,220,222,222,224,225,226,227,227,228,229,227,228,227,226,225,223,221,219,217,215,214,212,91,36,42,45,43,94,245,255,255,255,255,255,255,255,255,255,255,255,255,255,245,90,34,34,31,27,196,222,223,225,226,228,230,230,232,232,233,232,232,232,230,230,228,228,226,224,221,219,217,214,213,189,38,45,48,48,91,245,255,255,255,255,255,255,255,255,255,255,255,254,94,37,36,34,39,223,222,224,227,228,231,233,234,235,236,237,237,237,237,236,235,234,233,231,230,228,226,223,220,218,215,212,212,48,47,50,50,95,254,255,255,255,255,255,255,255,255,255,255,119,37,39,36,47,227,223,226,228,231,233,235,237,238,239,240,241,242,242,242,240,240,239,237,235,233,231,229,227,224,221,218,215,211,216,54,50,53,50,119,255,255,255,255,255,255,255,255,255,166,37,42,39,43,228,223,226,229,232,234,237,239,240,242,244,244,246,246,246,246,245,244,243,242,239,237,235,233,230,228,225,222,218,215,211,214,51,53,55,47,165,255,255,255,255,255,255,255,231,51,44,42,36,224,223,227,229,233,235,238,240,242,244,246,248,249,249,249,249,249,249,248,247,245,244,242,239,236,233,231,228,225,221,218,215,211,210,46,55,58,57,230,255,255,255,255,255,255,111,44,44,39,198,223,226,229,233,236,239,241,244,247,249,250,252,253,254,254,254,254,254,252,251,249,247,245,243,240,237,234,231,227,224,221,217,214,210,187,51,58,57,112,255,255,255,255,255,211,42,47,44,96,224,226,229,233,236,239,242,245,248,250,252,254,254,255,254,255,255,255,254,254,254,254,255,248,246,243,240,237,234,230,227,223,220,216,212,210,95,58,61,51,211,255,255,255,255,104,49,47,39,221,224,228,231,235,239,242,245,248,251,254,254,254,255,254,255,255,255,255,254,255,255,155,140,255,249,246,243,240,237,233,229,226,222,218,214,210,207,49,61,61,106,255,255,255,230,47,50,47,126,224,227,230,234,238,241,245,248,251,254,254,255,254,255,255,254,255,254,255,233,111,23,73,115,255,253,249,246,243,239,236,232,228,225,221,216,212,209,122,60,61,52,229,255,255,142,47,50,42,221,225,228,233,237,240,244,248,251,254,254,255,255,254,255,254,255,255,208,74,43,175,255,160,133,255,254,253,249,245,241,238,235,230,226,223,219,214,210,206,52,61,56,142,255,255,73,53,50,78,226,226,230,235,239,242,246,250,254,254,255,254,255,254,255,255,192,57,74,206,255,254,255,136,154,255,255,254,252,247,244,240,237,232,228,224,220,215,212,210,80,61,61,75,255,228,46,53,48,171,224,228,232,237,240,244,249,253,254,255,255,255,255,255,182,54,93,225,255,254,255,254,255,116,183,255,255,254,254,250,246,242,238,233,229,226,222,217,213,208,162,58,61,51,228,165,48,53,44,226,225,229,234,238,242,246,250,254,254,254,255,255,182,56,99,230,255,254,255,254,255,255,255,93,200,254,255,255,254,252,248,243,240,236,231,227,223,219,213,216,198,51,61,53,161,129,56,53,57,227,226,230,235,239,243,248,252,254,254,255,189,55,96,230,255,255,255,254,255,255,255,255,255,78,214,254,255,255,255,253,249,245,241,237,232,228,223,229,197,93,88,58,61,61,128,89,56,53,93,225,227,232,236,240,245,248,253,255,206,56,85,227,255,255,203,129,164,255,255,255,255,255,255,71,225,255,255,255,254,254,250,246,242,238,233,241,197,76,23,157,216,92,61,61,90,66,56,53,131,223,228,232,236,241,245,255,231,70,66,228,249,174,82,48,94,163,73,252,255,255,255,255,254,66,235,255,255,255,255,254,252,247,241,251,204,72,0,92,217,213,207,125,61,61,67,58,56,51,162,224,228,233,236,242,248,105,49,179,155,48,45,120,206,254,255,234,82,255,255,255,255,255,248,64,249,255,255,255,255,254,251,255,215,66,3,8,170,225,216,213,207,153,59,62,59,55,56,50,179,223,229,232,248,145,3,38,34,44,141,233,255,255,255,254,255,224,88,255,255,255,255,254,242,58,255,255,255,254,255,255,226,67,45,58,52,222,226,220,217,213,208,168,58,61,57,55,56,51,179,224,236,166,18,0,67,156,247,255,255,254,254,255,254,255,255,213,93,255,255,255,255,255,234,60,255,254,254,255,227,75,27,172,48,122,242,229,225,221,217,212,207,168,58,61,57,58,56,52,162,165,53,81,172,247,254,249,253,255,255,255,255,255,255,255,255,200,105,255,255,255,255,255,211,83,255,255,217,77,29,192,186,19,200,244,233,230,224,220,215,212,207,153,60,61,59,66,56,53,100,180,242,237,234,239,244,248,252,254,255,255,255,255,255,255,255,184,120,255,254,255,255,255,178,116,206,71,46,190,255,82,69,246,241,236,233,228,224,220,215,211,207,124,61,61,67,89,56,53,94,223,226,230,234,238,242,247,251,254,254,255,255,255,255,255,255,166,138,255,255,255,255,255,190,32,70,202,255,187,25,163,255,243,240,236,232,227,223,218,215,210,207,92,61,61,90,129,56,53,57,225,224,229,233,237,241,244,249,253,254,255,255,255,255,255,255,146,153,255,255,255,255,254,255,237,255,248,89,64,238,253,246,242,238,235,230,226,222,218,213,209,209,61,61,61,129,165,48,53,44,223,223,227,231,235,239,243,247,251,254,254,254,255,255,255,255,129,171,255,255,255,254,255,254,255,178,28,165,255,252,249,244,241,237,233,228,225,221,216,212,208,207,53,61,53,165,228,47,53,48,170,221,225,229,233,237,241,245,248,252,254,254,254,255,255,255,114,187,255,255,255,254,255,239,69,80,242,255,253,250,246,242,239,235,230,227,223,219,215,210,206,160,58,61,51,228,255,74,54,51,78,222,223,227,232,235,238,242,246,249,252,254,254,255,255,255,94,199,255,255,254,255,139,25,193,255,254,253,251,247,243,240,236,233,228,225,221,218,212,209,207,80,61,61,75,255,255,142,48,51,42,217,221,225,229,233,236,239,243,246,249,252,254,255,254,255,78,206,255,255,196,25,127,255,254,254,253,250,248,244,241,237,234,230,227,223,219,215,211,207,203,52,61,56,142,255,255,229,47,51,48,124,220,223,226,230,233,237,240,243,246,249,251,253,254,255,69,219,226,61,69,238,255,254,254,252,250,247,244,241,238,235,232,227,224,221,217,212,209,206,121,59,61,52,229,255,255,255,104,50,48,40,217,220,223,227,230,234,237,239,243,245,248,250,251,255,66,74,37,205,255,254,254,252,251,249,246,243,241,238,235,231,228,225,221,218,214,210,207,203,48,59,60,106,255,255,255,255,211,43,48,46,95,219,221,224,227,231,233,237,239,242,244,246,248,255,89,174,255,251,252,250,250,249,246,244,242,240,237,235,232,229,226,222,219,216,212,208,206,95,56,59,51,211,255,255,255,255,255,111,45,46,40,195,218,221,224,227,230,233,235,237,239,242,244,244,254,248,247,247,247,246,246,244,242,241,239,237,234,231,228,226,223,219,216,212,209,206,183,50,56,56,112,255,255,255,255,255,255,231,51,46,43,37,218,217,221,224,226,229,232,234,235,237,239,240,241,242,243,243,242,242,241,239,238,237,235,232,230,227,225,222,219,216,213,210,206,206,45,54,56,57,231,255,255,255,255,255,255,255,165,38,43,41,44,222,217,220,223,226,228,230,232,233,235,236,237,238,239,239,238,238,236,236,234,233,230,229,226,224,221,219,216,213,210,207,210,50,51,54,46,165,255,255,255,255,255,255,255,255,255,119,38,41,38,47,221,217,219,221,224,226,228,230,231,232,233,234,234,234,234,234,233,231,230,228,227,225,223,221,218,215,212,209,207,210,53,48,51,48,119,255,255,255,255,255,255,255,255,255,255,254,94,39,38,35,40,217,216,218,220,222,224,226,227,228,228,229,229,229,229,229,228,227,226,224,223,221,219,216,214,212,209,207,207,46,46,48,49,95,254,255,255,255,255,255,255,255,255,255,255,255,245,90,36,35,33,28,192,215,216,218,219,221,222,224,224,224,225,225,225,224,224,222,222,220,219,217,215,213,210,208,207,183,36,43,46,46,91,245,255,255,255,255,255,255,255,255,255,255,255,255,255,245,93,32,33,30,25,86,213,215,215,217,218,219,219,221,221,221,221,220,220,219,218,216,214,212,211,209,208,206,87,35,40,43,41,94,245,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,254,119,29,30,27,25,22,115,213,216,214,214,216,216,217,217,217,215,215,214,213,212,210,209,210,206,113,29,35,38,40,36,119,254,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,165,42,26,25,22,20,16,61,160,217,217,213,212,212,211,211,211,210,211,213,212,157,64,24,29,32,35,35,48,165,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,231,109,26,22,20,17,14,11,10,28,70,111,147,165,166,148,113,72,34,18,20,24,27,29,32,33,110,231,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,212,101,26,15,15,12,9,7,4,1,0,3,5,8,11,13,16,19,22,24,24,32,103,212,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,230,142,63,18,8,7,4,1,0,3,6,8,11,14,16,17,24,65,142,230,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,229,166,131,79,43,28,25,26,32,47,81,130,166,228,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255,255};

void drawFlash(void){
    fillScreen(ST7735_BLACK);
    int rx,gx,bx;
    int16_t *p = r;
    int16_t *p1 = g;
    int16_t *p2 = b;

    int line = 0;
    for(int16_t x=0; x < (25*50); x++){
        if(x % 25 == 0) line++;

        rx = pgm_read_byte(p++);
        bx = pgm_read_byte(p1++);
        gx = pgm_read_byte(p2++);

        //drawPixel((x % 50),(x / 50), Color565(bx,gx,rx));
        fillRect(14 + line*2, 30+(x % 25)*4,4,4, Color565(bx,gx,rx));
    }
}

int main(void){
    // init the 1.8 lcd display
    init();
    constructor(_width,_height);

    while(1){
        setRotation(0);
        // FLASH
        drawFlash();
        myDelay(500);
        invertDisplay(1);
        myDelay(500);
        invertDisplay(0);
        myDelay(1000);

        // COLORS AND 'T'
        fillScreen(Color565(255,0,0));
        fillRect(10,10,128-20,10,Color565(0,0,0));
        fillRect(64-5,10,10,140, Color565(0,0,0));
        myDelay(300);

        fillScreen(Color565(0,255,0));
        fillRect(10,10,128-20,10,Color565(0,0,0));
        fillRect(64-5,10,10,140, Color565(0,0,0));
        myDelay(300);

        fillScreen(Color565(0,0,255));
        fillRect(10,10,128-20,10,Color565(0,0,0));
        fillRect(64-5,10,10,140, Color565(0,0,0));
        myDelay(300);

        // TEXT
        fillScreen(ST7735_BLACK);
        setCursor(0,0);
        setTextWrap(1);
        print("Hallo dies ist ein Test von Tobi!");
        myDelay(5000);
        setTextSize(2);
        setRotation(1);
        fillScreen(ST7735_BLACK);
        setCursor(0,0);
        print("Hallo dies ist ein Test von Tobi!");
        myDelay(5000);


    }

    return 0;
}
