##
# Makefile for XP runners
#
# $Id$

.PHONY: unix windows test
INSTTARGET?=/usr/bin/
PHP?=php

all:
	@echo "Makefile for XP runners"
	@echo "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~"
	@echo "$(MAKE) clean          - Cleanup"
	@echo "$(MAKE) release        - Release runners @ xp-framework.net"
	@echo "$(MAKE) ar             - Create archives for release"
	@echo
	@echo "$(MAKE) unix           - Creates Un*x runners (/bin/sh)"
	@echo "$(MAKE) bsd            - Creates BSD runners (/bin/sh)"
	@echo "$(MAKE) cygwin         - Creates Cygwin runners (/bin/sh)"
	@echo "$(MAKE) windows        - Creates Windows runners (C#)"
	@echo
	@echo "$(MAKE) unix.install   - Installs Un*x runners (to /usr/bin/ or INSTTARGET)"
	@echo "$(MAKE) bsd.install    - Installs BSD runners (to /usr/bin/ or INSTTARGET)"
	@echo "$(MAKE) cygwin.install - Installs Cygwin runners (to /usr/bin/ or INSTTARGET)"
	@echo
	@echo "$(MAKE) test           - Run tests"

shared/%.php: shared/src/%.php shared/src/class-path.php shared/src/scan-path.php shared/src/bootstrap.php shared/src/xar-support.php
	perl -pe 's^require .(.+).;^open F, "shared/src/$$1" or die("$$1: $$!"); <F> for 1..2; join "", <F>;^ge' < $< > $@

unix: unix/src/*
	cd unix && $(MAKE) TARGET=default

unix.ar: unix unix/default/* shared/class-main.php shared/web-main.php
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/unix/g' > unix/xprt-update.sh
	sh ar.sh unix.ar unix/default/* unix/xprt-update.sh shared/class-main.php shared/web-main.php

generic.install:
	@echo "===> Installing XP runners to $(INSTTARGET) ..."
	@cp -v $(from)/* $(INSTTARGET)
	@echo "---> Done."

unix.install: unix
	$(MAKE) generic.install from=unix/default INSTTARGET=$(INSTTARGET)

bsd: unix/src/*
	cd unix && $(MAKE) TARGET=bsd

bsd.ar: bsd unix/bsd/* shared/class-main.php shared/web-main.php
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/bsd/g' > unix/xprt-update.sh
	sh ar.sh bsd.ar unix/bsd/* unix/xprt-update.sh shared/class-main.php shared/web-main.php

bsd.install: bsd
	$(MAKE) generic.install from=unix/bsd INSTTARGET=$(INSTTARGET)

cygwin: unix/src/*
	cd unix && $(MAKE) TARGET=cygwin

cygwin.ar: cygwin unix/cygwin/* shared/class-main.php shared/web-main.php
	cat unix/src/xprt-update.sh.in | sed -e 's/@TYPE@/cygwin/g' > unix/xprt-update.sh
	sh ar.sh cygwin.ar unix/cygwin/* unix/xprt-update.sh shared/class-main.php shared/web-main.php

cygwin.install: cygwin
	$(MAKE) generic.install from=unix/cygwin INSTTARGET=$(INSTTARGET)

windows: windows/src/*
	cd windows && $(MAKE)

windows.ar: windows windows/*.exe windows/src/xprt-update.bat shared/class-main.php shared/web-main.php
	sh ar.sh windows.ar windows/*.exe windows/src/xprt-update.bat windows/src/xpwin.bat shared/class-main.php shared/web-main.php

test: shared
	@(e=0 ; for i in `find test -name '*-test.php'` ; do echo -n "$$i: " ; $(PHP) -d include_path=test $$i ; r=$$? ; if [ $$r -ne 0 ] ; then e=$$r ; fi ; echo ; done ; exit $$e)

ar: windows.ar unix.ar bsd.ar cygwin.ar
	
clean:
	cd unix && $(MAKE) clean TARGET=default
	cd unix && $(MAKE) clean TARGET=bsd
	cd unix && $(MAKE) clean TARGET=cygwin
	cd windows && $(MAKE) clean

