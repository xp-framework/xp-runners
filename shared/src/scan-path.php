<?php namespace xp;

function path($in) {
  $qn= realpath($in);
  if (false === $qn) {
    throw new \Exception('[bootstrap] Classpath element ['.$in.'] not found');
  } else {
    return is_dir($qn) ? $qn.DIRECTORY_SEPARATOR : $qn;
  }
}

function scan($paths, $home= '.') {
  $include= array();
  foreach ($paths as $path) {
    if ('~' === $path{0}) {
      $path= $home.substr($path, 1);
    }

    if (!($d= @opendir($path))) continue;
    while ($e= readdir($d)) {
      if ('.pth' !== substr($e, -4)) continue;

      foreach (file($path.DIRECTORY_SEPARATOR.$e) as $line) {
        $line= trim($line);
        if ('' === $line || '#' === $line{0}) {
          continue;
        } else if ('!' === $line{0}) {
          $pre= true;
          $line= substr($line, 1);
        } else {
          $pre= false;
        }

        if ('~' === $line{0}) {
          $qn= $home.DIRECTORY_SEPARATOR.substr($line, 1);
        } else if ('/' === $line{0} || strlen($line) > 2 && (':' === $line{1} && '\\' === $line{2})) {
          $qn= $line;
        } else {
          $qn= $path.DIRECTORY_SEPARATOR.$line;
        }

        $pre ? array_unshift($include, path($qn)) : $include[]= path($qn);
      }
    }
    closedir($d);
  }
  return $include;
}
