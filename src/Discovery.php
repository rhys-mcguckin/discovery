<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Class Discovery
 * @package Discovery
 */
class Discovery {
  /**
   * @var SourceInterface[]
   */
  protected $sources;

  /**
   * @var string
   */
  protected $default;

  /**
   * @var CacheInterface
   */
  protected $cache;

  /**
   *
   */
  public function __construct() {
    $this->sources = array();
    $this->cache = new Cache();
  }

  /**
   * Adds a source for the discovery process.
   *
   * @param SourceInterface $source
   *
   * @return self
   */
  public function addSource(SourceInterface $source) {
    $this->sources[] = $source;
    $this->cache->reset();
    return $this;
  }

  /**
   * Sets a differing caching mechanism for the metadata information.
   */
  public function setCache(CacheInterface $cache) {
    $this->cache = $cache->reset();
    return $this;
  }

  /**
   * Refreshes the underlying cache.
   */
  protected function refresh() {
    if ($this->cache->exists()) {
      return;
    }

    // Cycle through the differing sources.
    foreach ($this->sources as $source) {

      // Cycle through all the classes deined by the sources.
      foreach ($source->getClasses() as $class) {
        // Get the meta information from the source.
        if (!($metas = $source->getClass($class))) {
          continue;
        }

        // Cycle through all the metas defined for a given class.
        foreach ($metas as $meta) {
          // Check the existance of required fields.
          if (empty($meta['type']) || empty($meta['subtype'])) {
            continue;
          }

          // Ensure a valid meta class.
          if (empty($meta['meta']) || !class_exists($meta['meta']) || !in_array('Discovery\\MetaInterface', class_implements($meta['meta']))) {
            $meta['meta'] = 'Discovery\\Meta';
          }

          // Create object and add it to the cache.
          $object = new $meta['meta']($class, $meta);
          $this->cache->setMeta($object);
        }
      }
    }
  }

  /**
   * Reset the cache to enable a rebuild.
   *
   * @return self
   */
  public function reset() {
    $this->cache->reset();
    return $this;
  }

  /**
   * Get all the variations of keys.
   *
   * @return string[]
   */
  public function getTypes() {
    $this->refresh();
    return $this->cache->getTypes();
  }

  /**
   * Get the list of subtypes for a type.
   *
   * @return string[]
   */
  public function getSubtypes($type) {
    $this->refresh();
    return $this->cache->getSubtypes($type);
  }

  /**
   * Get the meta from the given key.
   *
   * @param string $type
   *
   * @param string $subtype
   *
   * @return MetaInterface|NULL
   */
  public function getMeta($type, $subtype) {
    $this->refresh();
    return $this->cache->getMeta($type, $subtype);
  }

  /**
   * Creates an instance of the class using the meta information defined.
   *
   * @param string $type
   *
   * @param string $subtype
   *
   * @param array $args
   *
   * @return object|NULL
   */
  public function getClass($type, $subtype, $args = array()) {
    if (!($meta = $this->getMeta($type, $subtype))) {
      return NULL;
    }

    // Add any meta defined arguments to the argument list.
    if ($base_args = $meta->getValue('arguments')) {
      $args = array_merge($base_args, $args);
    }

    // Skip using reflection when there are no arguments.
    $class = $meta->getClass();
    if (!$args) {
      return new $class();
    }

    // Use reflection to create an instance of the class.
    $reflect = new \ReflectionClass($class);
    return $reflect->newInstanceArgs($args);
  }
}
