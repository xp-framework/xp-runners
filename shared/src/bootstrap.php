<?php namespace xp;

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
        $bootstrap= array($f);
      }
    }

    if (null === $bootstrap) {
      if ($merged) {
        throw new \Exception('[bootstrap] Cannot determine boot class path');
      } else {
        $paths= array_merge($paths, $merge());
        $merged= true;
      }
    }
  } while (null === $bootstrap);
  return array(array_merge($bootstrap, $include), array_values($paths));
}
