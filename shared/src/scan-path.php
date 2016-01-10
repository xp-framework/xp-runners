<?php namespace xp;

function path($in, $bail= true) {
  $qn= realpath($in);
  if (false === $qn) {
    if ($bail) {
      throw new \Exception('[bootstrap] Classpath element ['.$in.'] not found');
    }
    return null;
  } else {
    return is_dir($qn) ? $qn.DIRECTORY_SEPARATOR : $qn;
  }
}

function pathfiles($path) {
  $result= [];
  if ($pr= @opendir($path)) {
    while ($file= readdir($pr)) {
      if (0 !== substr_compare($file, '.pth', -4)) continue;

      foreach (file($path.DIRECTORY_SEPARATOR.$file) as $line) {
        $line= trim($line);
        if ('' === $line || '#' === $line{0}) {
          continue;
        } else {
          $result[]= $line;
        }
      }
    }
    closedir($pr);
  }
  return $result;
}

function scanpath(&$result, $paths, $base, $home) {
  $type= 'local';

  foreach ($paths as $path) {
    if ('' === $path) continue;

    // Handle ? and ! prefixes
    $bail= true;
    $overlay= null;
    if ('!' === $path{0}) {
      $overlay= 'overlay';
      $path= '!' === $path ? '.' : substr($path, 1);
    } else if ('?' === $path{0}) {
      $bail= false;
      $path= substr($path, 1);
    }

    // Expand file path
    if ('~' === $path{0}) {
      $expanded= $home.DIRECTORY_SEPARATOR.substr($path, 1);
    } else if ('/' === $path{0} || '\\' === $path{0} || strlen($path) > 2 && (':' === $path{1} && '\\' === $path{2})) {
      $expanded= $path;
    } else {
      $expanded= $base.DIRECTORY_SEPARATOR.$path;
    }

    // Resolve, check for XP core
    if ($resolved= path($expanded, $bail)) {
      if (null === $result['base']) {
        if (0 === substr_compare($resolved, '.xar', -4)) {
          if (is_file($f= 'xar://'.$resolved.'?__xp.php')) {
            $result['base']= $f;
            $result['core']= array($resolved);
            continue;
          }
        } else {
          if (is_file($f= $resolved.'__xp.php')) {
            $result['base']= $f;
            $type= 'core';
            // The rest of the path file is regarded as belonging to core
          }
        }
      }

      if (0 === substr_compare($resolved, '.php', -4)) {
        $result['files'][]= $resolved;
      } else {
        $result[$overlay ?: $type][]= $resolved;
      }
    }
  }
}