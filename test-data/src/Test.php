<?php
/**
 * @file
 */
namespace Test\Base;


class Test {
  /**
   * @var bool
   */
  protected $flag;

  /**
   * Constructor.
   */
  public function __construct($options = array()) {
    $this->flag = !empty($options['flag']);
  }

  /**
   * Function to test flag.
   */
  public function isFlag() {
    return $this->flag;
  }
}
