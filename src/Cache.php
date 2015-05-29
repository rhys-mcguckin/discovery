<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Class Cache
 * @package Discovery
 */
class Cache implements CacheInterface {
  /**
   * @var array
   */
  protected $cache;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->cache = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function reset() {
    unset($this->cache);
  }

  /**
   * {@inheritdoc}
   */
  public function exists() {
    return isset($this->cache);
  }

  /**
   * {@inheritdoc}
   */
  public function setMeta(MetaInterface $meta) {
    $this->cache[$meta->getType()][$meta->getSubtype()] = $meta;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypes() {
    return array_keys($this->cache);
  }

  /**
   * {@inheritdoc}
   */
  public function getSubtypes($type) {
    if (array_key_exists($type, $this->cache)) {
      return array_keys($this->cache[$type]);
    }
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getMeta($type, $subtype) {
    if (isset($this->cache[$type][$subtype])) {
      return $this->cache[$type][$subtype];
    }
    return NULL;
  }
}
