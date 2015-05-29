<?php
/**
 * @file
 */
namespace Discovery\Sources;

use Discovery\SourceInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Psr
 * @package Discovery\Sources
 */
class Psr implements SourceInterface {
  /**
   * The path in which to locate classes.
   *
   * @var string
   */
  protected $path;

  /**
   * The namespace prefix for classes.
   *
   * @var string
   */
  protected $prefix;

  /**
   * Constructor.
   *
   * @param string $path
   *   The base path used to locate a PSR defined class.
   *
   * @param string $prefix
   *   The namespace prefix used when locating classes.
   */
  public function __construct($path, $prefix = '') {
    $this->path = realpath($path);
    $this->prefix = trim($prefix, '\\');
  }

  /**
   * {@inheritdoc}
   */
  function getClasses() {
    // Ensure the path exists.
    if (!is_dir($this->path)) {
      return array();
    }

    $finder = new Finder();
    $finder->files()->in($this->path)->name('*.yml');

    $prefix = $this->prefix ? $this->prefix . '\\' : '';
    $classes = array();
    foreach ($finder as $file) {
      $path = substr($file->getRelativePathname(), 0, -4);
      if (file_exists($this->path . '/' . $path . '.php')) {
        $class = str_replace(DIRECTORY_SEPARATOR, '\\', $path);
        $classes[] = $prefix . $class;
      }
    }
    sort($classes);
    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  function getClass($class) {
    // Ensure we have a class that was defined by this class.
    $class = ltrim($class, '\\');
    $count = strlen($this->prefix);
    if (substr($class, 0, $count) !== $this->prefix) {
      return FALSE;
    }

    // Convert class to path.
    $class = ltrim(substr($class, $count), '\\');
    $path = $this->path . '/' . implode('/', explode('\\', $class));

    // Check that the class exists, and the meta information exists.
    if (!file_exists($path . '.yml') || !file_exists($path . '.php')) {
      return FALSE;
    }

    // Parse the YAML file.
    try {
      $result = Yaml::parse($path . '.yml');
    }
    catch (\Exception $e) {
      return FALSE;
    }

    // Single result for the YAML file.
    if (empty($result[0])) {
      $result = array($result);
    }

    return $result;
  }
}
