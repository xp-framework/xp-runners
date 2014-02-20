# XP Runners

The XP runners are bash scripts (on Unix-like systems) or C# programs (Windows) that are used to start XP programs.

## Supported platforms

* Un*x
* BSD
* Cygwin
* Windows (that is non-Cygwin)

## Compilation

Runners need to be compiled for the respective target platform. On Unix, BSD, Cygwin platform the compilation step consists of solely a `m4` preprocessing step. For Windows, C# files have to be compiled. You need the .NET Runtime 4.0 installed on your system.

See `make all` output for the compilation targets.

## Installation

For Unix, BSD, Cygwin install targets exist that do install the final scripts into /usr/bin (or a given different directory).

### Debian

We recommend creating Debian `.deb` files using the `checkinstall` utility:

```sh
% xp-runners$ sudo checkinstall \
  --type=debian \
  --pkgname=xp-runners \
  --pkgversion=1.0.0 \
  --pkglicense=BSD \
  --pkgarch all -y \
  --backup=no \
  --install=no \
  --nodoc \
  make unix.install
```
