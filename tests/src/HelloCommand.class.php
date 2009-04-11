<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses('util.cmd.Command');

  /**
   * Says hello
   *
   * @purpose  Tests
   */
  class HelloCommand extends Command {
    protected $name= '';
    
    /**
     * Sets name
     *
     * @param   string name
     */
    #[@arg(position= 0)]
    public function setName($name) {
      $this->name= $name;
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine('Hello ', $this->name);
    }
  }
?>
