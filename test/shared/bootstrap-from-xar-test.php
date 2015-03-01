<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';

exit($test->run(array_merge($base, [
  'xar' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php' => true], [$this->xar => true]],
      \xp\bootstrap([$this->xar], function() { return []; })
    );
  },

  'xar before current path' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php' => true], [$this->xar => true, $this->cwd => true]],
      \xp\bootstrap([$this->xar, $this->cwd], function() { return []; })
    );
  },

  'xar after current path' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php' => true], [$this->cwd => true, $this->xar => true]],
      \xp\bootstrap([$this->cwd, $this->xar], function() { return []; })
    );
  },

  'xar before dir' => function() use($path) {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php' => true, $path->compose($this->dir, '__xp.php') => true], [$this->xar => true, $this->dir => true]],
      \xp\bootstrap([$this->xar, $this->dir], function() { return []; })
    );
  },
])));