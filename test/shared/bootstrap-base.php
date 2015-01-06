<?php namespace xp\test;

$xars= require __DIR__.'/../../shared/src/xar-support.php';
$boot= require __DIR__.'/../../shared/src/bootstrap.php';

return [
  '@before' => function() use($path) {
    $this->cwd= $path->compose(__DIR__, '/');
    $this->dir= $path->compose(__DIR__, 'core/src/main/php/');
    $this->xar= $path->compose(__DIR__, 'core.xar');
    $this->lib= [
      'one' => [
        'dir' => $path->compose(__DIR__, 'lib/src/main/php/'),
        'xar' => $path->compose(__DIR__, 'lib/one.xar')
      ]
    ];
  }
];