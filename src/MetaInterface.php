<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Interface MetaInterface
 * @package Discovery
 */
interface MetaInterface {
  /**
   * Construct the meta information for a class.
   *
   * @param string $class
   *   The FQDN for the class using the $meta information.
   *
   *   It is expected that 'type' and 'subtype' keys both exist, with 'class'
   *   being optional to define the meta class used.
   *
   * @param array $meta
   *   The meta information as it is parsed by SourceInterface.
   *
   * @throws \InvalidArgumentException
   */
  public function __construct($class, $meta);

  /**
   * Get the general type of the meta information.
   *
   * @return string
   */
  public function getType();

  /**
   * Get the general type of the meta information.
   *
   * @return string
   */
  public function getSubtype();

  /**
   * Get the class name for this meta information.
   *
   * @return string
   */
  public function getClass();

  /**
   * Get a value from the meta information.
   *
   * @param string $key
   *
   * @param mixed $default
   *
   * @return mixed
   */
  public function getValue($key, $default = NULL);
}
