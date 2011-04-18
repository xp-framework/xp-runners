<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Say hello
   *
   * @purpose  Tests
   */
  class Hello extends Object {
    
    /**
     * Entry point method
     *
     * @param   string[] args
     */
    public static function main(array $args) {
      Console::writeLine('Hello ', $args[0]);
    }
  }
?>
