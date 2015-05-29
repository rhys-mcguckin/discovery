<?php
/**
 * @file
 */
namespace Test\Base;

class MultiTest {
  /**
   * @var string
   */
  protected $argument;

  /**
   * @var bool
   */
  protected $flag;

  /**
   * Constructor.
   */
  public function __construct($argument, $options = array()) {
    $this->argument = $argument;
    $this->flag = !empty($options['flag']);
  }

  /**
   * Function to test argument passing.
   */
  public function getArgument() {
    return $this->argument;
  }

  /**
   * Function to test flag.
   */
  public function isFlag() {
    return $this->flag;
  }
}