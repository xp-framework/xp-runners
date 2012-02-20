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
	@echo "$(MAKE) bsd          - Creates BSD runners (/bin/sh)"
	@echo "$(MAKE) cygwin       - Creates Cygwin runners (/bin/sh)"
	@echo "$(MAKE) windows      - Creates Windows runners (C#)"
	@echo
	@echo "$(MAKE) test.unix    - Tests Un*x runners"
	@echo "$(MAKE) test.bsd     - Tests BSD runners"
	@echo "$(MAKE) test.cygwin  - Tests Cygwin runners (/bin/sh)"
	@echo "$(MAKE) test.windows - Tests Windows runners"

unix: unix/src/*
	cd unix && $(MAKE) TARGET=default

unix.ar: unix unix/default/*
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/unix/g' > unix/xprt-update.sh
	sh ar.sh unix.ar unix/default/* unix/xprt-update.sh

bsd: unix/src/*
	cd unix && $(MAKE) TARGET=bsd

bsd.ar: bsd unix/bsd/*
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/bsd/g' > unix/xprt-update.sh
	sh ar.sh bsd.ar unix/bsd/* unix/xprt-update.sh

cygwin: unix/src/*
	cd unix && $(MAKE) TARGET=cygwin

cygwin.ar: cygwin unix/cygwin/* 
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/cygwin/g' > unix/xprt-update.sh
	sh ar.sh cygwin.ar unix/cygwin/* unix/xprt-update.sh

windows: windows/src/*
	cd windows && $(MAKE)

windows.ar: windows windows/*.exe windows/src/xprt-update.bat
	sh ar.sh windows.ar windows/*.exe windows/src/xprt-update.bat windows/src/xpwin.bat

test.windows: windows
	cd tests && $(MAKE) testrun on=windows

test.unix: unix
	cd tests && $(MAKE) testrun on=unix/default

test.bsd: bsd
	cd tests && $(MAKE) testrun on=unix/bsd

test.cygwin: cygwin
	cd tests && $(MAKE) testrun on=unix/cygwin

ar: windows.ar unix.ar bsd.ar cygwin.ar
	
release: ar
	scp setup *.ar cgi@xpsrv.net:/home/httpd/xp.php3.de/doc_root/downloads/releases/bin/

clean:
	cd unix && $(MAKE) clean TARGET=default
	cd unix && $(MAKE) clean TARGET=bsd
	cd unix && $(MAKE) clean TARGET=cygwin
	cd windows && $(MAKE) clean

