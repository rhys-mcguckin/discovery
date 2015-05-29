<?php
/**
 * @file
 */
namespace Discovery;

use Discovery\Sources\Composer;

class DiscoveryTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var \Discovery\Discovery
   */
  protected $discovery;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $test_path = realpath(__DIR__ . '/../test-data') . '/';

    $this->discovery = new Discovery();
    $this->discovery->addSource(new Composer($test_path));

    // Load the classes for testing purposes.
    require_once($test_path . 'src/Meta.php');
    require_once($test_path . 'src/Test.php');
    require_once($test_path . 'src/MultiTest.php');
  }

  /**
   * Test the getTypes and getSubtypes methods.
   */
  public function testGetTypes() {
    $types = array(
      'test'
    );

    // Expected types.
    $this->assertEquals($this->discovery->getTypes(), $types);

    $subtypes = array(
      'depth-test',
      'multitest',
      'multitest1',
      'psr0',
      'base',
      'dependency',
      'parent',
    );

    // Expected subtypes for existing type.
    $this->assertEquals($this->discovery->getSubtypes('test'), $subtypes);

    // Expected subtypes for non-existing type.
    $this->assertEquals($this->discovery->getSubtypes('invalid'), array());
  }

  /**
   *
   */
  public function testGetMeta() {
    // Check bogus meta.
    $meta = $this->discovery->getMeta('test', 'base');
    $this->assertEquals(get_class($meta), 'Discovery\\Meta');

    // Check good meta.
    $meta = $this->discovery->getMeta('test', 'depth-test');
    $this->assertEquals(get_class($meta), 'Test\\Base\\Meta');

    // Test no meta.
    $meta = $this->discovery->getMeta('test', 'multitest');
    $this->assertEquals(get_class($meta), 'Discovery\\Meta');
  }

  /**
   * Test the results of getClass().
   */
  public function testGetClass() {
    // Get class with invalid type.
    $this->assertNull($this->discovery->getClass('invalid', 'invalid'));

    // Get class with invalid subtype.
    $this->assertNull($this->discovery->getClass('test', 'invalid'));

    // Get class with no arguments.
    $class = $this->discovery->getClass('test', 'base');
    $this->assertEquals(get_class($class), 'Test\\Base\\Test');
    $this->assertFalse($class->isFlag());

    // Get class with no arguments plus extra arguments.
    $class = $this->discovery->getClass('test', 'base', array(array('flag' => TRUE)));
    $this->assertTrue($class->isFlag());

    // Get class with arguments.
    $class = $this->discovery->getClass('test', 'multitest');
    $this->assertEquals(get_class($class), 'Test\\Base\\MultiTest');
    $this->assertEquals($class->getArgument(), 'multitest');
    $this->assertFalse($class->isFlag());

    // Get class with arguments plus extra arguments.
    $class = $this->discovery->getClass('test', 'multitest1', array(array('flag' => TRUE)));
    $this->assertEquals(get_class($class), 'Test\\Base\\MultiTest');
    $this->assertEquals($class->getArgument(), 'multitest1');
    $this->assertTrue($class->isFlag());
  }
}
