<?php namespace xp;

function bootstrap($paths, $merge) {
  $bootstrap= array();
  $include= array();
  $merged= false;
  do {
    foreach ($paths as $path) {
      if (DIRECTORY_SEPARATOR === $path{strlen($path) - 1}) {
        $f= $path.'__xp.php';
      } else if (0 === substr_compare($path, '.xar', -4)) {
        $f= 'xar://'.$path.'?__xp.php';
      } else if (0 === substr_compare($path, '__xp.php', -8)) {
        $f= $path;
        $path= substr($path, 0, -8);
      } else {
        $bootstrap[$path]= false;
        continue;
      }

      $include[$path]= true;
      if (is_file($f)) $bootstrap[$f]= true;
    }

    if ($merged) {
      return array($bootstrap, $include);
    } else {
      $paths= $merge();
      $merged= true;
    }
  } while (true);
}
