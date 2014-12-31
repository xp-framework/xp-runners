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
}

return new Path();
