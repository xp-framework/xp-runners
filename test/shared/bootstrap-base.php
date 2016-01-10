<?php namespace xp\test;

$xars= require __DIR__.'/../../shared/src/xar-support.php';
$boot= require __DIR__.'/../../shared/src/bootstrap.php';

return [
  '@pass' => function($modulepath, $classpath) {
    return strtr('.;'.implode(';', $modulepath).';;.;'.implode(';', $classpath), ';', PATH_SEPARATOR);
  },

  '@before' => function() use($path) {
    $this->cwd= $path->compose(__DIR__, 'bootstrap/');
    $this->module= $path->compose(__DIR__, 'bootstrap/core');
    $this->dir= $path->compose(__DIR__, 'bootstrap/core/src/main/php');
    $this->xar= $path->compose(__DIR__, 'bootstrap/core.xar');
    $this->autoload= $path->compose(__DIR__, 'bootstrap/vendor/autoload.php');
  }
];