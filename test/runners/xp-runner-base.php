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
    $this->env= [];
    $this->prepare();

    file_put_contents($path->compose($this->tmp, 'xp.ini'), sprintf(
      "use=%s\n[runtime]\ndefault=%s\nmodules=\nextension=\ndate.timezone=Europe/Berlin",
      $this->tmp,
      PHP_BINARY
    ));
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
      $proc->execute($this->exe, ['-w', '"rtrim(ClassLoader::getDefault()->getResource(\"VERSION\"))"'], $this->env, $this->tmp)
    );
  },

  'cannot run nonexistant test class' => function() use($path, $proc) {
    $result= $proc->execute($this->exe, ['Test'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught exception/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/Class "Test" could not be found/', current($result)));
  },

  'run test class from class path' => function() use($path, $proc) {
    $this->assertEquals(
      [0 => ['Hello Classpath']],
      $proc->execute($this->exe, ['-cp', __DIR__.'/classes', 'Test'], $this->env, $this->tmp)
    );
  },

  'run test class from class path with argument' => function() use($path, $proc) {
    $this->assertEquals(
      [0 => ['Hello Tester']],
      $proc->execute($this->exe, ['-cp', __DIR__.'/classes', 'Test', 'Tester'], $this->env, $this->tmp)
    );
  },

  'uncaught exceptions' => function() use($path, $proc) {
    $result= $proc->execute($this->exe, ['-e', '"throw new Exception(\"Test\")"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Uncaught exception/', current($result)));
    $this->assertEquals(true, (bool)preg_grep('/  at lang.reflect.Method::invoke/', current($result)));
  },

  'cannot bootstrap when boot path does not contain framework core' => function() use($path, $proc) {
    file_put_contents($this->boot, '');
    $result= $proc->execute($this->exe, ['-w', '"Unreachable"'], $this->env, $this->tmp);
    $this->assertEquals(255, key($result));
    $this->assertEquals(true, (bool)preg_grep('/Cannot bootstrap|Cannot determine boot class path/', current($result)));
  }
];
