<?php namespace xp\io;

class Proc {
  public function execute($exe, array $args) {
    exec('"'.$exe.'" '.implode(' ', $args).' 2>&1', $out, $exit);
    return [$exit => $out];
  }
}

return new Proc();