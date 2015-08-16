<?php namespace xp\test;

$ar= require 'ar.php';
$test= require 'test.php';
$path= require 'path.php';
$proc= require 'proc.php';
$base= require 'xp-runner-base.php';

exit($test->run(array_merge($base, [
  '@prepare' => function() use($ar, $path) {
    $ar->extract($path->compose(__DIR__, '../../unix.ar'), 'class-main.php', $this->tmp);
    file_put_contents($this->boot, $path->compose(__DIR__, 'xp-rt-6.3.1.xar'));
  },

  '@version' => function() {
    return '6.3.1-dev';
  },

  'uncaught throwables' => function() use($path, $proc) {
    $result= $proc->execute($this->exe, ['-e', '"throw new \lang\Error(\"Test\")"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught exception/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/  at lang.reflect.Method::invoke/', current($result)));
  },

  'uncaught fatal error' => function() use($path, $proc) {
    if (defined('HHVM_VERSION')) return;

    $result= $proc->execute($this->exe, ['-e', '"[] + 0"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught error: Fatal error/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/Unsupported operand types/', current($result)));
  },

  'uncaught core error' => function() use($path, $proc) {
    if (defined('HHVM_VERSION')) return;

    $result= $proc->execute($this->exe, ['-e', '"class T implements \Traversable { }"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught error: Core error/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/interface Traversable/', current($result)));
  },

  'uncaught PHP7 exceptions' => function() use($path, $proc) {
    if (PHP_VERSION < '7.0.0') return;

    $result= $proc->execute($this->exe, ['-e', '"max([null])->invoke()"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught exception/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/  at lang.reflect.Method.+invoke/', current($result)));
  }
])));