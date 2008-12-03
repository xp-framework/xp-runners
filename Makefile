##
# Makefile for XP runners
#
# $Id$

.PHONY: unix windows

unix: unix/src/*
	cd unix && $(MAKE)
	sh ar.sh unix.ar unix/xp unix/xar unix/xpcli unix/unittest unix/doclet unix/cgen

windows: windows/src/*
	cd windows && $(MAKE)
	sh ar.sh windows.ar windows/*.exe

release:
	scp *.ar xpdoku@php3.de:/home/httpd/xp.php3.de/doc_root/downloads/releases/bin/

clean:
	cd unix && $(MAKE) clean
	cd windows && $(MAKE) clean
