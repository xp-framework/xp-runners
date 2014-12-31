<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$scan= require __DIR__.'/../src/scan-path.php';

$test->run([
  '@before' => function() use($path) {
    $this->classpath= $path->compose(__DIR__, '/scan-path-test-tmp/');
    mkdir($this->classpath);
    $this->home= $path->compose(__DIR__, '/');
  },

  '@after' => function() use($path) {
    $d= opendir($this->classpath);
    while ($entry= readdir($d)) {
      $f= $path->compose($this->classpath, $entry);
      is_file($f) && unlink($f);
    }
    closedir($d);
    rmdir($this->classpath);
  },

  // File handling
  'scan empty directory' => function() {
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'scan directory without path files' => function() use($path) {
    touch($path->compose($this->classpath, 'README.md'));
    touch($path->compose($this->classpath, 'code.php'));
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Absolute and relative path references
  'current directory evaluates to path files directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '.');
    $this->assertEquals(
      [$this->classpath],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'parent directory evaluates to path files parent directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '..');
    $this->assertEquals(
      [$path->compose(dirname($this->classpath), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'home directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '~');
    $this->assertEquals(
      [$this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'subdirectory of home directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '~/core');
    $this->assertEquals(
      [$path->compose($this->home, 'core/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'absoute directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '/');
    $this->assertEquals(
      [$path->compose(realpath('/'), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Parsing
  'lines starting with a hash are ignored' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '# This is a comment');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'empty file is ok' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'empty lines are ignored' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "\n\n\n");
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with whitespace only are ignored' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '    ');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Order
  'paths are returned in order listed' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ".\n..\n");
    $this->assertEquals(
      [$this->classpath, $path->compose(dirname($this->classpath), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with an exclamation mark are added up front' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ".\n!..\n");
    $this->assertEquals(
      [$path->compose(dirname($this->classpath), '/'), $this->classpath],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'home directory works with exclamation marks' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '!~');
    $this->assertEquals(
      [$this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with an exclamation mark only prepend current directory' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "~\n!");
    $this->assertEquals(
      [$this->classpath, $this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Resolution
  'paths get directory separator appended and normalized' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "~/core\n~/core/\n~/core//\n~//core\n~/core/../core");
    $normalized= $path->compose($this->home, 'core/');
    $this->assertEquals(
      [$normalized, $normalized, $normalized, $normalized, $normalized],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'file names are normalized' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "~/core.xar\n~//core.xar\n~/core/../core.xar");
    $normalized= $path->compose($this->home, 'core.xar');
    $this->assertEquals(
      [$normalized, $normalized, $normalized],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'non-existant entries lead to errors' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), 'does-not-exist');
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\] not found/',
      function() { \xp\scan([$this->classpath], $this->home); }
    );
  },
]);