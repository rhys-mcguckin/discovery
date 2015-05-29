<?php
/**
 * @file
 */
namespace Discovery;

/**
 * Class Meta
 * @package Discovery
 */
class Meta implements MetaInterface {
  /**
   * @var string
   */
  protected $class;

  /**
   * @var array
   */
  protected $meta;

  /**
   * {@inheritdoc}
   */
  public function __construct($class, $meta) {
    $this->class = $class;
    $this->meta = $meta;
    if (is_null($this->getType()) || is_null($this->getSubtype())) {
      throw new \InvalidArgumentException('Both "type" and "subtype" are required for the Meta class.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->getValue('type');
  }

  /**
   * {@inheritdoc}
   */
  public function getSubtype() {
    return $this->getValue('subtype');

  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return $this->class;
  }

  /**
   * {@inheritdoc}
   */
  public function getValue($key, $default = NULL) {
    if (array_key_exists($key, $this->meta)) {
      return $this->meta[$key];
    }
    return $default;
  }
}
