<?php namespace xp\test;

return [
  '@before' => function() use($ar, $path) {
    $this->tmp= $path->compose(__DIR__, '/xp-runner-test-tmp/');
    mkdir($this->tmp);

    if (0 === strncasecmp(PHP_OS, 'Win', 3)) {
      $ar->extract($path->compose(__DIR__, '../../windows.ar'), 'xp.exe', $this->tmp);
      $this->exe= $path->compose($this->tmp, 'xp.exe');
    } else if (preg_match('/^([A-Z]+)BSD/i', PHP_OS)) {
      $ar->extract($path->compose(__DIR__, '../../bsd.ar'), 'xp', $this->tmp);
      $this->exe= $path->compose($this->tmp, 'xp');
      chmod($this->exe, 0700);
    } else {
      $ar->extract($path->compose(__DIR__, '../../unix.ar'), 'xp', $this->tmp);
      $this->exe= $path->compose($this->tmp, 'xp');
      chmod($this->exe, 0700);
    }

    $this->boot= $path->compose($this->tmp, 'boot.pth');
    $this->env= array_merge($_ENV, ['USE_XP' => $this->tmp, 'PATH' => dirname(PHP_BINARY)]);
    $this->prepare();
  },

  '@after' => function() use($path) {
    if (is_dir($this->tmp)) {
      $path->remove($this->tmp);
    }
  },

  '@prepare' => function() {
    throw new Failed('Need to implement @prepare helper in subclasses');
  },

  '@version' => function() {
    throw new Failed('Need to implement @version helper in subclasses');
  },

  'write version' => function() use($path, $proc) {
    $this->assertEquals(
      [0 => [$this->version()]],
      $proc->execute($this->exe, ['-w', '"rtrim(ClassLoader::getDefault()->getResource(\"VERSION\"))"'], $this->env)
    );
  },
];
