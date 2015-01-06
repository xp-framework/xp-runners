<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$xars= require __DIR__.'/../src/xar-support.php';
$boot= require __DIR__.'/../src/bootstrap.php';

$test->run([
  '@before' => function() use($path) {
    $this->cwd= $path->compose(__DIR__, '/');
    $this->dir= $path->compose(__DIR__, 'core/src/main/php/');
    $this->xar= $path->compose(__DIR__, 'core.xar');
    $this->lib= [
      'one' => [
        'dir' => $path->compose(__DIR__, 'lib/src/main/php/'),
        'xar' => $path->compose(__DIR__, 'lib/one.xar')
      ]
    ];
  },

  // Bootstrapping from directory
  'bootstrap from dir' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir], function() { return []; })
    );
  },

  'bootstrap from directory before current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      \xp\bootstrap([$this->dir, $this->cwd], function() { return []; })
    );
  },

  'bootstrap from directory after current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      \xp\bootstrap([$this->cwd, $this->dir], function() { return []; })
    );
  },

  'bootstrap from directory after merging' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([], function() { return [$this->dir]; })
    );
  },

  'bootstrap from directory after merging after current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      \xp\bootstrap([$this->cwd], function() { return [$this->dir]; })
    );
  },

  'bootstrap from directory after merging before current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      \xp\bootstrap([], function() { return [$this->dir, $this->cwd]; })
    );
  },

  // Bootstrapping from xar
  'bootstrap from xar' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar]],
      \xp\bootstrap([$this->xar], function() { return []; })
    );
  },

  'bootstrap from xar before current path' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar, $this->cwd]],
      \xp\bootstrap([$this->xar, $this->cwd], function() { return []; })
    );
  },

  'bootstrap from xar after current path' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->cwd, $this->xar]],
      \xp\bootstrap([$this->cwd, $this->xar], function() { return []; })
    );
  },

  'bootstrap from xar before dir' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar]],
      \xp\bootstrap([$this->xar, $this->dir], function() { return []; })
    );
  },

  // Bootstrapping from file
  'bootstrap from file' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$path->compose($this->dir, '__xp.php')], function() { return []; })
    );
  },

  'bootstrap from file before current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      \xp\bootstrap([$path->compose($this->dir, '__xp.php'), $this->cwd], function() { return []; })
    );
  },

  'bootstrap from file after current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      \xp\bootstrap([$this->cwd, $path->compose($this->dir, '__xp.php')], function() { return []; })
    );
  },

  // Assert bootstrapping is not done more than once
  'both bootstrap directory and bootstrap xar in local path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir, $this->xar], function() { return []; })
    );
  },

  'both bootstrap directory and bootstrap file in local path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir, $path->compose($this->dir, '__xp.php')], function() { return []; })
    );
  },

  'both bootstrap directory and bootstrap xar in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([], function() { return [$this->dir, $this->xar]; })
    );
  },

  'both bootstrap directory and bootstrap file in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([], function() use($path) { return [$this->dir, $path->compose($this->dir, '__xp.php')]; })
    );
  },

  'bootstrap directory in local and in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir], function() { return [$this->dir]; })
    );
  },

  'bootstrap directory in local and xar in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir], function() { return [$this->xar]; })
    );
  },

  'bootstrap directory in local and file in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir], function() use($path) { return [$path->compose($this->dir, '__xp.php')]; })
    );
  },

  'bootstrap directory twice in local path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$this->dir, $this->dir], function() { return []; })
    );
  },

  'bootstrap directory twice in global path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([], function() { return [$this->dir, $this->dir]; })
    );
  },

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
]);