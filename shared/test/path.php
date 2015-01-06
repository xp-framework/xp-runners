<?php namespace xp\io;

class Path {

  public function compose() {
    return strtr(
      implode(DIRECTORY_SEPARATOR, array_map(
        function($in) { return strtr($in, '/', DIRECTORY_SEPARATOR); },
        func_get_args()
      )),
      [DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR => DIRECTORY_SEPARATOR]
    );
  }

  public function remove($dir) {
    $d= opendir($dir);
    while ($entry= readdir($d)) {
      if ('.' === $entry || '..' === $entry) continue;

      $f= $this->compose($dir, $entry);
      if (is_file($f)) {
        unlink($f);
      } else {
        $this->remove($f);
      }
    }
    closedir($d);
    rmdir($dir);
  }
}

return new Path();
