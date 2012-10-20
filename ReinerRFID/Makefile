PCSC_CFLAGS := $(shell pkg-config --cflags libpcsclite)
LDFLAGS := $(shell pkg-config --libs libpcsclite)

CFLAGS += $(PCSC_CFLAGS)

sample: sample.c
