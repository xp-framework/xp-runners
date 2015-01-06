<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';

exit($test->run(array_merge($base, [
  'file' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir]],
      \xp\bootstrap([$path->compose($this->dir, '__xp.php')], function() { return []; })
    );
  },

  'file before current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      \xp\bootstrap([$path->compose($this->dir, '__xp.php'), $this->cwd], function() { return []; })
    );
  },

  'file after current path' => function() use($path) {
    $this->assertEquals(
      [[$path->compose($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      \xp\bootstrap([$this->cwd, $path->compose($this->dir, '__xp.php')], function() { return []; })
    );
  },
])));