<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';

exit($test->run(array_merge($base, [

  // Library loading
  'library from directory' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->dir  => true, $this->lib['one']['dir']  => true]],
      \xp\bootstrap([$this->dir, $this->lib['one']['dir']], function() { return []; })
    );
  },

  'library from directory before bootstrap from merge path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->lib['one']['dir'] => true, $this->dir => true]],
      \xp\bootstrap([$this->lib['one']['dir']], function() { return [$this->dir]; })
    );
  },

  'library from xar' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->dir => true, $this->lib['one']['xar'] => true]],
      \xp\bootstrap([$this->dir, $this->lib['one']['xar']], function() { return []; })
    );
  },

  'library from xar before bootstrap from merge path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->lib['one']['xar'] => true, $this->dir => true]],
      \xp\bootstrap([$this->lib['one']['xar']], function() { return [$this->dir]; })
    );
  },

  'library from file' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true, $autoload => false], [$this->dir => true]],
      \xp\bootstrap([$this->dir, $autoload], function() { return []; })
    );
  },

  'library from file before bootstrap' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$autoload => false, $path->compose($this->dir, '__xp.php') => true], [$this->dir => true]],
      \xp\bootstrap([$autoload, $this->dir], function() { return []; })
    );
  },

  'library from file before bootstrap from merge path' => function() use($path) {
    $autoload= $path->compose($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[$autoload => false, $path->compose($this->dir, '__xp.php') => true], [$this->dir => true]],
      \xp\bootstrap([$autoload], function() { return [$this->dir]; })
    );
  },

  'library loading via dir in global include path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->dir => true, $this->lib['one']['dir'] => true]],
      \xp\bootstrap([$this->dir], function() { return [$this->lib['one']['dir']]; })
    );
  },

  'library loading via xar in global include path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php') => true], [$this->dir => true, $this->lib['one']['xar'] => true]],
      \xp\bootstrap([$this->dir], function() { return [$this->lib['one']['xar']]; })
    );
  },
])));