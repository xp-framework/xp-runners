# XP Runners

[![Build Status on TravisCI](https://secure.travis-ci.org/xp-framework/xp-runners.png)](http://travis-ci.org/xp-framework/xp-runners)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)

The XP runners are bash scripts (on Unix-like systems) or C# programs (Windows) that are used to start XP programs.

## Supported platforms

* Un*x
* BSD
* Cygwin
* Windows (that is non-Cygwin)

## Compilation

Runners need to be compiled for the respective target platform. On Unix, BSD, Cygwin platform the compilation step consists of solely a C preprocessor invocation. For Windows, C# files have to be compiled. You need the .NET Runtime 4.0 installed on your system.

See `make all` output for the compilation targets.

## Tests

Run `make test [PHP=/path/to/php/binary]` to test runners and the shared code.

## Installation

This is the preferrred way of installing the runners:

```sh
$ cd ~/bin
$ wget 'https://github.com/xp-framework/xp-runners/releases/download/v6.3.0/setup' -O - | php
```

### From this directory

For Unix, BSD, Cygwin install targets exist that do install the final scripts into /usr/bin (or a given different directory).

### Debian

We recommend creating Debian `.deb` files using the `checkinstall` utility:

```sh
% xp-runners$ sudo checkinstall \
  --type=debian \
  --pkgname=xp-runners \
  --pkgversion=6.3.0 \
  --pkggroup=devel \
  --pkglicense=BSD \
  --pkgarch all -y \
  --requires=php5-cli,realpath \
  --backup=no \
  --install=no \
  --nodoc \
  --reset-uids=yes \
  make unix.install
```
