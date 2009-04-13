##
# Makefile
#
# $Id$

all: xp xar xpcli unittest doclet cgen xcc

clean: 
	-rm xp xar xpcli unittest doclet cgen xcc

%: src/%.in src/common.in src/exec.in src/macros.h
	(echo "#!/bin/sh" ; $(CC) -E -I. -I./src -x c -C -P -imacros src/macros.h $< ) > $@
