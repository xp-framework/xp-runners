<?php namespace xp;

$test= require 'test.php';
$xar= require __DIR__.'/../src/xar-support.php';

// PHP 5.3 {
function bootstrap($paths, $merge) {
  $bootstrap= null;
  $merged= false;
  $include= array();
  do {
    foreach ($paths as $i => $path) {
      if (DIRECTORY_SEPARATOR === $path{strlen($path) - 1}) {
        $f= $path.'__xp.php';
      } else if (0 === substr_compare($path, '.xar', -4)) {
        $f= 'xar://'.$path.'?__xp.php';
      } else if (0 === substr_compare($path, '__xp.php', -8)) {
        $f= $path;
        $paths[$i]= substr($path, 0, -8);
      } else {
        $include[]= $path;
        unset($paths[$i]);
        continue;
      }

      if (null === $bootstrap && is_file($f)) {
        $bootstrap= $f;
      }
    }

    if (null === $bootstrap) {
      if ($merged) {
        throw new \Exception('Could not locate __xp.php');
      } else {
        $paths= array_merge($paths, $merge());
        $merged= true;
      }
    }
  } while (null === $bootstrap);
  return array(array_merge(array($bootstrap), $include), array_values($paths));
}
// }

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
    $this->cwd= path(__DIR__, '/');
    $this->dir= path(__DIR__, 'core/src/main/php/');
    $this->xar= path(__DIR__, 'core.xar');
    $this->lib= [
      'one' => [
        'dir' => path(__DIR__, 'lib/src/main/php/'),
        'xar' => path(__DIR__, 'lib/one.xar')
      ]
    ];
  },

  // Bootstrapping from directory
  'bootstrap from dir' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir]],
      bootstrap([$this->dir], null)
    );
  },

  'bootstrap from directory before current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      bootstrap([$this->dir, $this->cwd], null)
    );
  },

  'bootstrap from directory after current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      bootstrap([$this->cwd, $this->dir], null)
    );
  },

  'bootstrap from directory after merging' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir]],
      bootstrap([], function() { return [$this->dir]; })
    );
  },

  'bootstrap from directory after merging after current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      bootstrap([$this->cwd], function() { return [$this->dir]; })
    );
  },

  'bootstrap from directory after merging before current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      bootstrap([], function() { return [$this->dir, $this->cwd]; })
    );
  },

  'bootstrap from directory before xar' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->xar]],
      bootstrap([$this->dir, $this->xar], null)
    );
  },

  'bootstrap from directory before file' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->dir]],
      bootstrap([$this->dir, path($this->dir, '__xp.php')], null)
    );
  },

  // Bootstrapping from xar
  'bootstrap from xar' => function() {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar]],
      bootstrap([$this->xar], null)
    );
  },

  'bootstrap from xar before current path' => function() {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar, $this->cwd]],
      bootstrap([$this->xar, $this->cwd], null)
    );
  },

  'bootstrap from xar after current path' => function() {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->cwd, $this->xar]],
      bootstrap([$this->cwd, $this->xar], null)
    );
  },

  'bootstrap from xar before dir' => function() {
    $this->assertEquals(
      [['xar://'.$this->xar.'?__xp.php'], [$this->xar, $this->dir]],
      bootstrap([$this->xar, $this->dir], null)
    );
  },

  // Bootstrapping from file
  'bootstrap from file' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir]],
      bootstrap([path($this->dir, '__xp.php')], null)
    );
  },

  'bootstrap from file before current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->cwd]],
      bootstrap([path($this->dir, '__xp.php'), $this->cwd], null)
    );
  },

  'bootstrap from file after current path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->cwd, $this->dir]],
      bootstrap([$this->cwd, path($this->dir, '__xp.php')], null)
    );
  },

  // Library loading
  'library from directory' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['dir']]],
      bootstrap([$this->dir, $this->lib['one']['dir']], null)
    );
  },

  'library from directory before bootstrap from merge path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->lib['one']['dir'], $this->dir]],
      bootstrap([$this->lib['one']['dir']], function() { return [$this->dir]; })
    );
  },

  'library from xar' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->dir, $this->lib['one']['xar']]],
      bootstrap([$this->dir, $this->lib['one']['xar']], null)
    );
  },

  'library from xar before bootstrap from merge path' => function() {
    $this->assertEquals(
      [[path($this->dir, '__xp.php')], [$this->lib['one']['xar'], $this->dir]],
      bootstrap([$this->lib['one']['xar']], function() { return [$this->dir]; })
    );
  },

  'library from file' => function() {
    $autoload= path($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[path($this->dir, '__xp.php'), $autoload], [$this->dir]],
      bootstrap([$this->dir, $autoload], null)
    );
  },

  'library from file before bootstrap' => function() {
    $autoload= path($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[path($this->dir, '__xp.php'), $autoload], [$this->dir]],
      bootstrap([$autoload, $this->dir], null)
    );
  },

  'library from file before bootstrap from merge path' => function() {
    $autoload= path($this->lib['one']['dir'], 'autoload.php');
    $this->assertEquals(
      [[path($this->dir, '__xp.php'), $autoload], [$this->dir]],
      bootstrap([$autoload], function() { return [$this->dir]; })
    );
  },

  // Negative tests
  'empty local and merge paths' => function() {
    $this->assertException(
      'Exception',
      '/Could not locate __xp.php/',
      function() { bootstrap([], function() { return []; }); }
    );
  },

  'only library in local path' => function() {
    $this->assertException(
      'Exception',
      '/Could not locate __xp.php/',
      function() { bootstrap([$this->lib['one']['dir']], function() { return []; }); }
    );
  },

  'only library in merge path' => function() {
    $this->assertException(
      'Exception',
      '/Could not locate __xp.php/',
      function() { bootstrap([], function() { return [$this->lib['one']['dir']]; }); }
    );
  },
]);