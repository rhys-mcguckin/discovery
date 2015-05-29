<?php
/**
 * @file
 */
namespace Discovery;


class CacheTest extends \PHPUnit_Framework_TestCase {
  /**
   * @var Cache
   */
  protected $cache;

  /**
   * @var Meta[]
   */
  protected $meta;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->cache = new Cache();
    $this->meta = array(
      'test' => new Meta(
        'Test\\Class', array('type' => 'test', 'subtype' => 'type')
      ),
      'test_subtype' => new Meta(
        'Test\\Class', array('type' => 'test', 'subtype' => 'subtype')
      ),
      'group_test' => new Meta(
        'Test\\Class1', array('type' => 'group', 'subtype' => 'type')
      ),
      'group_dupe' => new Meta(
        'Test\\Class2', array('type' => 'group', 'subtype' => 'type')
      ),
    );
  }

  /**
   * Test the reset and exists functions.
   */
  public function testReset() {
    $cache = $this->cache;
    $meta = $this->meta['test'];

    // Empty exists on creation.
    $this->assertFalse($cache->exists(), 'Exists on creation');

    // Not empty exists after addition.
    $cache->setMeta($meta);
    $this->assertTrue($cache->exists(), 'Exists on addition.');

    // Empty exists on reset
    $cache->reset();
    $this->assertFalse($cache->exists(), 'Exists after reset.');
  }

  /**
   * Test the setMeta and getMeta methods.
   */
  public function testMeta() {
    $cache = $this->cache;
    $test = $this->meta['test'];
    $group = $this->meta['group_test'];
    $dupe = $this->meta['group_dupe'];

    // Test set meta.
    $cache->setMeta($test);
    $this->assertEquals($test, $cache->getMeta('test', 'type'), 'Set/Get check.');

    // Test when meta exists.
    $cache->setMeta($group);
    $cache->setMeta($dupe);
    $this->assertEquals($dupe, $cache->getMeta('group', 'type'), 'Set over existing meta.');

    // Test when meta does not exist.
    $this->assertNull($cache->getMeta('invalid', 'invalid'), 'Meta invalid type and subtype.');
  }

  /**
   * Test the getTypes and getSubtypes methods.
   */
  public function testGetTypes() {
    $cache = $this->cache;
    foreach ($this->meta as $meta) {
      $cache->setMeta($meta);
    }

    // Test existing groups.
    $this->assertEquals(array('test', 'group'), $cache->getTypes(), 'Existing groups.');

    // Test existing subtypes.
    $this->assertEquals(array('type', 'subtype'), $cache->getSubtypes('test'), 'Existing subtypes.');

    // Test subtypes with duplication.
    $this->assertEquals(array('type'), $cache->getSubtypes('group'), 'Duplicated subtypes.');

    // Test non-existing type.
    $this->assertEquals(array(), $cache->getSubtypes('invalid'), 'Invalid type subtypes.');
  }

}
