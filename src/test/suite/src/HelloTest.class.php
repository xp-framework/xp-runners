<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   * @see      xp://Hello
   * @purpose  Test
   */
  class HelloTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function hello() {
      $this->assertEquals('hello', $this->name);
    }
  }
?>
