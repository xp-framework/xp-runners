##
# Makefile
#
# $Id$

TARGET?=default

all: $(TARGET)/xp $(TARGET)/xar $(TARGET)/xpcli $(TARGET)/unittest $(TARGET)/doclet $(TARGET)/cgen $(TARGET)/xcc $(TARGET)/xpi
	@echo -n "===> " ; cat $(TARGET)/.target

clean: 
	-rm $(TARGET)/* xprt-update.sh

default/%: src/%.in src/common.in src/exec.in src/macros.h
	(echo "#!/bin/sh" ; $(CC) -E -I. -I./src -x c -C -P -imacros src/macros.h $< ) > $@

bsd/%: src/%.in src/common.in src/exec.in src/macros.h
	(echo "#!/bin/sh" ; $(CC) -E -I. -I./src -x c -C -P -D__BSD=1 -imacros src/macros.h $< ) > $@

cygwin/%: src/%.in src/common.in src/exec.in src/macros.h
	(echo "#!/bin/sh" ; $(CC) -E -I. -I./src -x c -C -P -D__CYGWIN=1 -imacros src/macros.h $< ) > $@
