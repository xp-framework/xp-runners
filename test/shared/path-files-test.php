<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$scan= require __DIR__.'/../../shared/src/scan-path.php';

exit($test->run([
  '@before' => function() use($path) {
    $this->classpath= $path->compose(__DIR__, '/scan-path-test-tmp/');
    mkdir($this->classpath);
  },

  '@after' => function() use($path) {
    if (is_dir($this->classpath)) {
      $path->remove($this->classpath);
    }
  },

  // No path files found
  'scan empty directory' => function() {
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  },

  'scan directory without path files' => function() use($path) {
    touch($path->compose($this->classpath, 'README.md'));
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  },

  // Versions with files
  'path file with single line' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '.');
    $this->assertEquals(['.'], \xp\pathfiles($this->classpath));
  },

  'path file with multiple lines' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ".\n..");
    $this->assertEquals(['.', '..'], \xp\pathfiles($this->classpath));
  },

  'multiple path files' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '.');
    file_put_contents($path->compose($this->classpath, 'glue.pth'), '..');
    $returned= \xp\pathfiles($this->classpath);
    sort($returned);
    $this->assertEquals(['.', '..'], $returned);
  },

  // Whitespace is ignored
  'empty path file' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), '');
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  },

  'path file consisting only of space' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ' ');
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  },

  'path file consisting only of an empty line' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "\n");
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  },

  'path file starting with an empty line' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "\n.");
    $this->assertEquals(['.'], \xp\pathfiles($this->classpath));
  },

  'path file ending with an empty line' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ".\n");
    $this->assertEquals(['.'], \xp\pathfiles($this->classpath));
  },

  'an empty line between two non-empty lines' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), ".\n\n..");
    $this->assertEquals(['.', '..'], \xp\pathfiles($this->classpath));
  },

  // Comments
  'comments are ignored' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "#..\n.");
    $this->assertEquals(['.'], \xp\pathfiles($this->classpath));
  },

  'path file consisting only of comments' => function() use($path) {
    file_put_contents($path->compose($this->classpath, 'class.pth'), "#\n#");
    $this->assertEquals([], \xp\pathfiles($this->classpath));
  }
]));
