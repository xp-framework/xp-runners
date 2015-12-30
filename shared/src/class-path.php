<?php namespace xp;

list($bootstrap, $include)= bootstrap(scan(array($cwd), $home), function() use($home) {
  $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  $paths= scan(array_unique(explode(PATH_SEPARATOR, substr($parts[0], 2))), $home);
  if (isset($parts[1]) && $parts[1] != '') {
    foreach (explode(PATH_SEPARATOR, $parts[1]) as $path) {
      if ('%' === $path{0}) {
        $paths= array_merge($paths, scan(array(substr($path, 1)), $home));
      } else {
        $paths[]= path($path);
      }
    }
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
