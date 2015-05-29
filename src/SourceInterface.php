<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Interface SourceInterface
 * @package Discovery
 */
interface SourceInterface {
  /**
   * Get a list of FQDN classes.
   *
   * @return string[]
   */
  function getClasses();

  /**
   * Get the meta information associated with the specific class.
   *
   * @param string $class
   *
   * @return array
   */
  function getClass($class);
}
