XP Runners ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 5.2.2 / 2015-03-01

* Fixed bootstrapping via Composer if a global XP Framework is installed.
  In this case, `__xp.php` is loaded via `vendor/autoload.php`. The old
  code simply checked for the file itself, which is not enough: Instead,
  the current context needs to be tested for the existance of a class
  called *xp*. This indicates the XP Framework has bootstrapped correctly.
  (@thekid)

## 5.2.1 / 2015-01-24

* Added HHVM nightly builds to the Travis-CI suite - (@thekid)
* Fixed issue #21: Warnings during startup with empty `use` directive
  (@thekid)
* Merged PR #20: Translate '~' into $home
  (@kiesel)
* Try fixing exes compiled on a box with .NET v4.5 not working on boxes
  with v4.0 installed.
  (@thekid)

## 5.2.0 / 2015-01-06

* Added [HHVM](http://hhvm.com/) support by using long command line options
  See https://github.com/facebook/hhvm/issues/1200
  (@thekid)
* Added test suite for ruinners, verifying XP5 and XP6 - (@thekid)
* Ensured bootstrapping is only performed once - (@thekid)
* Added support for class names and empty args in xpws' configuration:
  . `xpws` will check "./etc/web.ini" and pass "./etc" or "-" if not found.
  . `xpws -c -` will always be passed as "-", indicating *no configuration*
  . `xpws -c $arg` will check the $arg directory for a web.ini, and pass
    $arg if found, or ":$arg", indicating a class name, otherwise.
  See https://github.com/xp-framework/scriptlet/pull/4
  (@thekid)

## 5.1.0 / 2014-12-31

* Added support to bootstrap by including the path to __xp.php in a path
  file. This will allow greater composer interoperability.
  (@thekid)
* Changed startup errors to exceptions. Eases testability.
  (@thekid)
* Added test suite for shared code, verifying bootstrapping process, XAR
  file support and path file processing.
  (@thekid)

## 5.0.1 / 2014-09-09

* Fix runners requiring .NET 4.5 (again)
  (@thekid)
* Fix Un*x runners to look for  `[class|web]-main.php` alongside runner,
  not in current directory
  (@thekid)

## 5.0.0 / 2014-09-01

* Initial release: XP runners now bundle `[class|web]-main.php` and decouple
  it from the framework. See xp-framework/rfc#287
  (@thekid)

## 4.6.0 / 2014-04-13

* Implemented command line argument "-m" to xpws. Given "develop" (or 
  being omitted), will start development webserver. Any other value will 
  start xp.scriptlet.Server with the given mode. See PR #18
  (@thekid)
* Allowed starting xpws without web config - See PR #16
  (@thekid)

## 4.5.1 / 2014-02-20

* Documented how to create a debian package with checkinstall. See PR #15
  (@haimich)

## 4.5.0 / 2013-11-23

* Implemented Unicode I/O on Windows - See PR #12
  (@thekid)
* Fixed issue #14 - "Runners don't work without .NET 4.5"
  See http://marcgravell.blogspot.de/2012/09/iterator-blocks-missing-methods-and-net.html
  (@thekid)

## 4.4.0 / 2013-11-22

* Upgraded dependency to C# 4.0 - (@thekid)
* Changed argument handling to pass command line args to XP6 as UTF-7
  (@thekid)
* Made outputting UTF-8 to the Windows cmd.exe work w/o garbled output
  (@thekid)

## 4.3.0 / 2013-08-08

* Change code to invoke command using shell option `exec` which then 
  replaces the current process with the newly forked subprocess. This
  shortens the `ps` output footprint, as the invoker script, eg.
  `xpcli` will no longer appear there. Also this makes process control
  work where other processes start a runner, remember its PID and later
  send a KILL signal to that PID; before this change, it just killed
  the runner process, leaving the underlying `php` process alive.
  (@kiesel)

## 4.2.1 / 2013-03-28

* Added support fr "-w" [web root], "-c" [config dir] and "-cp" [class 
  path] args in xpws
  (@thekid)

## 4.2.0 / 2013-02-21

* Implemented "-i" command line options which lets user inspect web
  Depends on xp.scriptlet.Inspect in XP Framework (see PR #9)
  (@thekid)

## 4.1.5 / 2012-12-27

* Fixed issue #8: "Syntax error with double quotes" - (@thekid)

## 4.1.4 / 2012-11-29

* Fixed xp-forge/xp-maven-plugin#2 / issue #7: "System.NullReferenceException
  when no extension entry"
  (@cconstandachi, @thekid)

## 4.1.3 / 2012-09-28

* Added usage for XP Webserver - invoked by `xpws -?` - (@thekid)
* Added check for etc/web.ini and refuse to start - (@thekid)
* Allowed supplying a document root via "-r" - (@thekid)

## 4.1.2 / 2012-09-22

* Windows installer: Made more robust against self-overwriting which 
  *sometimes* causes problems (grrrr!)
  (@thekid)

## 4.1.1 / 2012-09-04

* Fixed bad substitution: The "//" command `${string//substring/replacement}`
  doesn't work on Ubuntu, which e.g. is the basis for Travis-CI
  (@thekid)

## 4.1.0 / 2012-09-02

* Implemented xp-framework/rfc#254: Builtin development webserver (xpws)
  (@thekid)

## 4.0.0 / 2012-08-23

* Added ability to define multiple PHP runtime configurations inside the
  ini file.
  ```ini
  rt=5.4

  [runtime@5.3]
  default=/usr/local/php53/php
  extension_dir=/usr/local/php53/ext

  [runtime@5.4]
  default=/usr/local/php54/php
  extension_dir=/usr/local/php54/ext

  [runtime]
  date.timezone=Europe/Berlin
  extension=php_gd.so
  ```
  (@thekid)

## 3.2.1 / 2012-07-18

* Merged PR #4: Also read configuration lines with out a newline at EOL
  (@ohinckel)

## 3.2.0 / 2012-02-20

* Added `xpwin.bat`. Implements issue #1 - "Windows non-shell integration"
  (@thekid)

## 3.1.0 / 2012-02-12

* Implemented xp-framework/rfc#237: The "-w" and "-d" command line options
  (@thekid)

## 3.0.0 / 2010-08-17

* Changed to use xp.runtime.Xar instead of xar.php. The latter will be 
  deprecated as it is basically a duplicate of class.php except for the 
  entry point invocation
  (@thekid)

## 2.6.2 / 2010-06-25

* Added support comments (lines beginning with ";") - (@thekid)

## 2.6.1 / 2010-03-21

* If inside a "real" Windows console window, set `LC_CONSOLE` to contain 
  input and output encoding
  (@thekid)

## 2.6.0 / 2010-03-14

* Embedded version & product information in .exe files
  (@thekid)
* Implemented xp-framework/rfc#178: XP.ini locations
  (@thekid)
* Added support shell links in Windows
  If xp.ini contains, for example, "use_xp=C:/xp/5.7-latest", and
  5.7-latest is a shell link created by "Create shortcut" in Windows
  explorer, it's actually a file called "5.7-latest.lnk". Resolve
  target path and use that. This is the closest to symlinks we can
  get, and although Windows supports symlinks (via "junctions" in
  NTFS / the "mklink" utility in Vista and 7), they're unuseable
  for us as creating them requires administrative permissions.
  First approach using the WshShell COM object, this requires a DLL
  called IWshRuntimeLibrary, created by TlbImp.exe (from the Windows
  SDK), called as follows:
  `TlbImp c:\windows\system32\wshom.ocx /out:windows\IWshRuntimeLibrary.dll`
  See http://stackoverflow.com/questions/139010/how-to-resolve-a-lnk-in-c
  (@thekid)

## 2.5.0 / 2010-03-12

* Added update.sh to (unix|bsd|cygwin) runner distribution - (@thekid)
* Included update batch script for Windows - (@thekid)
* Added command line arg "-r" to reflect classes - (@thekid)

## 2.4.1 / 2010-01-16

* Remove xpmon.exe workaround Cygwin problems
  Uses too much memory (~8 - 14 Megabytes) and causes other funny
  problems. Blog article has been adjusted accordingly
  (@thekid)

## 2.4.0 / 2010-01-15

* Fixed php.exe not being terminated
  See http://news.xp-framework.net/article/338/2010/01/15
  (@thekid)
* Catch Ctrl+C (only works in "real" consoles, not in a cygwin
  shell, for example) and kill the spawned process, see also:
  http://www.cygwin.com/ml/cygwin/2006-12/msg00151.html
  http://www.mail-archive.com/cygwin@cygwin.com/msg74638.html
  If we are not inside a real shell, accessing WindowHeight
  will raise an exception. In this case, setup a monitoring
  process that detects when this process is killed and will
  then ensure the child process is torn down.
  (@thekid)

## 2.3.0 / 2010-01-05

* Removed webstart.exe from distribution - (@thekid)
* Specialized Un*x runners:
  - BSD flavor, which checks for /proc and works around Process class
    problems when kern.ps_arg_cache_limit is too short (default: 256)
  - Cygwin flavor, which uses cygpath for converting paths
  - Default flavor, which will no longer work on CygWin
  (@thekid)

## 2.2.5 / 2009-12-24

* Implemented `proxy` option in setup script - (@thekid)

## 2.2.4 / 2009-10-03

* Fixed double quotes handling inside arguments correctly - (@thekid)

## 2.2.3 / 2009-06-16

* Added `xpi` command - (@thekid)
* Fixed `run.sh: line 7: cd: /home/Timm: No such file or directory` by
  handling quotes and whitespace correctly
  (@thekid) 

## 2.2.2 / 2009-04-28

* Added "-C" option ("Do not chdir to the script`s directory"); step 2
  for making xp runners work with CGI(-FCGI) SAPI 
  (@thekid)

## 2.2.1 / 2009-04-25

* Added usage to `xp` command - (@thekid)
* Prepended "-q" to arguments passed to PHP runtime; step 1 for making 
  xp runners work with CGI PHP
  (@thekid)

## 2.2.0 / 2009-04-14

* Remove d(undocumented) feature to place a php.ini file inside USE_XP
  and instead make all keys except "default" inside the [runtime] section
  to php.ini-keys. E.g. the following now works:
  ```ini
  use=~/devel/xp/trunk

  [runtime]
  default=/usr/local/bin/php5
  date.timezone=Europe/Berlin
  default_charset=UTF-8
  extension=php_mysql.so
  extension=php_ldap.so
  ```
  (@thekid)

## 2.1.1 / 2009-04-13

* Fixed section reading for certain shells - (@thekid)
* Added test suite - (@thekid)

## 2.1.0 / 2009-03-25

* Changed `xcc` to use xp.compiler.Runner instead of xp.compiler.Main
  for reasons of consistency with all other runners
  (@thekid)

## 2.0.1 / 2009-01-23

* Fixed `Fatal error: [bootstrap] Classpath element [] not found`
  Only occurred on a Debian Linux system but can be fixed generically
  (@thekid)
* Issued warning in setup if we cannot find xp.ini and create one
  (@thekid)

## 2.0.0 / 2009-01-09

* Included setup in release. Now the following one-liner is possible:
  `$ wget http://xp-framework.net/downloads/releases/bin/setup -O - |php`
  (@thekid)

## 1.2.2 / 2008-12-03

* Windows: Made startup exceptions more verbose - (@thekid)
* Fixed compatibility with .NET Framework v3.0 - (@thekid)

## 1.2.1 / 2008-12-01

* Removed debug - (@thekid, Ruben Wagner)

## 1.2.0 / 2008-11-24

* Changed passing includes inside include_path to use two path separators.
  Fixes problems with PHP ini file parsing routines and quoting - (@thekid)
* Fixed `bin/xp: 85: Syntax error: Bad substitution` which ccured on older
  Cygwin version and on FreeBSD - (@thekid)

## 1.1.0 / 2008-11-21

* Implemented xp-framework/rfc#173 - xp-tools.xar (@thekid)

## 1.0.0 / 2008-11-05

* Created initial release - (@thekid)
* Added support for `[runtime]` section with a key pointing to the PHP 
  runtime's filename in xp.ini - (@thekid)
* Implemented xp-framework/rfc#166 - XP Runners - (@thekid)
