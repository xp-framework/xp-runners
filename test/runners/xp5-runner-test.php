<?php namespace xp\test;

$ar= require 'ar.php';
$test= require 'test.php';
$path= require 'path.php';
$proc= require 'proc.php';
$base= require 'xp-runner-base.php';

exit($test->run(array_merge($base, [
  '@prepare' => function() use($path) {
    mkdir($path->compose($this->tmp, 'tools'));
    copy($path->compose(__DIR__, 'tools/class.php'), $path->compose($this->tmp, 'tools/class.php'));
    copy($path->compose(__DIR__, 'tools/lang.base.php'), $path->compose($this->tmp, 'tools/lang.base.php'));
    file_put_contents($this->boot, $path->compose(__DIR__, 'xp-rt-5.9.11.xar'));
  },

  '@version' => function() {
    return '5.9.11';
  }
])));