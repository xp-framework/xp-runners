<?php namespace xp;

list($bootstrap, $include)= bootstrap(scan(array($cwd), $home), function() use($home) {
  list($use, $inc)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  return array_merge(
    scan(array_unique(explode(PATH_SEPARATOR, substr($use, 2))), $home),
    array_map('xp\path', explode(PATH_SEPARATOR, $inc))
  );
});

foreach ($bootstrap as $file => $xp) {
  if ($xp && class_exists('xp', false)) continue;
  include $file;
}

if (!class_exists('xp', false)) {
  throw new \Exception('[bootstrap] Cannot determine boot class path from '.get_include_path());
}