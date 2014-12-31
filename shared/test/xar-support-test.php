<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$scan= require __DIR__.'/../src/xar-support.php';

$test->run([
  '@before' => function() use($path) {
    $this->lib= $path->compose(__DIR__, '/lib');
  },

  'registers xar wrapper' => function() {
    $this->assertEquals(
      true,
      in_array('xar', stream_get_wrappers())
    );
  },

  'can handle v1 archives' => function() use($path) {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire($path->compose($this->lib, 'v1.xar'))['index']
    );
  },

  'can handle v2 archives' => function() use($path) {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire($path->compose($this->lib, 'v2.xar'))['index']
    );
  }
]);