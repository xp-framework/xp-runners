<?php namespace xp;

function bootstrap($cwd, $home) {
  $result= array(
    'base'     => null,
    'overlay'  => array(),
    'core'     => array(),
    'local'    => array(),
    'files'    => array()
  );

  // We rely modules always includes "." at the beginning
  $parts= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  foreach (array_unique(explode(PATH_SEPARATOR, $parts[0])) as $path) {
    if ('' === $path) {
      continue;
    } else if ('~' === $path{0}) {
      $path= $home.substr($path, 1);
    }
    scanpath($result, pathfiles($path), $path, $home);
  }

  // We rely classpath always includes "." at the beginning
  if (isset($parts[1])) {
    foreach (explode(PATH_SEPARATOR, substr($parts[1], 2)) as $path) {
      scanpath($result, array($path), $cwd, $home);  
    }
  }

  $result['local'][]= path($cwd);
  return $result;
}