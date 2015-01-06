<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$xars= require __DIR__.'/../src/xar-support.php';

exit($test->run([
  '@before' => function() use($path) {
    $this->lib= $path->compose(__DIR__, '/lib');
  },

  'registers xar wrapper' => function() {
    $this->assertEquals(
      true,
      in_array('xar', stream_get_wrappers())
    );
  },

  // XAR v1
  'can handle v1 archives' => function() use($path) {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire($path->compose($this->lib, 'v1.xar'))['index']
    );
  },

  'can extract v1 archives' => function() use($path) {
    $this->assertEquals(
      "This file is contained in an archive!\n",
      file_get_contents('xar://'.$path->compose($this->lib, 'v1.xar').'?contained.txt')
    );
  },

  // XAR v2
  'can handle v2 archives' => function() use($path) {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire($path->compose($this->lib, 'v2.xar'))['index']
    );
  },

  'can extract v2 archives' => function() use($path) {
    $this->assertEquals(
      "This file is contained in an archive!\n",
      file_get_contents('xar://'.$path->compose($this->lib, 'v2.xar').'?contained.txt')
    );
  },
]));