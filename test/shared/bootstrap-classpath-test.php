<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';
$scan= require __DIR__.'/../../shared/src/scan-path.php';

exit($test->run(array_merge($base, [

  // Normal operation
  'directory in class path appears in local' => function() use($path) {
    $dir= $path->compose(__DIR__, 'lib/one/');
    set_include_path($this->pass([$this->module], [$dir]));
    $this->assertEquals([$dir, $this->cwd], \xp\bootstrap($this->cwd, null)['local']);
  },

  'xar in class path appears in local' => function() use($path) {
    $xar= $path->compose(__DIR__, 'lib/one.xar');
    set_include_path($this->pass([$this->module], [$xar]));
    $this->assertEquals([$xar, $this->cwd], \xp\bootstrap($this->cwd, null)['local']);
  },

  // Overlays
  'overlay directory in class path appears in overlays' => function() use($path) {
    $dir= $path->compose(__DIR__, 'lib/one/');
    set_include_path($this->pass([$this->module], ['!'.$dir]));
    $this->assertEquals([$dir], \xp\bootstrap($this->cwd, null)['overlay']);
  },

  'overlay xar in class path appears in overlays' => function() use($path) {
    $xar= $path->compose(__DIR__, 'lib/one.xar');
    set_include_path($this->pass([$this->module], ['!'.$xar]));
    $this->assertEquals([$xar], \xp\bootstrap($this->cwd, null)['overlay']);
  },

  // Error handling
  'nonexistant directory causes error' => function() {
    set_include_path($this->pass([$this->module], ['does-not-exist']));
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\] not found/',
      function() { \xp\bootstrap($this->cwd, null); }
    );
  },

  'nonexistant xar causes error' => function() {
    set_include_path($this->pass([$this->module], ['does-not-exist.xar']));
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\.xar\] not found/',
      function() { \xp\bootstrap($this->cwd, null); }
    );
  },

  'nonexistant file causes error' => function() {
    set_include_path($this->pass([$this->module], ['does-not-exist.php']));
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\.php\] not found/',
      function() { \xp\bootstrap($this->cwd, null); }
    );
  },

  'nonexistant file ignored if prefixed with question mark' => function() {
    set_include_path($this->pass([$this->module], ['?does-not-exist']));
    $this->assertEquals([$this->cwd], \xp\bootstrap($this->cwd, null)['local']);
  },
])));