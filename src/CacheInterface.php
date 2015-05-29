<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Interface CacheInterface
 * @package Discovery
 */
interface CacheInterface {
  /**
   * Reset the cache to enable a rebuild.
   *
   * @return self
   */
  public function reset();

  /**
   * Checks if the cache exists and has data in it.
   *
   * @return bool
   */
  public function exists();

  /**
   * Set for the given class, a meta.
   *
   * @param MetaInterface $meta
   *
   * @return self
   */
  public function setMeta(MetaInterface $meta);

  /**
   * Get all the variations of keys.
   *
   * @return string[]
   */
  public function getTypes();

  /**
   * Get the list of subtypes for a type.
   *
   * @return string[]
   */
  public function getSubtypes($type);

  /**
   * Get the meta from the given key.
   *
   * @param string $type
   *
   * @param string $subtype
   *
   * @return MetaInterface|NULL
   */
  public function getMeta($type, $subtype);
}
