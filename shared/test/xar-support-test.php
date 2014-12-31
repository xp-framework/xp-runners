<?php namespace xp\test;

$test= require 'test.php';
$scan= require __DIR__.'/../src/xar-support.php';

function path() {
  return strtr(
    implode(DIRECTORY_SEPARATOR, array_map(
      function($in) { return strtr($in, '/', DIRECTORY_SEPARATOR); },
      func_get_args()
    )),
    [DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR => DIRECTORY_SEPARATOR]
  );
}

$test->run([
  '@before' => function() {
    $this->lib= path(__DIR__, '/lib');
  },

  'registers xar wrapper' => function() {
    $this->assertEquals(
      true,
      in_array('xar', stream_get_wrappers())
    );
  },

  'can handle v1 archives' => function() {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire(path($this->lib, 'v1.xar'))['index']
    );
  },

  'can handle v2 archives' => function() {
    $this->assertEquals(
      ['contained.txt' => [38, 0, 0]],
      \xp\xar::acquire(path($this->lib, 'v2.xar'))['index']
    );
  }
]);