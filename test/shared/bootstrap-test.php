<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';

exit($test->run(array_merge($base, [

  // Library loading
  'library from directory' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['dir']]],
      \xp\bootstrap([$this->dir, $this->lib['one']['dir']], function() { return []; })
    );
  },

  'library from directory before bootstrap from merge path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->lib['one']['dir'], $this->dir]],
      \xp\bootstrap([$this->lib['one']['dir']], function() { return [$this->dir]; })
    );
  },

  'library from xar' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['xar']]],
      \xp\bootstrap([$this->dir, $this->lib['one']['xar']], function() { return []; })
    );
  },

  'library from xar before bootstrap from merge path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->lib['one']['xar'], $this->dir]],
      \xp\bootstrap([$this->lib['one']['xar']], function() { return [$this->dir]; })
    );
  },

  'library from file' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php'), $autoload], [$this->dir]],
      \xp\bootstrap([$this->dir, $autoload], function() { return []; })
    );
  },

  'library from file before bootstrap' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php'), $autoload], [$this->dir]],
      \xp\bootstrap([$autoload, $this->dir], function() { return []; })
    );
  },

  'library from file before bootstrap from merge path' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php'), $autoload], [$this->dir]],
      \xp\bootstrap([$autoload], function() { return [$this->dir]; })
    );
  },

  'library loading via dir in global include path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['dir']]],
      \xp\bootstrap([$this->dir], function() { return [$this->lib['one']['dir']]; })
    );
  },

  'library loading via xar in global include path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['xar']]],
      \xp\bootstrap([$this->dir], function() { return [$this->lib['one']['xar']]; })
    );
  },

  // Negative tests
  'empty local and merge paths' => function() use($path) {
    $this->assertException(
      'Exception',
      '/Cannot determine boot class path/',
      function() { \xp\bootstrap([], function() { return []; }); }
    );
  },

  'only library in local path' => function() use($path) {
    $this->assertException(
      'Exception',
      '/Cannot determine boot class path/',
      function() { \xp\bootstrap([$this->lib['one']['dir']], function() { return []; }); }
    );
  },

  'only library in merge path' => function() use($path) {
    $this->assertException(
      'Exception',
      '/Cannot determine boot class path/',
      function() { \xp\bootstrap([], function() { return [$this->lib['one']['dir']]; }); }
    );
  },
])));