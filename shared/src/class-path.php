<?php namespace xp;

list($bootstrap, $paths)= classpath(scan(array('.'), $home), function() {
  list($use, $inc)= explode(PATH_SEPARATOR.PATH_SEPARATOR, get_include_path());
  return array_merge(
    scan(array_unique(explode(PATH_SEPARATOR, substr($use, 2))), $home),
    array_map('xp\path', explode(PATH_SEPARATOR, $inc))
  );
});

foreach ($bootstrap as $file) {
  include $file;
}
