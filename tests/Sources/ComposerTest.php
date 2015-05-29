<?php
/**
 * @file
 */
namespace Discovery\Sources;

/**
 * Class ComposerTest
 * @package Discovery\Sources
 */
class ComposerTest extends \PHPUnit_Framework_TestCase {
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

  public function testGetClasses() {
    $composer = new Composer($this->test_path);

    $classes = array(
      'Test\\Base\\Deep\\Test',
      'Test\\Base\\MultiTest',
      'Test\\Base\\Psr0',
      'Test\\Base\\Test',
      'Test\\Dependency\\Test',
      'Test\\Parent\\Test',
    );
    $this->assertEquals($classes, $composer->getClasses(), 'Composer getClasses');
  }

  /**
   * Test the information returned by getClass().
   */
  public function testGetClass() {
    $composer = new Composer($this->test_path);

    $info = array(
      'type' => 'test',
      'label' => 'Test',
      'description' => 'Test'
    );
    $base = array($info + array('subtype' => 'base', 'meta' => 'Discovery\\BogusMeta'));
    $parent = array($info + array('subtype' => 'parent'));
    $dependency = array($info + array('subtype' => 'dependency'));

    $this->assertEquals($base, $composer->getClass('Test\\Base\\Test'), 'Base composer getClass');
    $this->assertEquals($parent, $composer->getClass('Test\\Parent\\Test'), 'Parent composer getClass');
    $this->assertEquals($dependency, $composer->getClass('Test\\Dependency\\Test'), 'Dependency composer getClass');

    $this->assertFalse($composer->getClass('Test\\Base\\Ignored'), 'Ignored composer getClass');
  }
}

