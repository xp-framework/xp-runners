<?php namespace xp;

list($bootstrap, $include)= bootstrap(scan(array($cwd), $home), function() use($home) {
  $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  $use= array_unique(explode(PATH_SEPARATOR, substr($parts[0], 2)));
  $paths= scan($use, $home);
  if (isset($parts[1]) && $parts[1] != '') {
    foreach (explode(PATH_SEPARATOR, $parts[1]) as $path) {
      if ('%' === $path{0}) {
        $module= substr($path, 1);
        foreach (array_merge(array($cwd ?: '.'), $use) as $search) {
          $dir= $search.DIRECTORY_SEPARATOR.$module;
          if (is_dir($dir)) {
            $paths= array_merge($paths, scan(array($dir), $home));
            continue 2;
          }
        }
        throw new \Exception('[bootstrap] Module ['.$module.'] not found in '.$parts[0]);
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
