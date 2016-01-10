<?php namespace xp\test;

$test= require 'test.php';
$path= require 'path.php';
$scan= require __DIR__.'/../../shared/src/scan-path.php';
$scan= require __DIR__.'/../../shared/src/xar-support.php';

exit($test->run([
  '@before' => function() use($path) {
    $this->classpath= $path->compose(__DIR__, '/scan-path-test-tmp/');
    $this->home= $path->compose($this->classpath, 'home/');
    $this->devel= $path->compose($this->home, 'devel/');
    $this->lib= $path->compose(__DIR__, 'lib');
    mkdir($this->classpath);
    mkdir($this->home);
    mkdir($this->devel);
  },

  '@after' => function() use($path) {
    if (is_dir($this->classpath)) {
      $path->remove($this->classpath);
    }
  },

  'does nothing when given an empty array' => function() {
    $result= [];
    \xp\scanpath($result, [], $this->classpath, $this->home);
    $this->assertEquals([], $result);
  },

  // Resolve
  'resolves current directory' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['.'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'local' => [$this->classpath]], $result);
  },

  'resolves parent directory' => function() use($path) {
    $result= ['base' => null];
    \xp\scanpath($result, ['..'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'local' => [$path->compose(__DIR__, '/')]], $result);
  },

  'resolves home directory' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['~'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'local' => [$this->home]], $result);
  },

  'resolves subdirectory of home directory' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['~/devel'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'local' => [$this->devel]], $result);
  },

  'resolves absoute directory' => function() use($path) {
    $result= ['base' => null];
    \xp\scanpath($result, ['/'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'local' => [$path->compose(realpath(DIRECTORY_SEPARATOR), '/')]], $result);
  },

  // Normalization
  'paths get directory separator appended and normalized' => function() use($path) {
    $result= ['base' => null];
    \xp\scanpath($result, ['one', './one', './/one', '../lib/one'], $this->lib, $this->home);
    $normalized= $path->compose($this->lib, 'one/');
    $this->assertEquals(
      ['base' => null, 'local' => [$normalized, $normalized, $normalized, $normalized]],
      $result
    );
  },

  'files get and normalized' => function() use($path) {
    $result= ['base' => null];
    \xp\scanpath($result, ['one.xar', './one.xar', './/one.xar', '../lib/one.xar'], $this->lib, $this->home);
    $normalized= $path->compose($this->lib, 'one.xar');
    $this->assertEquals(
      ['base' => null, 'local' => [$normalized, $normalized, $normalized, $normalized]],
      $result
    );
  },

  // Overlay notation
  'paths with an exclamation mark are added to overlay' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['!.'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'overlay' => [$this->classpath]], $result);
  },

  'home directory works with exclamation marks' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['!~'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'overlay' => [$this->home]], $result);
  },

  'lines with an exclamation mark only prepend current directory' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['!'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null, 'overlay' => [$this->classpath]], $result);
  },

  // Error handling
  'non-existant entries lead to errors' => function() {
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\] not found/',
      function() { $result= ['base' => null]; \xp\scanpath($result, ['does-not-exist'], $this->classpath, $this->home); }
    );
  },

  'non-existant entries with question marks are ok' => function() {
    $result= ['base' => null];
    \xp\scanpath($result, ['?does-not-exist'], $this->classpath, $this->home);
    $this->assertEquals(['base' => null], $result);
  },

  // Issue #21
  'empty use path does not raise warnings' => function() use($path) {
    $result= ['base' => null];
    \xp\scanpath($result, [''], $this->classpath, $this->home);
  }
]));
