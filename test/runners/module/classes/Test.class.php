<?php

use util\cmd\Console;

class Test { 
  public static function main($args) {
    Console::writeLine('Hello ', $args ? $args[0] : 'Module');
  }
}