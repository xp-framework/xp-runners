<?php namespace xp;

function bootstrap($cwd, $home) {
  $result= array(
    'base'     => null,
    'overlay'  => array(),
    'core'     => array(),
    'local'    => array(),
    'files'    => array()
  );
  $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());

  // Check local module first
  scanpath($result, pathfiles($cwd), $cwd, $home);

  // We rely classpath always includes "." at the beginning
  if (isset($parts[1])) {
    foreach (explode(PATH_SEPARATOR, substr($parts[1], 2)) as $path) {
      scanpath($result, array($path), $cwd, $home);
    }
  }

  // We rely modules always includes "." at the beginning
  foreach (array_unique(explode(PATH_SEPARATOR, substr($parts[0], 2))) as $path) {
    if ('' === $path) {
      continue;
    } else if ('~' === $path{0}) {
      $path= $home.substr($path, 1);
    }
    scanpath($result, pathfiles($path), $path, $home);
  }

  // Always add current directory
  $result['local'][]= path($cwd);
  return $result;
}