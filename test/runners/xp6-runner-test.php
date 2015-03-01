<?php namespace xp\test;

$ar= require 'ar.php';
$test= require 'test.php';
$path= require 'path.php';
$proc= require 'proc.php';
$base= require 'xp-runner-base.php';

exit($test->run(array_merge($base, [
  '@prepare' => function() use($ar, $path) {
    $ar->extract($path->compose(__DIR__, '../../unix.ar'), 'class-main.php', $this->tmp);
    file_put_contents($this->boot, $path->compose(__DIR__, 'xp-rt-6.0.0.xar'));
  },

  '@version' => function() {
    return '6.0.0';
  }
])));