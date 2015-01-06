<?php namespace xp\io;

class Proc {
  private static $opt= ['bypass_shell' => false];

  public function execute($exe, array $args, array $env= []) {
    $descriptors= [
      0 => ['pipe', 'r'],
      1 => ['pipe', 'w'],
      2 => STDERR
    ];

    $p= proc_open($exe.' '.implode(' ', $args), $descriptors, $pipes, getcwd(), $env, self::$opt);
    fclose($pipes[0]);
    $out= explode("\n", trim(stream_get_contents($pipes[1])));
    fclose($pipes[1]);
    $exit= proc_close($p);
    return [$exit => $out];
  }
}

return new Proc();