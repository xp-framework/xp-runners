##
# Makefile
#
# $Id$

all: xp xar xpcli unittest doclet cgen

clean: 
	-rm xp xar xpcli unittest doclet cgen

%: src/%.in src/common.in src/macros.h
	(echo "#!/bin/sh" ; $(CC) -E -I. -I./src -x c -C -P -imacros src/macros.h $< ) > $@
