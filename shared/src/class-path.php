<?php namespace xp;

$bootstrap= bootstrap($cwd, $home);

foreach ($bootstrap['files'] as $file) {
  require $file;
}

if (class_exists('xp', false)) {
  foreach ($bootstrap['overlay'] as $path) { \lang\ClassLoader::registerPath($path, true); }
  foreach ($bootstrap['local'] as $path) { \lang\ClassLoader::registerPath($path); }
} else if (isset($bootstrap['base'])) {
  $paths= array_merge($bootstrap['overlay'], $bootstrap['core'], $bootstrap['local']);
  require $bootstrap['base'];
} else {
  throw new \Exception('[bootstrap] Cannot determine boot class path from '.get_include_path());
}