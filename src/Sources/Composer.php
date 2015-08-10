<?php
/**
 * @file
 */
namespace Discovery\Sources;

use Discovery\SourceInterface;

/**
 * Class Composer
 * @package Discovery\Sources
 */
class Composer implements SourceInterface {
  /**
   * @var string
   */
  protected $path;

  /**
   * @var string
   */
  protected $vendor_path;

  /**
   * @var Psr[]
   */
  protected $psr;

  /**
   * @var Composer[]
   */
  protected $composer;

  /**
   * @var string[]
   */
  protected $handled;

  /**
   * Constructor.
   *
   * @param string $path
   *   The base path in which the composer.json is located.
   *
   * @param string $vendor_path
   *   The root vendor path.
   *
   * @param string[] $handled
   *   A list of the already included composer projects.
   */
  public function __construct($path, $vendor_path = '', $handled = array()) {
    $this->path = realpath($path);
    $this->vendor_path = realpath($vendor_path ? $vendor_path : $path . '/vendor');
    $this->handled = $handled;
  }

  /**
   * Ensure we have parsed and retrieved the appropriate list.
   */
  protected function getComposerInfo() {
    if (isset($this->psr)) {
      return;
    }

    $this->psr = array();
    $this->composer = array();

    // Attempt to parse the composer.json file.
    if (is_null($composer = @json_decode(@file_get_contents($this->path . '/composer.json')))) {
      return;
    }

    // Handle PSR-0 autoload behaviour.
    $psrs = array(
      'psr-0' => FALSE,
      'psr-4' => TRUE,
    );
    foreach ($psrs as $psr => $use_namespace) {
      if (empty($composer->autoload->{$psr})) {
        continue;
      }

      foreach ((array)$composer->autoload->{$psr} as $namespace => $path) {
        $path = is_array($path) ? $path : array($path);
        foreach ($path as $dir) {
          if (is_dir($this->path . '/' . $dir)) {
            $this->psr[] = new Psr($this->path . '/' . $dir, $use_namespace ? $namespace : '');
          }
        }
      }
    }


    // Create the composers list
    $requires = array('require', 'require-dev');
    foreach ($requires as $require) {
      if (empty($composer->$require)) {
        continue;
      }

      // Create a composer source for all requires.
      foreach ((array)$composer->$require as $path => $branch) {
        $json = $this->vendor_path . '/' . $path . '/composer.json';
        if (file_exists($json) && !in_array($json, $this->handled)) {
          $this->handled[] = $json;
          $this->composer[] = new Composer($this->vendor_path . '/' . $path, $this->vendor_path, $this->handled);
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  function getClasses() {
    $this->getComposerInfo();

    // Generate classes from the PSR and Composer sources.
    $classes = array();
    foreach ($this->psr as $psr) {
      $classes = array_merge($classes, $psr->getClasses());
    }
    foreach ($this->composer as $composer) {
      $classes = array_merge($classes, $composer->getClasses());
    }

    // Ensure classes are unique and ordered.
    $classes = array_unique($classes);
    sort($classes);
    return $classes;
  }

  /**
   * {@inheritdoc}
   */
  function getClass($class) {
    $this->getComposerInfo();

    // Get class from PSR sources.
    foreach ($this->psr as $psr) {
      if (($value = $psr->getClass($class)) !== FALSE) {
        return $value;
      }
    }

    // Get class from Composer dependencies.
    foreach ($this->composer as $composer) {
      if (($value = $composer->getClass($class)) !== FALSE) {
        return $value;
      }
    }

    return FALSE;
  }
}
