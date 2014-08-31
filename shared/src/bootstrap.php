<?php namespace xp;

$paths= scan(array('.'), $home);
$merged= false;
$bootstrap= array();
do {
  foreach ($paths as $i => $path) {
    if (DIRECTORY_SEPARATOR === $path{strlen($path) - 1}) {
      $f= $path.'__xp.php';
    } else if (0 === substr_compare($path, '.xar', -4)) {
      $f= 'xar://'.$path.'?__xp.php';
    } else {
      $bootstrap[]= $path;
      unset($paths[$i]);
      continue;
    }

    if (is_file($f)) {
      // DEBUG echo '-> '.$f, "\n";
      $bootstrap[]= $f;
      break;
    }
  }

  if ($merged && empty($bootstrap)) {
    trigger_error('[bootstrap] Cannot determine boot class path', E_USER_ERROR);
    exit(0x3d);
  } else if (!$merged) {
    // DEBUG echo "[MERGE $use, $inc]\n";
    list($use, $inc)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
    $paths= array_merge(
      $paths,
      scan(array_unique(explode(PATH_SEPARATOR, substr($use, 2))), $home),
      array_map('xp\path', explode(PATH_SEPARATOR, $inc))
    );
    $merged= true;
  }
} while (empty($bootstrap));
foreach ($bootstrap as $file) {
  include $file;
}
