<?php namespace xp;

function bootstrap($paths, $merge) {
  $bootstrap= array(null);
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
        $bootstrap[]= $path;
        continue;
      }

      if (is_file($f)) {
        if (null === $bootstrap[0]) {
          $bootstrap[0]= $f;
          $include[]= $path;
        }
      } else {
        $include[]= $path;
      }
    }

    if ($merged) {
      if (null === $bootstrap[0]) {
        throw new \Exception('[bootstrap] Cannot determine boot class path');
      }
      return array($bootstrap, $include);
    } else {
      $paths= $merge();
      $merged= true;
    }
  } while (true);
}
