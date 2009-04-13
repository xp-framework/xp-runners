##
# Makefile for XP runners
#
# $Id$

.PHONY: unix windows

all:
	@echo "Makefile for XP runners"
	@echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
	@echo "$(MAKE) clean        - Cleanup"
	@echo "$(MAKE) release      - Release runners @ xp-framework.net"
	@echo "$(MAKE) ar           - Create archives for release"
	@echo
	@echo "$(MAKE) unix         - Creates Un*x runners (/bin/sh)"
	@echo "$(MAKE) windows      - Creates Windows runners (C#)"
	@echo
	@echo "$(MAKE) test.unix    - Tests Un*x runners"
	@echo "$(MAKE) test.windows - Test Windows runners"

unix: unix/src/*
	cd unix && $(MAKE)

unix.ar: unix/xp unix/xar unix/xpcli unix/unittest unix/doclet unix/cgen unix/xcc
	sh ar.sh unix.ar unix/xp unix/xar unix/xpcli unix/unittest unix/doclet unix/cgen unix/xcc

windows: windows/src/*
	cd windows && $(MAKE)

windows.ar: windows/*.exe
	sh ar.sh windows.ar windows/*.exe

ar: windows.ar unix.ar
	
release: ar
	scp setup *.ar xpdoku@php3.de:/home/httpd/xp.php3.de/doc_root/downloads/releases/bin/

clean:
	cd unix && $(MAKE) clean
	cd windows && $(MAKE) clean

test.windows: windows
	cd tests && $(MAKE) testrun TYPE=windows

test.unix: unix
	cd tests && $(MAKE) testrun TYPE=unix
