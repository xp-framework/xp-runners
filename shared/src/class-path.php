<?php namespace xp;

list($bootstrap, $paths)= bootstrap(scan(array($cwd), $home), function() use($home) {
  $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  $use= $parts[0];
  $inc= isset($parts[1]) ? $parts[1]  : '';
  
  $paths= scan(array_unique(explode(PATH_SEPARATOR, substr($use, 2))), $home);
  if ($inc != '') {
    $paths= array_merge($paths, array_map('xp\path', explode(PATH_SEPARATOR, $inc)));
  }
  return $paths;
});

foreach ($bootstrap as $file => $xp) {
  if ($xp && class_exists('xp', false)) continue;
  include $file;
}

if (!class_exists('xp', false)) {
  throw new \Exception('[bootstrap] Cannot determine boot class path from '.get_include_path());
}
