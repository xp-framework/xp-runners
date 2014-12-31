<?php namespace xp\test;

$test= require 'test.php';
$scan= require __DIR__.'/../src/scan-path.php';

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
    $this->classpath= path(__DIR__, '/scan-path-test-tmp/');
    mkdir($this->classpath);
    $this->home= path(__DIR__, '/');
  },

  '@after' => function() {
    $d= opendir($this->classpath);
    while ($entry= readdir($d)) {
      $f= path($this->classpath, $entry);
      is_file($f) && unlink($f);
    }
    closedir($d);
    rmdir($this->classpath);
  },

  // File handling
  'scan empty directory' => function() {
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'scan directory without path files' => function() {
    touch(path($this->classpath, 'README.md'));
    touch(path($this->classpath, 'code.php'));
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Absolute and relative path references
  'current directory evaluates to path files directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '.');
    $this->assertEquals(
      [$this->classpath],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'parent directory evaluates to path files parent directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '..');
    $this->assertEquals(
      [path(dirname($this->classpath), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'home directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '~');
    $this->assertEquals(
      [$this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'subdirectory of home directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '~/core');
    $this->assertEquals(
      [path($this->home, 'core/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'absoute directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '/');
    $this->assertEquals(
      [path(realpath('/'), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Parsing
  'lines starting with a hash are ignored' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '# This is a comment');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'empty file is ok' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'empty lines are ignored' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), "\n\n\n");
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with whitespace only are ignored' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '    ');
    $this->assertEquals(
      [],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Order
  'paths are returned in order listed' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), ".\n..\n");
    $this->assertEquals(
      [$this->classpath, path(dirname($this->classpath), '/')],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with an exclamation mark are added up front' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), ".\n!..\n");
    $this->assertEquals(
      [path(dirname($this->classpath), '/'), $this->classpath],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'home directory works with exclamation marks' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), '!~');
    $this->assertEquals(
      [$this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'lines with an exclamation mark only prepend current directory' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), "~\n!");
    $this->assertEquals(
      [$this->classpath, $this->home],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  // Resolution
  'paths get directory separator appended and normalized' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), "~/core\n~/core/\n~/core//\n~//core\n~/core/../core");
    $normalized= path($this->home, 'core/');
    $this->assertEquals(
      [$normalized, $normalized, $normalized, $normalized, $normalized],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'file names are normalized' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), "~/core.xar\n~//core.xar\n~/core/../core.xar");
    $normalized= path($this->home, 'core.xar');
    $this->assertEquals(
      [$normalized, $normalized, $normalized],
      \xp\scan([$this->classpath], $this->home)
    );
  },

  'non-existant entries lead to errors' => function() {
    file_put_contents(path($this->classpath, 'class.pth'), 'does-not-exist');
    $this->assertException(
      'Exception',
      '/Classpath element \[.+does-not-exist\] not found/',
      function() { \xp\scan([$this->classpath], $this->home); }
    );
  },
]);