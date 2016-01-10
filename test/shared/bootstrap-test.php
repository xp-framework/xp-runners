<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$base= require 'bootstrap-base.php';
$scan= require __DIR__.'/../../shared/src/scan-path.php';

exit($test->run(array_merge($base, [

  // -m /path/to/core
  'core from module' => function() use($path) {
    set_include_path($this->pass([$this->module], []));
    $this->assertEquals(
      [
        'base'    => $path->compose($this->module, 'src/main/php/__xp.php'),
        'overlay' => [],
        'core'    => [$path->compose($this->module, 'src/main/php/')],
        'local'   => [$this->cwd],
        'files'   => []
      ],
      \xp\bootstrap($this->cwd, null)
    );
  },

  // -cp /path/to/core/src/main/php
  'core from directory in class path' => function() use($path) {
    set_include_path($this->pass([], [$this->dir]));
    $this->assertEquals(
      [
        'base'    => $path->compose($this->dir, '__xp.php'),
        'overlay' => [],
        'core'    => [$path->compose($this->dir, '/')],
        'local'   => [$this->cwd],
        'files'   => []
      ],
      \xp\bootstrap($this->cwd, null)
    );
  },

  // -cp /path/to/core.xar
  'core from xar in class path' => function() use($path) {
    set_include_path($this->pass([], [$this->xar]));
    $this->assertEquals(
      [
        'base'    => 'xar://'.$this->xar.'?__xp.php',
        'overlay' => [],
        'core'    => [$this->xar],
        'local'   => [$this->cwd],
        'files'   => []
      ],
      \xp\bootstrap($this->cwd, null)
    );
  },

  // -cp vendor/autoload.php
  'core from vendor/autoload.php in class path' => function() use($path) {
    set_include_path($this->pass([], [$this->autoload]));
    $this->assertEquals(
      [
        'base'    => null,
        'overlay' => [],
        'core'    => [],
        'local'   => [$this->cwd],
        'files'   => [$this->autoload]
      ],
      \xp\bootstrap($this->cwd, null)
    );
  },
])));