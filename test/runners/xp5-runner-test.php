<?php namespace xp\test;

$ar= require 'ar.php';
$test= require 'test.php';
$path= require 'path.php';
$proc= require 'proc.php';
$base= require 'xp-runner-base.php';

if (PHP_VERSION >= '7.0.0') {
  echo "XP5.X is not compatible with PHP7, skipping...\n";
  exit(0);
}

exit($test->run(array_merge($base, [
  '@prepare' => function() use($path) {
    mkdir($path->compose($this->tmp, 'tools'));
    copy($path->compose(__DIR__, 'tools/class.php'), $path->compose($this->tmp, 'tools/class.php'));
    copy($path->compose(__DIR__, 'tools/lang.base.php'), $path->compose($this->tmp, 'tools/lang.base.php'));
    file_put_contents($this->boot, $path->compose(__DIR__, 'xp-rt-5.9.11.xar'));
  },

  '@version' => function() {
    return '5.9.11';
  },

  'uncaught throwables' => function() use($path, $proc) {
    $result= $proc->execute($this->exe, ['-e', '"throw new Error(\"Test\")"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught exception/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/  at lang.reflect.Method::invoke/', current($result)));
  }
])));