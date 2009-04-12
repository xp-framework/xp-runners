##
# Makefile for XP runners
#
# $Id$

.PHONY: unix windows

unix: unix/src/* unix.ar
	cd unix && $(MAKE)

unix.ar: unix/xp unix/xar unix/xpcli unix/unittest unix/doclet unix/cgen
	sh ar.sh unix.ar unix/xp unix/xar unix/xpcli unix/unittest unix/doclet unix/cgen

windows: windows/src/* windows.ar
	cd windows && $(MAKE)

windows.ar: windows/*.exe
	sh ar.sh windows.ar windows/*.exe

release:
	scp setup *.ar xpdoku@php3.de:/home/httpd/xp.php3.de/doc_root/downloads/releases/bin/

clean:
	cd unix && $(MAKE) clean
	cd windows && $(MAKE) clean

test.windows: windows
	cd tests && $(MAKE) testrun TYPE=windows

test.unix: unix
	cd tests && $(MAKE) testrun TYPE=unix
