<?php
/**
 * @file
 */
namespace Discovery\Sources;

/**
 * Class PsrTest
 * @package Discovery\Sources
 */
class PsrTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var string
   */
  protected $test_path;

  /**
   * {@inheritdoc}
   */
  public function setup() {
    parent::setUp();

    $this->test_path = realpath(__DIR__ . '/../../test-data') . '/';
  }

  /**
   * Test the classes returned by getClasses().
   */
  public function testGetClasses() {
    $psr = new Psr($this->test_path . 'src', 'Test\\Base');
    $classes = array(
      'Test\\Base\\Deep\\Test',
      'Test\\Base\\MultiTest',
      'Test\\Base\\Test',
    );
    $this->assertEquals($classes, $psr->getClasses(), 'Psr-4 getClasses');

    $psr = new Psr($this->test_path . 'lib');
    $classes = array(
      'Test\\Base\\Psr0',
    );
    $this->assertEquals($classes, $psr->getClasses(), 'Psr-0 getClasses');
  }

  /**
   * Test the information returned by getClass().
   */
  public function testGetClass() {
    $psr = new Psr($this->test_path . 'src', 'Test\\Base');

    // Check single value for a class.
    $info = array(
      array(
        'type' => 'test',
        'subtype' => 'base',
        'label' => 'Test',
        'description' => 'Test',
        'meta' => 'Discovery\\BogusMeta',
      ),
    );
    $this->assertEquals($info, $psr->getClass('Test\\Base\\Test'), 'Test getClass');

    // Get multiple values for class.
    $info = array(
      array(
        'type' => 'test',
        'subtype' => 'multitest',
        'label' => 'Multi Test',
        'description' => 'Multi Test',
        'arguments' => array(
          'multitest',
        ),
      ),
      array(
        'type' => 'test',
        'subtype' => 'multitest1',
        'label' => 'Multi Test',
        'description' => 'Multi Test',
        'arguments' => array(
          'multitest1',
        ),
      ),
    );
    $this->assertEquals($info, $psr->getClass('\\Test\\Base\\MultiTest'), 'MultiTest getClass');

    // Check single value for nested class.
    $info = array(
      array(
        'type' => 'test',
        'subtype' => 'depth-test',
        'label' => 'Depth Test',
        'description' => 'Depth Test',
        'meta' => 'Test\\Base\\Meta',
      ),
    );
    $this->assertEquals($info, $psr->getClass('\\Test\\Base\\Deep\\Test'), 'Deep Test getClass');

    // Ensure that we do not get information for a non-existent class.
    $this->assertFalse($psr->getClass('Test\\Base\\Ignored'), 'Ignored getClass');
  }
}
