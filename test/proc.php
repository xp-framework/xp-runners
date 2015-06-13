<?php namespace xp\io;

class Proc {
  private static $opt= ['bypass_shell' => false];

  public function execute($exe, array $args, array $env= [], $wd= null) {
    $descriptors= [
      0 => ['pipe', 'r'],
      1 => ['pipe', 'w']
    ];

    $p= proc_open($exe.' '.implode(' ', $args).' 2>&1', $descriptors, $pipes, $wd ?: getcwd(), $env, self::$opt);
    fclose($pipes[0]);
    $out= explode("\n", trim(stream_get_contents($pipes[1])));
    fclose($pipes[1]);
    $exit= proc_close($p);
    return [$exit => $out];
  }
}

return new Proc();