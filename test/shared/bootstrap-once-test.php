<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';

exit($test->run(array_merge($base, [
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
])));